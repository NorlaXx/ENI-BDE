let activities = document.querySelectorAll(".activity");
let activityDetails = document.getElementById("activity-details");
const myPopup = new popup();

activities.forEach((activity) => {
  activity.addEventListener("click", () => {
    let latitude = activity.dataset.lat;
    let longitude = activity.dataset.long;
    let jsonData = JSON.parse(activity.dataset.activity);
    let canEdit = activity.dataset.edit === "true";
    let register = activity.dataset.register === "true";
    createComponents(jsonData, latitude, longitude, canEdit, register);
    customCss();
  });
});

function customCss() {
  let activityDetails = document.getElementById("activity-details");
  let map = document.getElementsByClassName("map");
  // Vérifie si le div activity-details est vide
  if (activityDetails.innerHTML !== "") {
    map[0].style.display = "none";
  } else {
    map[0].style.display = "block";
  }
}

function createComponents($activity, latitude, longitude, canEdit, register) {
  //Récupération de toutes les données
  let id = $activity.id.replaceAll("&@^", " ");
  let name = $activity.name.replaceAll("&@^", " ");
  let city = $activity.city.replaceAll("&@^", " ");
  let lieu = $activity.lieu.replaceAll("&@^", " ");
  let description = $activity.description.replaceAll("&@^", " ");
  let state = $activity.state.replaceAll("&@^", " ");
  let fileName = $activity.fileName.replaceAll("&@^", " ");
  let startDate = $activity.startDate.replaceAll("&@^", " ");
  let registrationDateLimit = $activity.registrationDateLimit.replaceAll(
    "&@^",
    " "
  );
  let duration = $activity.duration.replaceAll("&@^", " ");
  let creationDate = $activity.creationDate.replaceAll("&@^", " ");
  let nbLimitParticipants = $activity.nbLimitParticipants.replaceAll(
    "&@^",
    " "
  );
  let nbParticipants = $activity.nbParticipants.replaceAll("&@^", " ");
  let userList = $activity.userList.replaceAll("&@^", " ");
  try {
    userList = Array.from(userList.split("|"));
    userList = formatUserList(userList);
  } catch (e) {
    userList = [];
  }

  //Restart le contenu de la div
  activityDetails.innerHTML = "";

  //Image de la sortie
  let imgContainer = document.createElement("div");
  imgContainer.className = "img-container";
  let pictureDom = document.createElement("img");
  pictureDom.src = "/thumbnails/" + fileName;
  imgContainer.appendChild(pictureDom);

  //Titre et description
  let titleDescriptionContainer = document.createElement("div");
  titleDescriptionContainer.className = "title-description-container";

  //Titre de la sortie + infos date et participants
  let titleContainer = document.createElement("div");
  titleContainer.className = "title-container";
  let titleDom = document.createElement("p");
  let titleStrong = document.createElement("strong");
  titleStrong.innerHTML = name;
  let dateDom = document.createElement("p");
  dateDom.innerHTML = "(" + startDate + ")";

  titleDom.appendChild(titleStrong);
  titleDom.appendChild(dateDom);

  let partcipantsDom = document.createElement("p");
  partcipantsDom.id = "participants";
  partcipantsDom.innerHTML = nbParticipants + "/" + nbLimitParticipants;
  let picto = document.createElement("img");
  picto.src = "/pictos/picto_participants.svg";
  partcipantsDom.appendChild(picto);
  partcipantsDom.addEventListener("click", () => userHandler(id, userList));

  titleContainer.appendChild(titleDom);
  titleContainer.appendChild(partcipantsDom);

  //Description de la sortie
  let descriptionDom = document.createElement("p");
  descriptionDom.innerHTML = description;

  let separator = document.createElement("div");
  separator.className = "separator";

  titleDescriptionContainer.appendChild(titleContainer);
  titleDescriptionContainer.appendChild(descriptionDom);
  titleDescriptionContainer.appendChild(separator);

  //Détails de la sortie + la map
  let detailsMapContainer = document.createElement("div");
  detailsMapContainer.className = "details-container";
  let detailsDom = document.createElement("div");
  //Détails
  let locationDom = document.createElement("p");
  locationDom.innerHTML = "Lieu : " + lieu;
  let cityDom = document.createElement("p");
  cityDom.innerHTML = "Ville : " + city;
  let durationDom = document.createElement("p");
  durationDom.innerHTML = "Duree : " + duration + " min";
  let endRegisterDateDom = document.createElement("p");
  endRegisterDateDom.innerHTML =
    "Date fin inscription : " + registrationDateLimit;

  detailsDom.appendChild(locationDom);
  detailsDom.appendChild(cityDom);
  detailsDom.appendChild(durationDom);
  detailsDom.appendChild(endRegisterDateDom);

  //Map
  let imgMapDom = document.createElement("img");
  imgMapDom.src =
    "https://maps.googleapis.com/maps/api/staticmap?center=" +
    latitude +
    "," +
    longitude +
    "&zoom=13&size=350x200&markers=color:red%7Clabel:%7C" +
    latitude +
    "," +
    longitude +
    "&key=AIzaSyBMO3LVl_v7xhApZEBfLfG0N6oU5yQUHjA";

  detailsMapContainer.appendChild(detailsDom);
  detailsMapContainer.appendChild(imgMapDom);

  //Actions
  let actionsContainer = document.createElement("div");
  actionsContainer.className = "actions-container";
  let nbAvailablePlaces = nbLimitParticipants - nbParticipants;
  //Bouton Fermer
  let closeBtn = document.createElement("button");
  closeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i> Fermer';
  closeBtn.className = "button cancel";
  closeBtn.addEventListener("click", () => {
    activityDetails.innerHTML = "";
    customCss();
  });
  actionsContainer.appendChild(closeBtn);

  //Bouton Modifier
  if (canEdit && state === "ACT_CR") {
    let editLinkDom = document.createElement("a");
    editLinkDom.href = "/activity/update/" + id;
    let editBtn = document.createElement("button");
    editBtn.className = "button modify";
    editBtn.innerHTML = '<i class="fa-solid fa-pen"></i> Modifier';
    editLinkDom.appendChild(editBtn);
    actionsContainer.appendChild(editLinkDom);
  }

  //Bouton rejoindre
  if (nbAvailablePlaces > 0 && state === "ACT_INS" && !register) {
    let registerLinkDom = document.createElement("a");
    registerLinkDom.href = "/activity/add/inscrit/" + id;
    let registerBtn = document.createElement("button");
    registerBtn.className = "button";
    registerBtn.innerHTML =
      '<i class="fa-solid fa-right-to-bracket"></i> Rejoindre';
    registerLinkDom.appendChild(registerBtn);
    actionsContainer.appendChild(registerLinkDom);
  }

  //Bouton Désister
  if (register) {
    let unregisterLinkDom = document.createElement("a");
    unregisterLinkDom.href = "/activity/remove/inscrit/" + id;
    let unregisterBtn = document.createElement("button");
    unregisterBtn.className = "button cancel";
    unregisterBtn.innerHTML =
      '<i class="fa-solid fa-right-to-bracket fa-reverse"></i> Désister';
    unregisterLinkDom.appendChild(unregisterBtn);
    actionsContainer.appendChild(unregisterLinkDom);
  }

  activityDetails.appendChild(imgContainer);
  activityDetails.appendChild(titleDescriptionContainer);
  activityDetails.appendChild(detailsMapContainer);
  activityDetails.appendChild(actionsContainer);
}

const formatUserList = (userList) => {
  let result = [];

  userList.forEach((user) => {
    let userInfos = JSON.parse(user);
    result.push(userInfos);
  });

  return result;
};

const userHandler = (id, userList) => {
  myPopup.show();

  const div = document.createElement("div");
  div.className = "user-list";

  if (userList.length === 0) {
    const p = document.createElement("h2");
    p.innerHTML = "Aucun participant pour le moment";
    div.appendChild(p);

    return myPopup.importHTMLComponent(div);
  }

  const title = document.createElement("h2");
  title.innerHTML = "Listes des participants";
  div.appendChild(title);

  userList.forEach((user) => {
    const userDiv = document.createElement("div");
    userDiv.classList.add("user");
    const image = document.createElement("img");
    image.src = "/profilePictures/" + user.fileName;
    userDiv.appendChild(image);

    const p = document.createElement("p");
    p.innerHTML = user.firstName + " " + user.lastName;
    userDiv.appendChild(p);

    userDiv.addEventListener("click", () => {
      window.location.href = "/profil/search/" + user.id;
    });

    div.appendChild(userDiv);
  });

  myPopup.importHTMLComponent(div);
};
