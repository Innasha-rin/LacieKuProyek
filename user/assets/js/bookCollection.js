document.addEventListener("DOMContentLoaded", function () {
  // Elements
  const searchInput = document.getElementById("searchInput");
  const tableBody = document.getElementById("tableBody");
  const tableRows = tableBody.querySelectorAll(".table-row");

  // Search functionality
  searchInput.addEventListener("input", function () {
    const searchTerm = this.value.toLowerCase();

    tableRows.forEach((row) => {
      const titleCell = row.querySelector(".title-column");
      const title = titleCell.textContent.toLowerCase();

      if (title.includes(searchTerm)) {
        row.style.display = window.innerWidth <= 768 ? "block" : "flex";
      } else {
        row.style.display = "none";
      }
    });
  });

  // Handle window resize to update display style
  window.addEventListener("resize", function() {
    const visibleRows = Array.from(tableRows).filter(row => 
      row.style.display !== "none"
    );
    
    visibleRows.forEach(row => {
      row.style.display = window.innerWidth <= 768 ? "block" : "flex";
    });
  });

  // Drag to scroll functionality (only for desktop)
  let isDown = false;
  let startY;
  let scrollTop;

  function initScrollFunctionality() {
    if (window.innerWidth > 768) {
      tableBody.addEventListener("mousedown", handleMouseDown);
      tableBody.addEventListener("mouseleave", handleMouseLeave);
      tableBody.addEventListener("mouseup", handleMouseUp);
      tableBody.addEventListener("mousemove", handleMouseMove);
    } else {
      // Remove desktop scroll listeners on mobile
      tableBody.removeEventListener("mousedown", handleMouseDown);
      tableBody.removeEventListener("mouseleave", handleMouseLeave);
      tableBody.removeEventListener("mouseup", handleMouseUp);
      tableBody.removeEventListener("mousemove", handleMouseMove);
    }
  }

  function handleMouseDown(e) {
    isDown = true;
    tableBody.classList.add("active");
    startY = e.pageY - tableBody.offsetTop;
    scrollTop = tableBody.scrollTop;
  }

  function handleMouseLeave() {
    isDown = false;
    tableBody.classList.remove("active");
  }

  function handleMouseUp() {
    isDown = false;
    tableBody.classList.remove("active");
  }

  function handleMouseMove(e) {
    if (!isDown) return;
    e.preventDefault();
    const y = e.pageY - tableBody.offsetTop;
    const walk = (y - startY) * 2; // Scroll speed multiplier
    tableBody.scrollTop = scrollTop - walk;
  }

  // Initialize scroll functionality based on screen size
  initScrollFunctionality();

  // Re-initialize on window resize
  window.addEventListener("resize", initScrollFunctionality);

  // Touch events for mobile (keep existing functionality)
  tableBody.addEventListener(
    "touchstart",
    (e) => {
      isDown = true;
      tableBody.classList.add("active");
      startY = e.touches[0].pageY - tableBody.offsetTop;
      scrollTop = tableBody.scrollTop;
    },
    { passive: false },
  );

  tableBody.addEventListener(
    "touchend",
    () => {
      isDown = false;
      tableBody.classList.remove("active");
    },
    { passive: false },
  );

  tableBody.addEventListener(
    "touchcancel",
    () => {
      isDown = false;
      tableBody.classList.remove("active");
    },
    { passive: false },
  );

  tableBody.addEventListener(
    "touchmove",
    (e) => {
      if (!isDown) return;
      e.preventDefault();
      const y = e.touches[0].pageY - tableBody.offsetTop;
      const walk = (y - startY) * 2;
      tableBody.scrollTop = scrollTop - walk;
    },
    { passive: false },
  );

  // Enhanced PINJAM action handling
  function handlePinjamClick(event) {
    // Prevent default if it's a link
    event.preventDefault();
    
    const row = event.target.closest('.table-row');
    const titleElement = row.querySelector('.title-column a') || row.querySelector('.title-column');
    const bookTitle = titleElement.textContent.trim();
    
    // Get the book ID from the link href if available
    const pinjamLink = event.target.getAttribute('href');
    
    if (pinjamLink) {
      // If there's a link, navigate to it
      window.location.href = pinjamLink;
    } else {
      // Fallback alert
      alert(`Anda akan meminjam buku: ${bookTitle}`);
    }
  }

  // Add event listeners to all PINJAM buttons/links
  const actionCells = document.querySelectorAll(".action-column a, .action-column");
  actionCells.forEach((cell) => {
    if (cell.textContent.includes("PINJAM")) {
      cell.addEventListener("click", handlePinjamClick);
    }
  });

  // Add data attributes for mobile labels (if not already present)
  function addDataLabels() {
    tableRows.forEach(row => {
      const cells = row.querySelectorAll('.table-cell');
      cells.forEach(cell => {
        if (cell.classList.contains('number-column') && !cell.hasAttribute('data-label')) {
          cell.setAttribute('data-label', 'No');
        } else if (cell.classList.contains('title-column') && !cell.hasAttribute('data-label')) {
          cell.setAttribute('data-label', 'Judul');
        } else if (cell.classList.contains('author-column') && !cell.hasAttribute('data-label')) {
          cell.setAttribute('data-label', 'Penulis');
        } else if (cell.classList.contains('publisher-column') && !cell.hasAttribute('data-label')) {
          cell.setAttribute('data-label', 'Penerbit');
        } else if (cell.classList.contains('action-column') && !cell.hasAttribute('data-label')) {
          cell.setAttribute('data-label', 'Aksi');
        }
      });
    });
  }

  // Initialize data labels
  addDataLabels();
});