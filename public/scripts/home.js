let map;

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

initMap();
