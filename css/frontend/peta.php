<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit','1024M');
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SITIJO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.css" />
  <script src="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet-compass/dist/leaflet-compass.css" />
  <script src="https://unpkg.com/leaflet-compass/dist/leaflet-compass.min.js"></script>
  <link href="../css/style.css" rel="stylesheet">
  <script src="https://unpkg.com/leaflet-textpath/L.TextPath.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>

  <style>
    :root {
      --header: 60px;
      --brand: #0f3073;
    }

    html,
    body {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
    }

    #map {
      position: absolute;
      margin-top: -25px;
      bottom: 0;
      right: 0;
      left: 0;
      top: 85px;
      background: transparent !important;
    }

    .sidebar {
      position: fixed;
      top: 80px;
      left: 0;
      bottom: 0;
      width: 250px;
      background: #ffffffee;
      backdrop-filter: blur(6px);
      border-right: 1px solid #e0e0e0;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
      padding: 10px;
      font-size: 14px;
      z-index: 1100;
      overflow-y: auto;
      transition: transform 0.3s ease;
      border-radius: 0 10px 10px 0;
    }

    .leaflet-textpath {
      pointer-events: none;
    }

    .sidebar.hidden {
      transform: translateX(-100%);
    }

    .sidebar h5,
    .sidebar h6 {
      margin-top: 15px;
      margin-bottom: 10px;
      padding: 0;
      font-weight: 600;
      font-size: 15px;
      color: #02106eff;
      text-align: center;
    }

    .sidebar input,
    .sidebar select {
      border-radius: 6px;
      border: 1px solid #ddd;
      margin-bottom: 8px;
    }

    .toggle-btn {
      position: fixed;
      left: 10px;
      top: 80px;
      z-index: 1200;
      background: #ffcc00ff;
      color: #09017dff;
      border: none;
      padding: 6px 10px;
      border-radius: 5px;
    }

    .legend-container {
      background: white;
      padding: 8px;
      border-radius: 6px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      font-size: 13px;
      max-height: 250px;
      overflow-y: auto;
    }

    .layer-class {
      cursor: pointer;
      padding: 8px 10px;
      border-radius: 6px;
      background: #f8f9fa;
      margin-top: 5px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-weight: 500;
      font-size: 12px;
      transition: background 0.2s;
    }

    .layer-class:hover {
      background: #e9ecef;
    }

    .layer-sub {
      display: none;
      margin-left: 15px;
      padding: 5px 0;
    }

    .layer-sub div {
      padding: 5px 0;
      font-size: 13px;
    }

    .layer-sub input {
      margin-right: 5px;
    }

    .legend-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
      margin-bottom: 4px;
      font-size: 14px;
    }

    .legend-header .toggle {
      font-size: 15px;
      color: #06005cff;
    }

    .legend-content div {
      display: flex;
      align-items: center;
      margin-bottom: 3px;
    }

    .legend-content span {
      display: inline-block;
      width: 18px;
      height: 12px;
      margin-right: 6px;
      border: 1px solid #001659ff;
    }

    #loadingOverlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      color: #ffffffff;
      font-family: Arial, sans-serif;
      z-index: 9999;
    }

    .loader {
      border: 6px solid #f3f3f3;
      border-top: 6px solid #ff0080;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      animation: spin 1s linear infinite;
      margin-bottom: 20px;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    .locate-button {
      background: white;
      border-radius: 4px;
      padding: 6px;
      cursor: pointer;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.4);
    }

    .locate-button img {
      width: 20px;
      height: 20px;
      display: block;
    }

    .label-jalan {
      font-weight: bold;
      font-size: 8px;
      color: black;
      text-shadow:
        -1px -1px 0 white,
        1px -1px 0 white,
        -1px 1px 0 white,
        1px 1px 0 white;
      white-space: nowrap;
      pointer-events: none;
    }

    .coords-info {
      background: white;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 12px;
      box-shadow: 0 0 4px rgba(0, 0, 0, 0.3);
    }

    .leaflet-control-compass {
      background: white;
      border-radius: 50%;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .leaflet-control-compass img {
      width: 28px;
      height: 28px;
    }
  </style>
</head>

<body>
  <?php
  $page = "peta";
  include "../partials/header.php";
  ?>
  <div id="loadingOverlay">
    <div class="loader"></div>
    <h2>Sedang memuat peta...</h2>
    <p>Mohon tunggu sebentar</p>
  </div>

  <button class="toggle-btn">☰</button>
  <div class="sidebar hidden">
    <!--Fitur Pencarian Nama Jalan-->
    <div class="form-check mt-2" style="margin-left:50px; margin-top:10px;">
      <input type="checkbox" class="form-check-input" id="toggleCariJalan">
      <label class="form-check-label" for="toggleCariJalan"
        style="font-weight:bold; color:#000; font-size:13px;">
        Aktifkan Fitur Jalan
      </label>
    </div>
    <h5 class="mt-3">Fitur Jalan</h5>
    <input type=" text" id="searchJalan" class="form-control form-control-sm" placeholder="Cari nama jalan..." list="jalanList" disabled />
    <datalist id="jalanList"></datalist>
    <!--Fitur Pencarian Batas Kemantren dan Kelurahan-->
    <h5>Filter Wilayah</h5>
    <select id="selkemantren" class="form-select form-select-sm mb-2">
      <option value="">Pilih Kemantren…</option>
    </select>
    <select id="selkelurahan" class="form-select form-select-sm" disabled>
      <option value="">Pilih Kelurahan…</option>
    </select>
    <!--Fitur Penyajian Peta Berdasarkan Layer Utilitas-->
    <h6>Layer Infrastruktur</h6>
    <div id="layerTree" class="mb-3">
      <div class="layer-class" data-target="atribut_jalan">▶ Atribut Jalan </div>
      <div class="layer-sub" id="atribut_jalan">
        <div><input type="checkbox" data-layer="cermin_jalan"> Cermin Jalan </div>
        <div><input type="checkbox" data-layer="kamera_pengawas"> Kamera Pengawas </div>
        <div><input type="checkbox" data-layer="lampu_jalan"> Lampu Jalan </div>
        <div><input type="checkbox" data-layer="lampu_lalin"> Lampu Lalu Lintas </div>
        <div><input type="checkbox" data-layer="rambu_lalin"> Rambu Lalu Lintas </div>
      </div>
      <div class="layer-class" data-target="bangunan">▶ Bangunan </div>
      <div class="layer-sub" id="bangunan">
        <div><input type="checkbox" data-layer="bangunan"> Bangunan </div>
          <div><input type="checkbox" data-layer="rusunawa"> Rusunawa </div>
      </div>
      <div class="layer-class" data-target="halte">▶ Halte </div>
      <div class="layer-sub" id="halte">
        <div><input type="checkbox" data-layer="jalur_halte"> Jalur Halte Trans </div>
        <div><input type="checkbox" data-layer="titik_halte"> Titik Halte </div>
      </div>
      <div class="layer-class" data-target="infrastruktur_pendukung">▶ Infrastruktur Pendukung </div>
      <div class="layer-sub" id="infrastruktur_pendukung">
        <div><input type="checkbox" data-layer="hidran"> Hidran </div>
        <div><input type="checkbox" data-layer="rantai_pasok"> Rantai Pasok </div>
        <div><input type="checkbox" data-layer="reklame"> Reklame </div>
        <div><input type="checkbox" data-layer="titik_bm"> Titik Bench Mark </div>
      </div>
      <div class="layer-class" data-target="jaringan_drainase">▶ Jaringan Drainase </div>
      <div class="layer-sub" id="jaringan_drainase">
        <div><input type="checkbox" data-layer="inlet"> Inlet </div>
        <div><input type="checkbox" data-layer="manhole_drainase"> Manhole Drainase </div>
        <div><input type="checkbox" data-layer="sumur_resapan"> Sumur Resapan </div>
        <div><input type="checkbox" data-layer="zona_drainase"> Zona Drainase </div>
      </div>
      <div class="layer-class" data-target="jaringan_fiber_optik">▶ Jaringan Fiber Optik </div>
      <div class="layer-sub" id="jaringan_fiber_optik">
        <div><input type="checkbox" data-layer="kabel_fiber_optik"> Kabel Fiber Optik </div>
        <div><input type="checkbox" data-layer="manhole_fiber_optik"> Manhole Fiber Optik </div>
        <div><input type="checkbox" data-layer="tiang_fiber_optik"> Tiang Fiber Optik </div>
      </div>
      <div class="layer-class" data-target="jaringan_ipal">▶ Jaringan Instalasi Pengolahan Air Limbah </div>
      <div class="layer-sub" id="jaringan_ipal">
        <div><input type="checkbox" data-layer="manhole_sal"> Manhole Saluran Air Limbah </div>
        <div><input type="checkbox" data-layer="pipa_induk"> Pipa Induk </div>
        <div><input type="checkbox" data-layer="pipa_glontor"> Pipa Glontor </div>
        <div><input type="checkbox" data-layer="pipa_lateral"> Pipa Lateral </div>
      </div>
      <div class="layer-class" data-target="jaringan_jalan">▶ Jaringan Jalan</div>
      <div class="layer-sub" id="jaringan_jalan">
        <div><input type="checkbox" data-layer="jalan_kota"> Jalan Kota </div>
        <div><input type="checkbox" data-layer="jalan_lingkungan"> Jalan Lingkungan </div>
        <div><input type="checkbox" data-layer="jalur_pemandu"> Jalur Pemandu </div>
        <div><input type="checkbox" data-layer="jembatan"> Jembatan </div>
        <div><input type="checkbox" data-layer="trotoar"> Trotoar </div>
      </div>
      <div class="layer-class" data-target="jaringan_listrik">▶ Jaringan Listrik </div>
      <div class="layer-sub" id="jaringan_listrik">
        <div><input type="checkbox" data-layer="tegangan_menengah"> Jaringan Kabel Listrik Tegangan Menengah </div>
        <div><input type="checkbox" data-layer="tegangan_rendah"> Jaringan Kabel Listrik Tegangan Rendah </div>
        <div><input type="checkbox" data-layer="rumah_kabel"> Rumah Kabel </div>
        <div><input type="checkbox" data-layer="tiang_listrik"> Tiang Listrik </div>
        <div><input type="checkbox" data-layer="trafo_listrik"> Trafo Listrik </div>
      </div>
      <div class="layer-class" data-target="jaringan_pdam">▶ Jaringan Perusahaan Daerah Air Minum </div>
      <div class="layer-sub" id="jaringan_pdam">
        <div><input type="checkbox" data-layer="jaringan_pdam"> Jaringan Perusahaan Daerah Air Minum </div>
      </div>
      <div class="layer-class" data-target="sungai">▶ Sungai </div>
      <div class="layer-sub" id="sungai">
        <div><input type="checkbox" data-layer="sungai"> Sungai </div>
      </div>
    </div>
  </div>
  </div>
  <div id="map"></div>

  <script>
    const map = L.map('map', {
      zoomControl: false,
      minZoom: 10,
      maxZoom: 21
    }).setView([-7.7972, 110.3688], 15);
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
    L.Control.Compass = L.Control.extend({
      onAdd: function(map) {
        let container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-compass');
        container.innerHTML = `<img src="../assets/kompas.png" alt="Kompas">`;
        return container;
      }
    });
    new L.Control.Compass({
      position: 'topright'
    }).addTo(map);
    L.control.zoom({
      position: 'topright'
    }).addTo(map);
    L.Control.Expand = L.Control.extend({
      onAdd: function(map) {
        let container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
        container.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" 
           viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" 
           stroke-linecap="round" stroke-linejoin="round">
        <polyline points="15 3 21 3 21 9"></polyline>
        <polyline points="9 21 3 21 3 15"></polyline>
        <line x1="21" y1="3" x2="14" y2="10"></line>
        <line x1="3" y1="21" x2="10" y2="14"></line>
      </svg>`;
        container.style.width = "30px";
        container.style.height = "30px";
        container.style.background = "white";
        container.style.border = "1px solid #ccc";
        container.style.borderRadius = "4px";
        container.style.cursor = "pointer";
        container.style.display = "flex";
        container.style.alignItems = "center";
        container.style.justifyContent = "center";
        container.onclick = function() {
          let allBounds = L.latLngBounds();
          map.eachLayer(layer => {
            try {
              if (layer.getBounds) {
                allBounds.extend(layer.getBounds());
              } else if (layer.getLatLng) {
                allBounds.extend(layer.getLatLng());
              }
            } catch (e) {}
          });
          if (allBounds.isValid()) {
            map.fitBounds(allBounds, {
              padding: [20, 20]
            });
          } else {
            alert("Tidak ada data untuk ditampilkan.");
          }
        };
        return container;
      }
    });
    L.control.expand = function(opts) {
      return new L.Control.Expand(opts);
    };
    L.control.expand({
      position: 'topright'
    }).addTo(map);
  
    const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors',
      maxZoom: 21
    });
    const esri = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
      attribution: 'Tiles © Esri',
      maxZoom: 21
    });
    const esriTopo = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
      attribution: 'Tiles © Esri — Source: Esri, USGS, NOAA',
      maxZoom: 21
    });
    const esriStreet = L.tileLayer(
      'https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles © Esri — Source: Esri, HERE, Garmin, OpenStreetMap contributors, and the GIS user community',
        maxZoom: 21
      }
    );
    const carto = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
      attribution: '© CartoDB',
      maxZoom: 21
    });
    const voyager = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
      attribution: '© CartoDB',
      subdomains: 'abcd',
      maxZoom: 21
    });
    const ortho = L.tileLayer('https://sitijo.wuaze.com/assets/ortho/{z}/{x}/{y}.png', {
      attribution: '© Orthophoto',
      minZoom: 17,
      maxZoom: 20,
      tms: false,
      opacity: 1,
      noWrap: true,
      errorTileUrl: '../assets/blank.jpg'
    });
    esri.addTo(map);
    const baseLayers = {
      "Carto Light": carto,
      "Carto Voyager": voyager,
      "ESRI World Imagery": esri,
      "ESRI World Topographic": esriTopo,
      "ESRI World Street Map": esriStreet,
      "Open Street Map": osm,
      "Orthophoto 2023": ortho,
    };

    let layerUtilitas = {};
    const overlayMaps = {};
    Object.entries(layerUtilitas).forEach(([key, group]) => {
      Object.entries(group).forEach(([name, layer]) => {
        overlayMaps[`${key} - ${name}`] = layer;
      });
    });
    L.control.layers(baseLayers, overlayMaps, {
      position: 'topright'
    }).addTo(map);
    L.control.scale({
      metric: true,
      imperial: true,
      position: 'bottomleft'
    }).addTo(map);


    const legend = L.control({
      position: 'bottomright'
    });
    legend.onAdd = () => {
      const container = L.DomUtil.create('div', 'legend-container');
      container.innerHTML = `
    <div class="legend-header"><strong>Legenda</strong></div>
    <div class="legend-content"></div>
    <div class="legend-item">
      <svg width="30" height="10">
        <line x1="0" y1="5" x2="30" y2="5" stroke="#ff0000ff" stroke-dasharray="6,4" stroke-width="2"/>
      </svg>
      Batas Kelurahan
    </div>
  `;
      return container;
    };
    legend.addTo(map);

    function updateLegend() {
      const legendContent = document.querySelector(".legend-content");
      if (!legendContent) return;
      legendContent.innerHTML = "";
      const checkboxes = document.querySelectorAll("#layerTree input[type=checkbox]");
      let adaItem = false;

      checkboxes.forEach(cb => {
        if (!cb.checked) return;

        const sub = cb.dataset.layer;
        let style = null;
        let type = null;

        for (const cls in layerUtilitas) {
          if (!layerUtilitas[cls]) continue;
          if (layerUtilitas[cls][sub]) {
            const candidate = layerUtilitas[cls][sub];
            let firstLayer = null;
            if (candidate.getLayers) {
              const layers = candidate.getLayers();
              if (layers && layers.length) firstLayer = layers[0];
            } else if (candidate.eachLayer) {
              candidate.eachLayer(l => {
                if (!firstLayer) firstLayer = l;
              });
            }
            const first = firstLayer;
            if (first?.feature?.style) {
              style = first.feature.style;
              type = style.type || "polygon";
            } else if (first?.options) {
              style = first.options || null;
              type = style?.type || (first.feature ? "polygon" : null);
            }
          }
        }

        if (!style) return;

        let symbol = "";
        if (type === "line") {
          symbol = `<span style="display:inline-block;width:25px;height:3px;background:${style.color};margin-right:5px;"></span>`;
        } else if (type === "polygon") {
          symbol = `<span style="display:inline-block;width:20px;height:15px;background:${style.fillColor || style.color};border:2px solid ${style.color || "#000"};margin-right:5px;"></span>`;
        } else if (type === "multilinestring") {
          symbol = `<span style="display:inline-block;width:25px;height:3px;background:${style.color || style.color};border:2px solid ${style.color || "#000"};margin-right:5px;"></span>`;
        } else if (type === "multipolygon") {
          symbol = `<span style="display:inline-block;width:20px;height:15px;background:${style.fillColor || style.color};border:2px solid ${style.color || "#000"};margin-right:5px;"></span>`;
        } else if (type === "point" && style.marker) {
          symbol = `<img src="${style.marker}" width="15" height="15" style="margin-right:5px;">`;
        } else {
          symbol = `<span style="display:inline-block;width:15px;height:12px;background:#ccc;margin-right:6px;border:1px solid #666;"></span>`;
        }

        legendContent.innerHTML += `<div>${symbol}${cb.parentNode.textContent.trim()}</div>`;
        adaItem = true;
      });

      if (!adaItem) {
        legendContent.innerHTML = "<i>Tidak ada layer aktif</i>";
      }
    }

    const sidebar = document.querySelector(".sidebar");
    document.querySelector(".toggle-btn").addEventListener("click", () => {
      sidebar.classList.toggle("hidden");
    });
    document.querySelectorAll(".layer-class").forEach(el => {
      el.onclick = () => {
        const target = document.getElementById(el.dataset.target);
        const visible = target.style.display === "block";
        target.style.display = visible ? "none" : "block";
        const originalText = el.textContent.trim().replace(/^▶|▼/, "").trim();
        el.textContent = (visible ? "▶ " : "▼ ") + originalText;
      };
    });

    const selkemantren = document.getElementById("selkemantren");
    const selkelurahan = document.getElementById("selkelurahan");
    let dataWilayah = {};
    let allLayer = null;
    let layerByKemantren = {};
    let layerByKelurahan = {};
    let lastHighlight = null;
    let overlays = {};

    function highlightLayer(group) {
      if (lastHighlight) {
        lastHighlight.eachLayer(l => {
          l.setStyle({
            color: "#fff200e0",
            weight: 1.75,
            fillOpacity: 0.01
          });
        });
      }
      group.eachLayer(l => {
        l.setStyle({
          color: "#ff6200ff",
          weight: 3,
          fillOpacity: 0.1
        });
      });
      lastHighlight = group;
    }

    function highlightJalan(layers) {
      if (window.lastJalanHighlight) {
        window.lastJalanHighlight.forEach(l => l.setStyle({
          color: "orange",
          weight: 2
        }));
      }
      layers.forEach(l => l.setStyle({
        color: "orange",
        weight: 4
      }));
      window.lastJalanHighlight = layers;
      map.fitBounds(L.featureGroup(layers).getBounds(), {
        padding: [50, 50]
      });
    }
    const searchInput = document.getElementById("searchJalan");
    const jalanList = document.getElementById("jalanList");
    let jalanLayer;

    // === DATA BATAS ADMINISTRASI ===
    fetch("../backend/gis/batas_administrasi.php")
      .then(r => r.json())
      .then(geometry => {
        const wilayah = {};
        allLayer = L.geoJSON(geometry, {
          style: feature => {
            return {
              color: "#fa2b66ff",
              weight: 2.5,
              opacity: 1,
              dashArray: "12",
              fillOpacity: 0
            };
          },
          onEachFeature: (f, l) => {
            const neg = f.properties.negara;
            const prov = f.properties.provinsi;
            const kot = f.properties.kota;
            const kem = f.properties.kemantren;
            const kel = f.properties.kelurahan;
            const luas = f.properties.luas_kel;
            const id = f.properties.id_kel;
            if (!wilayah[kem]) wilayah[kem] = [];
            if (!wilayah[kem].includes(kel)) wilayah[kem].push(kel);
            if (!layerByKemantren[kem]) layerByKemantren[kem] = L.featureGroup();
            layerByKemantren[kem].addLayer(l);
            if (!layerByKelurahan[kel]) layerByKelurahan[kel] = L.featureGroup();
            layerByKelurahan[kel].addLayer(l);
            l.bindPopup(`
            <table style="border-collapse: collapse; width: 100%;">
              <tr><td><b>Negara</b></td><td>: ${neg || "-"}</td></tr>
              <tr><td><b>Provinsi</b></td><td>: ${prov || "-"}</td></tr>
              <tr><td><b>Kota</b></td><td>: ${kot || "-"}</td></tr>
              <tr><td><b>Kemantren</b></td><td>: ${kem || "-"}</td></tr>
              <tr><td><b>Kelurahan</b></td><td>: ${kel || "-"}</td></tr>
              <tr><td><b>Luas Area</b></td><td>: ${luas ? luas + " km²" : "-"}</td></tr>
            </table>`);
          }
        }).addTo(map);
        dataWilayah = wilayah;
        selkemantren.innerHTML = '<option value="">Pilih Kemantren…</option>';
        Object.keys(dataWilayah).forEach(k => {
          selkemantren.add(new Option(k, k));
        });
      });
    selkemantren.addEventListener("change", () => {
      const kem = selkemantren.value;
      selkelurahan.innerHTML = '<option value="">Pilih Kelurahan…</option>';
      selkelurahan.disabled = true;
      if (allLayer) map.removeLayer(allLayer);
      Object.values(layerByKemantren).forEach(g => map.removeLayer(g));
      Object.values(layerByKelurahan).forEach(g => map.removeLayer(g));
      if (kem && layerByKemantren[kem]) {
        layerByKemantren[kem].addTo(map);
        map.fitBounds(layerByKemantren[kem].getBounds());
        highlightLayer(layerByKemantren[kem]);
        dataWilayah[kem].forEach(kel => {
          selkelurahan.add(new Option(kel, kel));
        });
        selkelurahan.disabled = false;
      } else if (allLayer) {
        allLayer.addTo(map);
      }
    });
    selkelurahan.addEventListener("change", () => {
      const kel = selkelurahan.value;
      Object.values(layerByKelurahan).forEach(g => map.removeLayer(g));
      if (kel && layerByKelurahan[kel]) {
        layerByKelurahan[kel].addTo(map);
        map.fitBounds(layerByKelurahan[kel].getBounds());
        highlightLayer(layerByKelurahan[kel]);
      } else {
        const kem = selkemantren.value;
        if (kem && layerByKemantren[kem]) {
          layerByKemantren[kem].addTo(map);
          map.fitBounds(layerByKemantren[kem].getBounds());
          highlightLayer(layerByKemantren[kem]);
        }
      }
    });

    function loadUtilitas(data) {
      for (const key in data) {
        if (key === "jaringan_listrik") continue;
        const item = data[key];
        if (!layerUtilitas[key]) layerUtilitas[key] = {};
        if (item && item.type === "FeatureCollection" && item.features) {
          layerUtilitas[key][key] = L.geoJSON(item, {
            style: f => f.style || {
              color: "blue",
              weight: 1
            },
            onEachFeature: (feature, layer) => bindPopupUtilitas(feature, layer)
          });
        } else if (item && typeof item === "object") {
          for (const subKey in item) {
            const subItem = item[subKey];
            if (subItem && subItem.type === "FeatureCollection" && subItem.features) {
              layerUtilitas[key][subKey] = L.geoJSON(subItem, {
                style: f => f.style || {
                  color: "blue",
                  weight: 1
                },
                onEachFeature: (feature, layer) => bindPopupUtilitas(feature, layer)
              });
            }
          }
        }
      }
    }

    function bindPopupUtilitas(feature, layer) {
      if (feature.properties) {
        let props = feature.properties;
        let table = `<table style="border-collapse: collapse; width: 100%;">`;
        for (const key in props) {
          table += `<tr><td><b>${key}</b></td><td>: ${props[key] || "-"}</td></tr>`;
        }
        table += `</table>`;
        layer.bindPopup(table);
      }
    }

    function resolveIconPath(marker) {
      if (!marker) return "";
      if (marker.startsWith("/") || marker.startsWith("http")) {
        return marker;
      }
      if (marker.includes("/")) {
        return marker;
      }
      return marker;
    }

    Promise.all([
        fetch("../backend/gis/utilitas1.php").then(r => r.json()),
        fetch("../backend/gis/utilitas2.php").then(r => r.json()),
        fetch("../backend/gis/utilitas3.php").then(r => r.json()),
        fetch("../backend/gis/utilitas4.php").then(r => r.json()),
        fetch("../backend/gis/utilitas5.php").then(r => r.json()),
        fetch("../backend/gis/utilitas6.php").then(r => r.json()),
        fetch("../backend/gis/utilitas7.php").then(r => r.json()),
        fetch("../backend/gis/utilitas8.php").then(r => r.json()),
        fetch("../backend/gis/utilitas9.php").then(r => r.json()),
        fetch("../backend/gis/utilitas10.php").then(r => r.json()),
        fetch("../backend/gis/utilitas11.php").then(r => r.json()),
        fetch("../backend/gis/utilitas12.php").then(r => r.json()),
        fetch("../backend/gis/utilitas13.php").then(r => r.json()),
        fetch("../backend/gis/utilitas14.php").then(r => r.json()),
        fetch("../backend/gis/utilitas15.php").then(r => r.json())
      ])
      
        .then(allData => {
  const utilitasData = {};

  for (const data of allData) {
    for (const [key, fc] of Object.entries(data)) {
      if (utilitasData[key] && fc?.type === "FeatureCollection") {
        utilitasData[key].features.push(...fc.features);
      } else {
        utilitasData[key] = fc;
      }
    }
  }

  console.log("DATA UTILITAS GABUNGAN:", utilitasData);
  loadUtilitas(utilitasData);

        //ATRIBUT JALAN
        // 1.Cermin Jalan
        if (utilitasData.cermin_jalan) {
          let layer = L.geoJSON(utilitasData.cermin_jalan, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [30, 30]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
        <table style="border-collapse: collapse; width: 100%;">
          <tr><td><b>Kelas</b></td><td>: ${props.kelas || "-"}</td></tr>
          <tr><td><b>Subkelas</b></td><td>: ${props.subkelas || "-"}</td></tr>
          <tr><td><b>ID Cermin Jalan</b></td><td>: ${props.id || "-"}</td></tr>
        </table>
      `);
            }
          });
          if (!layerUtilitas["cermin_jalan"]) layerUtilitas["cermin_jalan"] = {};
          layerUtilitas["cermin_jalan"]["cermin_jalan"] = layer;
        }
        // 2. Kamera Pengawas
        if (utilitasData.kamera_pengawas) {
          let layer = L.geoJSON(utilitasData.kamera_pengawas, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [30, 30]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
        <table style="border-collapse: collapse; width: 100%;">
          <tr><td><b>Kelas</b></td><td>: ${props.kelas || "-"}</td></tr>
          <tr><td><b>Subkelas</b></td><td>: ${props.subkelas || "-"}</td></tr>
          <tr><td><b>ID Kamera Pengawas</b></td><td>: ${props.id || "-"}</td></tr>
        </table>
      `);
            }
          });
          if (!layerUtilitas["kamera_pengawas"]) layerUtilitas["kamera_pengawas"] = {};
          layerUtilitas["kamera_pengawas"]["kamera_pengawas"] = layer;
        }
        // 3.Lampu Jalan
        if (utilitasData.lampu_jalan) {
          let layer = L.geoJSON(utilitasData.lampu_jalan, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [20, 20]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
        <table style="border-collapse: collapse; width: 100%;">
          <tr><td><b>Kelas</b></td><td>: ${props.kelas || "-"}</td></tr>
          <tr><td><b>Subkelas</b></td><td>: ${props.subkelas || "-"}</td></tr>
          <tr><td><b>ID Lampu Jalan</b></td><td>: ${props.id || "-"}</td></tr>
          <tr><td><b>Kode Lampu Jalan</b></td><td>: ${props.kode || "-"}</td></tr>
          <tr><td><b>Kondisi</b></td><td>: ${props.kondisi || "-"}</td></tr>
          <tr><td><b>Pondasi</b></td><td>: ${props.pondasi || "-"}</td></tr>
          <tr><td><b>Daya Lampu</b></td><td>: ${props.daya_lampu || "-"}</td></tr>
          <tr><td><b>Teknologi</b></td><td>: ${props.teknologi || "-"}</td></tr>
        </table>
      `);
            }
          });
          if (!layerUtilitas["lampu_jalan"]) layerUtilitas["lampu_jalan"] = {};
          layerUtilitas["lampu_jalan"]["lampu_jalan"] = layer;
        }
        // 4. Lampu Lalin
        if (utilitasData.lampu_lalin) {
          let layer = L.geoJSON(utilitasData.lampu_lalin, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [25, 25]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Lampu Lalu Lintas</b></td><td>:${props.id || "-"}</td></tr>
            </table>
          `);
            }
          });
          if (!layerUtilitas["lampu_lalin"]) layerUtilitas["lampu_lalin"] = {};
          layerUtilitas["lampu_lalin"]["lampu_lalin"] = layer;
        }
        // 5. Rambu Lalin
        if (utilitasData.rambu_lalin) {
          let layer = L.geoJSON(utilitasData.rambu_lalin, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [20, 20]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Rambu Lalu Lintas</b></td><td>:${props.id || "-"}</td></tr>
            </table>
          `);
            }
          });
          if (!layerUtilitas["rambu_lalin"]) layerUtilitas["rambu_lalin"] = {};
          layerUtilitas["rambu_lalin"]["rambu_lalin"] = layer;
        }
        //BANGUNAN
        // 1. Bangunan
        if (utilitasData.bangunan) {
          let layer = L.geoJSON(utilitasData.bangunan, {
            style: feature => feature.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: feature.style.size || [15, 15]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
              <table style="border-collapse: collapse; width: 100%;">
                <tr><td><b>Kelas</b></td><td>: ${props.kelas || "-"}</td></tr>
                <tr><td><b>Subkelas</b></td><td>: ${props.subkelas || "-"}</td></tr>
                <tr><td><b>ID Bangunan</b></td><td>: ${props.id || "-"}</td></tr>
                <tr><td><b>Kemantren</b></td><td>: ${props.kemantren || "-"}</td></tr>
                <tr><td><b>Kelurahan</b></td><td>: ${props.kelurahan || "-"}</td></tr>
              </table>
            `);
            }
          });
          if (!layerUtilitas["bangunan"]) layerUtilitas["bangunan"] = {};
          layerUtilitas["bangunan"]["bangunan"] = layer;
        }
        //HALTE
        // 1. Titik Halte
        if (utilitasData.titik_halte) {
          let layer = L.geoJSON(utilitasData.titik_halte, {
            style: feature => feature.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: feature.style.size || [25, 25]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
              <table style="border-collapse: collapse; width: 100%;">
                <tr><td><b>Kelas</b></td><td>: ${props.kelas || "-"}</td></tr>
                <tr><td><b>Subkelas</b></td><td>: ${props.subkelas || "-"}</td></tr>
                <tr><td><b>ID Titik Halte</b></td><td>: ${props.id || "-"}</td></tr>
                <tr><td><b>Nama Halte</b></td><td>: ${props.nama || "-"}</td></tr>
              </table>
            `);
            }
          });
          if (!layerUtilitas["titik_halte"]) layerUtilitas["titik_halte"] = {};
          layerUtilitas["titik_halte"]["titik_halte"] = layer;
        }
        // 2.Jalur Halte
        if (utilitasData.jalur_halte) {
          let layer = L.geoJSON(utilitasData.jalur_halte, {
            style: feature => feature.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: feature.style.size || [15, 15]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
              <table style="border-collapse: collapse; width: 100%;">
                <tr><td><b>Kelas</b></td><td>: ${props.kelas || "-"}</td></tr>
                <tr><td><b>Subkelas</b></td><td>: ${props.subkelas || "-"}</td></tr>
                <tr><td><b>ID Jalur Halte</b></td><td>: ${props.id || "-"}</td></tr>
                <tr><td><b>Nama Jalur</b></td><td>: ${props.nama || "-"}</td></tr>
              </table>
            `);
            }
          });
          if (!layerUtilitas["jalur_halte"]) layerUtilitas["jalur_halte"] = {};
          layerUtilitas["jalur_halte"]["jalur_halte"] = layer;
        }
        // UTILITAS PENDUKUNG
        // 1.Hidran
        if (utilitasData.hidran) {
          let layer = L.geoJSON(utilitasData.hidran, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [25, 25]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Hidran</b></td><td>:${props.id || "-"}</td></tr>
            </table>
          `);
            }
          });
          if (!layerUtilitas["hidran"]) layerUtilitas["hidran"] = {};
          layerUtilitas["hidran"]["hidran"] = layer;
        }
        // 2.Rantai Pasok
        if (utilitasData.rantai_pasok) {
          let layer = L.geoJSON(utilitasData.rantai_pasok, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [18, 18]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID</b></td><td>:${props.id || "-"}</td></tr>
              <tr><td><b>Jenis</b></td><td>:${props.jenis || "-"}</td></tr>
              <tr><td><b>Nama</b></td><td>:${props.nama || "-"}</td></tr>
              <tr><td><b>Bentuk</b></td><td>:${props.bentuk || "-"}</td></tr>
              <tr><td><b>Alamat</b></td><td>:${props.alamat || "-"}</td></tr>
              <tr><td><b>No. Telepon</b></td><td>:${props.bentuk || "-"}</td></tr>
            </table>
          `);
            }
          });
          if (!layerUtilitas["rantai_pasok"]) layerUtilitas["rantai_pasok"] = {};
          layerUtilitas["rantai_pasok"]["rantai_pasok"] = layer;
        }
        // 3.Reklame
        if (utilitasData.reklame) {
          let layer = L.geoJSON(utilitasData.reklame, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [15, 15]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Reklame</b></td><td>:${props.id_reklame || "-"}</td></tr>
              <tr><td><b>Keterangan</b></td><td>:${props.keterangan || "-"}</td></tr>
            </table>
          `);
            }
          });
          if (!layerUtilitas["reklame"]) layerUtilitas["reklame"] = {};
          layerUtilitas["reklame"]["reklame"] = layer;
        }
        // 4.Titik Bench Mark
        if (utilitasData.titik_bm) {
          let layer = L.geoJSON(utilitasData.titik_bm, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [25, 25]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Titik</b></td><td>:${props.id || "-"}</td></tr>
              <tr><td><b>Nama Titik</b></td><td>:${props.nama || "-"}</td></tr>
              <tr><td><b>Easting</b></td><td>:${props.easting || "-"}</td></tr>
              <tr><td><b>Northing</b></td><td>:${props.northing || "-"}</td></tr>
              <tr><td><b>Tinggi</b></td><td>:${props.tinggi || "-"}</td></tr>
            </table>
          `);
            }
          });
          if (!layerUtilitas["titik_bm"]) layerUtilitas["titik_bm"] = {};
          layerUtilitas["titik_bm"]["titik_bm"] = layer;
        }
        //JARINGAN DRAINASE
        // 1.Inlet
        if (utilitasData.inlet) {
          let layer = L.geoJSON(utilitasData.inlet, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [15, 15]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Inlet</b></td><td>:${props.id || "-"}</td></tr>
            </table>
          `);
            }
          });
          if (!layerUtilitas["inlet"]) layerUtilitas["inlet"] = {};
          layerUtilitas["inlet"]["inlet"] = layer;
        }
        // 2.Manhole Drainase
        if (utilitasData.manhole_drainase) {
          let layer = L.geoJSON(utilitasData.manhole_drainase, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [20, 20]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Manhole Drainase</b></td><td>:${props.id || "-"}</td></tr>
            </table>
          `);
            }
          });
          if (!layerUtilitas["manhole_drainase"]) layerUtilitas["manhole_drainase"] = {};
          layerUtilitas["manhole_drainase"]["manhole_drainase"] = layer;
        }
        // 3.Sumur Resapan
        if (utilitasData.sumur_resapan) {
          let layer = L.geoJSON(utilitasData.sumur_resapan, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [20, 20]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Sumur Resapan</b></td><td>:${props.id || "-"}</td></tr>
            </table>
          `);
            }
          });
          if (!layerUtilitas["sumur_resapan"]) layerUtilitas["sumur_resapan"] = {};
          layerUtilitas["sumur_resapan"]["sumur_resapan"] = layer;
        }
        // 4.Zona Drainase
        if (utilitasData.zona_drainase) {
          let layer = L.geoJSON(utilitasData.zona_drainase, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [8, 8]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Zona Drainase</b></td><td>:${props.id || "-"}</td></tr>
              <tr><td><b>Zona Drainase</b></td><td>:${props.zona || "-"}</td></tr>
              <tr><td><b>Tipe Drainase</b></td><td>:${props.tipe || "-"}</td></tr>
              <tr><td><b>Fungsi Drainase</b></td><td>:${props.fungsi || "-"}</td></tr>
              <tr><td><b>Kondisi Drainase</b></td><td>:${props.kondisi || "-"}</td></tr>
              <tr><td><b>Arah Drainase</b></td><td>:${props.arah || "-"}</td></tr>
              <tr><td><b>Dimensi Drainase</b></td><td>:${props.dimensi || "-"}</td></tr>
            </table>
          `);
            }
          });
          if (!layerUtilitas["zona_drainase"]) layerUtilitas["zona_drainase"] = {};
          layerUtilitas["zona_drainase"]["zona_drainase"] = layer;
        }
        //JARINGAN FIBER OPTIK
        // 1.Kabel Fiber Optik
        if (utilitasData.kabel_fiber_optik) {
          let layer = L.geoJSON(utilitasData.kabel_fiber_optik, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [8, 8]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Kabel Fiber Optik</b></td><td>:${props.id || "-"}</td></tr>
              <tr><td><b>Provider</b></td><td>:${props.provider || "-"}</td></tr>
            </table>
          `);
            }
          });
          if (!layerUtilitas["kabel_fiber_optik"]) layerUtilitas["kabekabel_fiber_optikl_fo"] = {};
          layerUtilitas["kabel_fiber_optik"]["kabel_fiber_optik"] = layer;
        }
        // 2.Manhole Fiber Optik
        if (utilitasData.manhole_fiber_optik) {
          let layer = L.geoJSON(utilitasData.manhole_fiber_optik, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [20, 20]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Manhole Fiber Optik</b></td><td>:${props.id || "-"}</td></tr>
            </table>
          `);
            }
          });
          if (!layerUtilitas["manhole_fiber_optik"]) layerUtilitas["manhole_fiber_optik"] = {};
          layerUtilitas["manhole_fiber_optik"]["manhole_fiber_optik"] = layer;
        }
        // 3.Tiang Fiber Optik
        if (utilitasData.tiang_fiber_optik) {
          let layer = L.geoJSON(utilitasData.tiang_fiber_optik, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [20, 20]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Tiang Fiber Optik</b></td><td>:${props.id || "-"}</td></tr>
            </table>
          `);
            }
          });
          if (!layerUtilitas["tiang_fiber_optik"]) layerUtilitas["tiang_fiber_optik"] = {};
          layerUtilitas["tiang_fiber_optik"]["tiang_fiber_optik"] = layer;
        }
        // JARINGAN IPAL
        // 1.Manhole SAL 
        if (utilitasData.manhole_sal) {
          let layer = L.geoJSON(utilitasData.manhole_sal, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [15, 15]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Manhole SAL</b></td><td>:${props.id || "-"}</td></tr>
            </table>
          `);
            }
          });
          if (!layerUtilitas["manhole_sal"]) layerUtilitas["manhole_sal"] = {};
          layerUtilitas["manhole_sal"]["manhole_sal"] = layer;
        }
        // 2.Pipa Glontor
        if (utilitasData.pipa_glontor) {
          let layer = L.geoJSON(utilitasData.pipa_glontor, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [8, 8]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Pipa Glontor</b></td><td>:${props.id || "-"}</td></tr>
            </table>
          `);
            }
          });
          if (!layerUtilitas["pipa_glontor"]) layerUtilitas["pipa_glontor"] = {};
          layerUtilitas["pipa_glontor"]["pipa_glontor"] = layer;
        }
        // 3.Pipa Induk
        if (utilitasData.pipa_induk) {
          let layer = L.geoJSON(utilitasData.pipa_induk, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [8, 8]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Pipa Induk</b></td><td>:${props.id || "-"}</td></tr>
            </table>
          `);
            }
          });
          if (!layerUtilitas["pipa_induk"]) layerUtilitas["pipa_induk"] = {};
          layerUtilitas["pipa_induk"]["pipa_induk"] = layer;
        }
        // 4.Pipa Lateral
        if (utilitasData.pipa_lateral) {
          let layer = L.geoJSON(utilitasData.pipa_lateral, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [8, 8]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, layer) => {
              let props = feature.properties;
              layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Pipa Lateral</b></td><td>:${props.id || "-"}</td></tr>
            </table>
          `);
            }
          });
          if (!layerUtilitas["pipa_lateral"]) layerUtilitas["pipa_lateral"] = {};
          layerUtilitas["pipa_lateral"]["pipa_lateral"] = layer;
        }
        // JARINGAN JALAN
        // 1.Jalan Kota
        if (utilitasData.jalan_kota) {
          const jalanKotaFeatures = utilitasData.jalan_kota.features.filter(
            f => (f.properties.subkelas || "").toLowerCase() === "jalan kota"
          );

          const layerGroup = L.layerGroup();

          const geoLayer = L.geoJSON(jalanKotaFeatures, {
            style: f => f.style,
            pointToLayer: (feature, latlng) => {
              if (feature.style && feature.style.marker) {
                return L.marker(latlng, {
                  icon: L.icon({
                    iconUrl: feature.style.marker,
                    iconSize: [8, 8]
                  })
                });
              }
              return L.circleMarker(latlng, {
                radius: 5,
                color: feature.style?.color || "#444"
              });
            },
            onEachFeature: (feature, l) => {
              const props = feature.properties;

              l.bindPopup(`
        <table style="border-collapse: collapse; width: 100%;">
          <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
          <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
          <tr><td><b>ID Jalan</b></td><td>:${props.id_jalan || "-"}</td></tr>
          <tr><td><b>Nama Jalan</b></td><td>:${props.nama_jalan || "-"}</td></tr>
          <tr><td><b>Kemantren</b></td><td>:${props.kemantren || "-"}</td></tr>
          <tr><td><b>Kelurahan</b></td><td>:${props.kelurahan || "-"}</td></tr>
          <tr><td><b>Material</b></td><td>:${props.material || "-"}</td></tr>
          <tr><td><b>Lebar Minimal</b></td><td>:${props.lebar_min || "-"}</td></tr>
          <tr><td><b>Lebar Maksimal</b></td><td>:${props.lebar_max || "-"}</td></tr>
          <tr><td><b>Lebar Rata-Rata</b></td><td>:${props.lebar_rata || "-"}</td></tr>
          <tr>
  <td><b>Panjang Jalan</b></td>
  <td>: ${props.panjang ? props.panjang + " m" : "-"}</td>
</tr>

        </table>
      `);
              if (feature.geometry.type === "LineString" && l.setText) {
                l.setText(props.nama_jalan, {
                  repeat: false,
                  center: true,
                  offset: -3,
                  orientation: 0,
                  attributes: {
                    fill: "black",
                    "font-weight": "bold",
                    "font-size": "10px",
                    "paint-order": "stroke",
                    stroke: "white",
                    "stroke-width": 3
                  }
                });
              }
              if (feature.geometry.type === "Polygon" || feature.geometry.type === "MultiPolygon") {
                const center = turf.centroid(feature).geometry.coordinates;
                const latlng = L.latLng(center[1], center[0]);
                const label = L.marker(latlng, {
                  icon: L.divIcon({
                    className: "label-jalan",
                    html: `<span>${props.nama_jalan}</span>`,
                    iconSize: null,
                    iconAnchor: [0, 0]
                  }),
                  interactive: false
                });
                layerGroup.addLayer(label);
              }
            }
          });

          geoLayer.eachLayer(f => layerGroup.addLayer(f));

          if (!layerUtilitas["jalan_kota"]) layerUtilitas["jalan_kota"] = {};
          layerUtilitas["jalan_kota"]["jalan_kota"] = layerGroup;
          // Fitur Pencarian Nama Jalan
          const jalanMap = {};
          const namaJalanSet = new Set();
          geoLayer.eachLayer(l => {
            const nama = l.feature.properties.nama_jalan;
            if (!nama) return;
            const key = nama.toLowerCase();
            if (!jalanMap[key]) jalanMap[key] = [];
            jalanMap[key].push(l);
            namaJalanSet.add(nama);
          });
          jalanList.innerHTML = "";
          namaJalanSet.forEach(nama => {
            const option = document.createElement("option");
            option.value = nama;
            jalanList.appendChild(option);
          });
          const toggleCariJalan = document.getElementById("toggleCariJalan");
          searchInput.disabled = true;
          let jalanLayerActive = false;
          toggleCariJalan.addEventListener("change", () => {
            searchInput.disabled = !toggleCariJalan.checked;
            if (toggleCariJalan.checked) {
              searchInput.focus();
              if (!jalanLayerActive) {
                layerGroup.addTo(map);
                jalanLayerActive = true;
              }
            } else {
              if (window.lastJalanHighlight) {
                window.lastJalanHighlight.forEach(l => l.setStyle({
                  color: "#dcdcdcff",
                  weight: 0.1
                }));
                window.lastJalanHighlight = null;
              }
              searchInput.value = "";
              if (jalanLayerActive) {
                map.removeLayer(layerGroup);
                jalanLayerActive = false;
              }
            }
          });
          searchInput.addEventListener("input", e => {
            const value = e.target.value.toLowerCase().trim();
            if (!value) {
              if (window.lastJalanHighlight) {
                window.lastJalanHighlight.forEach(l => l.setStyle({
                  color: "#dcdcdcff",
                  weight: 0.1
                }));
                window.lastJalanHighlight = null;
              }
              return;
            }
            const matchedLayers = [];
            Object.keys(jalanMap).forEach(key => {
              if (key.includes(value)) matchedLayers.push(...jalanMap[key]);
            });
            if (matchedLayers.length) {
              if (window.lastJalanHighlight)
                window.lastJalanHighlight.forEach(l => l.setStyle({
                  color: "#dcdcdcff",
                  weight: 0.1
                }));
              matchedLayers.forEach(l => l.setStyle({
                color: "#ff00d9ff",
                weight: 4
              }));
              window.lastJalanHighlight = matchedLayers;
              map.fitBounds(L.featureGroup(matchedLayers).getBounds(), {
                padding: [50, 50]
              });
            } else if (window.lastJalanHighlight) {
              window.lastJalanHighlight.forEach(l => l.setStyle({
                color: "#dcdcdcff",
                weight: 0.1
              }));
              window.lastJalanHighlight = null;
            }
          });

          // 2.Jalan Lingkungan
          if (utilitasData.jalan_lingkungan) {
            let layer = L.geoJSON(utilitasData.jalan_lingkungan, {
              style: f => f.style,
              pointToLayer: (feature, latlng) => {
                if (feature.style && feature.style.marker) {
                  return L.marker(latlng, {
                    icon: L.icon({
                      iconUrl: feature.style.marker,
                      iconSize: [8, 8]
                    })
                  });
                }
                return L.circleMarker(latlng, {
                  radius: 5,
                  color: feature.style?.color || "#444"
                });
              },
              onEachFeature: (feature, layer) => {
                let props = feature.properties;
                layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Jalan Lingkungan</b></td><td>:${props.id_jalan || "-"}</td></tr>
              <tr><td><b>Nama Jalan</b></td><td>:${props.nama_jalan || "-"}</td></tr>
              <tr><td><b>Kemantren</b></td><td>:${props.kemantren || "-"}</td></tr>
              <tr><td><b>Kelurahan</b></td><td>:${props.kelurahan || "-"}</td></tr>
              <tr>
  <td><b>Panjang Jalan</b></td>
  <td>: ${props.panjang ? props.panjang + " m" : "-"}</td>
</tr>
            </table>
          `);
              }
            });
            if (!layerUtilitas["jalan_lingkungan"]) layerUtilitas["jalan_lingkungan"] = {};
            layerUtilitas["jalan_lingkungan"]["jalan_lingkungan"] = layer;
          }
          // 3.Jalur Pemandu
          if (utilitasData.jalur_pemandu) {
            let layer = L.geoJSON(utilitasData.jalur_pemandu, {
              style: f => f.style,
              pointToLayer: (feature, latlng) => {
                if (feature.style && feature.style.marker) {
                  return L.marker(latlng, {
                    icon: L.icon({
                      iconUrl: feature.style.marker,
                      iconSize: [8, 8]
                    })
                  });
                }
                return L.circleMarker(latlng, {
                  radius: 5,
                  color: feature.style?.color || "#444"
                });
              },
              onEachFeature: (feature, layer) => {
                let props = feature.properties;
                layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Jalan</b></td><td>:${props.id || "-"}</td></tr>
              <tr><td><b>Kemantren</b></td><td>:${props.kecamatan || "-"}</td></tr>
              <tr><td><b>Kelurahan</b></td><td>:${props.kelurahan || "-"}</td></tr>
              <tr>
  <td><b>Lebar Jalan</b></td>
  <td>: ${props.lebar ? props.lebar + " m" : "-"}</td>
</tr>
            </table>
          `);
              }
            });
            if (!layerUtilitas["jalur_pemandu"]) layerUtilitas["jalur_pemandu"] = {};
            layerUtilitas["jalur_pemandu"]["jalur_pemandu"] = layer;
          }
          // 4.Jembatan
          if (utilitasData.jembatan) {
            let layer = L.geoJSON(utilitasData.jembatan, {
              pointToLayer: (feature, latlng) => {
                if (feature.style && feature.style.marker) {
                  return L.marker(latlng, {
                    icon: L.icon({
                      iconUrl: feature.style.marker,
                      iconSize: feature.style.size || [40, 40]
                    })
                  });
                }
                return L.circleMarker(latlng, {
                  radius: feature.style?.size || 8,
                  color: feature.style?.color || "#ff0000",
                  fillOpacity: 0.8
                });
              },
              onEachFeature: (feature, layer) => {
                let props = feature.properties;
                layer.bindPopup(`
      <table style="border-collapse: collapse; width: 100%;">
        <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
        <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
        <tr><td><b>ID Jembatan</b></td><td>:${props.id || "-"}</td></tr>
        <tr><td><b>Nama Jembatan</b></td><td>:${props.nama || "-"}</td></tr>
        <tr><td><b>Latitude</b></td><td>:${props.latitude || "-"}</td></tr>
        <tr><td><b>Longitude</b></td><td>:${props.longitude || "-"}</td></tr>
      </table>
    `);
              }
            });

            if (!layerUtilitas["jembatan"]) layerUtilitas["jembatan"] = {};
            layerUtilitas["jembatan"]["jembatan"] = layer;
          }
          // 5.Trotoar
          if (utilitasData.trotoar) {
            let layer = L.geoJSON(utilitasData.trotoar, {
              style: f => f.style,
              pointToLayer: (feature, latlng) => {
                if (feature.style && feature.style.marker) {
                  return L.marker(latlng, {
                    icon: L.icon({
                      iconUrl: feature.style.marker,
                      iconSize: [8, 8]
                    })
                  });
                }
                return L.circleMarker(latlng, {
                  radius: 5,
                  color: feature.style?.color || "#444"
                });
              },
              onEachFeature: (feature, layer) => {
                let props = feature.properties;
                layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Trotoar</b></td><td>:${props.id_trotoar || "-"}</td></tr>
              <tr><td><b>Nama Jalan</b></td><td>:${props.nama_jalan || "-"}</td></tr>
              <tr><td><b>Kemantren</b></td><td>:${props.kemantren || "-"}</td></tr>
              <tr><td><b>Kelurahan</b></td><td>:${props.kelurahan || "-"}</td></tr>
              <td><b>Lebar Trotoar</b></td>
  <td>: ${props.lebar_tro ? props.lebar_tro + " m" : "-"}</td>
</tr>
            </table>
          `);
              }
            });
            if (!layerUtilitas["trotoar"]) layerUtilitas["trotoar"] = {};
            layerUtilitas["trotoar"]["trotoar"] = layer;
          }
          // JARINGAN LISTRIK
          // 1.Tegangan Menengah
          if (utilitasData.tegangan_menengah) {
            let layer = L.geoJSON(utilitasData.tegangan_menengah, {
              style: f => f.style,
              pointToLayer: (feature, latlng) => {
                if (feature.style && feature.style.marker) {
                  return L.marker(latlng, {
                    icon: L.icon({
                      iconUrl: feature.style.marker,
                      iconSize: [10, 10]
                    })
                  });
                }
                return L.circleMarker(latlng, {
                  radius: 5,
                  color: feature.style?.color || "#444"
                });
              },
              onEachFeature: (feature, layer) => {
                let props = feature.properties;
                layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID</b></td><td>:${props.id_tm || "-"}</td></tr>
              <tr><td><b>Klasifikasi</b></td><td>:${props.klasifikas || "-"}</td></tr>
              <tr><td><b>Nama Gardu Induk</b></td><td>:${props.nama_gi || "-"}</td></tr>
              <tr><td><b>Posisi Fasad</b></td><td>:${props.posisi_fas || "-"}</td></tr>
              <tr><td><b>Ukuran Kawat</b></td><td>:${props.ukuran_kaw || "-"}</td></tr>
            </table>
          `);
              }
            });
            if (!layerUtilitas["tegangan_menengah"]) layerUtilitas["tegangan_menengah"] = {};
            layerUtilitas["tegangan_menengah"]["tegangan_menengah"] = layer;
          }
          // 2.Tegangan Rendah
          if (utilitasData.tegangan_rendah) {
            let layer = L.geoJSON(utilitasData.tegangan_rendah, {
              style: f => f.style,
              pointToLayer: (feature, latlng) => {
                if (feature.style && feature.style.marker) {
                  return L.marker(latlng, {
                    icon: L.icon({
                      iconUrl: feature.style.marker,
                      iconSize: [10, 10]
                    })
                  });
                }
                return L.circleMarker(latlng, {
                  radius: 5,
                  color: feature.style?.color || "#444"
                });
              },
              onEachFeature: (feature, layer) => {
                let props = feature.properties;
                layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID</b></td><td>:${props.id_tr || "-"}</td></tr>
              <tr><td><b>Klasifikasi</b></td><td>:${props.klasifikas || "-"}</td></tr>
              <tr><td><b>Deskripsi</b></td><td>:${props.deskripsi || "-"}</td></tr>
              <tr><td><b>Nama Gardu Induk</b></td><td>:${props.nama_gi || "-"}</td></tr>
              <tr><td><b>Posisi Fasad</b></td><td>:${props.posisi_fas || "-"}</td></tr>
              <tr><td><b>Bahan Kawat</b></td><td>:${props.bahan_kawa || "-"}</td></tr>
              <tr><td><b>Ukuran Kawat</b></td><td>:${props.ukuran_kaw || "-"}</td></tr>
            </table>
          `);
              }
            });
            if (!layerUtilitas["tegangan_rendah"]) layerUtilitas["tegangan_rendah"] = {};
            layerUtilitas["tegangan_rendah"]["tegangan_rendah"] = layer;
          }
          // 3.Rumah Kabel
          if (utilitasData.rumah_kabel) {
            let layer = L.geoJSON(utilitasData.rumah_kabel, {
              style: f => f.style,
              pointToLayer: (feature, latlng) => {
                if (feature.style && feature.style.marker) {
                  return L.marker(latlng, {
                    icon: L.icon({
                      iconUrl: feature.style.marker,
                      iconSize: [25, 25]
                    })
                  });
                }
                return L.circleMarker(latlng, {
                  radius: 5,
                  color: feature.style?.color || "#444"
                });
              },
              onEachFeature: (feature, layer) => {
                let props = feature.properties;
                layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Rumah Kabel</b></td><td>:${props.id || "-"}</td></tr>
            </table>
          `);
              }
            });
            if (!layerUtilitas["rumah_kabel"]) layerUtilitas["rumah_kabel"] = {};
            layerUtilitas["rumah_kabel"]["rumah_kabel"] = layer;
          }
          // 4.Tiang Listrik
          if (utilitasData.tiang_listrik) {
            let layer = L.geoJSON(utilitasData.tiang_listrik, {
              style: f => f.style,
              pointToLayer: (feature, latlng) => {
                if (feature.style && feature.style.marker) {
                  return L.marker(latlng, {
                    icon: L.icon({
                      iconUrl: feature.style.marker,
                      iconSize: [25, 25]
                    })
                  });
                }
                return L.circleMarker(latlng, {
                  radius: 5,
                  color: feature.style?.color || "#444"
                });
              },
              onEachFeature: (feature, layer) => {
                let props = feature.properties;
                layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Tiang Listrik</b></td><td>:${props.id || "-"}</td></tr>
            </table>
          `);
              }
            });
            if (!layerUtilitas["tiang_listrik"]) layerUtilitas["tiang_listrik"] = {};
            layerUtilitas["tiang_listrik"]["tiang_listrik"] = layer;
          }
          // 4.Trafo Listrik
          if (utilitasData.trafo_listrik) {
            let layer = L.geoJSON(utilitasData.trafo_listrik, {
              style: f => f.style,
              pointToLayer: (feature, latlng) => {
                if (feature.style && feature.style.marker) {
                  return L.marker(latlng, {
                    icon: L.icon({
                      iconUrl: feature.style.marker,
                      iconSize: [18, 18]
                    })
                  });
                }
                return L.circleMarker(latlng, {
                  radius: 5,
                  color: feature.style?.color || "#444"
                });
              },
              onEachFeature: (feature, layer) => {
                let props = feature.properties;
                layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID Trafo Listrik</b></td><td>:${props.id || "-"}</td></tr>
            </table>
          `);
              }
            });
            if (!layerUtilitas["trafo_listrik"]) layerUtilitas["trafo_listrik"] = {};
            layerUtilitas["trafo_listrik"]["trafo_listrik"] = layer;
          }
          // JARINGAN PDAM
          // 1.Jaringan PDAM 
          if (utilitasData.jaringan_pdam) {
            let layer = L.geoJSON(utilitasData.jaringan_pdam, {
              style: f => f.style,
              pointToLayer: (feature, latlng) => {
                if (feature.style && feature.style.marker) {
                  return L.marker(latlng, {
                    icon: L.icon({
                      iconUrl: feature.style.marker,
                      iconSize: [18, 18]
                    })
                  });
                }
                return L.circleMarker(latlng, {
                  radius: 5,
                  color: feature.style?.color || "#444"
                });
              },
              onEachFeature: (feature, layer) => {
                let props = feature.properties;
                layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID</b></td><td>:${props.id_pdam || "-"}</td></tr>
              <tr><td><b>Jenis</b></td><td>:${props.jenis || "-"}</td></tr>
             <tr><td><b>Diameter</b></td><td>:${props.diameter || "-"}</td></tr>
            </table>
          `);
              }
            });
            if (!layerUtilitas["jaringan_pdam"]) layerUtilitas["jaringan_pdam"] = {};
            layerUtilitas["jaringan_pdam"]["jaringan_pdam"] = layer;
          }
          // Sungai
          // 1.Sungai
          if (utilitasData.sungai) {
            let layer = L.geoJSON(utilitasData.sungai, {
              style: f => f.style,
              pointToLayer: (feature, latlng) => {
                if (feature.style && feature.style.marker) {
                  return L.marker(latlng, {
                    icon: L.icon({
                      iconUrl: feature.style.marker,
                      iconSize: [18, 18]
                    })
                  });
                }
                return L.circleMarker(latlng, {
                  radius: 5,
                  color: feature.style?.color || "#444"
                });
              },
              onEachFeature: (feature, layer) => {
                let props = feature.properties;
                layer.bindPopup(`
            <table style ="border-collapse: collapse; width: 100%;">
              <tr><td><b>Kelas</b></td><td>:${props.kelas || "-"}</td></tr>
              <tr><td><b>Subkelas</b></td><td>:${props.subkelas || "-"}</td></tr>
              <tr><td><b>ID</b></td><td>:${props.id || "-"}</td></tr>
              <tr><td><b>Nama Sungai</b></td><td>:${props.nama || "-"}</td></tr>
              <td><b>Panjang</b></td>
  <td>: ${props.panjang ? props.panjang + " m" : "-"}</td>
</tr>
            </table>
          `);
              }
            });
            if (!layerUtilitas["sungai"]) layerUtilitas["sungai"] = {};
            layerUtilitas["sungai"]["sungai"] = layer;
          }
        }
        document.getElementById("loadingOverlay").style.display = "none";

        document.querySelectorAll("#layerTree input[type=checkbox]").forEach(cb => {
          cb.disabled = false;
          cb.addEventListener("change", () => {
            const sub = cb.dataset.layer;
            for (const cls in layerUtilitas) {
              if (layerUtilitas[cls][sub]) {
                if (cb.checked) {
                  map.addLayer(layerUtilitas[cls][sub]);
                } else {
                  map.removeLayer(layerUtilitas[cls][sub]);
                }
              }
            }
            updateLegend();
          });
        });
        document.getElementById("loadingOverlay").style.display = "none";
      })
      .catch(err => {
        console.error("Gagal ambil data utilitas:", err);
        document.querySelector("#loadingOverlay h2").innerText = "Gagal memuat data!";
        document.querySelector("#loadingOverlay p").innerText = "Silakan refresh halaman.";
      });
  </script>
</body>

</html>