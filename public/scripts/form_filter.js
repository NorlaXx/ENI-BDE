const getNbFilter = () => {
  document
    .querySelector(".content .filter form")
    .addEventListener("submit", formSubmitHandler);
};

const formSubmitHandler = () => {
  const inputs = document.querySelectorAll("input, select");

  let filledFieldsCount = 0;

  inputs.forEach((input) => {
    if (
      (input.type === "text" ||
        input.type === "email" ||
        input.type === "number" ||
        input.type === "date" ||
        input.tagName.toLowerCase() === "select") &&
      input.value
    ) {
      filledFieldsCount++;
    }

    if (
      (input.type === "checkbox" || input.type === "radio") &&
      input.checked
    ) {
      filledFieldsCount++;
    }
  });

  localStorage.setItem("filledFieldsCount", filledFieldsCount);
};

const responsiveFilter = () => {
  document
    .getElementById("toggle-form-filter")
    .addEventListener("click", function () {
      const formContainer = document.getElementById("form-container");

      formContainer.classList.toggle("active");
      formContainer.classList.toggle("hidden");
    });
};

const resetFilterNumber = () => {
  const subtitle = document.querySelector(".content .filter h2");
  const filledFieldsCount = localStorage.getItem("filledFieldsCount");
  localStorage.removeItem("filledFieldsCount");

  if (filledFieldsCount !== null) {
    subtitle.innerHTML = `Filtres (${filledFieldsCount})`;
  }
};

const init = () => {
  resetFilterNumber();
  responsiveFilter();
  getNbFilter();
};

document.addEventListener("DOMContentLoaded", init);
