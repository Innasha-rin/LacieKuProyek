document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("editBukuForm");
    const id = document.getElementById("id").value;
    let coverPath = ""; // Variable to store the current cover path
    
    // Load book data when page loads
    loadBookData(id);
    
    // Function to load book data
    function loadBookData(id) {
        fetch(`../views/editBuku_ambil.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Fill form fields with book data
                    document.getElementById("judul").value = data.judul || "";
                    document.getElementById("penulis").value = data.penulis || "";
                    document.getElementById("penerbit").value = data.penerbit || "";
                    document.getElementById("tahun_terbit").value = data.tahun_terbit || "";
                    document.getElementById("jumlah_halaman").value = data.jumlah_halaman || "";
                    document.getElementById("isbn").value = data.isbn || "";
                    document.getElementById("sinopsis").value = data.sinopsis || "";
                    document.getElementById("stok").value = data.stok || "";
                    
                    // Set the selected category
                    const kategoriSelect = document.getElementById("kategori");
                    for (let i = 0; i < kategoriSelect.options.length; i++) {
                        if (kategoriSelect.options[i].value === data.kategori) {
                            kategoriSelect.options[i].selected = true;
                            break;
                        }
                    }
                    
                    // Display current cover image
                    if (data.cover) {
                        coverPath = data.cover;
                        const coverField = document.querySelector('.form-field:first-of-type .input-wrapper');
                        
                        // Check if preview container already exists
                        let previewContainer = document.getElementById("coverPreviewContainer");
                        if (!previewContainer) {
                            previewContainer = document.createElement("div");
                            previewContainer.id = "coverPreviewContainer";
                            
                            const coverLabel = document.createElement("div");
                            coverLabel.classList.add("cover-label");
                            coverLabel.textContent = "Cover Saat Ini:";
                            
                            const coverImg = document.createElement("img");
                            coverImg.id = "currentCoverPreview";
                            coverImg.src = `../../CoverBook, OLLIE/${data.cover}`;
                            coverImg.alt = "Cover Buku";
                            
                            // Add timestamp to prevent caching
                            coverImg.src += `?t=${new Date().getTime()}`;
                            
                            previewContainer.appendChild(coverLabel);
                            previewContainer.appendChild(coverImg);
                            coverField.appendChild(previewContainer);
                        } else {
                            // Update the image if container exists
                            const coverImg = document.getElementById("currentCoverPreview");
                            coverImg.src = `../../CoverBook, OLLIE/${data.cover}?t=${new Date().getTime()}`;
                        }
                    }
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error loading book data:", error);
                alert("Terjadi kesalahan saat memuat data buku");
            });
    }
    
    // Preview for new cover image when selected
    const coverInput = document.getElementById("cover");
    coverInput.addEventListener("change", function() {
        const coverField = document.querySelector('.form-field:first-of-type .input-wrapper');
        
        // Remove previous new cover preview if exists
        const existingPreview = document.getElementById("newCoverPreview");
        if (existingPreview) {
            existingPreview.remove();
        }
        
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const previewContainer = document.createElement("div");
                previewContainer.id = "newCoverPreview";
                
                const coverLabel = document.createElement("div");
                coverLabel.classList.add("cover-label");
                coverLabel.textContent = "Cover Baru:";
                
                const coverImg = document.createElement("img");
                coverImg.src = e.target.result;
                coverImg.alt = "Preview Cover Baru";
                
                previewContainer.appendChild(coverLabel);
                previewContainer.appendChild(coverImg);
                coverField.appendChild(previewContainer);
            }
            
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Handle form submission with AJAX
    form.addEventListener("submit", function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Show loading state
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        submitButton.innerHTML = "Memproses...";
        submitButton.disabled = true;
        
        fetch("../views/editBuku_proses.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Reset button state
            submitButton.innerHTML = originalButtonText;
            submitButton.disabled = false;
            
            if (data.success) {
                alert("Buku berhasil diperbarui!");
                
                // Redirect to manajemenBuku page after successful update
                window.location.href = "manajemenBuku.php";
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            submitButton.innerHTML = originalButtonText;
            submitButton.disabled = false;
            alert("Terjadi kesalahan saat memperbarui buku");
        });
    });
});