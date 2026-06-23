<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

<script type="text/javascript" src="{{ asset('js/functionTable.js') }}" defer></script>
<script src="{{ asset('js/gridDragDrop.js') }}" defer></script>
<script src="{{ asset('js/delete.js') }}" defer></script>
<script src="{{ asset('js/switchFunctionEvent.js') }}" defer></script>
<script src="{{ asset('js/mainroads.js') }}" defer></script>
<script src="{{ asset('js/simulationTimer.js') }}" defer></script>

<link href="{{ asset('css/functionTableStyle.css') }}" type="text/css" rel="stylesheet"/>
<link href="{{ asset('css/gridStyle.css') }}" type="text/css" rel="stylesheet"/>
<link href="{{ asset('css/effectTableStyle.css') }}" type="text/css" rel="stylesheet"/>
<link href="{{ asset('css/layout.css') }}" type="text/css" rel="stylesheet"/>
<link href="{{ asset('css/navbarStyle.css') }}" type="text/css" rel="stylesheet"/>
<link href="{{ asset('css/functionEventsTableStyle.css') }}" type="text/css" rel="stylesheet"/>
<link href="{{ asset('css/eventTableStyle.css') }}" type="text/css" rel="stylesheet"/>

<title>Metropolis</title>

</head>
<body>
    <x-navbar/>

<div class="container">
    <div class="gridSection">
        <div class="global-events-zone" data-global-drop-zone>
            <div class="global-events-zone-header">
                <span class="global-events-zone-label">Global Events</span>
                <span class="global-events-zone-hint">Drop events here to apply to entire grid</span>
            </div>

            <div class="global-events-zone-items">
                @if(isset($globalEvents) && $globalEvents->count() > 0)
                    @foreach($globalEvents as $globalEvent)
                        <div class="global-event-chip" data-global-event-id="{{ $globalEvent->id }}">
                            <img
                                src="{{ asset($globalEvent->image_url) }}"
                                alt=""
                                class="global-event-chip-icon"
                            >

                            <span class="global-event-chip-name">{{ $globalEvent->name }}</span>

                            <button
                                class="global-event-remove"
                                type="button"
                                data-remove-global="{{ $globalEvent->id }}"
                                aria-label="Remove {{ $globalEvent->name }} from global events"
                            >&times;</button>

                            <div class="global-event-tooltip hidden">
                                <div class="global-event-tooltip-header">
                                    <img
                                        src="{{ asset($globalEvent->image_url) }}"
                                        alt=""
                                        class="global-event-tooltip-icon"
                                    >

                                    <div>
                                        <strong>{{ $globalEvent->name }}</strong>

                                        @if($globalEvent->name === 'Day')
                                            <br>
                                            <span class="global-event-tooltip-duration">
                                                Active: 6:00 - 18:00
                                            </span>
                                        @elseif($globalEvent->name === 'Night')
                                            <br>
                                            <span class="global-event-tooltip-duration">
                                                Active: 18:00 - 6:00
                                            </span>
                                        @elseif($globalEvent->length)
                                            <br>
                                            <span class="global-event-tooltip-duration">
                                                Duration: {{ $globalEvent->length }} {{ $globalEvent->length_unit }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="global-event-tooltip-effects">
                                    <strong>Effects:</strong>

                                    <ul>
                                        @foreach($globalEvent->effects as $effect)
                                            <li class="{{ $effect->effect > 0 ? 'positiveEffect' : ($effect->effect < 0 ? 'negativeEffect' : 'neutralEffect') }}">
                                                {{ $effect->category_name }}: {{ $effect->effect > 0 ? '+' : '' }}{{ $effect->effect }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <span class="global-events-zone-empty">No global events active</span>
                @endif
            </div>
        </div>

        <x-grid
            :cells="$cells"
            :categories="$categories"
            :event-grid-cells="$eventGridCells"
        />

        <div class="road-controls" aria-label="Main road controls">
            <button id="toggle-mainroad-button" type="button">
                Show Main Road Overlay
            </button>

            <button id="createRouteButton" type="button">
                Create Route
            </button>
        </div>

        <div class="gridSavePanel">
            <form method="POST" action="{{ route('grid.save') }}" class="gridSaveForm">
                @csrf

                
                <input
                    id="savedGridName"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Grid layout"
                    maxlength="255"
                    required
                >

                <button type="submit">Save grid</button>
            </form>

            @if (session('status'))
                <p class="gridSaveMessage">{{ session('status') }}</p>
            @endif

            @error('name')
                <p class="gridSaveError">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="sidebar">
        <div class="effectsSection">
            <x-effectTable
                :categories="$categories"
                :effect-totals="$effectTotals"
                :quality-of-life="$qualityOfLife"
            />
        </div>

        <div class="functionsSection">
            <x-functionTable :functions="$functions" :categories="$categories" :events="$events" />
        </div>
    </div>
</div>

<script>
    // Day/Night: smoothly transition blues based on simulation time
    // function lerpColor(a, b, t) {
    //     t = Math.max(0, Math.min(1, t));
    //     var ah = parseInt(a.replace('#', ''), 16);
    //     var bh = parseInt(b.replace('#', ''), 16);
    //     var ar = (ah >> 16) & 0xff, ag = (ah >> 8) & 0xff, ab = ah & 0xff;
    //     var br = (bh >> 16) & 0xff, bg = (bh >> 8) & 0xff, bb = bh & 0xff;
    //     var rr = Math.round(ar + (br - ar) * t);
    //     var rg = Math.round(ag + (bg - ag) * t);
    //     var rb = Math.round(ab + (bb - ab) * t);
    //     return '#' + ((rr << 16) | (rg << 8) | rb).toString(16).padStart(6, '0');
    // }

    // function updateDayNightColors(simulationMinute) {
    //     var cycleLength = 24 * 60; // 1440 minutes
    //     var normalizedMinute = ((simulationMinute % cycleLength) + cycleLength) % cycleLength;

    //     // Day: 6:00 (360) to 18:00 (1080)
    //     // Night: 18:00 (1080) to 6:00 (360)
    //     var dayStart = 360;
    //     var dayEnd = 1080;
    //     var nightStart = 1080;
    //     var nightEnd = 360;

    //     var dayBlue = '#add8e6'; // lightblue
    //     var nightBlue = '#5072a4'; // medium blue for night

    //     var t; // 0 = day, 1 = night

    //     if (normalizedMinute >= dayStart && normalizedMinute < dayEnd) {
    //         // During day: 6:00-18:00, stays at dayBlue
    //         t = 0;
    //     } else if (normalizedMinute >= dayEnd && normalizedMinute < nightStart + cycleLength) {
    //         // Evening transition: 18:00-24:00 (1080-1440)
    //         var eveningProgress = (normalizedMinute - dayEnd) / (cycleLength - dayEnd);
    //         t = eveningProgress;
    //     } else {
    //         // Night to morning: 0:00-6:00 (0-360)
    //         var morningProgress = normalizedMinute / dayStart;
    //         t = 1 - morningProgress;
    //     }

    //     var currentBlue = lerpColor(dayBlue, nightBlue, t);
    //     var navbar = document.querySelector('.navbar');
    //     if (navbar) {
    //         navbar.style.backgroundColor = currentBlue;
    //     }
    // }

    // window.addEventListener('simulation:tick', function (event) {
    //     var minute = event.detail.minute || event.detail.cycleMinute;
    //     if (minute === undefined) return;

    //     updateDayNightColors(minute);

    //     fetch('/grid/check-day-night', {
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/json',
    //             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    //         },
    //         body: JSON.stringify({
    //             simulation_minute: minute
    //         })
    //     })
    //     .then(function (response) { return response.json(); })
    //     .then(function (data) {
    //         if (data.success && data.changed) {
    //             if (data.effectTotals) {
    //                 window.updateEffectTable(data.effectTotals, data.qualityOfLife);
    //             }
    //             // Update the global events zone
    //             var zoneItems = document.querySelector('.global-events-zone-items');
    //             if (zoneItems && data.globalEvents) {
    //                 updateGlobalEventsZone(zoneItems, data.globalEvents);
    //             }
    //         }
    //     })
    //     .catch(function (error) {
    //         console.error('Day/night check error:', error);
    //     });
    // });

    // function updateGlobalEventsZone(zoneItems, globalEvents) {
    //     if (globalEvents.length > 0) {
    //         var html = '';
    //         globalEvents.forEach(function (evt) {
    //             var activeTime = '';
    //             if (evt.name === 'Day') activeTime = 'Active: 6:00 - 18:00';
    //             else if (evt.name === 'Night') activeTime = 'Active: 18:00 - 6:00';
    //             else if (evt.length) activeTime = 'Duration: ' + evt.length + ' ' + evt.length_unit;

    //             html += '<div class="global-event-chip" data-global-event-id="' + evt.id + '">';
    //             html += '<img src="' + evt.image_url + '" alt="" class="global-event-chip-icon">';
    //             html += '<span class="global-event-chip-name">' + evt.name + '</span>';
    //             html += '<button class="global-event-remove" type="button" data-remove-global="' + evt.id + '" aria-label="Remove ' + evt.name + ' from global events">&times;</button>';
    //             html += '<div class="global-event-tooltip hidden">';
    //             html += '<div class="global-event-tooltip-header">';
    //             html += '<img src="' + evt.image_url + '" alt="" class="global-event-tooltip-icon">';
    //             html += '<div><strong>' + evt.name + '</strong><br><span class="global-event-tooltip-duration">' + activeTime + '</span></div></div>';
    //             html += '<div class="global-event-tooltip-effects"><strong>Effects:</strong><ul>';
    //             if (evt.effects) {
    //                 evt.effects.forEach(function (eff) {
    //                     var cls = eff.effect > 0 ? 'positiveEffect' : (eff.effect < 0 ? 'negativeEffect' : 'neutralEffect');
    //                     html += '<li class="' + cls + '">' + eff.category_name + ': ' + (eff.effect > 0 ? '+' : '') + eff.effect + '</li>';
    //                 });
    //             }
    //             html += '</ul></div></div></div>';
    //         });
    //         zoneItems.innerHTML = html;
    //     } else {
    //         zoneItems.innerHTML = '<span class="global-events-zone-empty">No global events active</span>';
    //     }
    //     // Re-attach tooltip event listeners
    //     document.querySelectorAll('.global-event-chip').forEach(function (chip) {
    //         var tooltip = chip.querySelector('.global-event-tooltip');
    //         chip.addEventListener('mouseenter', function () { tooltip.classList.remove('hidden'); });
    //         chip.addEventListener('mouseleave', function () { tooltip.classList.add('hidden'); });
    //     });
    //     attachGlobalRemoveListeners();
    // }

    // document.addEventListener('DOMContentLoaded', function () {
    //     // Tooltip hover for global chips
    //     var chips = document.querySelectorAll('.global-event-chip');

    //     chips.forEach(function (chip) {
    //         var tooltip = chip.querySelector('.global-event-tooltip');

    //         chip.addEventListener('mouseenter', function () {
    //             tooltip.classList.remove('hidden');
    //         });

    //         chip.addEventListener('mouseleave', function () {
    //             tooltip.classList.add('hidden');
    //         });
    //     });

    //     // Global drop zone: drag-and-drop events from library or grid
    //     var dropZone = document.querySelector('[data-global-drop-zone]');

    //     if (dropZone) {
    //         dropZone.addEventListener('dragover', function (ev) {
    //             ev.preventDefault();
    //             dropZone.classList.add('global-events-zone--dragover');
    //         });

    //         dropZone.addEventListener('dragleave', function (ev) {
    //             // Only remove highlight if leaving the zone entirely
    //             if (!dropZone.contains(ev.relatedTarget)) {
    //                 dropZone.classList.remove('global-events-zone--dragover');
    //             }
    //         });

    //         dropZone.addEventListener('drop', function (ev) {
    //             ev.preventDefault();
    //             dropZone.classList.remove('global-events-zone--dragover');

    //             var dragData = null;
    //             var rawData = ev.dataTransfer.getData('text/plain');

    //             if (rawData) {
    //                 try {
    //                     dragData = JSON.parse(rawData);
    //                 } catch (e) {
    //                     return;
    //                 }
    //             }

    //             if (!dragData) return;

    //             // Only handle events (from library or from grid)
    //             if (dragData.source !== 'eventLibrary' && dragData.source !== 'gridEvent') {
    //                 return;
    //             }

    //             var eventId = dragData.eventId;
    //             if (!eventId) return;

    //             // If event was on a grid cell, remove it from there first
    //             if (dragData.source === 'gridEvent' && dragData.fromCellId) {
    //                 if (typeof window.removeEventFromGrid === 'function') {
    //                     window.removeEventFromGrid(dragData.fromCellId, eventId, false);
    //                 }
    //                 var fromCell = document.querySelector('.gridCell[data-id="' + dragData.fromCellId + '"]');
    //                 if (fromCell) {
    //                     var eventImg = fromCell.querySelector('.gridEventImage[data-event-id="' + eventId + '"]');
    //                     if (eventImg) eventImg.remove();
    //                     var container = fromCell.querySelector('.gridEvents');
    //                     if (container && container.children.length === 0) container.remove();
    //                     if (!fromCell.querySelector('.gridImage') && !fromCell.querySelector('.gridEventImage')) {
    //                         fromCell.classList.remove('occupied');
    //                         fromCell.classList.add('available');
    //                     }
    //                 }
    //             }

    //             // Toggle this event as global
    //             fetch('/grid/toggle-global-event', {
    //                 method: 'POST',
    //                 headers: {
    //                     'Content-Type': 'application/json',
    //                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    //                 },
    //                 body: JSON.stringify({
    //                     event_id: eventId
    //                 })
    //             })
    //             .then(function (response) { return response.json(); })
    //             .then(function (data) {
    //                 if (data.success) {
    //                     if (data.effectTotals) {
    //                         window.updateEffectTable(data.effectTotals, data.qualityOfLife);
    //                     }
    //                     // Reload the global events zone
    //                     var zoneItems = dropZone.querySelector('.global-events-zone-items');
    //                     if (zoneItems && data.globalEvents) {
    //                         if (data.globalEvents.length > 0) {
    //                             var html = '';
    //                             data.globalEvents.forEach(function (evt) {
    //                                 html += '<div class="global-event-chip" data-global-event-id="' + evt.id + '">';
    //                                 html += '<img src="' + evt.image_url + '" alt="" class="global-event-chip-icon">';
    //                                 html += '<span class="global-event-chip-name">' + evt.name + '</span>';
    //                                 html += '<button class="global-event-remove" type="button" data-remove-global="' + evt.id + '" aria-label="Remove ' + evt.name + ' from global events">&times;</button>';
    //                                 html += '<div class="global-event-tooltip hidden">';
    //                                 html += '<div class="global-event-tooltip-header">';
    //                                 html += '<img src="' + evt.image_url + '" alt="" class="global-event-tooltip-icon">';
    //                                 html += '<div><strong>' + evt.name + '</strong></div></div>';
    //                                 html += '<div class="global-event-tooltip-effects"><strong>Effects:</strong><ul>';
    //                                 if (evt.effects) {
    //                                     evt.effects.forEach(function (eff) {
    //                                         var cls = eff.effect > 0 ? 'positiveEffect' : (eff.effect < 0 ? 'negativeEffect' : 'neutralEffect');
    //                                         html += '<li class="' + cls + '">' + eff.category_name + ': ' + (eff.effect > 0 ? '+' : '') + eff.effect + '</li>';
    //                                     });
    //                                 }
    //                                 html += '</ul></div></div></div>';
    //                             });
    //                             zoneItems.innerHTML = html;
    //                         } else {
    //                             zoneItems.innerHTML = '<span class="global-events-zone-empty">No global events active</span>';
    //                         }
    //                         // Re-attach tooltip event listeners
    //                         document.querySelectorAll('.global-event-chip').forEach(function (chip) {
    //                             var tooltip = chip.querySelector('.global-event-tooltip');
    //                             chip.addEventListener('mouseenter', function () { tooltip.classList.remove('hidden'); });
    //                             chip.addEventListener('mouseleave', function () { tooltip.classList.add('hidden'); });
    //                         });
    //                         // Re-attach remove button listeners
    //                         attachGlobalRemoveListeners();
    //                     }
    //                 }
    //             })
    //             .catch(function (error) {
    //                 console.error('Global event toggle error:', error);
    //             });
    //         });
    //     }

    //     // Remove button for global events
    //     function attachGlobalRemoveListeners() {
    //         document.querySelectorAll('[data-remove-global]').forEach(function (btn) {
    //             btn.removeEventListener('click', handleGlobalRemove);
    //             btn.addEventListener('click', handleGlobalRemove);
    //         });
    //     }

    //     function handleGlobalRemove(ev) {
    //         ev.stopPropagation();
    //         var eventId = ev.currentTarget.getAttribute('data-remove-global');
    //         if (!eventId) return;

    //         fetch('/grid/toggle-global-event', {
    //             method: 'POST',
    //             headers: {
    //                 'Content-Type': 'application/json',
    //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    //             },
    //             body: JSON.stringify({
    //                 event_id: eventId
    //             })
    //         })
    //         .then(function (response) { return response.json(); })
    //         .then(function (data) {
    //             if (data.success) {
    //                 if (data.effectTotals) {
    //                     window.updateEffectTable(data.effectTotals, data.qualityOfLife);
    //                 }
    //                 var zoneItems = document.querySelector('.global-events-zone-items');
    //                 if (zoneItems && data.globalEvents) {
    //                     if (data.globalEvents.length > 0) {
    //                         var html = '';
    //                         data.globalEvents.forEach(function (evt) {
    //                             html += '<div class="global-event-chip" data-global-event-id="' + evt.id + '">';
    //                             html += '<img src="' + evt.image_url + '" alt="" class="global-event-chip-icon">';
    //                             html += '<span class="global-event-chip-name">' + evt.name + '</span>';
    //                             html += '<button class="global-event-remove" type="button" data-remove-global="' + evt.id + '" aria-label="Remove ' + evt.name + ' from global events">&times;</button>';
    //                             html += '<div class="global-event-tooltip hidden">';
    //                             html += '<div class="global-event-tooltip-header">';
    //                             html += '<img src="' + evt.image_url + '" alt="" class="global-event-tooltip-icon">';
    //                             html += '<div><strong>' + evt.name + '</strong></div></div>';
    //                             html += '<div class="global-event-tooltip-effects"><strong>Effects:</strong><ul>';
    //                             if (evt.effects) {
    //                                 evt.effects.forEach(function (eff) {
    //                                     var cls = eff.effect > 0 ? 'positiveEffect' : (eff.effect < 0 ? 'negativeEffect' : 'neutralEffect');
    //                                     html += '<li class="' + cls + '">' + eff.category_name + ': ' + (eff.effect > 0 ? '+' : '') + eff.effect + '</li>';
    //                                 });
    //                             }
    //                             html += '</ul></div></div></div>';
    //                         });
    //                         zoneItems.innerHTML = html;
    //                     } else {
    //                         zoneItems.innerHTML = '<span class="global-events-zone-empty">No global events active</span>';
    //                     }
    //                     document.querySelectorAll('.global-event-chip').forEach(function (chip) {
    //                         var tooltip = chip.querySelector('.global-event-tooltip');
    //                         chip.addEventListener('mouseenter', function () { tooltip.classList.remove('hidden'); });
    //                         chip.addEventListener('mouseleave', function () { tooltip.classList.add('hidden'); });
    //                     });
    //                     attachGlobalRemoveListeners();
    //                 }
    //             }
    //         })
    //         .catch(function (error) {
    //             console.error('Global event remove error:', error);
    //         });
    //     }

    //     attachGlobalRemoveListeners();
    // });
</script>

</body>
</html>
