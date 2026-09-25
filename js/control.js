//koordinat mouse
    const coordsDiv = L.control({
      position: "bottomleft"
    });
    coordsDiv.onAdd = function() {
      const div = L.DomUtil.create("div", "coords-info");
      div.innerHTML = "Lat: - , Lng: -";
      return div;
    };
    coordsDiv.addTo(map);
    map.on("mousemove", function(e) {
      document.querySelector(".coords-info").innerHTML =
        "Lat: " + e.latlng.lat.toFixed(6) + ", Lng: " + e.latlng.lng.toFixed(6);
    });
//zoom in dan zoom out
    L.control.zoom({
      position: 'topright'
    }).addTo(map);

//scale 
    L.control.scale({
      metric: true,
      imperial: true,
      position: 'bottomleft'
    }).addTo(map);