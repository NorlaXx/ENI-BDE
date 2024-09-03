let map;
let allMarker = {};

async function initMap() {
  const position = { lat: 48.11677281009136, lng: -1.6821197695243197 };

  const { Map } = await google.maps.importLibrary("maps");

  map = new Map(document.getElementById("map"), {
    zoom: 13,
    center: position,
    streetViewControl: false,
    disableDefaultUI: true,
    mapTypeControl: false,
    scaleControl: true,
    zoomControl: true,
    mapId: "BIPBIP",
  });
}

const onLoadHandler = async () => {
  const activitiesData = document.getElementById("activityContent");
  for (let i = 0; i < activitiesData.children.length; i++) {
    const dataset = activitiesData.children[i].dataset;
    const { lat, long } = dataset;
    const position = { lat: parseFloat(lat), lng: parseFloat(long) };

    const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

    const marker = new AdvancedMarkerElement({
      map: map,
      position: position,
      title: "Hello World!",
    });

    allMarker[lat + ";" + long] = marker;
  }
};

document.addEventListener("DOMContentLoaded", onLoadHandler);

initMap();
