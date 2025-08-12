document.addEventListener("DOMContentLoaded", function () {
    let searchInput = document.getElementById("searchInput");
  
    // Fungsi untuk mengambil daftar buku dari PHP
    function loadBooks(query = "") {
      let xhr = new XMLHttpRequest();
      xhr.open("GET", "/tugas/proyek-perpustakaan/admin/views/manajemenBuku_cariBuku.php?search=" + query, true);
      xhr.onload = function () {
        if (xhr.status == 200) {
          document.getElementById("tableBody").innerHTML = xhr.responseText;
        }
      };
      xhr.send();
    }
  
    // Panggil loadBooks saat halaman dimuat
    loadBooks();
  
    // Pencarian buku dengan AJAX
    searchInput.addEventListener("keyup", function () {
      loadBooks(this.value);
    });
  });