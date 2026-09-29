<?php
session_start();
require_once dirname(__DIR__) . '/partials/helpers.php';

/*
 * URL PDF dari R2
 * Pastikan file di R2 berada di:
 * assets/Panduan WEBGIS SITIJO.pdf
 */
$pdfPanduan = asset_url('Panduan WEBGIS SITIJO.pdf');
?>

<!DOCTYPE html>

<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

```
<title>Panduan Upload Data</title>

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">

<style>
    body {
        font-family: 'Montserrat', sans-serif;
        background: #f0f2f5;
        padding: 0;
        color: #212529;
    }

    .main-container {
        max-width: 1200px;
        margin: 100px auto;
        padding: 20px;
    }

    .card {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom: 25px;
    }

    h4 {
        margin: 0 0 15px;
        color: #042a51;
        font-size: 24px;
        text-align: center;
        font-weight: bold;
    }

    iframe.video-box {
        width: 100%;
        height: 430px;
        border-radius: 10px;
        border: none;
        display: block;
        margin-bottom: 15px;
    }

    iframe.pdf-box {
        width: 100%;
        height: 550px;
        border-radius: 10px;
        border: 1px solid #d0d0d0;
        display: block;
        margin-top: 10px;
        background: #fff;
    }

    .download-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        background: #003366;
        color: #fff;
        border-radius: 6px;
        font-weight: 600;
        text-decoration: none;
        transition: 0.3s;
        margin-top: 10px;
    }

    .download-btn:hover {
        background: #1c2f70;
        color: #fff;
    }

    @media (max-width: 767px) {

        .main-container {
            padding: 15px;
            margin: 80px auto;
        }

        h4 {
            font-size: 20px;
        }

        iframe.video-box,
        iframe.pdf-box {
            height: 300px;
        }
    }

    @media (min-width: 768px) and (max-width: 991px) {

        body {
            margin-top: 100px;
        }

        h4 {
            font-size: 22px;
        }

        iframe.video-box {
            height: 380px;
        }

        iframe.pdf-box {
            height: 380px;
        }
    }

    @media (min-width: 992px) {

        h4 {
            font-size: 24px;
        }

        iframe.video-box {
            height: 430px;
        }

        iframe.pdf-box {
            height: 550px;
        }
    }
</style>
```

</head>

<body>

```
<?php include dirname(__DIR__) . "/partials/header.php"; ?>

<?php include dirname(__DIR__) . "/partials/sidebar.php"; ?>


<div class="main-container">

    <!-- =========================================
         VIDEO PANDUAN
    ========================================== -->
    <div class="card">

        <h4>🎬 Panduan Penggunaan WEBGIS</h4>

        <iframe
            class="video-box"
            src="https://drive.google.com/file/d/13STCML0QjTnBF-4mq1PibAbgCy0wLoYz/preview"
            title="Video Panduan Penggunaan WEBGIS"
            allow="autoplay"
            allowfullscreen>
        </iframe>

    </div>


    <!-- =========================================
         PDF PANDUAN
    ========================================== -->
    <div class="card">

        <h4>📄 Panduan Penggunaan WEBGIS</h4>

        <iframe
            class="pdf-box"
            src="<?= htmlspecialchars($pdfPanduan, ENT_QUOTES, 'UTF-8') ?>"
            title="Panduan Penggunaan WEBGIS PDF">
        </iframe>

        <a
            href="<?= htmlspecialchars($pdfPanduan, ENT_QUOTES, 'UTF-8') ?>"
            target="_blank"
            rel="noopener noreferrer"
            class="download-btn">

            <i class="bi bi-download"></i>

            Download Panduan PDF

        </a>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
```

</body>

</html>
