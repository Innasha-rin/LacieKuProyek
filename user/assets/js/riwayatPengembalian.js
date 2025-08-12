/**
 * Enhanced search functionality for both desktop table and mobile cards
 */
document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("searchInput");
  const tableContainer = document.getElementById("tableContainer");
  const tableRows = document.querySelectorAll(".table-row:not(.table-header)");

  // Create mobile card view container
  const mobileCardView = document.createElement("div");
  mobileCardView.className = "mobile-card-view";
  mobileCardView.id = "mobileCardView";
  
  // Add no results message element
  const noResultsElement = document.createElement("div");
  noResultsElement.className = "no-results";
  noResultsElement.textContent = "Tidak ada hasil yang ditemukan";
  
  // Insert mobile view after table
  tableContainer.appendChild(mobileCardView);
  tableContainer.appendChild(noResultsElement);

  // Function to create mobile cards from table data
  function createMobileCards() {
    mobileCardView.innerHTML = "";
    
    tableRows.forEach((row, index) => {
      const cells = row.querySelectorAll("td");
      if (cells.length === 0) return;

      const card = document.createElement("div");
      card.className = "return-card";
      card.setAttribute("data-row-index", index);

      const cardHeader = document.createElement("div");
      cardHeader.className = "return-card-header";

      const cardNumber = document.createElement("div");
      cardNumber.className = "return-card-number";
      cardNumber.textContent = `#${cells[0].textContent.trim()}`;

      const cardTitle = document.createElement("div");
      cardTitle.className = "return-card-title";
      cardTitle.textContent = cells[1].textContent.trim();

      cardHeader.appendChild(cardNumber);
      cardHeader.appendChild(cardTitle);

      const cardDetails = document.createElement("div");
      cardDetails.className = "return-card-details";

      // Create detail items
      const details = [
        { label: "Tanggal Pinjam", value: cells[2].textContent.trim() },
        { label: "Tanggal Kembali", value: cells[3].textContent.trim() },
        { label: "Status Buku", value: cells[4].textContent.trim(), className: "return-card-status" },
        { label: "Keterlambatan", value: cells[5].textContent.trim() + " hari" },
        { label: "Denda", value: cells[6].textContent.trim(), className: "return-card-fine" }
      ];

      details.forEach(detail => {
        const detailDiv = document.createElement("div");
        detailDiv.className = "return-card-detail";

        const labelDiv = document.createElement("div");
        labelDiv.className = "return-card-label";
        labelDiv.textContent = detail.label;

        const valueDiv = document.createElement("div");
        valueDiv.className = `return-card-value ${detail.className || ""}`;
        valueDiv.textContent = detail.value;

        detailDiv.appendChild(labelDiv);
        detailDiv.appendChild(valueDiv);
        cardDetails.appendChild(detailDiv);
      });

      card.appendChild(cardHeader);
      card.appendChild(cardDetails);
      mobileCardView.appendChild(card);
    });
  }

  // Create mobile cards on page load
  createMobileCards();

  // Enhanced search functionality for both desktop and mobile
  if (searchInput) {
    searchInput.addEventListener("input", function () {
      const searchTerm = this.value.toLowerCase().trim();
      let hasResults = false;

      // Filter desktop table rows
      tableRows.forEach((row) => {
        const rowText = row.textContent.toLowerCase();
        const shouldShow = rowText.includes(searchTerm);

        row.style.display = shouldShow ? "flex" : "none";

        if (shouldShow) {
          hasResults = true;
        }
      });

      // Filter mobile cards
      const mobileCards = document.querySelectorAll(".return-card");
      mobileCards.forEach((card) => {
        const cardText = card.textContent.toLowerCase();
        const shouldShow = cardText.includes(searchTerm);

        card.style.display = shouldShow ? "block" : "none";

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
        // Trigger the input event to update the table and cards
        this.dispatchEvent(new Event("input"));
      }
    });
  }

  /**
   * Enhanced drag to scroll functionality for both desktop and mobile
   */
  if (tableContainer) {
    let isDown = false;
    let startY;
    let scrollTop;
    let startX;
    let scrollLeft;

    // Mouse events for drag scrolling
    tableContainer.addEventListener("mousedown", (e) => {
      // Only enable drag scrolling on the table container, not on mobile cards
      if (window.innerWidth <= 768) return;
      
      isDown = true;
      tableContainer.classList.add("grabbing");
      startY = e.pageY - tableContainer.offsetTop;
      startX = e.pageX - tableContainer.offsetLeft;
      scrollTop = tableContainer.scrollTop;
      scrollLeft = tableContainer.scrollLeft;
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
      if (!isDown || window.innerWidth <= 768) return;
      e.preventDefault();
      const y = e.pageY - tableContainer.offsetTop;
      const x = e.pageX - tableContainer.offsetLeft;
      const walkY = (y - startY) * 2;
      const walkX = (x - startX) * 2;
      tableContainer.scrollTop = scrollTop - walkY;
      tableContainer.scrollLeft = scrollLeft - walkX;
    });

    // Touch events for mobile scrolling (natural scrolling for mobile cards)
    let touchStartY = 0;
    let touchStartX = 0;

    tableContainer.addEventListener(
      "touchstart",
      (e) => {
        touchStartY = e.touches[0].pageY;
        touchStartX = e.touches[0].pageX;
      },
      { passive: true }
    );

    tableContainer.addEventListener(
      "touchmove",
      (e) => {
        // Allow natural scrolling on mobile
        if (window.innerWidth <= 768) return;
        
        const touchY = e.touches[0].pageY;
        const touchX = e.touches[0].pageX;
        const walkY = (touchY - touchStartY) * 1.5;
        const walkX = (touchX - touchStartX) * 1.5;
        
        tableContainer.scrollTop -= walkY;
        tableContainer.scrollLeft -= walkX;
        
        touchStartY = touchY;
        touchStartX = touchX;
      },
      { passive: true }
    );
  }

  // Handle window resize to recreate mobile cards if needed
  let resizeTimeout;
  window.addEventListener("resize", function () {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
      if (window.innerWidth <= 768) {
        createMobileCards();
        // Reapply current search filter
        if (searchInput && searchInput.value) {
          searchInput.dispatchEvent(new Event("input"));
        }
      }
    }, 250);
  });

  // Add smooth scrolling for better UX
  if (tableContainer) {
    tableContainer.style.scrollBehavior = "smooth";
  }
});