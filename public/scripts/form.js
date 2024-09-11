const fileInputs = document.querySelectorAll('input[type="file"]');

fileInputs.forEach((input) => {
  input.addEventListener("change", inputChangeHandler);
});

const inputChangeHandler = (e) => {
  const file = e.target.files[0];
  const label = document.querySelector(`label[for=${e.target.id}]`);
  label.textContent = "Fichier sélectionné : " + file.name;
};
