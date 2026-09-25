<?php
session_start();

$R2_ASSET_URL = getenv('R2_ASSET_URL');

function asset_url($file)
{
    global $R2_ASSET_URL;

    if (!empty($R2_ASSET_URL)) {
        return rtrim($R2_ASSET_URL, '/') . '/assets/' . ltrim($file, '/');
    }

    return '/assets/' . ltrim($file, '/');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SITIJO</title>
  <link href="css/style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

  <style>
    html {
      scroll-behavior: smooth;
    }

    :root {
      --biru-pupr: #003366;
      --kuning-pupr: #f2b705;
      --abu-lembut: #f9fafc;
      --abu-teks: #555;
      --putih: #ffffff;
    }

    body {
      padding-left: env(safe-area-inset-left);
      padding-right: env(safe-area-inset-right);
    }

    html,
    body {
      margin: 0;
      padding: 0;
      overflow-x: hidden;
      overflow-y: auto;
      font-family: 'Roboto', sans-serif;
      background-color: var(--abu-lembut);
      color: var(--abu-teks);
    }

    .main {
      margin-left: 0;
      min-height: 100vh;
      margin-top: -3px;
    }

    section {
      padding-top: 60px;
      padding-bottom: 60px;
    }

    .carousel-item {
      min-height: 40vh;
      background-size: cover;
      background-position: center;
    }

    .carousel-item.fullheight {
      height: 100vh;
      background-size: cover;
      background-position: center;
    }

    .carousel-caption {
      bottom: 25%;
      text-align: left;
    }

    .carousel-caption h1 {
      font-size: clamp(24px, 4vw, 55px);
      font-weight: bold;
      font-family: 'Montserrat', sans-serif
    }

    .carousel-caption p {
      font-size: clamp(14px, 1.2vw, 18px);
      font-family: 'Times New Roman', Times, serif;
      max-width: 650px;
    }

    .btn-explore {
      background-color: var(--kuning-pupr);
      color: var(--biru-pupr);
      border-radius: 20px;
      font-weight: 700;
      font-size: 14px;
      padding: 10px 24px;
      transition: all 0.3s ease-in-out;
    }

    .btn-explore:hover {
      background-color: #ffd84d;
      transform: translateY(-3px);
    }

    #map {
      width: 100%;
      max-width: 1100px;
      height: 500px;
      border-radius: 10px;
      margin: 0 auto 30px auto;
      background: #ccc;
    }

    #contact {
      font-family: 'Roboto', sans-serif;
      font-size: 17px;
      line-height: 1.4;
      color: #fff;
    }

    #contact h3 {
      font-weight: 700;
      font-size: 1.4rem;
      margin-bottom: 1rem;
    }

    #contact p {
      margin-bottom: 4px;
    }

    #contact .fw-bold {
      font-weight: 500;
      margin-top: 6px;
      margin-bottom: 2px;
    }

    #contact .mb-2 {
      margin-bottom: 4px !important;
    }

    #contact .mb-4 {
      margin-bottom: 8px !important;
    }

    #contact a {
      color: #1f0270ff;
      transition: color 0.3s ease;
    }

    #contact a:hover {
      color: #4e8cff;
    }

    .contact-fade {
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.8s ease-out;
    }

    .contact-fade.visible {
      opacity: 1;
      transform: translateY(0);
    }

    #contact small {
      font-size: 0.8rem;
      opacity: 0.8;
    }

    .footer-highlight {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 12px 10px;
      background: rgba(78, 140, 255, 0.1);
      border-radius: 6px;
      margin-top: 1rem;
      text-align: center;
    }

    .footer-highlight small {
      color: #ffffff;
      font-size: 0.85rem;
      font-weight: 400;
      padding: 4px 12px;
      background: rgba(255, 255, 255, 0.08);
      border-radius: 4px;
      box-shadow: 0 0 8px rgba(78, 140, 255, 0.3);
    }

    .text-center a {
      text-decoration: none !important;
    }

    .animated-item {
      transition: all 0.3s ease;
      border-radius: 12px;
    }

    .animated-item:hover {
      background-color: #f8f9fa;
      transform: translateX(5px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .contact-info i:hover {
      animation: bounce 0.6s;
    }

    #organisasi .list-group-item {
      transition: all 0.3s ease;
      border: none;
      border-bottom: 1px solid #f1f1f1;
    }

    #organisasi .list-group-item:hover {
      background: linear-gradient(90deg, #f0f7ff, #ffffff);
      transform: translateX(5px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    #fiturCarousel .carousel-control-prev,
    #fiturCarousel .carousel-control-next {
      width: 5%;
    }

    #fiturCarousel .carousel-control-prev {
      left: -40px;
    }

    #fiturCarousel .carousel-control-next {
      right: -40px;
    }

    #fiturCarousel .carousel-control-prev-icon,
    #fiturCarousel .carousel-control-next-icon {
      background-size: 100%, 100%;
      width: 2rem;
      height: 2rem;
      filter: invert(1);
    }

    .card h6 {
      font-size: 13px;
    }

    #organisasi {
      background-color: #eaf5ffaa;
    }

    #organisasi .list-group-item {
      transition: all 0.3s ease;
      border: none;
      border-bottom: 1px solid #f1f1f1;
    }

    .kilas-box {
      background: #fff;
      border-radius: 40px;
      padding: 40px;
      max-width: 1000px;
      margin: 40px auto;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .kilas-box h2 {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .kilas-box p {
      color: #444;
      margin-bottom: 30px;
    }

    .data-items {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 40px;
      margin-top: 30px;
    }

    .item {
      text-align: center;
    }

    .item .icon {
      margin: 0 auto 15px auto;
      background: #f0f4ff;
      color: #0044cc;
      font-size: 30px;
      width: 70px;
      height: 70px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
      transition: transform 0.3s ease;
    }

    .item .icon:hover {
      transform: scale(1.15);
    }

    .item h3 {
      font-size: 24px;
      font-weight: bold;
      margin: 0;
    }

    .item p {
      font-size: 14px;
      color: #666;
      margin: 5px 0 0 0;
    }

    .counter {
      font-size: 1.8rem;
      font-weight: 700;
      color: #001f54;
    }

    .card {
      transition: all 0.3s ease;
      border-radius: 16px;
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
      transition: 0.25s ease;
      border-radius: 20px !important;
    }

    .card-hover:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
    }

    .fitur-card {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      padding: 20px 10px;
      transition: all 0.3s ease;
      position: relative;
      cursor: pointer;
    }

    .fitur-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }

    .fitur-icon {
      width: 80px;
      height: 80px;
      object-fit: contain;
      margin-bottom: 10px;
    }

    .fitur-card h6 {
      font-size: 0.8rem;
      color: #000;
      font-weight: 600;
    }

    .fitur-card::before {
      content: attr(data-tooltip);
      position: absolute;
      bottom: 110%;
      left: 50%;
      transform: translateX(-50%);
      background: #0d1b2a;
      color: #fff;
      padding: 10px 12px;
      border-radius: 10px;
      font-size: 0.75rem;
      line-height: 1.2;
      width: 220px;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.25s ease, transform 0.25s ease;
      z-index: 10;
    }

    .fitur-card::after {
      content: "";
      position: absolute;
      bottom: 100%;
      left: 50%;
      transform: translateX(-50%);
      border-width: 7px;
      border-style: solid;
      border-color: #0d1b2a transparent transparent transparent;
      opacity: 0;
      transition: opacity 0.25s ease;
    }

    .fitur-card:hover::before,
    .fitur-card:hover::after {
      opacity: 1;
      transform: translateX(-50%) translateY(-5px);
    }

    @keyframes switchWord {

      0%,
      25% {
        transform: translateY(0);
      }

      50%,
      75% {
        transform: translateY(-1em);
      }

      100% {
        transform: translateY(0);
      }
    }

    @keyframes bounce {

      0%,
      20%,
      50%,
      80%,
      100% {
        transform: translateY(0);
      }

      40% {
        transform: translateY(-10px);
      }

      60% {
        transform: translateY(-5px);
      }
    }

    @media (max-width: 992px) {
      .carousel-caption {
        bottom: 18%;
        text-align: center;
      }

      .carousel-caption p {
        font-size: 15px;
      }

      #map {
        height: 400px;
      }

      #organisasi .container {
        margin-left: auto;
        margin-right: auto;
        padding: 0 15px;
      }

    }

    @media (max-width: 768px) {
      section {
        padding-top: 40px;
        padding-bottom: 40px;
      }

      .carousel-item.fullheight {
        height: 80vh;
      }

      .carousel-caption {
        bottom: 10%;
        padding: 0 20px;
        text-align: center;
      }

      .carousel-caption h1 {
        font-size: 22px;
      }

      .carousel-caption p {
        font-size: 13px;
      }

      .carousel-item img,
      .carousel-item.fullheight {
        width: 100%;
        object-fit: cover;
      }

      .btn-explore {
        font-size: 12px;
        padding: 8px 16px;
      }

      #map {
        height: 300px;
      }

      #fiturCarousel .carousel-control-prev {
        left: -20px;
      }

      #fiturCarousel .carousel-control-next {
        right: -20px;
      }

      #organisasi .container {
        margin: 0 auto;
        max-width: 1200px;
        padding: 0 20px;
      }

      .kilas-box {
        border-radius: 20px;
        padding: 25px 15px;
      }

      .item {
        flex: 1 1 45%;
      }

      .btn-explore:focus-visible {
        outline: 3px solid #f2b70580;
        outline-offset: 3px;
      }

      .item .icon:hover {
        transform: scale(1.08);
        transition: transform 0.4s ease, box-shadow 0.4s ease;
      }
    }

    @media (max-width: 480px) {
      .carousel-caption h1 {
        font-size: 18px;
      }

      .carousel-caption p {
        font-size: 12px;
      }

      .kilas-box {
        padding: 20px 10px;
      }

      .item {
        flex: 1 1 100%;
      }
    }

    @media (max-width: 360px) {
      .carousel-caption h1 {
        font-size: 16px;
      }

      .carousel-caption p {
        font-size: 11px;
      }

      .btn-explore {
        font-size: 10px;
        padding: 6px 12px;
      }

      .kilas-box {
        padding: 15px 10px;
      }
    }
  </style>
</head>

<body>
  <?php
  include $_SERVER['DOCUMENT_ROOT'] . "/partials/header.php";
  include $_SERVER['DOCUMENT_ROOT'] . "/partials/sidebar.php";
  ?>
  <div class="main">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active fullheight" style="background-image:url('<?= asset_url('background.jpg') ?>');">
          <div class="carousel-caption text-start">
            <h1>Satu Data, Satu Peta Infrastruktur Kota Yogyakarta</h1>
            <p>Sistem Informasi Infrastruktur Kota Yogyakarta (SITIJO) merupakan platform digital internal Dinas Pekerjaan Umum, Perumahan dan Kawasan Permukiman Kota Yogyakarta yang menyajikan data spasial dan data atribut penunjang infrastruktur Kota Yogyakarta.</p>
            <?php if (isset($_SESSION['user'])): ?>
              <button onclick="window.location.href='/frontend/peta.php'" class="btn-explore">Explore Peta</button>
            <?php else: ?>
              <button onclick="window.location.href='/backend/auth/signin.php'" class="btn-explore">Explore Peta</button>
            <?php endif; ?>
          </div>
        </div>
        <div class="carousel-item fullheight" style="background-image:url('<?= asset_url('background.jpg') ?>');">
          <div class="carousel-caption">
            <h1>Kolaborasi dan Integrasi Data</h1>
            <p>Website based Geographic Information System (WEBGIS) SITIJO dapat digunakan sebagai sarana koordinasi antar Bidang dan UPT di lingkup DPUPKP Kota Yogyakarta untuk pengelolaan data infrastruktur.</p>
          </div>
        </div>
        <div class="carousel-item fullheight" style="background-image:url('<?= asset_url('background.jpg') ?>');">
          <div class="carousel-caption text-end">
            <h1>Akses Terbatas dan Terlindungi</h1>
            <p>Platform digital ini hanya dapat diakses oleh Bidang, UPT, dan beberapa pihak terkait di lingkup DPUPKP Kota Yogyakarta.</p>
          </div>
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>
    </div>
  </div>

  <section id="fitur" class="py-5" style="background-color: #fff;">
    <div class="container text-center">
      <h2 style="text-align:center; margin-bottom:2rem; font-family:'Montserrat', sans-serif; font-weight:700; color: #001f54">
        <strong>FITUR</strong> yang Tersedia pada <strong>#</strong>
        <span style="display:inline-block; position:relative; width:150px; text-align: left; height:1em; overflow:hidden; vertical-align:middle; line-height:1em;">
          <span style="position:absolute; animation:switchWord 4s infinite; font-weight:bold;">
            <span style="color: #213878; display:block; height:1em;">WEBGIS</span>
            <span style="color: #FFCC00; display:block; height:1em;">SITIJO</span>
          </span>
        </span>
      </h2>
      <div class="row justify-content-center g-4">
        <?php
        function link_or_signin($page)
        {
          return isset($_SESSION['user']) ? $page : 'signin.php';
        }
        ?>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
          <a href="<?= link_or_signin('index.php#organisasi') ?>">
            <div class="fitur-card" data-tooltip="Informasi profil dari Dinas Pekerjaan Umum, Perumahan, dan Kawasan Permukiman. Selain itu terdapat profil Bidang dan UPT terkait dengan WEBGIS SITIJO.">
              <img src="<?= asset_url('profil.png') ?>" alt="Profil" class="fitur-icon">
              <h6>Informasi Profil DPUPKP Kota Yogyakarta</h6>
            </div>
          </a>
        </div>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
          <a href="<?= link_or_signin('frontend/jenisdata.php') ?>">
            <div class="fitur-card" data-tooltip="Informasi jenis ketersediaan data infrastruktur yang tersedia pada WEBGIS SITIJO. ">
              <img src="<?= asset_url('data.png') ?>" alt="Jenis data" class="fitur-icon">
              <h6>Ketersediaan Data Infrastruktur</h6>
            </div>
          </a>
        </div>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
          <a href="<?= link_or_signin('frontend/peta.php') ?>">
            <div class="fitur-card" data-tooltip="Visualisasi data infrastruktur secara interaktif dilengkapi dengan komponen peta pendukung.">
              <img src="<?= asset_url('peta.png') ?>" alt="Peta" class="fitur-icon">
              <h6>Peta Infrastruktur</h6>
            </div>
          </a>
        </div>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
          <a href="<?= link_or_signin('backend/data/update_data.php') ?>">
            <div class="fitur-card" data-tooltip="Fitur update data infrastruktur memungkinkan pengguna dapat mengubah atribut dan menghapus data, yang dapat terintegrasi dengan database dan WEBGIS.">
              <img src="<?= asset_url('update.png') ?>" alt="Update" class="fitur-icon">
              <h6>Update Data Infrastruktur</h6>
            </div>
          </a>
        </div>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
          <a href="<?= link_or_signin('backend/data/tambah_data.php') ?>">
            <div class="fitur-card" data-tooltip="Fitur tambah data infrastruktur memungkinkan pengguna dapat menambah data geometri yang dapat dilengkapi dengan informasi non spasial (atribut data).">
              <img src="<?= asset_url('tambah.png') ?>" alt="Tambah" class="fitur-icon">
              <h6>Tambah Data Infrastruktur</h6>
            </div>
          </a>
        </div>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
          <a href="<?= link_or_signin('backend/data/download_data.php') ?>">
            <div class="fitur-card" data-tooltip="Fitur download data infrastruktur memungkinkan pengguna dapat mengunduh data geometri yang diekstrak dengan formast shapefile.">
              <img src="<?= asset_url('download.png') ?>" alt="Download" class="fitur-icon">
              <h6>Download Data Infrastruktur</h6>
            </div>
          </a>
        </div>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
          <a href="<?= link_or_signin('backend/data/riwayat_akses.php') ?>">
            <div class="fitur-card" data-tooltip="Riwayat akses pengguna memungkinkan administrator mengevaluasi dan mengidentifikasi akses pengguna terhadap WEBGIS SITIJO.">
              <img src="<?= asset_url('riwayat.png') ?>" alt="Riwayat Akses" class="fitur-icon">
              <h6>Riwayat Akses Pengguna</h6>
            </div>
          </a>
        </div>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
          <a href="<?= link_or_signin('frontend/panduan.php') ?>">
            <div class="fitur-card" data-tooltip="Panduan penggunaan WEBGIS SITIJO memungkinkan pengguna mengakses WEBGIS dengan 2 cara yaitu melalui booklet pengguna atau video tutorial.">
              <img src="<?= asset_url('panduan.png') ?>" alt="Panduan" class="fitur-icon">
              <h6>Panduan Penggunaan WEBGIS SITIJO</h6>
            </div>
          </a>
        </div>
      </div>
    </div>
  </section>

  <?php if (!isset($_SESSION['user'])): ?>
    <div class="modal fade" id="signinModal" tabindex="-1" aria-labelledby="signinModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header" style="background-color:#f2b705;">
            <h5 class="modal-title" id="signinModalLabel" style="color: #fff; font-weight: bold">
              <i class="bi bi-info-circle-fill"></i> Informasi
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body text-center">
            <p style="font-size: 18px;">Silahkan Anda <b>Sign In</b> untuk mengakses fitur ini.</p>
          </div>
          <div class="modal-footer justify-content-center">
            <a href="backend/auth/signin.php?redirect=/web_pupr/index.php" class="btn btn-primary">
              <i class="bi bi-box-arrow-in-right"></i> Sign In
            </a>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <section id="organisasi" class="py-5" style="background-color: #eaf5ffaa;">
    <div class="container px-3 px-md-5">
      <h2 class="mb-3" style="font-family:'Montserrat', sans-serif; font-weight:700; color:#001f54;">
        <span style="border-left:4px solid #213878; padding-left:10px;" </span>
          <strong>BIDANG TERKAIT</strong> Data Infrastruktur pada
          <strong>#</strong>
          <span style="display:inline-block; position:relative; width:145px; height:1em; overflow:hidden; vertical-align:middle; line-height:1em;">
            <span style="position:absolute; animation:switchWord 4s infinite; font-weight:bold;">
              <span style="color:#213878; display:block; height:1em;">WEBGIS</span>
              <span style="color:#FFCC00; display:block; height:1em;">SITIJO</span>
            </span>
          </span>
      </h2>
      <p class="text-muted mb-5" style="font-size:0.95rem;">
        Telusuri bidang-bidang yang berkaitan dengan penyajian data geospasial pada Sistem Informasi Infrastruktur Kota Yogyakarta (SITIJO)
      </p>
      <div class="text-end mt-4">
        <button id="toggleBidang" class="btn btn-outline-warning btn-sm px-4 rounded-pill" style="margin-top: -90px">Lihat Semua</button>
      </div>
      <div class="row g-4" id="bidangList" style="margin-top: -50px;">
        <div class="col-6 col-md-4 col-lg-3 bidang-item">
          <div class="card border-0 shadow-sm text-center p-3 h-100">
            <div class="mx-auto mb-3" style="width:100px; height:100px; border-radius:50%; overflow:hidden;">
              <img src="<?= asset_url('penataan_bangunan.png') ?>" alt="Bidang Penataan Bangunan" class="img-fluid">
            </div>
            <h6 class="fw-bold mb-2" style="color:#001f54;">Bidang Penataan Bangunan</h6>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3 bidang-item">
          <div class="card border-0 shadow-sm text-center p-3 h-100">
            <div class="mx-auto mb-3" style="width:100px; height:100px; border-radius:50%; overflow:hidden;">
              <img src="<?= asset_url('konstruksi.png') ?>" alt="Bidang Pengendalian Jasa Konstruksi" class="img-fluid">
            </div>
            <h6 class="fw-bold mb-2" style="color:#001f54;">Bidang Pengendalian & Pembinaan Jasa Konstruksi</h6>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3 bidang-item">
          <div class="card border-0 shadow-sm text-center p-3 h-100">
            <div class="mx-auto mb-3" style="width:100px; height:100px; border-radius:50%; overflow:hidden;">
              <img src="<?= asset_url('perumahan.png') ?>" alt="Bidang Perumahan dan Kawasan Permukiman" class="img-fluid">
            </div>
            <h6 class="fw-bold mb-2" style="color:#001f54;">Bidang Perumahan & Kawasan Permukiman</h6>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3 bidang-item">
          <div class="card border-0 shadow-sm text-center p-3 h-100">
            <div class="mx-auto mb-3" style="width:100px; height:100px; border-radius:50%; overflow:hidden;">
              <img src="<?= asset_url('jalan_jembatan.png') ?>" alt="Bidang Jalan dan Jembatan" class="img-fluid">
            </div>
            <h6 class="fw-bold mb-2" style="color:#001f54;">Bidang Jalan & Jembatan</h6>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3 bidang-item" style="display: none;">
          <div class="card border-0 shadow-sm text-center p-3 h-100">
            <div class="mx-auto mb-3" style="width:100px; height:100px; border-radius:50%; overflow:hidden;">
              <img src="<?= asset_url('drainase.png') ?>" alt="Bidang SDA dan Drainase" class="img-fluid">
            </div>
            <h6 class="fw-bold mb-2" style="color:#001f54;">Bidang Sumber Daya Air & Drainase</h6>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3 bidang-item" style="display: none;">
          <div class="card border-0 shadow-sm text-center p-3 h-100">
            <div class="mx-auto mb-3" style="width:100px; height:100px; border-radius:50%; overflow:hidden;">
              <img src="<?= asset_url('pal.png') ?>" alt="UPT Pengelolaan Air Limbah" class="img-fluid">
            </div>
            <h6 class="fw-bold mb-2" style="color:#001f54;">UPT Pengelolaan Air Limbah</h6>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3 bidang-item" style="display: none;">
          <div class="card border-0 shadow-sm text-center p-3 h-100">
            <div class="mx-auto mb-3" style="width:100px; height:100px; border-radius:50%; overflow:hidden;">
              <img src="<?= asset_url('rusunawa.png') ?>" alt="UPT Pengelolaan Rusunawa" class="img-fluid">
            </div>
            <h6 class="fw-bold mb-2" style="color:#001f54;">UPT Pengelolaan Rusunawa</h6>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3 bidang-item" style="display: none;">
          <div class="card border-0 shadow-sm text-center p-3 h-100">
            <div class="mx-auto mb-3" style="width:100px; height:100px; border-radius:50%; overflow:hidden;">
              <img src="<?= asset_url('pju.png') ?>" alt="UPT Penerangan Jalan Umum" class="img-fluid">
            </div>
            <h6 class="fw-bold mb-2" style="color:#001f54;">UPT Penerangan Jalan Umum</h6>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="fitur" class="py-5" style="background-color: #fff;">
  <div class="container text-center">
    <h2 style="text-align:center; margin-bottom:2rem; font-family:'Montserrat', sans-serif; font-weight:700; color: #001f54">
      <strong>WEBSITE</strong> DPUPKP Kota Yogyakarta
    </h2>
    <p class="text-muted mb-5" style="font-size:0.95rem; margin-top: -30px">
      Ketersediaan Website di DPUPKP Kota Yogyakarta
    </p>
    <div class="row g-4 justify-content-center">
      <div class="col-6 col-md-4 col-lg-3 bidang-item d-flex justify-content-center">
        <a href="https://lintas.geo-ai.id/" target="_blank" class="text-decoration-none" style="width:100%; max-width:280px;">
          <div class="card shadow-sm border-0 p-3 rounded-4 card-hover" style="height: 320px;">
            <div class="text-center mb-3">
              <img src="<?= asset_url('lintas.png') ?>"
                alt="Logo Lintas"
                style="width:60px; height:60px; object-fit:contain;">
            </div>
            <p class="text-muted mb-1" style="font-size: 0.9rem;">
              Bidang Jalan dan Jembatan
            </p>
            <hr>
            <h5 class="fw-bold" style="color:#001f54; min-height: 48px;">
              LINTAS (Layanan Informasi Jembatan Kota Yogyakarta)
            </h5>
            <div class="text-end mt-auto">
              <span style="font-size: 22px; color:#001f54;">→</span>
            </div>
          </div>
        </a>
      </div>

      <div class="col-6 col-md-4 col-lg-3 bidang-item d-flex justify-content-center">
        <a href="https://pu.jogjakota.go.id/" target="_blank" class="text-decoration-none" style="width:100%; max-width:280px;">
          <div class="card shadow-sm border-0 p-3 rounded-4 card-hover" style="height: 320px;">
            <div class="text-center mb-3">
              <<img src="<?= asset_url('logo.png') ?>"
                alt="Logo DPUPKP"
                style="width:60px; height:60px; object-fit:contain;">
            </div>
            <p class="text-muted mb-1" style="font-size: 0.9rem;">
              Dinas Pekerjaan Umum Perumahan dan Kawasan Permukiman
            </p>
            <hr>
            <h5 class="fw-bold" style="color:#001f54; min-height: 48px;">
              WEBSITE DPUPKP Kota Yogyakarta
            </h5>
            <div class="text-end mt-auto">
              <span style="font-size: 22px; color:#001f54;">→</span>
            </div>
          </div>
        </a>
      </div>
<div class="col-6 col-md-4 col-lg-3 bidang-item d-flex justify-content-center">
        <a href="https://yogyapsu.vercel.app/" target="_blank" class="text-decoration-none" style="width:100%; max-width:280px;">
          <div class="card shadow-sm border-0 p-3 rounded-4 card-hover" style="height: 320px;">
            <div class="text-center mb-3">
              <img src="<?= asset_url('yogyapsu.png') ?>"
                alt="Logo Yogyapsu"
                style="width:60px; height:60px; object-fit:contain;">
            </div>
            <p class="text-muted mb-1" style="font-size: 0.9rem;">
               Dinas Pekerjaan Umum Perumahan dan Kawasan Permukiman
            </p>
            <hr>
            <h5 class="fw-bold" style="color:#001f54; min-height: 48px;">
              (YOGYAPSU) Sistem Informasi PSU Perumahan Kota Yogyakarta 
            </h5>
            <div class="text-end mt-auto">
              <span style="font-size: 22px; color:#001f54;">→</span>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>
</section>

  <section id="kilasdata" class="kilas-data">
    <div class="kilas-box">
      <h2 style="text-align:center; margin-bottom:2rem; font-family:'Montserrat', sans-serif; font-weight:700; color: #001f54">Kilas
        <strong>BATASAN DATA</strong> pada <strong>#</strong>
        <span style="display:inline-block; position:relative; width:150px; text-align: left; height:1em; overflow:hidden; vertical-align:middle; line-height:1em;">
          <span style="position:absolute; animation:switchWord 4s infinite; font-weight:bold;">
            <span style="color: #213878; display:block; height:1em;">WEBGIS</span>
            <span style="color: #FFCC00; display:block; height:1em;">SITIJO</span>
          </span>
        </span>
      </h2>
      <p>
        Kota Yogyakarta secara astronomis terletak di antara 7°15'24" sampai dengan 7°49'26" Lintang Selatan dan 110°24'19" sampai dengan 110°28'53" Bujur Timur, dengan ketinggian rata-rata 114 m di atas permukaan laut.
      </p>
      <div class="data-items" style="display:flex; justify-content:center; gap:40px; flex-wrap:wrap; text-align:center; margin-top:20px;">
        <div class="item" style="flex:1; min-width:150px;">
          <div class="icon" style="background: #fffef0ff; color:var(--biru-pupr); font-size:30px; width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px; box-shadow:0 3px 6px rgba(0,0,0,0.15); transition:transform 0.3s;">
            <img src="<?= asset_url('luas.png') ?>" alt="luas" class="img-fluid">
          </div>
          <h3 class="counter" data-target="32.82">0</h3>
          <p>Luas Wilayah (km²)</p>
        </div>
        <div class="item" style="flex:1; min-width:150px;">
          <div class="icon" style="background: #fffef0ff; color:var(--biru-pupr); font-size:30px; width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px; box-shadow:0 3px 6px rgba(0,0,0,0.15); transition:transform 0.3s;">
            <img src="<?= asset_url('kecamatan.png') ?>" alt="luas" class="img-fluid">
          </div>
          <h3 class="counter" data-target="14">0</h3>
          <p>Kemantren</p>
        </div>
        <div class="item" style="flex:1; min-width:150px;">
          <div class="icon" style="background: #fffef0ff; color:var(--biru-pupr); font-size:30px; width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px; box-shadow:0 3px 6px rgba(0,0,0,0.15); transition:transform 0.3s;">
            <img src="<?= asset_url('kelurahan.png') ?>" alt="luas" class="img-fluid">
          </div>
          <h3 class="counter" data-target="45">0</h3>
          <p>Kelurahan</p>
        </div>
        <div class="item" style="flex:1; min-width:150px;">
          <div class="icon" style="background: #fffef0ff; color:var(--biru-pupr); font-size:30px; width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px; box-shadow:0 3px 6px rgba(0,0,0,0.15); transition:transform 0.3s;">
            <img src="<?= asset_url('rw.png') ?>" alt="luas" class="img-fluid">
          </div>
          <h3 class="counter" data-target="616">0</h3>
          <p>RW</p>
        </div>
        <div class="item" style="flex:1; min-width:150px;">
          <div class="icon" style="background: #fffef0ff; color:var(--biru-pupr); font-size:30px; width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px; box-shadow:0 3px 6px rgba(0,0,0,0.15); transition:transform 0.3s;">
            <img src="<?= asset_url('rt.png') ?>" alt="luas" class="img-fluid">
          </div>
          <h3 class="counter" data-target="2535">0</h3>
          <p>RT</p>
        </div>
      </div>

      <script>
        document.querySelectorAll('.icon').forEach(icon => {
          icon.addEventListener('mouseenter', () => {
            icon.style.transform = 'translateY(-8px) scale(1.1)';
          });
          icon.addEventListener('mouseleave', () => {
            icon.style.transform = 'translateY(0) scale(1)';
          });
        });
      </script>
    </div>
  </section>

  <script>
    function animateCounter(counter) {
      const target = +counter.getAttribute("data-target");
      const duration = 2000;
      const frameRate = 60;
      const totalFrames = Math.round((duration / 1000) * frameRate);
      let frame = 0;
      const start = 0;
      const increment = (target - start) / totalFrames;
      const timer = setInterval(() => {
        frame++;
        const current = start + increment * frame;
        if (target % 1 === 0) {
          counter.textContent = Math.floor(current);
        } else {
          counter.textContent = current.toFixed(2);
        }
        if (frame >= totalFrames) {
          clearInterval(timer);
          counter.textContent = target;
        }
      }, 1000 / frameRate);
    }
    document.addEventListener("DOMContentLoaded", () => {
      const counters = document.querySelectorAll(".counter");
      counters.forEach(counter => {
        animateCounter(counter);
      });
    });
  </script>

  <section id="contact" class="py-5" style="background:var(--biru-pupr);color:#fff;">
    <div class="container">
      <div class="row align-items-center">
        <h2 style="display:flex; justify-content:center; align-items:center; gap:0.5rem; margin-bottom:2rem; margin-left: 400px; font-family:'Montserrat', sans-serif; font-weight:700;">
          <strong>Kontak</strong><strong>#</strong>
          <span style="display:inline-block; position:relative; width:1000px; height:1em; overflow:hidden;">
            <span style="position:absolute; left:50%; transform:translateX(-50%); animation:switchWord 4s infinite; font-weight:bold; text-align:left;margin-left:-470px;">
              <span style="color: #ffa200ff; display:block; height:1em;">DPUPKP</span>
              <span style="color: #FFCC00; display:block; height:1em;">KOTA YOGYAKARTA</span>
            </span>
          </span>
        </h2>
        <div class="col-lg-6 mb-4">
          <div class="contact-info" style="margin-left: 40px;">
            <p class="mb-3 fw-bold contact-fade">
              <i class="fas fa-building me-2 text-primary"></i>
              Dinas Pekerjaan Umum Perumahan dan Kawasan Permukiman<br>
              Pemerintah Kota Yogyakarta
            </p>
            <p class="mb-3 contact-fade">
              <i class="fas fa-map-marker-alt me-2 text-warning"></i>
              Jl. Kenari No. 56, Kelurahan Muja Muju, Kemantren Umbulharjo,<br>
              Kota Yogyakarta, Daerah Istimewa Yogyakarta 55165
            </p>
            <p class="mb-2 fw-bold contact-fade">
              <i class="fas fa-phone-alt me-2 text-success"></i>
              Telepon
            </p>
            <p class="mb-3 contact-fade">(0274) 515867, (0274) 586795, (0274) 515866</p>
            <p class="mb-2 fw-bold contact-fade">
              <i class="fas fa-fax me-2 text-info"></i>
              Fax
            </p>
            <p class="mb-3 contact-fade">(0274) 586795</p>
          </div>
        </div>
        <div class="col-lg-6 text-center">
          <iframe src="https://www.google.com/maps/embed?pb=!4v1760506376645!6m8!1m7!1sOfaHpDghWUdfRJ6CkyIGIA!2m2!1d-7.799226170639185!2d110.3908016988914!3f179.78600593360554!4f-24.951894744737785!5f0.4000000000000002"
            width="100%"
            height="350"
            style="border:0;border-radius:10px;"
            allow="accelerometer; gyroscope; fullscreen"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      </div>
    </div>

    <div class="text-center mt-3">
      <small class="d-block mb-2">SOSIAL MEDIA</small>
      <a href="https://www.instagram.com/dpupkpkotajogja" class="text-white me-3">
        <i class="fab fa-instagram fa-lg"></i>
      </a>
      <a href="https://youtube.com/@dinaspupkpkotayogyakarta" class="text-white me-3">
        <i class="fab fa-youtube fa-lg"></i>
      </a>
      <a href="mailto:puperkim@jogjakota.go.id" class="text-white">
        <i class="fas fa-envelope fa-lg"></i>
      </a>
    </div>

    <div class="text-center mt-3">
      <small class="d-block mb-2 text-warning fw-bold">HOTLINE</small>
      <a href="mailto:upik@jogjakota.go.id" class="text-white me-3">
        <i class="fas fa-envelope-open-text fa-lg"></i>
      </a>
      <a href="sms:08122780001" class="text-white" title="Hotline SMS">
        <i class="fas fa-sms fa-lg"></i>
      </a>
    </div>

    </div>
    </div>
    <footer class="text-center py-4" style="background: var(--biru-pupr); color: white;">
      <div class="container">
        <p class="mb-1 fw-bold">© 2025 Dinas Pekerjaan Umum, Perumahan dan Kawasan Permukiman Kota Yogyakarta</p>
      </div>
    </footer>
    </div>
  </section>

  <script>
    const sectionIds = ['fitur', 'organisasi', 'kilasdata'];
    const sections = sectionIds.map(id => document.getElementById(id)).filter(el => el);
    sections.forEach((section, index) => {
      const observer = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting) {
          section.classList.add('aos-animate');
          if (section.id === 'kilasdata' && !counterStarted) {
            runCounter();
            counterStarted = true;
          }
        }
      }, {
        threshold: 0.15
      });
      observer.observe(section);
    });
    window.addEventListener('load', () => {
      AOS.init({
        duration: 800,
        once: true
      });
    });
    document.getElementById('toggleBidang').addEventListener('click', function() {
      const hiddenItems = document.querySelectorAll('#bidangList .bidang-item[style*="display: none"]');
      const btn = this;
      if (hiddenItems.length > 0) {
        hiddenItems.forEach(item => item.style.display = '');
        btn.textContent = 'Tampilkan Lebih Sedikit';
      } else {
        const allItems = document.querySelectorAll('#bidangList .bidang-item');
        allItems.forEach((item, i) => {
          if (i >= 4) item.style.display = 'none';
        });
        btn.textContent = 'Lihat Semua';
      }
    });
    const counters = document.querySelectorAll('.counter');
    let counterStarted = false;
    function runCounter() {
      counters.forEach(counter => {
        const target = parseFloat(counter.getAttribute('data-target'));
        const isDecimal = counter.getAttribute('data-target').includes(".");
        const steps = 300;
        let count = 0;
        let increment = target / steps;
        const update = () => {
          count += increment;
          if (count < target) {
            counter.innerText = isDecimal ? count.toFixed(2) : Math.floor(count);
            requestAnimationFrame(update);
          } else {
            counter.innerText = isDecimal ? target.toFixed(2) : target;
          }
        };
        update();
      });
    }
    function smoothScrollTo(targetY, duration = 4000) {
      const startY = window.scrollY;
      const distance = targetY - startY;
      let startTime = null;
      function easeInOutCubic(t) {
        return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
      }
      function animation(currentTime) {
        if (!startTime) startTime = currentTime;
        const timeElapsed = currentTime - startTime;
        const progress = Math.min(timeElapsed / duration, 1);
        window.scrollTo(0, startY + distance * easeInOutCubic(progress));
        if (progress < 1) requestAnimationFrame(animation);
      }
      requestAnimationFrame(animation);
    }
    const observerOptions = {
      threshold: 0.15
    };
    sections.forEach((section) => {
      const observer = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting) {
          section.classList.add('aos-animate');
          if (section.id === 'kilasdata' && !counterStarted) {
            runCounter();
            counterStarted = true;
          }
        }
      }, {
        threshold: 0.15
      });
      observer.observe(section);
    });
    const contactItems = document.querySelectorAll('.contact-fade');
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        } else {
          entry.target.classList.remove('visible');
        }
      });
    }, {
      threshold: 0.1
    });
    contactItems.forEach(item => {
      observer.observe(item);
    });
    AOS.init({
      duration: 800,
      once: true
    });
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const fiturLinks = document.querySelectorAll('a[href="signin.php"]');
      fiturLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          const signinModal = new bootstrap.Modal(document.getElementById('signinModal'));
          signinModal.show();
        });
      });
    });
  </script>
</body>
</html>
