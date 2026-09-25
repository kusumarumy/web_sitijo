const legend = L.control({ position: 'bottomright' });
legend.onAdd = () => {
  const container = L.DomUtil.create('div', 'legend-container');
  container.innerHTML = `<div class="legend-header"><strong>Legenda</strong></div>
                         <div class="legend-content"></div>`;
  return container;
};
legend.addTo(map);

function updateLegend() {
  const legendContent = document.querySelector(".legend-content");
  legendContent.innerHTML = "";

  document.querySelectorAll("#layerTree input[type=checkbox]").forEach(cb => {
    if (cb.checked) {
      legendContent.innerHTML += `<div>✔ ${cb.parentNode.textContent.trim()}</div>`;
    }
  });
}
