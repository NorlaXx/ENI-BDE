const lieuSelector = document.getElementById("activity_lieu");
const citySelector = document.getElementById("city_selector");
const allLieux = [];

const loadAllLieux = () => {
  for (let i = 0; i < lieuSelector.options.length; i++) {
    const city = lieuSelector.options[i].dataset.ville;
    if (allLieux.includes(city)) continue;
    allLieux.push(city);
  }
};

const init = () => {
  citySelector.addEventListener("change", citySelectorChange);
};

const citySelectorChange = (e) => {
  const idSelected = e.target.value;
  const selected = getSelectedById(idSelected, citySelector);

  updateByCity(selected);
};

const updateByCity = (city) => {
  for (let i = 0; i < lieuSelector.options.length; i++) {
    if (lieuSelector.options[i].dataset.ville.toLowerCase() === city) {
      lieuSelector.options[i].style.display = "block";
    } else {
      lieuSelector.options[i].style.display = "none";
    }
  }

  for (let i = 0; i < lieuSelector.options.length; i++) {
    if (lieuSelector.options[i].style.display === "block") {
      lieuSelector.selectedIndex = i;
      break;
    }
  }
};

const getSelectedById = (id, text) => {
  const allOptions = text.querySelectorAll("option");
  const toList = Array.from(allOptions).map((option) => option.textContent);
  return toList[id - 1].toLowerCase();
};

document.addEventListener("DOMContentLoaded", () => {
  loadAllLieux();

  allLieux.forEach((lieu) => {
    citySelector.innerHTML += `<option value="${
      allLieux.indexOf(lieu) + 1
    }">${lieu}</option>`;
  });

  init();

  const idSelected = citySelector.value;
  const selected = getSelectedById(idSelected, citySelector);
  updateByCity(selected);
});
