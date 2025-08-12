/**
 * Riwayat Pembayaran - Search and Scroll/Drag Functionality
 * Updated for additional columns (NIM and ID Peminjaman)
 */

document.addEventListener("DOMContentLoaded", function () {
  // Get DOM elements
  const searchInput = document.getElementById("searchInput");
  const tableContainer = document.getElementById("tableContainer");
  const tableRows = document.querySelectorAll(
    ".table-row:not(.table-header-row)",
  );

  // Variables for drag scrolling
  let isDown = false;
  let startY;
  let scrollTop;

  // Initialize search functionality
  initSearch();

  // Initialize drag scroll functionality
  initDragScroll();

  /**
   * Initialize search functionality
   */
  function initSearch() {
    searchInput.addEventListener("input", function () {
      const searchTerm = this.value.toLowerCase().trim();

      // Filter table rows based on search term
      tableRows.forEach((row) => {
        // Skip the no-data row if it exists
        if (row.querySelector('.no-data')) {
          return;
        }
        
        const rowText = row.textContent.toLowerCase();
        
        // Search in NIM, name, book title, payment status, and verification status
        if (searchTerm === "" || rowText.includes(searchTerm)) {
          row.classList.remove("hidden");
        } else {
          row.classList.add("hidden");
        }
      });
      
      // Show/hide no results message
      updateNoResultsMessage();
    });

    // Clear search when ESC key is pressed
    searchInput.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        this.value = "";
        tableRows.forEach((row) => row.classList.remove("hidden"));
        updateNoResultsMessage();
      }
    });
  }
  
  /**
   * Update no results message visibility
   */
  function updateNoResultsMessage() {
    const visibleRows = Array.from(tableRows).filter(row => 
      !row.classList.contains("hidden") && !row.querySelector('.no-data')
    );
    
    const noDataRow = document.querySelector('.no-data');
    const searchTerm = searchInput.value.toLowerCase().trim();
    
    if (visibleRows.length === 0 && searchTerm !== "" && !noDataRow) {
      // Create and show "no results" message - updated colspan to 11 for new columns
      const tbody = document.querySelector('.payment-table tbody');
      const noResultsRow = document.createElement('tr');
      noResultsRow.className = 'table-row no-results-row';
      noResultsRow.innerHTML = '<td colspan="11" class="no-data">Tidak ditemukan hasil pencarian.</td>';
      tbody.appendChild(noResultsRow);
    } else if (visibleRows.length > 0) {
      // Remove "no results" message if it exists
      const noResultsRow = document.querySelector('.no-results-row');
      if (noResultsRow) {
        noResultsRow.remove();
      }
    }
  }

  /**
   * Initialize drag scroll functionality
   */
  function initDragScroll() {
    // Mouse events for desktop
    tableContainer.addEventListener("mousedown", function (e) {
      // Don't start drag if clicking on badges or text
      if (e.target.classList.contains('status-badge') || 
          e.target.classList.contains('verification-badge')) {
        return;
      }
      
      isDown = true;
      tableContainer.style.cursor = "grabbing";
      startY = e.pageY - tableContainer.offsetTop;
      scrollTop = tableContainer.scrollTop;
      e.preventDefault();
    });

    tableContainer.addEventListener("mouseleave", function () {
      isDown = false;
      tableContainer.style.cursor = "grab";
    });

    tableContainer.addEventListener("mouseup", function () {
      isDown = false;
      tableContainer.style.cursor = "grab";
    });

    tableContainer.addEventListener("mousemove", function (e) {
      if (!isDown) return;
      e.preventDefault();
      const y = e.pageY - tableContainer.offsetTop;
      const walk = (y - startY) * 2; // Scroll speed multiplier
      tableContainer.scrollTop = scrollTop - walk;
    });

    // Touch events for mobile
    tableContainer.addEventListener("touchstart", function (e) {
      isDown = true;
      startY = e.touches[0].pageY - tableContainer.offsetTop;
      scrollTop = tableContainer.scrollTop;
    });

    tableContainer.addEventListener("touchend", function () {
      isDown = false;
    });

    tableContainer.addEventListener("touchmove", function (e) {
      if (!isDown) return;
      const y = e.touches[0].pageY - tableContainer.offsetTop;
      const walk = (y - startY) * 2;
      tableContainer.scrollTop = scrollTop - walk;
    });

    // Add wheel scroll event for better user experience
    tableContainer.addEventListener("wheel", function (e) {
      e.preventDefault();
      tableContainer.scrollTop += e.deltaY;
    });
  }

  // Add visual feedback when scrolling
  tableContainer.addEventListener("scroll", function () {
    // Add subtle shadow effect when scrolling
    if (tableContainer.scrollTop > 0) {
      tableContainer.style.boxShadow = "inset 0 5px 5px -5px rgba(0,0,0,0.2)";
    } else {
      tableContainer.style.boxShadow = "none";
    }
  });
  
  // Add hover effects for status badges
  document.addEventListener('mouseover', function(e) {
    if (e.target.classList.contains('status-badge') || 
        e.target.classList.contains('verification-badge')) {
      e.target.style.transform = 'scale(1.05)';
      e.target.style.transition = 'transform 0.2s ease';
    }
  });
  
  document.addEventListener('mouseout', function(e) {
    if (e.target.classList.contains('status-badge') || 
        e.target.classList.contains('verification-badge')) {
      e.target.style.transform = 'scale(1)';
    }
  });
});