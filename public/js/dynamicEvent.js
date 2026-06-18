const timerSpeed = 5000;

let events = document.querySelectorAll(".gridEventImage");
let allIDs = [];
events.forEach((event) =>{
    event.style.visibility = 'hidden';
    allIDs.push(event.getAttribute("data-event-id"));
});

function onlyUnique(value, index, array) {
  return array.indexOf(value) === index;
}

var eventIDs = allIDs.filter(onlyUnique);

function setDynamicProgress(minTimer){
    let elapsedHours = Math.floor(minTimer/60);
    eventIDs.forEach((event) =>{
        let route = document.querySelectorAll(`.gridEventImage[data-event-id="${event}"]`);
        route.forEach((point) =>{
            let currentLower = point.getAttribute("event-speed")*(point.getAttribute("route-state")-1);
            let currentUpper = point.getAttribute("event-speed")*(point.getAttribute("route-state"));
                point.style.visibility = 'hidden';
                if (currentLower <= elapsedHours && elapsedHours < currentUpper){
                   point.style.visibility = 'visible';
                }
        });
    });
}