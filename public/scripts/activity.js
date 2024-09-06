let activities = document.querySelectorAll(".activity");
let activityDetails = document.getElementById("activity-details");

activities.forEach((activity) => {
    activity.addEventListener("click", () => {
        let latitude = activity.dataset.lat;
        let longitude = activity.dataset.long;
        let jsonData = JSON.parse(activity.dataset.activity);
        console.log(latitude);
        console.log(longitude);
        createComponents(jsonData, latitude, longitude);
        customCss();
    })
});

function customCss(){
    let activityDetails = document.getElementById('activity-details');
    let map = document.getElementsByClassName('map');
    // Vérifie si le div activity-details est vide
    if (activityDetails.innerHTML !== "") {
        map[0].style.display = "none";
    }
}

function createComponents($activity, latitude, longitude) {
    //Récupération de toutes les données
    let pictureFileName = $activity.pictureFileName.replaceAll("&@^", " ");
    let name = $activity.name.replaceAll("&@^", " ");
    let ville = $activity.ville.replaceAll("&@^", " ");
    let lieu = $activity.lieu.replaceAll("&@^", " ");
    let description = $activity.description.replaceAll("&@^", " ");
    let state = $activity.state.replaceAll("&@^", " ");
    let dateDebut = $activity.dateDebut.replaceAll("&@^", " ");
    let dateFinalInscription = $activity.dateFinalInscription.replaceAll("&@^", " ");
    let duree = $activity.duree.replaceAll("&@^", " ");
    let dateCreation = $activity.dateCreation.replaceAll("&@^", " ");
    let nbLimitParticipants = $activity.nbLimitParticipants.replaceAll("&@^", " ");



    activityDetails.innerHTML = "";
    //Image de la sortie
    let imgContainer = document.createElement("div");
    imgContainer.className = "img-container";
    let pictureDom = document.createElement("img");
    pictureDom.src = "/thumbnails/" + pictureFileName;
    imgContainer.appendChild(pictureDom);

    //Titre et description
    let titleDescriptionContainer = document.createElement("div");
    titleDescriptionContainer.className = "title-description-container";

    //Titre de la sortie
    let titleContainer = document.createElement("div");
    titleContainer.className = "title-container";
    let titleDom = document.createElement("h2");
    titleDom.innerHTML = name;
    let dateDom = document.createElement("p");
    dateDom.innerHTML = "(" + dateDebut + ")";
    let partcipantsDom = document.createElement("p");
    partcipantsDom.innerHTML = nbLimitParticipants;

    titleContainer.appendChild(titleDom);
    titleContainer.appendChild(dateDom);
    titleContainer.appendChild(partcipantsDom);

    let descriptionDom = document.createElement("p");
    descriptionDom.innerHTML = description;

    titleDescriptionContainer.appendChild(titleContainer);
    titleDescriptionContainer.appendChild(descriptionDom);

    //Détails de la sortie
    let detailsMapContainer = document.createElement("div");
    detailsMapContainer.className = "details-container";
    let detailsDom = document.createElement("div");
    //Détails
    let locationDom = document.createElement("p");
    locationDom.innerHTML = "Lieu : " + lieu;
    let cityDom = document.createElement("p");
    cityDom.innerHTML = "Ville : " + ville;
    let durationDom = document.createElement("p");
    durationDom.innerHTML = "Duree : " + duree + " min";
    let endRegisterDateDom = document.createElement("p");
    endRegisterDateDom.innerHTML = "Date fin inscription : " + dateFinalInscription;

    detailsDom.appendChild(locationDom);
    detailsDom.appendChild(cityDom);
    detailsDom.appendChild(durationDom);
    detailsDom.appendChild(endRegisterDateDom);

    detailsMapContainer.appendChild(detailsDom);


    //Map
    let imgMapDom = document.createElement("img");
    imgMapDom.src = "https://maps.googleapis.com/maps/api/staticmap?center="+latitude+","+longitude+"&zoom=13&size=400x400&markers=color:red%7Clabel:%7C"+latitude+","+longitude+"&key=AIzaSyBMO3LVl_v7xhApZEBfLfG0N6oU5yQUHjA";

    detailsMapContainer.appendChild(imgMapDom);

    //Actions
    let actionsContainer = document.createElement("div");
    actionsContainer.className = "actions-container";
    //TODO Actions en fonction de l'utilisateur

    activityDetails.appendChild(imgContainer);
    activityDetails.appendChild(titleDescriptionContainer);
    activityDetails.appendChild(detailsMapContainer);
}