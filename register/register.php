<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register Page</title>
    <link rel="stylesheet" href="../assets/css/register.css" />
  </head>
  <body>
    <main class="register-container">
        <div class="register-wrapper">
            <img
            src="../assets/images/login_ollie_logo.png"
            class="logo-image"
            alt="Logo"
            />
          <section class="register-form-container">
            <div class="register-form-wrapper">
              <h1 class="register-heading">Register</h1>
              <form class="register-form" action="verification.php" method="POST">
                <div class="form-group">
                  <label for="nim" class="form-label">NIM</label>
                  <input type="text" name="nim" id="nim" placeholder="NIM" class="input-field" required/>
                </div>
                <div class="form-group">
                  <label for="nama" class="form-label">Nama</label>
                  <input type="text" name="nama" id="nama" placeholder="Nama" class="input-field" required/>
                </div>
                <div class="form-group">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" name="email" id="email" placeholder="Email" class="input-field" required/>
                </div>
                <div class="form-group">
                  <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                  <select name="jenis_kelamin" id="jenis_kelamin" class="input-field" required>
                    <option value="" disabled selected>Jenis Kelamin</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                  <input type="date" name="tanggal_lahir" id="tanggal_lahir" placeholder="Tanggal Lahir" class="input-field" required />
                </div>
                <div class="form-group">
                  <label for="no_hp" class="form-label">Nomor HP</label>
                  <input type="tel" name="no_hp" id="no_hp" placeholder="Nomor HP" class="input-field" required/>
                </div>
                <div class="form-group">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" name="password" id="password" placeholder="Password" class="input-field" required/>
                </div>
                <button type="submit" class="register-button">Register</button>
              </form>
            </div>
          </section>
        </div>
      </main>
  </body>
</html>