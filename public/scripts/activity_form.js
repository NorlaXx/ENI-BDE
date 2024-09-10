const citySelector = document.getElementById("activity_campus");
const lieuSelector = document.getElementById("activity_lieu");
const allLieux = [];

const loadAllLieux = () => {
  for (let i = 0; i < lieuSelector.options.length; i++) {
    allLieux.push(lieuSelector.options[i].dataset.ville.toLowerCase());
  }
  console.log(allLieux);
};

const init = () => {
  citySelector.addEventListener("change", citySelectorChange);
};

const citySelectorChange = (e) => {
  const idSelected = e.target.value;
  const selected = getSelectedById(idSelected, citySelector.innerText);

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
  const toList = text.split("\n");
  return toList[id - 1].toLowerCase();
};

document.addEventListener("DOMContentLoaded", () => {
  const idSelected = citySelector.value;
  const selected = getSelectedById(idSelected, citySelector.innerText);
  updateByCity(selected);

  loadAllLieux();
  init();
});
