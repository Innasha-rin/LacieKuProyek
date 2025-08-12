document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("formTambahBuku").addEventListener("submit", function (e) {
        e.preventDefault(); // Mencegah reload halaman

        let formData = new FormData(this); // Ambil data form

        fetch("../views/tambahBukuBaru_proses.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload(); // Reload halaman setelah sukses
                window.location.href = 'manajemenBuku.php';
            } else {
                alert("Gagal: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Terjadi kesalahan dalam proses.");
        });
    });
});
