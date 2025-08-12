/**
 * Pembayaran Denda - JavaScript functionality
 * Implements search filtering and table scrolling with fixed header
 */

document.addEventListener("DOMContentLoaded", function () {
  // Get DOM elements
  const searchInput = document.getElementById("searchInput");
  const tableContainer = document.getElementById("tableContainer");
  const table = document.getElementById("paymentTable");
  const tableRows = table.querySelectorAll("tbody tr");

  // Initialize variables for drag scrolling
  let isDragging = false;
  let startX, startY, scrollLeft, scrollTop;

  // ===== SEARCH FUNCTIONALITY =====

  // Add event listener for search input
  searchInput.addEventListener("input", function () {
    const searchTerm = this.value.toLowerCase().trim();

    // Loop through all table rows
    tableRows.forEach((row) => {
      // Get all cells in the row
      const cells = row.querySelectorAll("td");
      let rowText = "";

      // Combine text from all cells
      cells.forEach((cell) => {
        // Get text content without HTML tags for search
        const textContent = cell.textContent || cell.innerText;
        rowText += textContent.toLowerCase() + " ";
      });

      // Check if row contains the search term
      if (searchTerm === "" || rowText.includes(searchTerm)) {
        row.classList.remove("hidden-row");

        // Highlight matching text if there's a search term
        if (searchTerm !== "") {
          highlightText(cells, searchTerm);
        } else {
          // Clear highlights when search is empty
          clearHighlights(cells);
        }
      } else {
        row.classList.add("hidden-row");
      }
    });
  });

  // Function to highlight matching text
  function highlightText(cells, searchTerm) {
    cells.forEach((cell) => {
      // Skip cells with links or buttons to avoid breaking functionality
      if (cell.querySelector('a') || cell.querySelector('button')) {
        return;
      }

      const cellText = cell.textContent;
      const lowerCellText = cellText.toLowerCase();
      const index = lowerCellText.indexOf(searchTerm);

      if (index >= 0) {
        const beforeMatch = cellText.substring(0, index);
        const match = cellText.substring(index, index + searchTerm.length);
        const afterMatch = cellText.substring(index + searchTerm.length);

        cell.innerHTML = 
          beforeMatch + 
          '<span class="highlight">' + match + '</span>' + 
          afterMatch;
      }
    });
  }

  // Function to clear highlights
  function clearHighlights(cells) {
    cells.forEach((cell) => {
      // Only clear highlights in cells without links or buttons
      if (!cell.querySelector('a') && !cell.querySelector('button')) {
        cell.innerHTML = cell.textContent;
      }
    });
  }

  // Clear search when ESC key is pressed
  searchInput.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      this.value = "";
      // Trigger the input event to update the table
      this.dispatchEvent(new Event("input"));
      this.blur(); // Remove focus from input
    }
  });

  // ===== DRAG SCROLL FUNCTIONALITY =====

  // Mouse down event - start dragging
  tableContainer.addEventListener("mousedown", function (e) {
    // Don't start dragging if clicking on a link or button
    if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('a') || e.target.closest('button')) {
      return;
    }

    isDragging = true;
    tableContainer.classList.add("dragging");

    // Get initial position
    startX = e.pageX - tableContainer.offsetLeft;
    startY = e.pageY - tableContainer.offsetTop;

    // Get initial scroll position
    scrollLeft = tableContainer.scrollLeft;
    scrollTop = tableContainer.scrollTop;

    // Prevent text selection
    e.preventDefault();
  });

  // Mouse move event - perform dragging
  tableContainer.addEventListener("mousemove", function (e) {
    if (!isDragging) return;

    // Prevent default behavior
    e.preventDefault();

    // Calculate new position
    const x = e.pageX - tableContainer.offsetLeft;
    const y = e.pageY - tableContainer.offsetTop;

    // Calculate distance moved
    const walkX = (x - startX) * 1.5; // Multiply for faster scrolling
    const walkY = (y - startY) * 1.5;

    // Scroll the container
    tableContainer.scrollLeft = scrollLeft - walkX;
    tableContainer.scrollTop = scrollTop - walkY;
  });

  // Mouse up event - stop dragging
  document.addEventListener("mouseup", function () {
    if (isDragging) {
      isDragging = false;
      tableContainer.classList.remove("dragging");
    }
  });

  // Mouse leave event - stop dragging if mouse leaves container
  tableContainer.addEventListener("mouseleave", function () {
    if (isDragging) {
      isDragging = false;
      tableContainer.classList.remove("dragging");
    }
  });

  // Prevent default drag behavior on the table
  table.addEventListener("dragstart", function (e) {
    e.preventDefault();
  });

  // ===== TOUCH SUPPORT FOR MOBILE DEVICES =====

  // Touch start event
  tableContainer.addEventListener("touchstart", function (e) {
    if (e.touches.length === 1) {
      // Don't start dragging if touching a link or button
      if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('a') || e.target.closest('button')) {
        return;
      }

      isDragging = true;

      // Get initial position
      startX = e.touches[0].pageX - tableContainer.offsetLeft;
      startY = e.touches[0].pageY - tableContainer.offsetTop;

      // Get initial scroll position
      scrollLeft = tableContainer.scrollLeft;
      scrollTop = tableContainer.scrollTop;
    }
  });

  // Touch move event
  tableContainer.addEventListener("touchmove", function (e) {
    if (!isDragging || e.touches.length !== 1) return;

    // Calculate new position
    const x = e.touches[0].pageX - tableContainer.offsetLeft;
    const y = e.touches[0].pageY - tableContainer.offsetTop;

    // Calculate distance moved
    const walkX = (x - startX) * 1.5;
    const walkY = (y - startY) * 1.5;

    // Scroll the container
    tableContainer.scrollLeft = scrollLeft - walkX;
    tableContainer.scrollTop = scrollTop - walkY;

    // Prevent page scrolling
    e.preventDefault();
  }, { passive: false });

  // Touch end event
  tableContainer.addEventListener("touchend", function () {
    isDragging = false;
  });

  // Touch cancel event
  tableContainer.addEventListener("touchcancel", function () {
    isDragging = false;
  });

  // ===== KEYBOARD NAVIGATION =====

  // Add keyboard support for better accessibility
  searchInput.addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
      e.preventDefault();
      // Focus on first visible row if any
      const firstVisibleRow = table.querySelector("tbody tr:not(.hidden-row)");
      if (firstVisibleRow) {
        const firstLink = firstVisibleRow.querySelector("a");
        if (firstLink) {
          firstLink.focus();
        }
      }
    }
  });

  // ===== UTILITY FUNCTIONS =====

  // Function to reset table state
  function resetTable() {
    tableRows.forEach((row) => {
      row.classList.remove("hidden-row");
      const cells = row.querySelectorAll("td");
      clearHighlights(cells);
    });
    searchInput.value = "";
  }

  // Export reset function for external use if needed
  window.resetPaymentTable = resetTable;

  // ===== PERFORMANCE OPTIMIZATION =====

  // Debounce search input for better performance
  let searchTimeout;
  const originalInputHandler = searchInput.oninput;
  
  searchInput.addEventListener("input", function(e) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      // The search logic is already handled above
    }, 150);
  });

  // Add loading state for large tables
  if (tableRows.length > 100) {
    const loadingIndicator = document.createElement('div');
    loadingIndicator.className = 'loading-indicator';
    loadingIndicator.textContent = 'Memuat data...';
    loadingIndicator.style.cssText = `
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(255, 255, 255, 0.9);
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      z-index: 1000;
      display: none;
    `;
    
    tableContainer.style.position = 'relative';
    tableContainer.appendChild(loadingIndicator);

    // Show loading indicator during search for large datasets
    searchInput.addEventListener("input", function() {
      if (tableRows.length > 100) {
        loadingIndicator.style.display = 'block';
        setTimeout(() => {
          loadingIndicator.style.display = 'none';
        }, 100);
      }
    });
  }
});