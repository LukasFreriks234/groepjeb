let events = document.querySelectorAll(".gridEventImage");
let allIDs = [];
events.forEach((event) =>{
    if(event.getAttribute("route-state") != 1){
        event.style.visibility = 'hidden';
    }
    allIDs.push(event.getAttribute("data-event-id"));
});

function onlyUnique(value, index, array) {
  return array.indexOf(value) === index;
}

var eventIDs = allIDs.filter(onlyUnique);

function nextEvent(ev,len){
    let eventId = ev.getAttribute("data-event-id");
    let routeState = parseInt(ev.getAttribute("route-state"));
    let followUp = document.querySelector(`.gridEventImage[data-event-id="${eventId}"][route-state="${routeState+1}"]`);
    ev.style.visibility = 'hidden';
    followUp.style.visibility = 'visible';
}

eventIDs.forEach((event) =>{
    let route = document.querySelectorAll(`.gridEventImage[data-event-id="${event}"]`);
    route.forEach((point) =>{
        setTimeout(() => nextEvent(point,route.length),60000*parseFloat(point.getAttribute("event-speed"))*parseInt(point.getAttribute("route-state")));
    });
});