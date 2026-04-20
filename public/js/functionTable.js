function filterFunction() {
  var boxes, active, catagoryMap, filterRows, buildings, input, filter, name, i, category, searchRows;
  boxes = document.querySelectorAll(".function_filter");
  active = new Set([])
  boxes.forEach(elt => active.add(elt.checked));
  catagoryMap = new Map();
  boxes.forEach(elt => catagoryMap.set(elt.value, elt.checked));
  listItem = document.getElementById("functions_list").querySelectorAll("li");

  if (active.has(true)){
    filterRows = [];
    for (const item1 of listItem){
      category = item1.querySelector(".function_category").getAttribute('name');
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

  for (const row of filterRows) {
    name = row.querySelector(".function_name").innerText;
    if (name.toUpperCase().indexOf(filter) > -1) {
      searchRows.push(row);
      }
    }

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
document.querySelectorAll(".function_filter").forEach(elt => elt.addEventListener("click", filterFunction));