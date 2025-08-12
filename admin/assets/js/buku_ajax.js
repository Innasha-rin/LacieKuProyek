document.addEventListener("DOMContentLoaded", function() {
    // Ambil parameter ID buku dari URL
    const urlParams = new URLSearchParams(window.location.search);
    const id_buku = urlParams.get('id');

    if (id_buku) {
        // Perbaikan: Gunakan path absolute dengan / di depan untuk memastikan konsistensi path
        fetch(`../views/buku_ambilDataBuku.php?id=${id_buku}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Tentukan label berdasarkan stok
                    let stokElement = document.getElementById("stok");
                    if (data.stok < 1) {
                        stokElement.innerHTML = `<div class="availability-badge">TIDAK TERSEDIA</div>`;
                    } else {
                        stokElement.innerHTML = `<div class="availability-badge">TERSEDIA</div>`;
                    }
                    
                    // Perbaikan: Periksa apakah cover memiliki path lengkap atau tidak
                    let coverPath;
                    if (data.cover && data.cover.startsWith('http')) {
                        // Jika cover sudah berupa URL lengkap, gunakan langsung
                        coverPath = data.cover;
                    } else {
                        // Gunakan path absolut ke folder images
                        coverPath = `../../CoverBook, OLLIE/${data.cover}`;
                        // Alternatif jika struktur folder berbeda:
                        // coverPath = `../../assets/images/${data.cover}`;
                    }
                    
                    document.getElementById("buku-detail").innerHTML = `
                        <div class="book-image-container"><img src="${coverPath}" alt="Cover Buku - ${data.judul}"></div>
                        <section class="book-details-container">
                        <h2 class="book-title">${data.judul}</h2>
                        <div class="book-description">Sinopsis: ${data.sinopsis}</div>
                        <div class="book-info-badge">Jumlah halaman: ${data.jumlah_halaman}</div>
                        <div class="book-info-badge">Penulis: ${data.penulis}</div>
                        <div class="book-info-badge">ISBN: ${data.isbn}</div>
                        <div class="book-info-badge">Tahun terbit: ${data.tahun_terbit}</div>
                        <div class="book-info-badge">Penerbit: ${data.penerbit}</div>
                        </section>
                    `;
                } else {
                    document.getElementById("buku-detail").innerHTML = `<p>Buku tidak ditemukan.</p>`;
                }
            })
            .catch(error => {
                document.getElementById("buku-detail").innerHTML = `<p>Error loading data.</p>`;
                console.error(error);
            });
    } else {
        document.getElementById("buku-detail").innerHTML = `<p>ID buku tidak valid.</p>`;
    }
});