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