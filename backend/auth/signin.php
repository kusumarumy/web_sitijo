<?php
session_start();

if (isset($_SESSION['user'])) {
  $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '/index.php';
  header("Location: " . htmlspecialchars($redirect));
  exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In - SITIJO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Montserrat', sans-serif;
      background: linear-gradient(2deg, #ffffffff 0%, #74a4ceff 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .login-container {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .login-box {
      background: #fff;
      padding: 30px;
      border-radius: 16px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      animation: fadeInUp 1s ease forwards;
      position: relative;
    }

    @keyframes fadeInUp {
      0% {
        opacity: 0;
        transform: translateY(30px);
      }

      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .login-box h2 {
      background: #213878;
      color: #fff;
      text-align: center;
      padding: 14px;
      margin: -30px -30px 25px -30px;
      border-radius: 16px 16px 0 0;
      letter-spacing: 1px;
    }

    .login-box label {
      font-weight: 600;
      margin-bottom: 6px;
      display: block;
    }

    .login-box input[type="text"],
    .login-box input[type="password"] {
      width: 100%;
      padding: 12px 15px;
      margin-bottom: 15px;
      border-radius: 10px;
      border: 1px solid #ccc;
      background: #f2f5ff;
      transition: all 0.3s ease;
    }

    .login-box input[type="text"]:focus,
    .login-box input[type="password"]:focus {
      border-color: #001a66;
      box-shadow: 0 0 8px rgba(0, 26, 102, 0.3);
      outline: none;
    }

    .login-box .remember {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      font-size: 14px;
    }

    .login-box .remember input {
      margin-right: 6px;
    }

    .login-box button {
      width: 100%;
      padding: 14px;
      background: #ffcc00;
      border: none;
      border-radius: 10px;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .login-box button:hover {
      background: #ffb400;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .login-box .password-wrapper {
      position: relative;
    }

    .login-box .password-wrapper i {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #001a66;
      transition: all 0.3s ease;
    }

    .login-box .password-wrapper i:hover {
      color: #ffcc00;
      transform: translateY(-50%) scale(1.2);
    }

    .error {
      color: #e53935;
      text-align: center;
      margin-bottom: 12px;
      font-weight: 600;
    }

    @media(max-width:480px) {
      .login-box {
        padding: 20px;
      }

      .login-box h2 {
        font-size: 18px;
      }
    }
  </style>
</head>

<body>
  <?php include __DIR__ . "/../../partials/header.php"; ?>

  <div class="login-container">
    <div class="login-box">
      <h2>SIGN IN</h2>
      <?php if (isset($_SESSION['error'])): ?>
        <div class="error"><?= $_SESSION['error'];
                            unset($_SESSION['error']); ?></div>
      <?php endif; ?>
      <form action="../../backend/auth/cek_signin.php" method="POST">
        <input type="hidden" name="redirect" value="<?= isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : '' ?>">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password</label>
        <div class="password-wrapper">
          <input type="password" id="password" name="password" placeholder="Masukkan password" required>
          <i class="bi bi-eye-slash-fill" id="togglePassword"></i>
        </div>
        <div class="remember">
          <input type="checkbox" id="remember" name="remember">
          <label for="remember">Remember me!</label>
        </div>
        <button type="submit">SIGN IN</button>
      </form>
    </div>
  </div>

  <script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    togglePassword.addEventListener('click', function() {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      this.classList.toggle('bi-eye');
      this.classList.toggle('bi-eye-slash-fill');
    });
  </script>
</body>

</html>