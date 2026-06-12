(() => {
    const CYCLE_LENGTH_MINUTES = 24 * 60;
    const SIMULATION_MINUTE_MS = 1000;

    const clockRoot = document.querySelector('[data-simulation-clock]');
    const clockRing = document.querySelector('[data-simulation-clock-ring]');

    if (!clockRoot) {
        return;
    }

    const timeDisplay = clockRoot.querySelector('[data-simulation-clock-time]');
    const progressBar = clockRoot.querySelector('[data-simulation-clock-progress]');
    const dayCounter = clockRoot.querySelector('[data-simulation-day-counter]');

    let elapsedMinutes = 0;
    let dayCount = 0;
    let timerId = null;

    function formatTime(totalMinutes) {
        const minutesInDay = totalMinutes % CYCLE_LENGTH_MINUTES;
        const hours = Math.floor(minutesInDay / 60);
        const minutes = minutesInDay % 60;

        return String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0');
    }

    function updateClock() {
        const currentMinute = elapsedMinutes % CYCLE_LENGTH_MINUTES;
        const progress = (currentMinute / CYCLE_LENGTH_MINUTES) * 100;
        const progressRatio = progress / 100;

        if (timeDisplay) {
            timeDisplay.textContent = formatTime(elapsedMinutes);
        }

        if (progressBar) {
            progressBar.style.setProperty('--simulation-progress', String(progressRatio));
        }

        clockRoot.style.setProperty('--simulation-progress', String(progressRatio));

        window.dispatchEvent(new CustomEvent('simulation:tick', {
            detail: {
                minute: currentMinute,
                cycleMinute: currentMinute,
                cycleLength: CYCLE_LENGTH_MINUTES,
                progress,
            },
        }));
    }

    function advanceClock() {
        elapsedMinutes = (elapsedMinutes + 24) % CYCLE_LENGTH_MINUTES;

        if (elapsedMinutes === 0) {
            dayCount += 1;

            if (dayCounter) {
                dayCounter.textContent = String(dayCount);
            }

            window.dispatchEvent(new CustomEvent('simulation:loop', {
                detail: {
                    cycleLength: CYCLE_LENGTH_MINUTES,
                    day: dayCount,
                },
            }));
        }

        updateClock();
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
        get day() {
            return dayCount;
        },
        reset() {
            elapsedMinutes = 0;
            dayCount = 0;
            if (dayCounter) {
                dayCounter.textContent = '0';
            }
            updateClock();
        },
        setMinute(minute) {
            const normalizedMinute = Number.parseInt(minute, 10) || 0;
            elapsedMinutes = ((normalizedMinute % CYCLE_LENGTH_MINUTES) + CYCLE_LENGTH_MINUTES) % CYCLE_LENGTH_MINUTES;
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

    updateClock();
    window.simulationClock.start();
})();