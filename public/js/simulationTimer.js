(() => {
    const CYCLE_LENGTH_MINUTES = 24 * 60;
    const SIMULATION_MINUTE_MS = 1000;
    const STORAGE_KEY_ELAPSED = 'simulation_elapsed_minutes';
    const STORAGE_KEY_DAYS = 'simulation_day_count';
    const STORAGE_KEY_TIMESTAMP = 'simulation_last_tick';
    const STORAGE_KEY_SPEED = 'simulation_speed';

    const clockRoot = document.querySelector('[data-simulation-clock]');
    const clockRing = document.querySelector('[data-simulation-clock-ring]');

    if (!clockRoot) {
        return;
    }

    const timeDisplay = clockRoot.querySelector('[data-simulation-clock-time]');
    const progressBar = clockRoot.querySelector('[data-simulation-clock-progress]');
    const dayCounter = clockRoot.querySelector('[data-simulation-day-counter]');
    const speedInput = clockRoot.querySelector('[data-simulation-speed-input]');
    const speedConfirm = clockRoot.querySelector('[data-simulation-speed-confirm]');
    const pauseButton = clockRoot.querySelector('[data-simulation-pause]');

    let elapsedMinutes = 0;
    let dayCount = 0;
    let timerId = null;
    let minutesPerTick = 24;

    function loadState() {
        const savedElapsed = localStorage.getItem(STORAGE_KEY_ELAPSED);
        const savedDays = localStorage.getItem(STORAGE_KEY_DAYS);
        const savedTimestamp = localStorage.getItem(STORAGE_KEY_TIMESTAMP);
        const savedSpeed = localStorage.getItem(STORAGE_KEY_SPEED);

        elapsedMinutes = savedElapsed !== null ? Number.parseInt(savedElapsed, 10) || 0 : 0;
        dayCount = savedDays !== null ? Number.parseInt(savedDays, 10) || 0 : 0;
        minutesPerTick = savedSpeed !== null ? Number.parseInt(savedSpeed, 10) || 24 : 24;

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
                    dayCount += loopsCompleted;
                }
            }
        }
    }

    function saveState() {
        localStorage.setItem(STORAGE_KEY_ELAPSED, String(elapsedMinutes));
        localStorage.setItem(STORAGE_KEY_DAYS, String(dayCount));
        localStorage.setItem(STORAGE_KEY_TIMESTAMP, String(Date.now()));
        localStorage.setItem(STORAGE_KEY_SPEED, String(minutesPerTick));
    }

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

        if (dayCounter) {
            dayCounter.textContent = String(dayCount);
        }

        window.dispatchEvent(new CustomEvent('simulation:tick', {
            detail: {
                minute: currentMinute,
                cycleMinute: currentMinute,
                cycleLength: CYCLE_LENGTH_MINUTES,
                progress,
                day: dayCount,
            },
        }));
    }

    function advanceClock() {
        elapsedMinutes = (elapsedMinutes + minutesPerTick) % CYCLE_LENGTH_MINUTES;

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

        console.log('Speed change triggered, input value:', speedInput.value);

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
            dayCount = 0;
            if (dayCounter) {
                dayCounter.textContent = '0';
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

    loadState();
    updateClock();

    // If the day counter HTML element exists, ensure it shows the restored value
    if (dayCounter) {
        dayCounter.textContent = String(dayCount);
    }

    window.simulationClock.start();
})();