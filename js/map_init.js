const map = L.map('map', {
      zoomControl: false,
      minZoom: 10,
      maxZoom: 21
    }).setView([-7.7972, 110.3688], 15);
    // batas tile yang ada
    const z = 20;
    const xMin = 845679,
      xMax = 845732;
    const yMin = 546974,
      yMax = 547162;
    // sudut kiri bawah & kanan atas
    const southWest = [tile2lat(yMax + 1, z), tile2lon(xMin, z)];
    const northEast = [tile2lat(yMin, z), tile2lon(xMax + 1, z)];
    const orthoBounds = [southWest, northEast];
    // basemap
    const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    const esri = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
      attribution: 'Tiles © Esri'
    });
    const ortho = L.tileLayer('http://localhost/web_pupr/ortho/20/{x}/{y}.png', {
      minZoom: 20,
      maxZoom: 20,
      attribution: '© Orthophoto',
      errorTileUrl: 'blank.png',
      bounds: orthoBounds,
      noWrap: true
    });
    const carto = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
      attribution: '© CartoDB'
    });
    // DEBUG: tampilkan semua request tile di console
    ortho.on('tileloadstart', function(e) {
      console.log("LOAD TILE:", e.tile.src);
    });
    ortho.on('tileerror', function(e) {
      console.warn("TILE ERROR:", e.tile.src);
    });
    ortho.on('tileload', function(e) {
      console.log("TILE OK:", e.tile.src);
    });
    // Pasang ke peta
    ortho.addTo(map);
    const baseLayers = {
      "Open Street Map": osm,
      "ESRI Satellite": esri,
      "Orthophoto": ortho,
      "Carto Light": carto
    };
    const layersControl = L.control.layers(baseLayers, null, {
      position: 'topright',
      collapsed: true
    }).addTo(map);
    // auto zoom kalau pilih Orthophoto
    map.on('baselayerchange', function(e) {
      if (e.name === "Orthophoto") {
        map.fitBounds(orthoBounds);
      }
    });
    // thumbnail basemap
    map.whenReady(() => {
      const labels = document.querySelectorAll('.leaflet-control-layers-base label');
      const thumbnails = {
        "Open Street Map": "https://a.tile.openstreetmap.org/10/550/380.png",
        "ESRI Satellite": "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/10/380/550",
        "Orthophoto": "http://localhost/web_pupr/ortho/20/845708/547131.png", // FIXED path
        "Carto Light": "https://a.basemaps.cartocdn.com/light_all/10/550/380.png"
      };
      labels.forEach(label => {
        const text = label.textContent.trim();
        if (thumbnails[text]) {
          const img = document.createElement('img');
          img.src = thumbnails[text];
          img.style.width = "50px";
          img.style.marginRight = "6px";
          label.prepend(img);
        }
      });
    });