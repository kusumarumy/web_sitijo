// === Symbology RBI untuk utilitas ===
const symbology = {
  // --- Jaringan Listrik ---
  "Jaringan Kabel Listrik": { color: "orange", weight: 2 },
  "Tiang Listrik":          { marker: "tiang_listrik.png" },
  "Gardu Listrik":          { marker: "gardu.png" },
  "Trafo Listrik":          { marker: "trafo.png" },

  // --- Jaringan FO ---
  "Jaringan FO": { color: "blue", weight: 2 },
  "Tiang FO":    { marker: "tiang_fo.png" },
  "Manhole FO":  { marker: "manhole_fo.png" },

  // --- Jaringan IPAL ---
  "Jaringan IPAL": { color: "purple", weight: 2 },
  "Manhole IPAL":  { marker: "manhole_ipal.png" },

  // --- Drainase ---
  "Jaringan Drainase": { color: "cyan", weight: 2 },
  "Inlet Drainase":    { marker: "inlet.png" },
  "Titik Resapan":     { marker: "sumur_resapan.png" },
  "Zona Irigasi":      { color: "lightblue", weight: 2 },

  // --- Jalan ---
  "Jalan Kota":       { color: "red", weight: 3 },
  "Jalan Lingkungan": { color: "blue", weight: 2 },
  "Trotoar":          { color: "green", weight: 2 },
  "Jembatan":         { marker: "bridge.png" },

  // --- Transportasi ---
  "Halte":               { marker: "halte.png" },
  "Halte -> Jalur Trans":{ color: "brown", weight: 2 },

  // --- Fasilitas Publik ---
  "Lampu Jalan":    { marker: "lampu_jalan.png" },
  "Lampu Lalu Lintas": { marker: "lampu_lalin.png" },
  "Rambu Lalin":    { marker: "rambu.png" },
  "Reklame":        { marker: "reklame.png" },
  "Hidran":         { marker: "hidran.png" },
  "Kamera Pengawas":{ marker: "cctv.png" },
  "Papan Tanah Negara": { marker: "papan.png" },
  "Rumah Kabel":    { marker: "rumah_kabel.png" },
  "Cermin":         { marker: "cermin.png" },

  // --- PDAM ---
  "Jaringan PDAM": { color: "navy", weight: 2 },
  "Pipa Induk":    { color: "darkblue", weight: 3 },
  "Pipa Lateral":  { color: "skyblue", weight: 2 },
  "Pipa Glontor":  { color: "aqua", weight: 2 },

  // --- Sungai ---
  "Sungai": { color: "blue", weight: 2, dashArray: "5,5" }
};
