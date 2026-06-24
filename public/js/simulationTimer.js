(() => {
    const CYCLE_LENGTH_MINUTES = 24 * 60;
    const SIMULATION_MINUTE_MS = 1000;
    const STORAGE_KEY_ELAPSED = 'simulation_elapsed_minutes';
    const STORAGE_KEY_DATE = 'simulation_date';
    const STORAGE_KEY_TIMESTAMP = 'simulation_last_tick';
    const STORAGE_KEY_SPEED = 'simulation_speed';
    const STORAGE_KEY_PAUSED = 'simulation_paused';

    const START_DATE = new Date(2026, 0, 1); // January 1, 2026

    const clockRoot = document.querySelector('[data-simulation-clock]');
    const clockRing = document.querySelector('[data-simulation-clock-ring]');

    if (!clockRoot) {
        return;
    }

    const timeDisplay = clockRoot.querySelector('[data-simulation-clock-time]');
    const progressBar = clockRoot.querySelector('[data-simulation-clock-progress]');
    const dateDisplay = clockRoot.querySelector('[data-simulation-date]');
    const datePopup = clockRoot.querySelector('[data-simulation-date-popup]');
    const speedInput = clockRoot.querySelector('[data-simulation-speed-input]');
    const speedConfirm = clockRoot.querySelector('[data-simulation-speed-confirm]');
    const pauseButton = clockRoot.querySelector('[data-simulation-pause]');

    let elapsedMinutes = 0;
    let dayOffset = 0; // Number of days since START_DATE
    let timerId = null;
    let minutesPerTick = 24;
    let wasPaused = false;
    let currentYear = 2026;
    let currentMonth = 0; // 0 = January
    let calendarViewYear = 2026;
    let calendarViewMonth = 0;
    let currentDay = 1;

    function isLeapYear(year) {
        return (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
    }

    function daysInMonth(year, month) {
        return [31, isLeapYear(year) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
    }

    function advanceDateByDays(offset, days) {
        let y = 2026;
        let m = 0; // January
        let d = 1 + offset + days;

        while (true) {
            const dim = daysInMonth(y, m);
            if (d <= dim) break;
            d -= dim;
            m++;
            if (m >= 12) {
                m = 0;
                y++;
            }
        }

        return { year: y, month: m, day: d };
    }

    function formatDate(dateObj) {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return months[dateObj.month] + ' ' + dateObj.day + ', ' + dateObj.year;
    }

    function getDayOfWeek(year, month, day) {
        // Zeller-like: 0=Sun, 1=Mon, ..., 6=Sat
        const d = new Date(year, month, day);
        return d.getDay();
    }

    function buildCalendar(year, month, highlightYear, highlightMonth, highlightDay) {
        const monthsFull = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        const totalDays = daysInMonth(year, month);
        const startDow = getDayOfWeek(year, month, 1);
        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        let html = '<table class="calendar-table"><thead><tr>';
        for (let i = 0; i < 7; i++) {
            html += '<th>' + dayNames[i] + '</th>';
        }
        html += '</tr></thead><tbody>';

        let day = 1;
        let done = false;
        for (let row = 0; row < 6 && !done; row++) {
            html += '<tr>';
            for (let col = 0; col < 7; col++) {
                if ((row === 0 && col < startDow) || day > totalDays) {
                    html += '<td class="calendar-empty"></td>';
                } else {
                    const isHighlighted = (year === highlightYear && month === highlightMonth && day === highlightDay);
                    html += '<td class="calendar-day' + (isHighlighted ? ' calendar-day--current' : '') + '">' + day + '</td>';
                    day++;
                    if (day > totalDays) done = true;
                }
            }
            html += '</tr>';
        }
        html += '</tbody></table>';
        return html;
    }

    function refreshCalendarView() {
        const prevBtn = clockRoot.querySelector('[data-calendar-prev]');
        const nextBtn = clockRoot.querySelector('[data-calendar-next]');
        const titleEl = clockRoot.querySelector('[data-calendar-title]');
        const calendarContainer = clockRoot.querySelector('[data-simulation-date-calendar]');
        if (!calendarContainer) return;

        const monthsFull = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        if (titleEl) {
            titleEl.textContent = monthsFull[calendarViewMonth] + ' ' + calendarViewYear;
        }

        calendarContainer.innerHTML = buildCalendar(calendarViewYear, calendarViewMonth, currentYear, currentMonth, currentDay);
    }

    function calendarPrev() {
        calendarViewMonth--;
        if (calendarViewMonth < 0) {
            calendarViewMonth = 11;
            calendarViewYear--;
        }
        refreshCalendarView();
    }

    function calendarNext() {
        calendarViewMonth++;
        if (calendarViewMonth > 11) {
            calendarViewMonth = 0;
            calendarViewYear++;
        }
        refreshCalendarView();
    }

    function syncCalendarViewToCurrentDate() {
        calendarViewYear = currentYear;
        calendarViewMonth = currentMonth;
        refreshCalendarView();
    }

    function getCurrentDate() {
        return advanceDateByDays(0, dayOffset);
    }

    function loadState() {
        const savedElapsed = localStorage.getItem(STORAGE_KEY_ELAPSED);
        const savedDate = localStorage.getItem(STORAGE_KEY_DATE);
        const savedTimestamp = localStorage.getItem(STORAGE_KEY_TIMESTAMP);
        const savedSpeed = localStorage.getItem(STORAGE_KEY_SPEED);
        const savedPaused = localStorage.getItem(STORAGE_KEY_PAUSED);

        elapsedMinutes = savedElapsed !== null ? Number.parseInt(savedElapsed, 10) || 0 : 0;
        dayOffset = savedDate !== null ? Number.parseInt(savedDate, 10) || 0 : 0;
        minutesPerTick = savedSpeed !== null ? Number.parseInt(savedSpeed, 10) || 24 : 24;
        wasPaused = savedPaused === 'true';

        // Clamp speed to a sane range
        if (minutesPerTick < 1) minutesPerTick = 1;
        if (minutesPerTick > CYCLE_LENGTH_MINUTES) minutesPerTick = CYCLE_LENGTH_MINUTES;

        if (speedInput) {
            speedInput.value = String(minutesPerTick);
        }

        // Catch up on missed time since the last tick
        if (savedTimestamp !== null) {
            const lastTick = Number.parseInt(savedTimestamp, 10);
            const now = Date.now();
            const elapsedMs = now - lastTick;

            if (elapsedMs > 0) {
                const missedMinutes = Math.floor(elapsedMs / SIMULATION_MINUTE_MS);
                if (missedMinutes > 0) {
                    const totalMinutes = elapsedMinutes + missedMinutes * minutesPerTick;
                    const loopsCompleted = Math.floor(totalMinutes / CYCLE_LENGTH_MINUTES);
                    elapsedMinutes = totalMinutes % CYCLE_LENGTH_MINUTES;
                    dayOffset += loopsCompleted;
                }
            }
        }
    }

    function saveState() {
        localStorage.setItem(STORAGE_KEY_ELAPSED, String(elapsedMinutes));
        localStorage.setItem(STORAGE_KEY_DATE, String(dayOffset));
        localStorage.setItem(STORAGE_KEY_TIMESTAMP, String(Date.now()));
        localStorage.setItem(STORAGE_KEY_SPEED, String(minutesPerTick));
        localStorage.setItem(STORAGE_KEY_PAUSED, String(timerId === null));
    }

    function formatTime(totalMinutes) {
        const minutesInDay = totalMinutes % CYCLE_LENGTH_MINUTES;
        const hours = Math.floor(minutesInDay / 60);
        const minutes = minutesInDay % 60;

        return String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0');
    }

    function updateCurrentDateTracking() {
        const d = getCurrentDate();
        currentYear = d.year;
        currentMonth = d.month;
        currentDay = d.day;
    }

    function updateClock() {
        const currentMinute = elapsedMinutes % CYCLE_LENGTH_MINUTES;
        const progress = (currentMinute / CYCLE_LENGTH_MINUTES) * 100;
        const progressRatio = progress / 100;
        setDynamicProgress(dayOffset * CYCLE_LENGTH_MINUTES + elapsedMinutes);

        if (timeDisplay) {
            timeDisplay.textContent = formatTime(elapsedMinutes);
        }

        if (progressBar) {
            progressBar.style.setProperty('--simulation-progress', String(progressRatio));
        }

        clockRoot.style.setProperty('--simulation-progress', String(progressRatio));

        updateCurrentDateTracking();

        if (dateDisplay) {
            dateDisplay.textContent = formatDate(getCurrentDate());
        }

        window.dispatchEvent(new CustomEvent('simulation:tick', {
            detail: {
                minute: currentMinute,
                cycleMinute: currentMinute,
                cycleLength: CYCLE_LENGTH_MINUTES,
                progress,
                date: formatDate(getCurrentDate()),
                dayOffset: dayOffset,
            },
        }));
    }

    function advanceClock() {
        const newMinutes = elapsedMinutes + minutesPerTick;
        const cyclesCompleted = Math.floor(newMinutes / CYCLE_LENGTH_MINUTES);

        if (cyclesCompleted > 0) {
            dayOffset += cyclesCompleted;

            if (dateDisplay) {
                dateDisplay.textContent = formatDate(getCurrentDate());
            }

            window.dispatchEvent(new CustomEvent('simulation:loop', {
                detail: {
                    cycleLength: CYCLE_LENGTH_MINUTES,
                    date: formatDate(getCurrentDate()),
                    dayOffset: dayOffset,
                },
            }));
        }

        elapsedMinutes = newMinutes % CYCLE_LENGTH_MINUTES;
        saveState();
        updateClock();
    }

    function restartTimer() {
        if (timerId !== null) {
            window.clearInterval(timerId);
            timerId = null;
        }
        timerId = window.setInterval(advanceClock, SIMULATION_MINUTE_MS);
    }

    function handleSpeedChange() {
        if (!speedInput) return;

        const rawValue = Number.parseInt(speedInput.value, 10);

        if (Number.isNaN(rawValue) || rawValue < 1) {
            minutesPerTick = 1;
            speedInput.value = '1';
        } else if (rawValue > CYCLE_LENGTH_MINUTES) {
            minutesPerTick = CYCLE_LENGTH_MINUTES;
            speedInput.value = String(CYCLE_LENGTH_MINUTES);
        } else {
            minutesPerTick = rawValue;
        }

        saveState();
        restartTimer();
    }

    function getMinuteFromPointerEvent(event) {
        if (!clockRing) {
            return null;
        }

        const rect = clockRing.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;
        const deltaX = event.clientX - centerX;
        const deltaY = event.clientY - centerY;
        const distanceFromCenter = Math.sqrt((deltaX ** 2) + (deltaY ** 2));
        const radius = Math.min(rect.width, rect.height) / 2;

        if (distanceFromCenter > radius) {
            return null;
        }

        const angleFromTop = (Math.atan2(deltaY, deltaX) * 180 / Math.PI + 90 + 360) % 360;
        const minuteOfDay = Math.round((angleFromTop / 360) * CYCLE_LENGTH_MINUTES) % CYCLE_LENGTH_MINUTES;

        return minuteOfDay;
    }

    function snapToQuarterHour(minute) {
        return (Math.round(minute / 15) * 15) % CYCLE_LENGTH_MINUTES;
    }

    function jumpToPointer(event) {
        const minute = getMinuteFromPointerEvent(event);

        if (minute === null) {
            return;
        }

        window.simulationClock.setMinute(snapToQuarterHour(minute));
    }

    function togglePause() {
        if (timerId !== null) {
            window.clearInterval(timerId);
            timerId = null;
            if (pauseButton) {
                pauseButton.textContent = '\u25B6';
                pauseButton.setAttribute('aria-label', 'Resume the simulation');
            }
        } else {
            timerId = window.setInterval(advanceClock, SIMULATION_MINUTE_MS);
            if (pauseButton) {
                pauseButton.textContent = '\u23F8';
                pauseButton.setAttribute('aria-label', 'Pause the simulation');
            }
        }
        saveState();
    }

    window.simulationClock = {
        get minute() {
            return elapsedMinutes % CYCLE_LENGTH_MINUTES;
        },
        get cycleLength() {
            return CYCLE_LENGTH_MINUTES;
        },
        get progress() {
            return (elapsedMinutes % CYCLE_LENGTH_MINUTES) / CYCLE_LENGTH_MINUTES;
        },
        get dayOffset() {
            return dayOffset;
        },
        get date() {
            return formatDate(getCurrentDate());
        },
        get speed() {
            return minutesPerTick;
        },
        setSpeed(val) {
            const parsed = Number.parseInt(val, 10);
            if (Number.isNaN(parsed) || parsed < 1) return;
            minutesPerTick = Math.min(parsed, CYCLE_LENGTH_MINUTES);
            if (speedInput) {
                speedInput.value = String(minutesPerTick);
            }
            saveState();
            restartTimer();
        },
        reset() {
            elapsedMinutes = 0;
            dayOffset = 0;
            if (dateDisplay) {
                dateDisplay.textContent = formatDate(getCurrentDate());
            }
            saveState();
            updateClock();
        },
        setMinute(minute) {
            const normalizedMinute = Number.parseInt(minute, 10) || 0;
            elapsedMinutes = ((normalizedMinute % CYCLE_LENGTH_MINUTES) + CYCLE_LENGTH_MINUTES) % CYCLE_LENGTH_MINUTES;
            saveState();
            updateClock();
        },
        pause() {
            if (timerId !== null) {
                window.clearInterval(timerId);
                timerId = null;
            }
        },
        start() {
            if (timerId !== null) {
                return;
            }

            timerId = window.setInterval(advanceClock, SIMULATION_MINUTE_MS);
        },
    };

    if (clockRing) {
        clockRing.addEventListener('click', jumpToPointer);
    }

    if (speedConfirm) {
        speedConfirm.addEventListener('click', handleSpeedChange);
    }

    if (speedInput) {
        speedInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                handleSpeedChange();
            }
        });
    }

    if (pauseButton) {
        pauseButton.addEventListener('click', togglePause);
    }

    if (dateDisplay && datePopup) {
        // Toggle popup on click
        dateDisplay.addEventListener('click', function (e) {
            e.stopPropagation();
            const isVisible = datePopup.classList.contains('navbar-date-popup--visible');
            if (isVisible) {
                datePopup.setAttribute('aria-hidden', 'true');
                datePopup.classList.remove('navbar-date-popup--visible');
            } else {
                syncCalendarViewToCurrentDate();
                datePopup.setAttribute('aria-hidden', 'false');
                datePopup.classList.add('navbar-date-popup--visible');
            }
        });

        // Auto-sync calendar view when opening if visible was just toggled
        // Close popup when clicking outside
        document.addEventListener('click', function (e) {
            if (!dateDisplay.contains(e.target) && !datePopup.contains(e.target)) {
                datePopup.setAttribute('aria-hidden', 'true');
                datePopup.classList.remove('navbar-date-popup--visible');
            }
        });
    }

    const calendarContainer = clockRoot.querySelector('[data-simulation-date-calendar]');

    if (calendarContainer) {
        calendarContainer.addEventListener('click', function (e) {
            const td = e.target.closest('td');
            if (!td || td.classList.contains('calendar-empty')) return;

            const dayNum = parseInt(td.textContent, 10);
            if (Number.isNaN(dayNum)) return;

            e.stopPropagation();

            // Calculate the day offset for the clicked date (year, month, dayNum)
            let targetDays = 0;
            // Count days from Jan 1, 2026 to the start of the viewed month/year
            for (let y = 2026; y < calendarViewYear; y++) {
                targetDays += isLeapYear(y) ? 366 : 365;
            }
            for (let m = 0; m < calendarViewMonth; m++) {
                targetDays += daysInMonth(calendarViewYear, m);
            }
            // Add the day of month (1-based) - 1 because dayOffset 0 = Jan 1
            targetDays += dayNum - 1;

            dayOffset = targetDays;
            elapsedMinutes = 0;
            saveState();
            updateClock();

            if (dateDisplay) {
                dateDisplay.textContent = formatDate(getCurrentDate());
            }

            // Resync calendar highlight
            syncCalendarViewToCurrentDate();
        });
    }

    const prevBtn = clockRoot.querySelector('[data-calendar-prev]');
    const nextBtn = clockRoot.querySelector('[data-calendar-next]');

    if (prevBtn) {
        prevBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            calendarPrev();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            calendarNext();
        });
    }

    loadState();
    updateClock();

    // Ensure the date display shows the restored value
    if (dateDisplay) {
        dateDisplay.textContent = formatDate(getCurrentDate());
    }

    syncCalendarViewToCurrentDate();

    // If was paused before page navigation, don't start the timer and show play button
    if (wasPaused) {
        timerId = null;
        if (pauseButton) {
            pauseButton.textContent = '\u25B6';
            pauseButton.setAttribute('aria-label', 'Resume the simulation');
        }
    } else {
        window.simulationClock.start();
    }




    const recurringEvents = document.querySelectorAll('[data-type="recurring"]');

    recurringEvents.forEach((eventItem) => {
        eventItem.classList.add('recurring');
        eventItem.addEventListener('click', () => {
            const nextDate = calculateNextDate(eventItem);
            const dateText = document.querySelector(".navbar-date-display-value").textContent.trim();
            const timeText = document.querySelector(".navbar-clock-time").textContent.trim();
            const currentDateTime = new Date(`${dateText} ${timeText}`);
            const msPerTick = minutesPerTick * 60 * 1000;
            const previousTime = new Date(currentDateTime - msPerTick);
            if (previousTime < nextDate && currentDateTime >= nextDate) {
                // event triggered
                console.log("event triggered", nextDate);
                eventItem.classList.remove('event-is-global');
            }
            else {
                setTimeout(() => {
                    const gridImages = document.querySelectorAll('.gridEventImage');

                    gridImages.forEach((img) => {
                        const idGrid = img.dataset.eventId;
                        const idEvent = eventItem.dataset.eventId;
                        if (idGrid === idEvent) {
                            img.classList.add("event-is-global");
                        }
                    });
                }, 500);

            }
        })

        setTimeout(() => {
            window.addEventListener("simulation:tick", () => {
                const gridImages = document.querySelectorAll('.gridEventImage');

                const dateText = document.querySelector(".navbar-date-display-value").textContent.trim();
                const timeText = document.querySelector(".navbar-clock-time").textContent.trim();
                const currentDateTime = new Date(`${dateText} ${timeText}`);

                gridImages.forEach((img) => {
                    const eventId = img.dataset.eventId;

                    fetch('/grid/check-recurring', {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            event_id: eventId,
                            current_datetime: currentDateTime,
                            minutes_per_tick: minutesPerTick
                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.triggered) {
                                console.log("event triggered", eventId);
                                // event triggered

                                img.classList.remove('event-is-global');
                                img.classList.add('event-triggered');
                            } else {
                                if (!data.is_recurring) {
                                    return
                                }
                                else {
                                    img.classList.add('event-is-global');
                                    img.classList.remove('event-triggered');
                                }
                            }

                        });
                });
            });
        }, 100);
    })

    // const nextDate = eventItem.nextDate;
    const dateText = document.querySelector(".navbar-date-display-value").textContent.trim();
    const timeText = document.querySelector(".navbar-clock-time").textContent.trim();

    const currentDateTime = new Date(`${dateText} ${timeText}`);

    console.log(currentDateTime);
})();

function calculateNextDate(eventItem) {
    if (eventItem.getAttribute('active') === 'true') {
        const startDate = new Date(document.querySelector(".navbar-date-display-value").textContent);
        const frequency = eventItem.dataset.frequency;
        const amount = Number(eventItem.dataset.amount);
        console.log(frequency, amount);
        const startTime = eventItem.dataset.startTime;
        const eventId = eventItem.dataset.eventId;

        if (frequency == 'daily') {
            const nextDate = new Date(startDate);
            nextDate.setDate(nextDate.getDate() + amount);

            const newDateTime = new Date(nextDate);
            const [hours, minutes, seconds] = startTime.split(':').map(Number);

            newDateTime.setHours(hours, minutes, seconds);

            const formattedDate = toMysqlDatetime(newDateTime);

            fetch("/grid/save-next-date", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    event_id: eventId,
                    next_date: formattedDate
                })
            });
        }

        else if (frequency == 'weekly') {
            const weekdays = JSON.parse(eventItem.dataset.weekly || "[]")
                .map(d => d.toLowerCase());

            const weekdayMap = {
                sunday: 0,
                monday: 1,
                tuesday: 2,
                wednesday: 3,
                thursday: 4,
                friday: 5,
                saturday: 6
            };

            const currentDay = startDate.getDay();

            let minDiff = Infinity;

            for (const dayName of weekdays) {
                const targetDay = weekdayMap[dayName];

                if (targetDay === undefined) continue;

                let diff = targetDay - currentDay;

                if (diff < 0) diff += 7;

                if (diff < minDiff) {
                    minDiff = diff;
                }
            }

            const nextDate = new Date(startDate);
            nextDate.setDate(startDate.getDate() + minDiff + ((amount - 1) * 7));

            const newDateTime = new Date(nextDate);
            const [hours, minutes, seconds] = startTime.split(':').map(Number);

            newDateTime.setHours(hours, minutes, seconds);
            const formattedDate = toMysqlDatetime(newDateTime);
            fetch("/grid/save-next-date", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    event_id: eventId,
                    next_date: formattedDate
                })
            });
        }

        else if (frequency == "monthly") {
            const days = JSON.parse(eventItem.dataset.dateNumber || "[]")
                .map(Number)
                .filter(n => Number.isFinite(n) && n > 0);

            if (days.length == 0) {
                const ordinals = JSON.parse(eventItem.dataset.ordinalNumber || "[]");
                const weekdays = JSON.parse(eventItem.dataset.weekday || "[]");

                const rules = ordinals
                    .map((o, i) => ({
                        ordinal: o,
                        weekday: weekdays[i]
                    }))
                    .filter(r => r.ordinal && r.weekday);

                const weekdayMap = {
                    sunday: 0,
                    monday: 1,
                    tuesday: 2,
                    wednesday: 3,
                    thursday: 4,
                    friday: 5,
                    saturday: 6
                };

                const ordinalMap = {
                    first: 0,
                    second: 1,
                    third: 2,
                    fourth: 3,
                    fifth: 4
                };

                function getWeekdayMatches(year, month, weekday) {
                    const result = [];
                    const date = new Date(year, month, 1);

                    while (date.getMonth() === month) {
                        if (date.getDay() === weekdayMap[weekday]) {
                            result.push(new Date(date));
                        }
                        date.setDate(date.getDate() + 1);
                    }

                    return result;
                }

                const start = new Date(startDate);
                const year = start.getFullYear();
                const month = start.getMonth();

                let nextDate = null;

                for (const rule of rules) {

                    let targetMonth = month;
                    let targetYear = year;

                    const matches = getWeekdayMatches(targetYear, targetMonth, rule.weekday);

                    let target;

                    if (rule.ordinal === "last") {
                        target = matches.at(-1);
                    }
                    else if (rule.ordinal === "next to last") {
                        target = matches.at(-2);
                    }
                    else {
                        target = matches[ordinalMap[rule.ordinal]];
                    }

                    if (target && target < start) {

                        const future = new Date(year, month + amount, 1);

                        const nextMatches = getWeekdayMatches(
                            future.getFullYear(),
                            future.getMonth(),
                            rule.weekday
                        );

                        if (rule.ordinal === "last") {
                            target = nextMatches.at(-1);
                        }
                        else if (rule.ordinal === "next to last") {
                            target = nextMatches.at(-2);
                        }
                        else {
                            target = nextMatches[ordinalMap[rule.ordinal]];
                        }
                    }

                    if (!nextDate || (target && target < nextDate)) {
                        nextDate = target;
                    }
                }
                const newDateTime = new Date(nextDate);
                const [hours, minutes, seconds] = startTime.split(':').map(Number);

                newDateTime.setHours(hours, minutes, seconds);

                const formattedDate = toMysqlDatetime(newDateTime);

                fetch("/grid/save-next-date", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        event_id: eventId,
                        next_date: formattedDate
                    })
                });
            }
            else {

                const currentDay = startDate.getDate();

                let bestDiff = Infinity;
                let bestDay = null;

                for (const day of days) {
                    let diff = day - currentDay;

                    if (diff >= 0 && diff < bestDiff) {
                        bestDiff = diff;
                        bestDay = day;
                    }
                }

                const nextDate = new Date(startDate);

                if (bestDay !== null) {
                    nextDate.setDate(bestDay);
                } else {
                    nextDate.setMonth(nextDate.getMonth() + amount);

                    nextDate.setDate(Math.min(...days));
                }

                const newDateTime = new Date(nextDate);
                const [hours, minutes, seconds] = startTime.split(':').map(Number);

                newDateTime.setHours(hours, minutes, seconds);

                const formattedDate = toMysqlDatetime(newDateTime);

                fetch("/grid/save-next-date", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        event_id: eventId,
                        next_date: formattedDate
                    })
                });
            }
        }

        else if (frequency == 'yearly') {
            const nextDate = new Date(startDate);

            nextDate.setFullYear(startDate.getFullYear() + amount);

            const newDateTime = new Date(nextDate);
            const [hours, minutes, seconds] = startTime.split(':').map(Number);

            newDateTime.setHours(hours, minutes, seconds);

            fetch("/grid/save-next-date", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    event_id: eventId,
                    next_date: newDateTime
                })
            });
        }
    }

    function toMysqlDatetime(date) {
        const d = new Date(date);

        const pad = (n) => String(n).padStart(2, '0');

        return (
            d.getFullYear() + '-' +
            pad(d.getMonth() + 1) + '-' +
            pad(d.getDate()) + ' ' +
            pad(d.getHours()) + ':' +
            pad(d.getMinutes()) + ':' +
            pad(d.getSeconds())
        );
    }
}
