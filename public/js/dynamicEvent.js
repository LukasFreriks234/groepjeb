const timerSpeed = 5000;

const events = document.querySelectorAll(`.gridEventImage[dynamic-event="1"]`);
let allIDs = [];
events.forEach((event) =>{
    event.classList.remove("draggableGridEvent", "gridEventImage");
    event.classList.add("gridEventDynamicImage");
    event.setAttribute("draggable", "false");
    event.setAttribute("data-drag-enabled", "false");
    event.style.visibility = 'hidden';
    allIDs.push(event.getAttribute("data-event-id"));
});

function onlyUnique(value, index, array) {
  return array.indexOf(value) === index;
}

var eventIDs = allIDs.filter(onlyUnique);
let eventDict = {};

function setDynamicProgress(minTimer){
    eventIDs.forEach((event) =>{
        let elapsedHours = Math.floor((minTimer-eventDict[event]-1)/60);
        let route = document.querySelectorAll(`.gridEventDynamicImage[data-event-id="${event}"]`);
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

let eventButtons = document.querySelectorAll(".dynamicEventItem");

const startDate = new Date("Jan 1, 2026");

eventButtons.forEach((button) =>{
    button.addEventListener("click", ()=>{
            let simDate = new Date(document.querySelector(".navbar-date-display-value").textContent);
            let simMinutes = parseFloat(document.querySelector(".navbar-clock").getAttribute("style").split(" ")[1].split(";")[0]);
            let simSpeed = parseInt(document.getElementById("simulation-speed").value); 
            let simDelta;
        if(button.getAttribute("active") == "false"){
            button.setAttribute("active", "true");
            button.style.backgroundColor = 'aquamarine';
            simDelta = (simDate-startDate)/60000+simMinutes*1440+simSpeed;
        } else{
            button.style.backgroundColor = 'lightblue';
            button.setAttribute("active", "false");
            simDelta = NaN;
        }
        eventDict[parseInt(button.getAttribute("data-event-id"))] = simDelta;
    })
});

let cells = document.querySelectorAll(".gridCell")

function getNeighbors(dataDict, dataQL, CellId){
    let selectedCell = document.querySelector(`.gridCell[data-id="${CellId}"]`)
    let xC=parseInt(selectedCell.getAttribute("data-x"));
    let yC=parseInt(selectedCell.getAttribute("data-y"));
    let neighbors;
    if(xC == 0){
        if(yC== 0){
            neighbors = document.querySelectorAll(`.gridCell[data-x="${xC}"][data-y="${yC}"], .gridCell[data-x="${xC+1}"][data-y="${yC}"], .gridCell[data-x="${xC}"][data-y="${yC+1}"]`);
        } else if(yC == 2){
            neighbors = document.querySelector(`.gridCell[data-x="${xC}"][data-y="${yC}"], .gridCell[data-x="${xC+1}"][data-y="${yC}"], .gridCell[data-x="${xC}"][data-y="${yC-1}"]`);
        } else {
            neighbors = document.querySelectorAll(`.gridCell[data-x="${xC}"][data-y="${yC}"], .gridCell[data-x="${xC+1}"][data-y="${yC}"], .gridCell[data-x="${xC}"][data-y="${yC+1}"], .gridCell[data-x="${xC}"][data-y="${yC-1}"]`);
        }
    }else if(xC == 3){
        if(yC == 0){
            neighbors = document.querySelectorAll(`.gridCell[data-x="${xC}"][data-y="${yC}"], .gridCell[data-x="${xC-1}"][data-y="${yC}"], .gridCell[data-x="${xC}"][data-y="${yC+1}"]`);
        } else if(yC == 2){
            neighbors = document.querySelectorAll(`.gridCell[data-x="${xC}"][data-y="${yC}"], .gridCell[data-x="${xC-1}"][data-y="${yC}"], .gridCell[data-x="${xC}"][data-y="${yC-1}"]`);
        } else {
            neighbors = document.querySelectorAll(`.gridCell[data-x="${xC}"][data-y="${yC}"], .gridCell[data-x="${xC-1}"][data-y="${yC}"], .gridCell[data-x="${xC}"][data-y="${yC+1}"], .gridCell[data-x="${xC}"][data-y="${yC-1}"]`);
        }
    } else{
        if(yC == 0){
            neighbors = document.querySelectorAll(`.gridCell[data-x="${xC}"][data-y="${yC}"], .gridCell[data-x="${xC+1}"][data-y="${yC}"], .gridCell[data-x="${xC}"][data-y="${yC+1}"], .gridCell[data-x="${xC-1}"][data-y="${yC}"]`);
        } else if(yC == 2){
            neighbors = document.querySelectorAll(`.gridCell[data-x="${xC}"][data-y="${yC}"], .gridCell[data-x="${xC+1}"][data-y="${yC}"], .gridCell[data-x="${xC-1}"][data-y="${yC}"], .gridCell[data-x="${xC}"][data-y="${yC-1}"]`);
        } else {
            neighbors = document.querySelectorAll(`.gridCell[data-x="${xC}"][data-y="${yC}"], .gridCell[data-x="${xC+1}"][data-y="${yC}"], .gridCell[data-x="${xC}"][data-y="${yC+1}"], .gridCell[data-x="${xC-1}"][data-y="${yC}"], .gridCell[data-x="${xC}"][data-y="${yC-1}"]`);
        }
    }

    let EQ = 0;
    let MO = 0;
    let RE = 0;
    let SA = 0;
    let SE = 0;
    
    neighbors.forEach((neighbor) =>{
        let hiddenEvents = neighbor.querySelectorAll(`.gridEventDynamicImage[style="visibility: hidden;"]`);
        hiddenEvents.forEach((event) =>{
            EQ-=parseInt(event.getAttribute("environmental-quality"));
            MO-=parseInt(event.getAttribute("mobility"));
            RE-=parseInt(event.getAttribute("recreation"));
            SA-=parseInt(event.getAttribute("safety"));
            SE-=parseInt(event.getAttribute("services"));
        })
    });

    let QL=EQ+MO+RE+SA+SE;

    return [{"Environmental Quality":EQ+dataDict["Environmental Quality"],"Mobility":MO+dataDict["Mobility"],"Recreation":RE+dataDict["Recreation"],"Safety":SA+dataDict["Safety"],"Services":SE+dataDict["Services"]},QL+dataQL];
}

function dynamicEffects(dataDict, dataQL){
    let EQ = 0;
    let MO = 0;
    let RE = 0;
    let SA = 0;
    let SE = 0;
    
    let dynamicEvents = document.querySelectorAll(`.gridEventDynamicImage[style="visibility: hidden;"]`);
    dynamicEvents.forEach((event) =>{
        EQ-=parseInt(event.getAttribute("environmental-quality"));
        MO-=parseInt(event.getAttribute("mobility"));
        RE-=parseInt(event.getAttribute("recreation"));
        SA-=parseInt(event.getAttribute("safety"));
        SE-=parseInt(event.getAttribute("services"));
    })

    let QL=EQ+MO+RE+SA+SE;

    return [{"Environmental Quality":EQ+dataDict["Environmental Quality"],"Mobility":MO+dataDict["Mobility"],"Recreation":RE+dataDict["Recreation"],"Safety":SA+dataDict["Safety"],"Services":SE+dataDict["Services"]},QL+dataQL];
}