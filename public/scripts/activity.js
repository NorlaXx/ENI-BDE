let activities = document.querySelectorAll(".activity");
let activityDetails = document.getElementById("activity-details");

activities.forEach((activity) => {
    activity.addEventListener("click", () => {
        let jsonData = JSON.parse(activity.dataset.activity);
        createComponents(jsonData);
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

function createComponents($activity) {
    activityDetails.innerHTML = "";
    //Image de la sortie
    let imgContainer = document.createElement("div");
    let picture = document.createElement("img");
    picture.src = "/thumbnails/" + $activity.pictureFileName;

    //Titre de la sortie


    // let titleContainer = document.createElement("div");
    // let title = activity.children[0].children[1].children[0].cloneNode(true);
    // console.log(activity);
    //
    //
    //
    // imgContainer.appendChild(picture);
    // activityDetails.appendChild(imgContainer);
}