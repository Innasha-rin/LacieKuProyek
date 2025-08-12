document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".delete-book").forEach(button => {
        button.addEventListener("click", function () {
            let bookId = this.getAttribute("data-id"); // Ambil ID dari atribut data-id
            deleteBook(bookId);
        });
    });
});

function deleteBook(id) {
    if (confirm("Apakah Anda yakin ingin menghapus buku ini?")) {
        fetch("../views/manajemenBuku_hapusBuku.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: "id=" + id,
        })
        .then(response => response.text()) // Ganti sementara ke text() untuk debugging
        .then(data => {
            console.log("Server Response:", data); // Debug respons di console

            try {
                let jsonData = JSON.parse(data); // Coba parse JSON
                if (jsonData.success) {
                    alert("Buku berhasil dihapus!");
                    location.reload();
                } else {
                    alert("Gagal menghapus buku: " + jsonData.message);
                }
            } catch (error) {
                alert("Kesalahan parsing JSON: " + error.message);
                console.error("Data yang diterima:", data);
            }
        })
        .catch(error => console.error("Error:", error));
    }
}
