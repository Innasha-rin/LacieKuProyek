<?php
include 'login_back.php';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Page</title>
    <link rel="stylesheet" href="assets/css/login.css">
  </head>
  <body>
    <main class="login-page">
      <div class="login-container">
        <img
          src="assets/images/login_ollie_logo.png"
          alt="Logo"
          class="logo"
        />
        <section class="login-card">
        <form class="login-form" method="POST">
          <h1 class="login-heading">Login</h1>
          <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" placeholder="Email" class="email-input" name="email" id="email" required />
          </div>
          <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" placeholder="Password" class="password-input" name="password" id="password" required />
          </div>
           <a href="changePass/gantiPassword.php" class="forgot-password">Lupa password?</a>
           <p class="salah-input"><?= $message ?></p>
           <button type="submit" class="login-button">Login</button>
        </form>
        
        <div class="register-prompt">
              <p class="register-text">Tidak punya akun? <a href="register/register.php" class="register-link">daftar</a></p>
            </div>
        </section>
      </div>
    </main>
  </body>
</html>