<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Password</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/gantiPassword.css" />
  </head>
  <body>
    <main class="password-reset-container">
      <section class="password-reset-card">
        <h1 class="password-reset-title">Ganti Password</h1>
        <p class="password-reset-subtitle">
          Masukkan email dan role anda untuk dikirimi link<br />
          untuk ganti password
        </p>
        <form
          class="password-reset-form"
          action="kirimLinkReset.php"
          method="POST"
        >
          <div class="email-input-container">
            <input
              type="email"
              name="email"
              placeholder="email"
              class="email-input"
              required
            />
          </div>

          <div class="email-input-container">
          <select class="role-input" name="role" required>
          <option value="" selected disabled>Pilih role</option>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        </div>
          
          <button type="submit" class="submit-button">Kirim</button>
        </form>
      </section>
    </main>
  </body>
</html>
