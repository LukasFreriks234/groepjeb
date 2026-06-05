function filterFunction() {
  var boxes, active, catagoryMap, filterRows, buildings, input, filter, name, i, category, searchRows;
  boxes = document.querySelectorAll(".functionFilter");
  active = new Set([])
  boxes.forEach(elt => active.add(elt.checked));
  catagoryMap = new Map();
  boxes.forEach(elt => catagoryMap.set(elt.value, elt.checked));
  listItem = document.getElementById("functionsList").querySelectorAll("li");

  // filter functie
  if (active.has(true)){
    filterRows = [];
    for (const item1 of listItem){
      category = item1.querySelector(".functionCategory").getAttribute('name');
      if (catagoryMap.get(category)){
          filterRows.push(item1);
      }
    }
  } else {
    filterRows = Array.from(listItem);
  }

  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  searchRows = [];

  // zoek functie
  for (const row of filterRows) {
    name = row.querySelector(".functionName").innerText;
    if (name.toUpperCase().indexOf(filter) > -1) {
      searchRows.push(row);
      }
    }

  // functie wanneer beide functies worden toegepast
  for (const item2 of listItem) {
    if (searchRows.includes(item2)){
      item2.style.display = "";
    } else {
      item2.style.display = "none";
    }
  }
}

window.addEventListener("load", filterFunction);
document.getElementById("myInput").addEventListener("keyup", filterFunction);
document.querySelectorAll(".functionFilter").forEach(elt => elt.addEventListener("click", filterFunction));

function filterEvents() {
  const boxes = document.querySelectorAll(".eventFilter");
  const listItem = document.getElementById("eventsList").querySelectorAll("li");

  const active = new Set();
  const typeMap = new Map();

  boxes.forEach(el => {
    active.add(el.checked);
    typeMap.set(el.value, el.checked);
  });

  let filterRows;

  // FILTER op type (one-off / recurring)
  if (active.has(true)) {
    filterRows = [];

    for (const item of listItem) {
      const type = item.dataset.type;
      const dynamic = item.dataset.dynamic;

      if (
          typeMap.get(type) ||
          (typeMap.get("dynamic") && dynamic === "1")
      ) {
          filterRows.push(item);
      }
    }
  } else {
    filterRows = Array.from(listItem);
  }

  // SEARCH
  const input = document.getElementById("eventSearch");
  const filter = input.value.toUpperCase();

  const searchRows = [];

  for (const row of filterRows) {
    const name = row.querySelector(".functionName").innerText;

    if (name.toUpperCase().includes(filter)) {
      searchRows.push(row);
    }
  }

  // APPLY
  for (const item of listItem) {
    item.style.display = searchRows.includes(item) ? "" : "none";
  }
}

window.addEventListener("load", filterEvents);

document.getElementById("eventSearch")
  .addEventListener("keyup", filterEvents);

document.querySelectorAll(".eventFilter")
  .forEach(el => el.addEventListener("click", filterEvents));