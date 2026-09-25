let layerUtilitas = {};

fetch("data/utilitas.php")
  .then(r => r.json())
  .then(data => {
    console.log("DATA UTILITAS:", data);
    // contoh: jalan kota
    if (data.jalan_kota) {
      const layer = L.geoJSON(data.jalan_kota, {
        style: f => f.style || { color: "#ff0000", weight: 2, type: "line" },
        onEachFeature: (feature, layer) => {
          const props = feature.properties;
          layer.bindPopup(`<b>${props.nama_jalan || "-"}</b>`);
        }
      });
      if (!layerUtilitas["Jaringan Jalan"]) layerUtilitas["Jaringan Jalan"] = {};
      layerUtilitas["Jaringan Jalan"]["jalan_kota"] = layer;
    }
  });
