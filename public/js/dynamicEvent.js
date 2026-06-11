const timerSpeed = 60000;

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

function nextEvent(ev,recurr,len){
    let eventId = ev.getAttribute("data-event-id");
    let routeState = parseInt(ev.getAttribute("route-state"));
    let followUp = document.querySelector(`.gridEventImage[data-event-id="${eventId}"][route-state="${routeState%len+1}"]`);
    ev.style.visibility = 'hidden';
    followUp.style.visibility = 'visible';
    if (routeState == len && !recurr){
        followUp.style.visibility = 'hidden';
    }
}

function routeAnimation (path, recurr){
    path.forEach((point) =>{
        setTimeout(() => nextEvent(point,recurr,path.length),timerSpeed*parseFloat(point.getAttribute("event-speed"))*parseInt(point.getAttribute("route-state")));
    });
}

eventIDs.forEach((event) =>{
    let route = document.querySelectorAll(`.gridEventImage[data-event-id="${event}"]`);
    let recurrence = route[0].getAttribute('recurring') != '';
    routeAnimation(route,recurrence);
    if (recurrence){
        setInterval(() => routeAnimation(route,recurrence), timerSpeed*route[0].getAttribute("event-speed")*route.length);
    }
});