let activities = document.querySelectorAll(".activity");
let activityDetails = document.getElementById("activity-details");
const google_key = "AIzaSyBMO3LVl_v7xhApZEBfLfG0N6oU5yQUHjA";
const myPopup = new popup();

const init = () => {
  window.addEventListener(
    "resize",
    () => {
      activities.forEach((activity) => {
        mobileContent(activity);
      });
    },
    true
  );

  activities.forEach((activity) => {
    mobileContent(activity);
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
};

const customCss = () => {
  let activityDetails = document.getElementById("activity-details");
  let map = document.getElementsByClassName("map");

  map[0].style.display = activityDetails.innerHTML !== "" ? "none" : "block";
};

const createComponents = (
  $activity,
  latitude,
  longitude,
  canEdit,
  register
) => {
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
  let titleDom = getText("<strong>" + name + "</strong>");
  let dateDom = getText("(" + startDate + ")");

  titleDom.appendChild(dateDom);

  let partcipantsDom = getText(
    nbParticipants + "/" + nbLimitParticipants,
    "participants"
  );

  let picto = document.createElement("img");
  picto.src = "/pictos/picto_participants.svg";
  partcipantsDom.appendChild(picto);
  partcipantsDom.addEventListener("click", () => userHandler(id, userList));

  titleContainer.appendChild(titleDom);
  titleContainer.appendChild(partcipantsDom);

  //Description de la sortie
  let descriptionDom = getText(description);

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
  let locationDom = getText("Lieu : " + lieu);
  let cityDom = getText("Ville : " + city);
  let durationDom = getText("Duree : " + duration + " min");
  let endRegisterDateDom = getText(
    "Date fin inscription : " + registrationDateLimit
  );

  detailsDom.appendChild(locationDom);
  detailsDom.appendChild(cityDom);
  detailsDom.appendChild(durationDom);
  detailsDom.appendChild(endRegisterDateDom);

  //Map
  let imgMapDom = document.createElement("img");
  imgMapDom.src = getImageLink(longitude, latitude);
  detailsMapContainer.appendChild(detailsDom);
  detailsMapContainer.appendChild(imgMapDom);

  let actionsContainer = document.createElement("div");
  actionsContainer.className = "actions-container";
  let nbAvailablePlaces = nbLimitParticipants - nbParticipants;

  //close Button
  let closeBtn = getButton(
    "button close",
    '<i class="fa-solid fa-times"></i> Fermer'
  );
  closeBtn.addEventListener("click", () => {
    activityDetails.innerHTML = "";
    customCss();
  });
  actionsContainer.appendChild(closeBtn);

  //Boutons d'actions
  if (canEdit && state === "ACT_CR") {
    let updateButton = getButtonLink(
      "/activity/update/" + id,
      "button modify",
      '<i class="fa-solid fa-pen"></i> Modifier'
    );
    actionsContainer.appendChild(updateButton);
  }

  if (nbAvailablePlaces > 0 && state === "ACT_INS" && !register) {
    let joinButton = getButtonLink(
      "/activity/add/inscrit/" + id,
      "button",
      '<i class="fa-solid fa-right-to-bracket"></i> Rejoindre'
    );
    actionsContainer.appendChild(joinButton);
  }

  if (register) {
    let desisterButton = getButtonLink(
      "/activity/remove/inscrit/" + id,
      "button cancel",
      '<i class="fa-solid fa-right-to-bracket fa-reverse"></i> Désister'
    );
    actionsContainer.appendChild(desisterButton);
  }

  activityDetails.appendChild(imgContainer);
  activityDetails.appendChild(titleDescriptionContainer);
  activityDetails.appendChild(detailsMapContainer);
  activityDetails.appendChild(actionsContainer);
};

const getButtonLink = (href, className, innerHTML) => {
  let buttonLink = document.createElement("a");
  buttonLink.href = href;
  let button = getButton(className, innerHTML);
  buttonLink.appendChild(button);
  return buttonLink;
};

const getButton = (className, innerHTML) => {
  let button = document.createElement("button");
  button.className = className;
  button.innerHTML = innerHTML;

  return button;
};

const getText = (text, id = "") => {
  const p = document.createElement("p");
  p.id = id;
  p.innerHTML = text;
  return p;
};

const formatUserList = (userList) => {
  let result = [];

  userList.forEach((user) => {
    let userInfos = JSON.parse(user);
    result.push(userInfos);
  });

  return result;
};

const getImageLink = (longitude, latitude) => {
  return `https://maps.googleapis.com/maps/api/staticmap?center=${latitude},${longitude}&zoom=13&size=350x200&markers=color:red%7Clabel:%7C${latitude},${longitude}&key=${google_key}`;
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

const mobileContent = (activity) => {
  let campus = activity.dataset.campus;
  let userCampus = activity.dataset.usercampus;

  activity.style.display =
    window.screen.width <= 768 && campus !== userCampus ? "none" : "flex";
};

document.addEventListener("DOMContentLoaded", init);
