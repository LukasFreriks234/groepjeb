let mainRoadMode = false;
let createRouteMode = false;

let selectedStartCell = null;
let selectedEndEvent = null;

const mainRoadButton = document.getElementById("toggle-mainroad-button");
const grid = document.querySelector(".metropolisGrid");
const tabButtonEvent = document.getElementById("tabButtonEvent");
const createRouteButton = document.getElementById("createRouteButton");

createRouteButton.style.display = "none";

mainRoadButton.addEventListener("click", () => {
    mainRoadMode = !mainRoadMode;

    grid.classList.toggle("main-road-mode", mainRoadMode);

    if (!mainRoadMode) {
        createRouteMode = false;
        createRouteButton.innerHTML = "Create Route";
        selectedStartCell = null;
        selectedEndEvent = null;
    }

    if (mainRoadMode) {
        mainRoadButton.innerHTML = "Hide Main Road Overlay"; 
        tabButtonEvent.style.display = "none";
        createRouteButton.style.display = null;
        

    } else {
        mainRoadButton.innerHTML = "Show Main Road Overlay";
        tabButtonEvent.style.display = null;
        createRouteButton.style.display = "none";
        clearRoute();
    }
});

createRouteButton.addEventListener("click", () => {

    createRouteMode = !createRouteMode;

    createRouteButton.innerHTML = createRouteMode
        ? "Back"
        : "Create Route";

    clearRoute();
    selectedStartCell = null;
    selectedEndEvent = null;
});

document.querySelectorAll(".gridCell").forEach(cell => {

    cell.addEventListener("click", () => {
        if (createRouteMode){
            if(cell.querySelector(".main-road-icon")){
                startCellId = cell.dataset.id;
            }
            if (cell.querySelector(".draggableGridEvent")){
                endEventId = cell.dataset.id;

                loadRoute();
            }            
        }

        if(!createRouteMode && mainRoadMode){
        fetch(`/grid/main-road/${cell.dataset.id}`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json"
            }
        })
        .then(response => response.json())
        .then(data => {

            const icon = cell.querySelector(".main-road-icon");

            if (data.mainRoad) {

                if (!icon) {
                    const div = document.createElement("div");
                    div.classList.add("main-road-icon");

                    const img = document.createElement("img");
                    img.src = "/images/mainroad.png";
                    img.alt = "main road";

                    div.appendChild(img);
                    cell.appendChild(div);
                }

            } else {
                cell.querySelector(".main-road-icon")?.remove();
            }
        });
        }
    });

});

function loadRoute() {

    fetch("/mainroad/route", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json"
        }, 
        body: JSON.stringify({
            start_cell_id: startCellId,
            end_cell_id: endEventId
        })
    })
    .then(res => {

    if(!res.ok){
        throw new Error("Route bestaat niet.");
    }
        return res.json();
    })
    .then(data => {
        renderRoute(data.route);
    })
    .catch(err => {
        console.error(err);
    });
}

function renderRoute(route) {
    document.querySelectorAll(".route-overlay").forEach(el => el.remove());

   
    route.forEach(([x, y]) => {

        const cell = document.querySelector(
            `.gridCell[data-x="${x}"][data-y="${y}"]`
        );

        if (!cell) return;

        const div = document.createElement("div");
        div.classList.add("route-overlay");

        cell.appendChild(div);
    });

}

function clearRoute() {
    document.querySelectorAll(".route-overlay").forEach(el => el.remove());
}