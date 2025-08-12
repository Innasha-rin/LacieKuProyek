<?php
require '../../session/sessionUser.php';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FAQ</title>
    <link rel="stylesheet" href="../assets/css/hamburger.css" />
    <link rel="stylesheet" href="../assets/css/faq.css"/>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap"
      rel="stylesheet"
    />
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
        <a href="user start page.php"><div class="nav-item">Home</div></a>
        <a href="bookCollection.php"><div class="nav-item">Koleksi Buku</div></a>
        <a href="paymentFine.php"><div class="nav-item">Pembayaran Denda</div></a>
        <a href="report book.php"><div class="nav-item">Lapor Buku</div></a>
        <a href="riwayatPeminjaman.php"><div class="nav-item">Riwayat Peminjaman</div></a>
        <a href="riwayatPengembalian.php"><div class="nav-item">Riwayat Pengembalian</div></a>
        <a href="riwayatPembayaran.php"><div class="nav-item">Riwayat Pembayaran</div></a>
        <a href="user account.php"><div class="nav-item">Profil</div></a>
        <a href="notificationPage.php"><div class="nav-item">Notifikasi</div></a>
        <a href="faq.php"><div class="nav-item">FAQ</div></a>
        <a href="../../logout.php"><div class="nav-item highlight">Keluar</div></a>
    </nav>
    <main class="page-container">
      <section class="content-wrapper">
        <h1 class="page-title">FAQ</h1>
        <div class="faq-grid">
          <article class="faq-card">
            <div class="card-content">
              <h2 class="category-title">
                Masalah Teknis &amp; Laporan Kerusakan/Hilang
              </h2>
              <ul class="faq-list">
              <li class="faq-item">
                Apa yang harus saya lakukan jika buku yang saya pinjam rusak
                atau hilang? Laporkan melalui menu Lapor Buku pada Dashboard →
                Masukkan ID Pengembalian → Masukkan deskripsi kerusakan/hilang →
                Menghadap Pustakawan → Memberikan ID Pengembalian → Ditindaklanjuti.
              </li>
              </ul>
              <ul class="faq-list">
              <li class="faq-item">
                Bagaimana jika buku yang saya kembalikan tidak sesuai dengan
                yang saya pinjam? Pustakawan akan melakukan verifikasi dengan PIN Pengembalian 
                anda dan meminta mahasiswa pergi ke menu Lapor Buku untuk
                membuat laporan dan pustakawan akan menindaklanjuti.
              </li>
              </ul>
            </div>
          </article>

          <article class="faq-card">
            <div class="card-content">
              <h2 class="category-title">Peminjaman &amp; Pengembalian Buku</h2>
              <ul class="faq-list">
              <li class="faq-item">
                Bagaimana cara meminjam buku di OLLIE? Login ke akun OLLIE →
                Koleksi buku → Pilih buku → Klik tombol &quot;Pinjam&quot; →
                Pergi ke perpustakaan dalam waktu dekat → Sampaikan ID
                Peminjaman ke pustakawan → Pustakawan memverifikasi dan 
                memberikan buku terkait.
              </li>
              </ul>
              <ul class="faq-list">
              <li class="faq-item">
                Berapa lama durasi peminjaman buku? Maksimal 1 minggu sejak
                tanggal peminjaman.
              </li>
              </ul>
              <ul class="faq-list">
              <li class="faq-item">
                Berapa jumlah maksimal buku yang dapat dipinjam? Maksimal 2 buku
                dalam satu waktu.
              </li>
              </ul>
            </div>
          </article>

          <article class="faq-card">
            <div class="card-content">
              <h2 class="category-title">Denda &amp; Pembayaran</h2>
              <ul class="faq-list">
              <li class="faq-item">
                Apa yang terjadi jika saya terlambat mengembalikan buku? Denda
                Rp2.000,00 per hari akan dikenakan jika terlambat mengembalikan
                buku.
              </li>
              </ul>
              <ul class="faq-list">
              <li class="faq-item">
                Bagaimana cara membayar denda? Masuk ke menu
                Pembayaran Denda → Scan QR Code menggunakan aplikasi scan QR Code →
                Lakukan pembayaran → Unggah bukti pembayaran → Pustakawan mengonfirmasi pembayaran.
                Dalam kasus denda karena buku hilang/rusak, pustakawan akan menindaklanjuti secara langsung.
              </li>
              </ul>
            </div>
          </article>

          <article class="faq-card">
            <div class="card-content">
              <h2 class="category-title">Pendaftaran &amp; Login</h2>
              <ul class="faq-list">
              <li class="faq-item">
                Siapa saja yang bisa menggunakan OLLIE? Hanya mahasiswa aktif
                kampus yang bisa menggunakan OLLIE (dengan verifikasi NIM).
              </li>
              </ul>
              <ul class="faq-list">
              <li class="faq-item">
                Bagaimana cara mendaftar akun? Masuk ke halaman pendaftaran →
                Masukkan NIM, nama, email, dan informasi lainnya → Verifikasi
                email → login.
              </li>
              </ul>
            </div>
          </article>
        </div>
      </section>
    </main>
    <script src="../assets//js/hamburger.js"></script>
    <script src="../assets/js/faq.js"></script>
  </body>
</html>
