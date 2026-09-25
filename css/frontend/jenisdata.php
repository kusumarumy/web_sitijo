<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jenis Data</title>
  <link href="../css/style.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: #f5f7fa;
      color: #333;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .main {
      margin: 80px 30px;
      height: calc(100vh - 100px);
      overflow-y: auto;
    }

    .cards-wrapper {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 1.5rem;
      padding: 1rem;
    }

    .card {
      background: #ffffff;
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s, box-shadow 0.3s;
      text-align: center;
      cursor: pointer;
      flex: 1 1 280px;
      max-width: 320px;
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .card img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 50%;
      margin-bottom: 1rem;
      transition: transform 0.3s;
    }

    .card p {
      font-size: 1.1rem;
      font-weight: 600;
      margin: 0;
      color: #444;
      text-transform: uppercase;
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }

    .card:hover img {
      transform: scale(1.05);
    }

    #infoModal .modal-dialog {
      width: 60vw;
      max-width: 800px;
      max-height: 85vh;
    }

    #infoModal .modal-content {
      border-radius: 12px;
      overflow-y: auto;
      max-height: 70vh;
    }

    #infoModal .modal-body {
      display: flex !important;
      flex-direction: row !important;
      align-items: flex-start;
      justify-content: space-between;
      gap: 0.5rem;
    }

    #infoModal .modal-left {
      flex: 0 0 35%;
      max-width: 35%;
      min-width: 200px;
      text-align: left;
      padding-right: 1rem;
    }

    #infoModal .modal-left h6 {
      font-family: 'Times New Roman', Times, serif;
      font-weight: bold;
      font-size: 18px;
      color: black;
    }

    #infoModal .modal-left table {
      font-family: 'Times New Roman', Times, serif;
      font-size: 15px;
      color: black;
      width: 95%;
      margin: auto;
    }

    #infoModal .modal-left p {
      margin-top: 0.5rem;
      margin-bottom: 1.5rem;
      color: #000000ff;
      font-family: 'Times New Roman', Times, serif;
      text-align: justify;
    }

    #infoModal .modal-right {
      flex: 0 0 65%;
      max-width: 65%;
      min-width: 300px;
      text-align: left;
      padding-left: 1rem;
    }

    #infoModal .modal-right h6 {
      font-family: 'Times New Roman', Times, serif;
      font-weight: bold;
      font-size: 17px;
      color: black;
    }

    #infoModal .modal-title h6 {
      font-size: 15px;
    }

    #infoModal .modal-right p {
      text-align: justify;
      font-size: 15px;
      font-family: 'Times New Roman', Times, serif;
      color: black;
      margin-right: 30px"
    }

    #infoModal .modal-left ol#modalSubkelas,
    #infoModal .modal-left ol#modalSubkelas li {
      font-family: "Times New Roman", Times, serif;
      text-align: justify;
      color: black;
    }

    #modalChart {
      max-width: 400px;
      max-height: 350px;
      width: 100%;
      height: auto;
      margin: 0 auto;
      display: block;
    }

    #modalSubkelas {
      padding-left: 1.2em;
      color: #555;
    }

    @media (max-width: 1200px) {
      .main {
        margin: 70px 20px;
      }

      .card img {
        width: 100px;
        height: 100px;
      }

      .card p {
        font-size: 1rem;
      }

      #infoModal .modal-dialog {
        width: 80vw;
      }
    }

    @media (max-width: 1024px) {
      .main {
        margin: 70px 20px;
      }

      .cards-wrapper {
        gap: 1rem;
      }

      .card {
        flex: 1 1 45%;
        max-width: 45%;
        padding: 1rem;
      }

      .card img {
        width: 90px;
        height: 90px;
      }

      .card p {
        font-size: 0.95rem;
      }

      #infoModal .modal-body {
        flex-direction: column;
      }

      #infoModal .modal-left,
      #infoModal .modal-right {
        max-width: 100%;
        flex: 1 1 100%;
        padding: 0.5rem;
      }

      #infoModal .modal-dialog {
        width: 75vw;
        max-width: 700px;
      }
    }

    @media (max-width: 768px) {
      .main {
        margin: 60px 15px;
      }

      .cards-wrapper {
        gap: 1rem;
      }

      .card {
        flex: 1 1 100%;
        max-width: 100%;
      }

      .card img {
        width: 80px;
        height: 80px;
      }

      .card p {
        font-size: 0.9rem;
      }

      #infoModal .modal-dialog {
        width: 90vw;
        max-width: 600px;
      }

      #infoModal .modal-body {
        flex-direction: column;
      }
    }

    @media (max-width: 480px) {
      body {
        font-size: 14px;
      }

      .main {
        margin: 50px 10px;
      }

      .cards-wrapper {
        flex-direction: column;
        align-items: center;
      }

      .card {
        width: 100%;
        max-width: 100%;
        padding: 1rem;
      }

      .card img {
        width: 70px;
        height: 70px;
      }

      .card p {
        font-size: 0.85rem;
      }

      #infoModal .modal-dialog {
        width: 95vw;
        max-width: 95vw;
      }

      #infoModal .modal-content {
        width: 95vw;
        max-width: 95vw;
        font-size: 12px;
      }

      #infoModal .modal-body {
        padding: 0.5rem;
        flex-direction: column;
      }

      #infoModal .modal-body h6 {
        font-size: 12px;
      }

      #infoModal .modal-left,
      #infoModal .modal-right {
        flex: 1 1 100%;
        max-width: 100%;
        font-size: 12px;
      }

      #infoModal .modal-right h6,
      #infoModal .modal-right p {
        font-size: 12px;
        line-height: 1.4;
      }

      #infoModal .modal-left table {
        font-size: 12px
      }

      #modalChart {
        max-width: 100%;
        height: auto;
      }
    }
  </style>
</head>

<body>
  <?php include "../partials/sidebar.php"; ?>
  <?php include "../partials/header.php"; ?>
  <div class="main">
    <div class="cards-wrapper" id="gridContainer">
    </div>
  </div>
  <div class="modal fade" id="infoModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-bottom-0">
          <h5 class="modal-title">
            <strong id="modalTitle">KELAS</strong>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="modal-left">
            <h6>A. Deskripsi</h6>
            <p id="modalDesc"></p>
            <h6>B. Subkelas</h6>
            <ol id="modalSubkelas"></ol>
            <h6>
              C. Ketersediaan Data
            </h6>
            <table class="table table-sm table-bordered align-middle" id="progressTable">
              <thead class="table-secondary">
                <tr>
                  <th style="width:10%; text-align:center;">No.</th>
                  <th style="width:60%; text-align:center;">Subkelas</th>
                  <th style="width:30%; text-align:center;">Jumlah Data</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
          <div class="modal-right">
            <h6>
              D. Statistika Progres Ketersediaan Data
            </h6>
            <p>
              Statistika data disusun berdasarkan jumlah data yang telah diperoleh dan diolah sesuai dengan klasifikasi kelas dan subkelas.
            </p>
            <canvas id="modalChart" width="300" height="300" style="margin-top:1rem;"></canvas>
            <div class="chart-legend-wrapper">
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      function generateSoftColors(n) {
        const baseColors = ['#FFB3BA', '#fffcbaff', '#BAFFC9', '#bad7ffff', '#f1baffff', '#efccecff', '#D1C4E9', '#B2EBF2', '#F8BBD0', '#DCEDC8'];
        const colors = [];
        for (let i = 0; i < n; i++) {
          colors.push(baseColors[i % baseColors.length]);
        }
        return colors;
      }
      const dataManual = {
        "Atribut Jalan": {
          icon: "../assets/atribut_jalan.jpg",
          desc: "Atribut Jalan merupakan fasilitas penunjang di sepanjang area jalan yang mencakup cermin jalan, kamera pengawas, lampu jalan, lampu lalu lintas, dan rambu lalu lintas.",
          subkelas: ["Cermin Jalan", "Kamera Pengawas", "Lampu Jalan", "Lampu Lalu Lintas", "Rambu Lalu Lintas"],
          chartData: [8.7, 8.7, 100, 26.09, 26.09],
          jumlahData: ["25 Fitur", "29 Fitur", "9290 Fitur", "517 Fitur", "1767 Fitur"],
        },
        "Bangunan": {
          icon: "../assets/bangunan.jpg",
          desc: "Berdasarkan Peraturan Pemerintah Republik Indonesia Nomor 16 Tahun 2021 tentang Peraturan Pelaksanaan Undang-Undang Nomor 28 Tahun 2002 tentang Bangunan Gedung, bahwa bangunan gedung merupakan wujud fisik hasil pekerjaan konstruksi yang menyatu dengan tempat kedudukannya, sebagian atau seluruhnya berada di atas dan/atau di dalam tanah dan/atau di air, yang berfungsi sebagai tempat manusia melakukan kegiatannya, baik untuk hunian atau tempat tinggal, kegiatan keagamaan, kegiatan usaha, kegiatan sosial dan budaya, maupun kegiatan khusus.",
          subkelas: ["Bangunan", "Rusunawa"],
          chartData: [33.33, 0],
          jumlahData: ["Fitur Bangunan Tahap 1", "0"],
        },
        "Halte": {
          icon: "../assets/halte.jpg",
          desc: "Berdasarkan Peraturan Menteri Perhubungan Republik Indonesia Nomor PM 15 Tahun 2019 tentang Penyelenggaraan Angkutan Orang dengan Kendaraan Bermotor Umum dalam Trayek, bahwa halte adalah tempat persinggahan atau rambu pemberhentian angkatan umum yang dilalui oleh setiap trayek yang melayani angkutan secara terus menerus serta berhenti pada tempat untuk menaikkan dan menurunkan penumpang yang telah ditetapkan untuk angkutan perkotaan.",
          subkelas: ["Titik Halte", "Jalur Halte Trans"],
          chartData: [100, 100],
          jumlahData: ["180 Fitur", "280 km"],
        },
        "Infrastruktur Pendukung": {
          icon: "../assets/utilitas_pendukung.jpg",
          desc: "Infrastruktur Pendukung merupakan fasilitas penunjang infrastruktur yang mencakup hidran, rantai pasok, reklame, dan titik bench mark.",
          subkelas: ["Hidran", "Rantai Pasok", "Reklame", "Titik Bench Mark"],
          chartData: [8.70, 100, 26.09, 26.09],
          jumlahData: ["6 Fitur", "183 Fitur", "294 Fitur", "70 Fitur"],
        },
        "Jaringan Drainase": {
          icon: "../assets/jaringan_drainase.png",
          desc: "Berdasarkan Peraturan Menteri Pekerjaan Umum dan Perumahan Rakyat Republik Indonesia Nomor 12/PRT/M/2014 tentang Penyelenggaraan Sistem Drainase Perkotaan, bahwa sistem drainase perkotaan adalah satu kesatuan sistem teknis dan non teknis dari prasarana dan sarana drainase perkotaan. Prasarana drainase adalah lengkungan atau saluran air di permukaan atau di bawah tanah, baik yang terbentuk secara alami maupun dibuat oleh manusia, yang berfungsi menyalurka kelebihan air dari suatu kawasan ke badan air penerima. Sedangkan sarana drainase adalah banguna pelengkap yang merupakan bangunan yang ikut mengatur dan mengena=dalikan sistem aliran air hujan agar aman dan mudah melewati jalan, belokan daerah curam seperti gorong-gorong, pertemuan saluran, bangunan terjunan, jembatan, tali-tali air, pompa dan pintu air.",
          subkelas: ["Inlet", "Manhole Drainase", "Sumur Resapan", "Zona Drainase"],
          chartData: [26.09, 26.09, 100, 100],
          jumlahData: ["7865 Fitur", "3363 Fitur", "2443 Fitur", "387 km"],
        },
        "Jaringan Fiber Optik": {
          icon: "../assets/jaringan_fo.jpg",
          desc: "Jaringan Fiber Optik merupakan teknologi transmisi data yang menggunakan bahan serat kaca atau plastik untuk menstransmisikan sinyal cahaya dari satu lokasi ke lokasi lainnya yang mencakup kabel fiber optik, manhole fiber optik, dan tiang fiber optik.",
          subkelas: ["Kabel Fiber Optik", "Manhole Fiber Optik", "Tiang Fiber Optik"],
          chartData: [26.09, 26.09, 26.09],
          jumlahData: ["324 km", "341 Fitur", "1960 Fitur"],
        },
        "Jaringan Instalasi Pengolahan Air Limbah": {
          icon: "../assets/jaringan_ipal.jpg",
          desc: "Berdasarkan Peraturan Menteri Pekerjaan Umum dan Perumahan Rakyat Republik Indonesia Nomor 4/PRT/M/2017 tentang Penyelenggaraan Sistem Pengelolaan Air Limbah Domestik (SPALD), bahwa sistem pengelolaan air limbah domestik adalah serangkaian kegiatan pengelolaan air limbah domestik dalam satu kesatuan dengan prasarana dan sarana pengelolaan air limbah domestik.",
          subkelas: ["Manhole Saluran Air Limbah", "Pipa Induk", "Pipa Glontor", "Pipa Lateral"],
          chartData: [26.09, 100, 100, 100],
          jumlahData: ["1511 Fitur", "58 km", "115 km", "22 km"],
        },
        "Jaringan Jalan": {
          icon: "../assets/jaringan_jalan.jpg",
          desc: "Berdasarkan Undang-Undang Republik Indonesia Nomor 38 Tahun 2004 tentang Jalan, bahwa jalan adalah prasarana trasnportasi darat yang meliputi segala bagian jalan, termasuk bangunan pelengkap dan perlengkapannya yang diperuntukkan bagi lalu lintas, yang berada pada permukaan tanah, di atas permukaan tanah, di bawah permukaan tanah dan/atau air, serta di atas permukaan air, kecuali jalan kereta api, jalan lori, dan jalan kabel.",
          subkelas: ["Jembatan", "Jalan Lingkungan", "Jalan Kota", "Jalur Pemandu", "Trotoar"],
          chartData: [100, 100, 100, 26.09, 100],
          jumlahData: ["54 Fitur", "594 km", "230 km", 0, 0],
        },
        "Jaringan Listrik": {
          icon: "../assets/jaringan_listrik.jpg",
          desc: "Berdasarkan Peraturan Menteri Energi dan Sumber Daya Mineral Republik Indonesia Nomor 20 Tahun 2020 Tentang Aturan Jaringan Sistem Tenaga Listrik (Grid Code), bahwa sistem tenaga listrik adalah suatu ranglaian dalam tenaga listrik yang berfungsi untuk menyalurkan tenaga listrik dari pembangkit tenaga listrik ke konsumen tenaga listrik.",
          subkelas: ["Jaringan Kabel Listrik Tegangan Menengah", "Jaringan Kabel Listrik Tegangan Rendah", "Rumah Kabel", "Tiang Listrik", "Trafo Listrik"],
          chartData: [100, 100, 8.70, 26.09, 8.70],
          jumlahData: ["190 km", "232 km", "115 Fitur", "2771 Fitur", "310 Fitur"],
        },
        "Jaringan Perusahaan Daerah Air Minum": {
          icon: "../assets/jaringan_pdam.jpg",
          desc: "Berdasarkan Peraturan Menteri Pekerjaan Umum dan Perumahan Rakyat Republik Indonesia Nomor 4 Tahun 2020 tentang Prosedur Operasional Standar Penyelenggaraan Sistem Penyediaan Air Minum, bahwa sistem penyediaan air minum merupakan satu kesatuan sarana dan prasarana penyediaan air minum yang mengikuti proses dasar manajemen untuk penyediaan air minum kepada masyarakat.",
          subkelas: ["Jaringan Perusahaan Daerah Air Minum"],
          chartData: [100],
          jumlahData: ["699 km"],
        },
        "Sungai": {
          icon: "../assets/sungai.jpg",
          desc: "Berdasarkan Peraturan Pemerintah Republik Indonesia Nomor 38 Tahun 2011 tentang Sungai, bahwa sungai adalah alur atau wadah air alami dan/atau buatan berupa jaringan pengaliran air beserta air di dalamnya, mulai dari hulu sampai muara, dengan dibatasi kanan dan kiri oleh garis sempadan.",
          subkelas: ["Sungai"],
          chartData: [100],
          jumlahData: ["49 km"],
        },
      };
      const grid = document.getElementById("gridContainer");
      Object.entries(dataManual).forEach(([kelas, meta]) => {
        const card = document.createElement("div");
        card.className = "card";
        card.innerHTML = `
        <img src="${meta.icon}" alt="${kelas}">
        <p>${kelas}</p>
      `;
        card.onclick = () => {
          document.getElementById("modalTitle").textContent = kelas;
          document.getElementById("modalDesc").textContent = meta.desc;
          const ol = document.getElementById("modalSubkelas");
          ol.innerHTML = "";
          meta.subkelas.forEach(sub => {
            const li = document.createElement("li");
            li.textContent = sub;
            ol.appendChild(li);
          });
          if (window.modalChartInstance) {
            window.modalChartInstance.destroy();
          }
          const modalEl = document.getElementById("infoModal");
          const modal = new bootstrap.Modal(modalEl);
          modal.show();
          modalEl.addEventListener('shown.bs.modal', function handler() {
            const ctx = document.getElementById("modalChart").getContext("2d");
            window.modalChartInstance = new Chart(ctx, {
              type: "bar",
              data: {
                labels: meta.subkelas,
                datasets: [{
                  label: "Jumlah (%)",
                  data: meta.chartData,
                  backgroundColor: generateSoftColors(meta.subkelas.length),
                  borderColor: '#003366',
                  borderWidth: 1.2
                }]
              },
              options: {
                indexAxis: 'y',
                responsive: true,
                animation: {
                  duration: 800
                },
                plugins: {
                  legend: {
                    display: false
                  },
                  tooltip: {
                    callbacks: {
                      label: function(context) {
                        const value = context.raw ?? 0;
                        return `${context.label}: ${value}`;
                      }
                    }
                  },
                  title: {
                    display: true,
                    text: 'Progres Ketersediaan Data Setiap Subkelas ',
                    color: '#000',
                    font: {
                      size: 12,
                      weight: 'bold'
                    }
                  }
                },
                scales: {
                  x: {
                    beginAtZero: true,
                    min: 0,
                    max: 100,
                    ticks: {
                      color: '#333',
                      font: {
                        size: 11
                      },
                      callback: value => value + '%'
                    },
                    title: {
                      display: true,
                      text: 'Persentase Data (%)',
                      color: '#000',
                      font: {
                        size: 12,
                        weight: 'bold'
                      }
                    },
                    grid: {
                      color: '#e5e5e5'
                    }
                  },
                  y: {
                    labels: meta.subkelas,
                    ticks: {
                      color: '#000',
                      font: {
                        size: 12,
                        weight: '500'
                      }
                    },
                    grid: {
                      display: false
                    }
                  }
                }

              }
            });
            modalEl.removeEventListener('shown.bs.modal', handler);
          });
          const tbody = document.querySelector("#progressTable tbody");
          tbody.innerHTML = "";
          meta.subkelas.forEach((sub, i) => {
            const row = document.createElement("tr");
            row.innerHTML = `
    <td style="text-align:center;">${i + 1}</td>
    <td>${sub}</td>
    <td style="text-align:center;">${meta.jumlahData?.[i] ?? "-"}</td>
  `;
            tbody.appendChild(row);
          });
        };
        grid.appendChild(card);
      });
    </script>
</body>

</html>