/**
 * Search functionality for filtering table rows
 */
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const tableContainer = document.getElementById("tableContainer");
    const tableRows = document.querySelectorAll(".table-row:not(.table-header)");
  
    // Add no results message element
    const noResultsElement = document.createElement("div");
    noResultsElement.className = "no-results";
    noResultsElement.textContent = "Tidak ada hasil yang ditemukan";
    tableContainer.appendChild(noResultsElement);
  
    // Search functionality
    if (searchInput) {
      searchInput.addEventListener("input", function () {
        const searchTerm = this.value.toLowerCase().trim();
        let hasResults = false;
  
        tableRows.forEach((row) => {
          const rowText = row.textContent.toLowerCase();
          const shouldShow = rowText.includes(searchTerm);
  
          row.style.display = shouldShow ? "flex" : "none";
  
          if (shouldShow) {
            hasResults = true;
          }
        });
  
        // Show/hide no results message
        noResultsElement.style.display = hasResults ? "none" : "block";
      });
  
      // Clear search when ESC key is pressed
      searchInput.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
          this.value = "";
          // Trigger the input event to update the table
          this.dispatchEvent(new Event("input"));
        }
      });
    }
  
    /**
     * Drag to scroll functionality
     */
    if (tableContainer) {
      let isDown = false;
      let startY;
      let scrollTop;
  
      // Mouse events for drag scrolling
      tableContainer.addEventListener("mousedown", (e) => {
        isDown = true;
        tableContainer.classList.add("grabbing");
        startY = e.pageY - tableContainer.offsetTop;
        scrollTop = tableContainer.scrollTop;
        e.preventDefault();
      });
  
      tableContainer.addEventListener("mouseleave", () => {
        isDown = false;
        tableContainer.classList.remove("grabbing");
      });
  
      tableContainer.addEventListener("mouseup", () => {
        isDown = false;
        tableContainer.classList.remove("grabbing");
      });
  
      tableContainer.addEventListener("mousemove", (e) => {
        if (!isDown) return;
        e.preventDefault();
        const y = e.pageY - tableContainer.offsetTop;
        const walk = (y - startY) * 2; // Scroll speed multiplier
        tableContainer.scrollTop = scrollTop - walk;
      });
  
      // Touch events for mobile drag scrolling
      tableContainer.addEventListener(
        "touchstart",
        (e) => {
          isDown = true;
          startY = e.touches[0].pageY - tableContainer.offsetTop;
          scrollTop = tableContainer.scrollTop;
        },
        { passive: true },
      );
  
      tableContainer.addEventListener(
        "touchend",
        () => {
          isDown = false;
        },
        { passive: true },
      );
  
      tableContainer.addEventListener(
        "touchmove",
        (e) => {
          if (!isDown) return;
          const y = e.touches[0].pageY - tableContainer.offsetTop;
          const walk = (y - startY) * 2;
          tableContainer.scrollTop = scrollTop - walk;
        },
        { passive: true },
      );
    }
  });
  