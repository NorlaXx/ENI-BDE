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

  callPosition();
}

const callPosition = () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const pos = {
          lat: position.coords.latitude,
          lng: position.coords.longitude,
        };

        map.setCenter(pos);
      },
      () => {
        alert("Une erreur est survenue lors de la géolocalisation");
      }
    );
  } else {
    alert("Une erreur est survenue lors de la géolocalisation");
  }
};

const onLoadHandler = async () => {
  const activitiesData = document.getElementById("activityContent");
  for (let i = 0; i < activitiesData.children.length; i++) {
    const dataset = activitiesData.children[i].dataset;
    const { lat, long, id } = dataset;
    const position = { lat: parseFloat(lat), lng: parseFloat(long) };

    const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

    const marker = new AdvancedMarkerElement({
      map: map,
      position: position,
      title: "Hello World!",
    });

    marker.addListener("click", () => {
      markerHandler(lat, long, id);
    });

    marker.id = id;
    marker.latitude = lat;
    marker.longitude = long;

    allMarker[lat + ";" + long] = marker;
  }
};

const findActivitiesComponents = (_id) => {
  const activitiesData = document.getElementById("activityContent");
  for (let i = 0; i < activitiesData.children.length; i++) {
    const dataset = activitiesData.children[i].dataset;
    const { id } = dataset;
    if (_id === id) {
      return activitiesData.children[i];
    }
  }
};

const removeHighlight = () => {
  const activitiesData = document.getElementById("activityContent");
  for (let i = 0; i < activitiesData.children.length; i++) {
    activitiesData.children[i].classList.remove("highlight");
  }
};

const resetAllMarkerScale = async () => {
  const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary(
    "marker"
  );
  for (const key in allMarker) {
    const marker = allMarker[key];
    marker.setMap(null);

    console.log(marker.latitude, marker.longitude);

    let position = {
      lat: parseFloat(marker.latitude),
      lng: parseFloat(marker.longitude),
    };

    const newMarker = new AdvancedMarkerElement({
      map: map,
      position: position,
      title: "Hello World!",
    });

    newMarker.addListener("click", () => {
      markerHandler(marker.latitude, marker.longitude, marker.id);
    });

    allMarker[marker.latitude + ";" + marker.longitude] = marker;
  }

  return;
};

const markerHandler = async (lat, long, id) => {
  const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary(
    "marker"
  );

  await resetAllMarkerScale();

  const marker = allMarker[lat + ";" + long];
  if (!marker) return;

  removeHighlight();
  const myActivity = findActivitiesComponents(id);
  myActivity.classList.add("highlight");

  marker.setMap(null);
  const pinScaled = new PinElement({
    scale: 1.2,
  });

  lat = parseFloat(lat);
  long = parseFloat(long);

  const markerViewScaled = new AdvancedMarkerElement({
    map,
    position: { lat, lng: long },
    content: pinScaled.element,
  });

  markerViewScaled.addListener("click", () => {
    markerHandler(lat, long, id);
  });

  markerViewScaled.id = id;
  markerViewScaled.latitude = lat;
  markerViewScaled.longitude = long;

  allMarker[lat + ";" + long] = markerViewScaled;
};

document.addEventListener("DOMContentLoaded", onLoadHandler);

initMap();
