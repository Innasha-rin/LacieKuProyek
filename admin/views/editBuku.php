<?php
require '../../session/sessionAdmin.php';
?>
<?php
$id_buku = $_GET['id'] ?? '';
if (empty($id_buku)) {
    // Redirect jika tidak ada ID buku
    header('Location: manajemenBuku.php');
    exit;
}
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Buku</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/editBuku.css" />
    <link rel="stylesheet" href="../assets/css/hamburger.css" />
    <style>
      /* Tambahan style untuk preview cover */
      #coverPreviewContainer, #newCoverPreview {
        margin-top: 10px;
      }
      #coverPreviewContainer img, #newCoverPreview img {
        max-width: 150px;
        max-height: 200px;
        border: 1px solid #ddd;
        padding: 5px;
      }
      .cover-label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
      }
    </style>
  </head>
  <body>
  <header>
        <div class="logo"><img src="../assets/images/ollie_teks.png" alt="logoOllie"></div>
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </header>

    <nav class="nav-menu">
        <a href="dashboard.php"><div class="nav-item">Home</div></a>
        <a href="manajemenBuku.php"><div class="nav-item">Manajemen Buku</div></a>
        <a href="peminjamanBuku.php"><div class="nav-item">Peminjaman Buku</div></a>
        <a href="pengembalianBuku.php"><div class="nav-item">Pengembalian Buku</div></a>
        <a href="pembayaranDenda.php"><div class="nav-item">Pembayaran Denda</div></a>
        <a href="daftarUsers.php"><div class="nav-item">Daftar Pengguna</div></a>
        <a href="riwayatPeminjaman.php"><div class="nav-item">Riwayat Peminjaman</div></a>
        <a href="riwayatPengembalian.php"><div class="nav-item">Riwayat Pengembalian</div></a>
        <a href="riwayatPembayaran.php"><div class="nav-item">Riwayat Pembayaran</div></a>
        <a href="akunAdmin.php"><div class="nav-item">Profil</div></a>
        <a href="../../logout.php"><div class="nav-item highlight">Keluar</div></a>
    </nav>
    <main class="page-container">
      <section class="form-container">
        <h1 class="form-title">Edit Buku</h1>
        <!-- Form dengan enctype yang diperlukan untuk file upload dan action yang menunjuk ke file proses -->
        <form class="book-form" id="editBukuForm" method="POST" enctype="multipart/form-data" action="../views/editBuku_proses.php">
          <!-- ID buku sebagai hidden field -->
          <input type="hidden" id="id" name="id" value="<?= htmlspecialchars($id_buku) ?>" />
          
          <div class="form-field">
            <label class="field-label" for="cover">Upload Cover Buku</label>
            <div class="input-wrapper">
              <input
                type="file"
                id="cover"
                name="cover"
                accept="image/jpeg,image/png,image/gif"
                class="form-input file-input"
              />
              <small>Unggah gambar baru untuk mengganti cover saat ini (format: JPG, PNG, GIF). Kosongkan jika tidak ingin mengubah cover.</small>
              <div class="upload-status"></div>
              <!-- Container untuk preview cover lama akan ditambahkan oleh JavaScript -->
            </div>
          </div>

          <div class="form-field">
            <label class="field-label" for="judul">Judul</label>
            <div class="input-wrapper">
              <input
                type="text"
                id="judul"
                name="judul"
                placeholder="Judul"
                class="form-input"
                required
              />
            </div>
          </div>

          <div class="form-field">
            <label class="field-label" for="penulis">Penulis</label>
            <div class="input-wrapper">
              <input
                type="text"
                id="penulis"
                name="penulis"
                placeholder="Penulis"
                class="form-input"
                required
              />
            </div>
          </div>

          <div class="form-field">
            <label class="field-label" for="penerbit">Penerbit</label>
            <div class="input-wrapper">
              <input
                type="text"
                id="penerbit"
                name="penerbit"
                placeholder="Penerbit"
                class="form-input"
                required
              />
            </div>
          </div>

          <div class="form-field">
            <label class="field-label" for="tahun_terbit">Tahun terbit</label>
            <div class="input-wrapper">
              <input
                type="text"
                id="tahun_terbit"
                name="tahun_terbit"
                placeholder="Tahun Terbit"
                class="form-input"
                required
              />
            </div>
          </div>

          <div class="form-field">
            <label class="field-label" for="jumlah_halaman">Jumlah halaman</label>
            <div class="input-wrapper">
              <input
                type="number"
                id="jumlah_halaman"
                name="jumlah_halaman"
                placeholder="Jumlah Halaman"
                class="form-input"
                required
              />
            </div>
          </div>

          <div class="form-field">
            <label class="field-label" for="kategori">Kategori Buku</label>
            <div class="input-wrapper">
              <select id="kategori" name="kategori" class="form-input form-select" required>
                <option value="" disabled selected>Kategori</option>
                <option value="Akutansi">Akutansi</option>
                <option value="Komputer">Komputer</option>
                <option value="Mesin">Mesin</option>
                <option value="Fiksi dan Cerita">Fiksi Dan Cerita</option>
                <option value="Teknik Sipil">Teknik Sipil</option>
                <option value="Ilmu Sosial">Ilmu Sosial</option>
                <option value="Manajemen">Manajemen</option>
                <option value="Bisnis & Manajemen">Bisnis & Manajemen</option>
                <option value="Metodologi Penelitian, Bisnis">Metodologi Penelitian, Bisnis</option>
                <option value="Bahasa">Bahasa</option>
                <option value="Teknik Digital">Teknik Digital</option>
                <option value="Geografi">Geografi</option>
                <option value="Elektro">Elektro</option>
                <option value="Matematika">Matematika</option>
                <option value="Biologi">Biologi</option>
                <option value="Teknologi Umum">Teknologi Umum</option>
                <option value="Teknologi Kimia">Teknologi Kimia</option>
                <option value="Arsitektur">Arsitektur</option>
                <option value="Ilmu Bangunan">Ilmu Bangunan</option>
                <option value="Sipil Kearsipan">Sipil Kearsipan</option>
                <option value="Karya Sastra">Karya Sastra</option>
              </select>
            </div>
          </div>

          <div class="form-field">
            <label class="field-label" for="isbn">ISBN</label>
            <div class="input-wrapper">
              <input
                type="text"
                id="isbn"
                name="isbn"
                placeholder="ISBN"
                class="form-input"
              />
            </div>
          </div>

          <div class="form-field">
            <label class="field-label" for="stok">Stok</label>
            <div class="input-wrapper">
              <input
                type="number"
                id="stok"
                name="stok"
                placeholder="Stok"
                class="form-input"
                required
              />
            </div>
          </div>

          <div class="form-field synopsis-field">
            <label class="field-label" for="sinopsis">Sinopsis</label>
            <div class="input-wrapper">
              <textarea
                id="sinopsis"
                name="sinopsis"
                placeholder="Sinopsis"
                class="form-textarea"
              ></textarea>
            </div>
          </div>

          <div class="button-container">
            <button type="submit" class="submit-button">Simpan Perubahan</button>
          </div>
        </form>
      </section>
    </main>
    <script src="../assets/js/hamburger.js"></script>
    <script src="../assets/js/editBuku_ajax.js"></script>
  </body>
</html>