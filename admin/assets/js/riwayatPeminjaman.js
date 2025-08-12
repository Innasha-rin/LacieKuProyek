/**
 * Riwayat Peminjaman - Search and Scroll/Drag Functionality
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
          const rowText = row.textContent.toLowerCase();
  
          if (searchTerm === "" || rowText.includes(searchTerm)) {
            row.classList.remove("hidden");
          } else {
            row.classList.add("hidden");
          }
        });
      });
  
      // Clear search when ESC key is pressed
      searchInput.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
          this.value = "";
          tableRows.forEach((row) => row.classList.remove("hidden"));
        }
      });
    }
  
    /**
     * Initialize drag scroll functionality
     */
    function initDragScroll() {
      // Mouse events for desktop
      tableContainer.addEventListener("mousedown", function (e) {
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
  });
  