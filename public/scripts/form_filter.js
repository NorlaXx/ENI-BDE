const subtitle = document.querySelector('.content .filter h2');

function getNbFilter(){
    document.querySelector('.content .filter form').addEventListener('submit', (e) => {
        const inputs = document.querySelectorAll('input, select');

        let filledFieldsCount = 0;

        inputs.forEach(input => {
            // Pour les champs de type 'text', 'email', 'number', et les 'select'
            console.log(input.type);
            if ((input.type === 'text' || input.type === 'email' || input.type === 'number' || input.type === 'date' || input.tagName.toLowerCase() === 'select') && input.value) {
                filledFieldsCount++;
            }

            // Pour les champs de type 'checkbox' et 'radio'
            if ((input.type === 'checkbox' || input.type === 'radio') && input.checked) {
                filledFieldsCount++;
            }
        })
        localStorage.setItem('filledFieldsCount', filledFieldsCount);
    })

    document.addEventListener('DOMContentLoaded', () => {
        const filledFieldsCount = localStorage.getItem('filledFieldsCount');
        localStorage.removeItem('filledFieldsCount');

        if (filledFieldsCount !== null) {
            subtitle.innerHTML = '';
            subtitle.innerHTML = `Filtres (${filledFieldsCount})`;
        }

    });
}

function responsiveFilter(){
    document.getElementById("toggle-form-filter").addEventListener("click", function() {
        const formContainer = document.getElementById("form-container");

        // Toggle the "active" class to show or hide the form
        if (formContainer.classList.contains("active")) {
            formContainer.classList.remove("active");
            formContainer.classList.add("hidden");
        } else {
            formContainer.classList.remove("hidden");
            formContainer.classList.add("active");
        }
    });
}

responsiveFilter();

getNbFilter();