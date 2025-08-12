// Wait for the DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function () {
    // Get references to DOM elements
    const searchInput = document.getElementById("searchInput");
    const userTable = document.getElementById("userTable");
    const tableHeader = userTable.querySelector(".table-header");
    
    // Create a container for table rows (excluding header)
    const tableContent = document.createElement("div");
    tableContent.className = "table-content";
    tableContent.style.overflowY = "auto";
    tableContent.style.maxHeight = "calc(100% - " + tableHeader.offsetHeight + "px)";
    
    // Move all rows except header to the new container
    const tableRows = userTable.querySelectorAll(".table-row");
    tableRows.forEach(row => {
        tableContent.appendChild(row);
    });
    
    // Insert the content container after the header
    tableHeader.after(tableContent);
    
    // Initialize variables for drag functionality
    let isDragging = false;
    let startY;
    let scrollTop;
    
    // Fungsi untuk memuat data
    function loadData(searchValue = '') {
        fetch(`../views/daftarUsers_cariUsers.php?search=${encodeURIComponent(searchValue)}`)
            .then(response => response.text())
            .then(data => {
                // Clear existing table content except header
                tableContent.innerHTML = '';
                
                // Add new data to the table content container
                tableContent.insertAdjacentHTML('beforeend', data);
                
                // Set up event listeners for new delete buttons
                setupDeleteButtons();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memuat data pengguna');
            });
    }
    
    // Fungsi untuk menambahkan event listener ke tombol hapus
    function setupDeleteButtons() {
        const deleteButtons = tableContent.querySelectorAll('.delete-user');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                
                // Konfirmasi penghapusan
                if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
                    deleteUser(userId);
                }
            });
        });
    }
    
    // Fungsi untuk menghapus pengguna
    function deleteUser(userId) {
        // Membuat objek FormData untuk mengirim data
        const formData = new FormData();
        formData.append('id', userId);
        
        // Mengirim permintaan fetch untuk menghapus pengguna
        fetch('../views/daftarUsers_hapusUsers.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Jika berhasil, refresh data
                alert(data.message);
                loadData(searchInput.value);
            } else {
                // Jika gagal, tampilkan pesan error
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus pengguna');
        });
    }
    
    // Memuat data saat halaman dimuat
    loadData();
    
    // Memuat data ketika pencarian berubah
    searchInput.addEventListener('input', function() {
        loadData(this.value);
    });

    // ===== DRAG/SCROLL FUNCTIONALITY =====
    // Mouse down event - start dragging
    tableContent.addEventListener("mousedown", function (e) {
        isDragging = true;
        startY = e.pageY;
        scrollTop = tableContent.scrollTop;
        tableContent.style.cursor = "grabbing";
        
        // Prevent default text selection during drag
        e.preventDefault();
    });
  
    // Mouse move event - perform dragging
    document.addEventListener("mousemove", function (e) {
        if (!isDragging) return;
        
        const y = e.pageY;
        const walk = (y - startY) * 2; // Multiply by 2 for faster scrolling
        tableContent.scrollTop = scrollTop - walk;
    });
  
    // Mouse up event - stop dragging
    document.addEventListener("mouseup", function () {
        isDragging = false;
        tableContent.style.cursor = "grab";
    });
  
    // Mouse leave event - stop dragging if cursor leaves the window
    document.addEventListener("mouseleave", function () {
        if (isDragging) {
            isDragging = false;
            tableContent.style.cursor = "grab";
        }
    });
  
    // Touch events for mobile devices
    tableContent.addEventListener("touchstart", function (e) {
        isDragging = true;
        startY = e.touches[0].pageY;
        scrollTop = tableContent.scrollTop;
        
        // Prevent default scroll behavior
        e.preventDefault();
    });
  
    tableContent.addEventListener("touchmove", function (e) {
        if (!isDragging) return;
        
        const y = e.touches[0].pageY;
        const walk = (y - startY) * 2;
        tableContent.scrollTop = scrollTop - walk;
        
        // Prevent default scroll behavior
        e.preventDefault();
    });
  
    tableContent.addEventListener("touchend", function () {
        isDragging = false;
    });
  
    // Prevent context menu on right-click for better user experience
    tableContent.addEventListener("contextmenu", function (e) {
        e.preventDefault();
    });
  
    // Add wheel event for smooth scrolling
    tableContent.addEventListener("wheel", function (e) {
        e.preventDefault();
        tableContent.scrollTop += e.deltaY;
    });
});