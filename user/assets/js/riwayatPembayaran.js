/**
 * Riwayat Pembayaran - Search and Scroll/Drag Functionality (Mobile Responsive)
 */

document.addEventListener("DOMContentLoaded", function () {
  // Get DOM elements
  const searchInput = document.getElementById("searchInput");
  const tableContainer = document.getElementById("tableContainer");
  const mobileCardsContainer = document.getElementById("mobileCardsContainer");
  const tableRows = document.querySelectorAll(".table-row:not(.table-header-row)");
  const mobileCards = document.querySelectorAll(".payment-card");

  // Variables for drag scrolling
  let isDown = false;
  let startY;
  let scrollTop;

  /**
   * Search functionality for both desktop and mobile
   */
  function initSearch() {
    searchInput.addEventListener("input", function () {
      const searchTerm = this.value.toLowerCase().trim();

      // Filter table rows (desktop)
      tableRows.forEach((row) => {
        if (row.querySelector('.no-data')) {
          return;
        }
        
        const rowText = row.textContent.toLowerCase();
        
        if (searchTerm === "" || rowText.includes(searchTerm)) {
          row.classList.remove("hidden");
        } else {
          row.classList.add("hidden");
        }
      });

      // Filter mobile cards
      mobileCards.forEach((card) => {
        const cardSearchContent = card.getAttribute('data-search-content') || '';
        
        if (searchTerm === "" || cardSearchContent.includes(searchTerm)) {
          card.classList.remove("hidden");
        } else {
          card.classList.add("hidden");
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
        mobileCards.forEach((card) => card.classList.remove("hidden"));
        updateNoResultsMessage();
      }
    });
  }
  
  /**
   * Update no results message visibility for both desktop and mobile
   */
  function updateNoResultsMessage() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    
    // Desktop table
    const visibleRows = Array.from(tableRows).filter(row => 
      !row.classList.contains("hidden") && !row.querySelector('.no-data')
    );
    
    const noDataRow = document.querySelector('.no-data');
    
    if (visibleRows.length === 0 && searchTerm !== "" && !noDataRow && tableRows.length > 0) {
      const tbody = document.querySelector('.payment-table tbody');
      const noResultsRow = document.createElement('tr');
      noResultsRow.className = 'table-row no-results-row';
      noResultsRow.innerHTML = '<td colspan="9" class="no-data">Tidak ditemukan hasil pencarian.</td>';
      tbody.appendChild(noResultsRow);
    } else if (visibleRows.length > 0) {
      const noResultsRow = document.querySelector('.no-results-row');
      if (noResultsRow) {
        noResultsRow.remove();
      }
    }

    // Mobile cards
    const visibleCards = Array.from(mobileCards).filter(card => 
      !card.classList.contains("hidden")
    );
    
    const existingMobileNoResults = document.querySelector('.mobile-no-results');
    
    if (visibleCards.length === 0 && searchTerm !== "" && !existingMobileNoResults && mobileCards.length > 0) {
      const noResultsCard = document.createElement('div');
      noResultsCard.className = 'mobile-no-data mobile-no-results';
      noResultsCard.textContent = 'Tidak ditemukan hasil pencarian.';
      mobileCardsContainer.appendChild(noResultsCard);
    } else if (visibleCards.length > 0 && existingMobileNoResults) {
      existingMobileNoResults.remove();
    }
  }

  /**
   * Initialize drag scroll functionality for desktop table
   */
  function initDragScroll() {
    if (!tableContainer) return;

    // Mouse events for desktop
    tableContainer.addEventListener("mousedown", function (e) {
      // Don't start drag if clicking on badges or text
      if (e.target.classList.contains('status-badge') || 
          e.target.classList.contains('verification-badge') ||
          e.target.classList.contains('id-peminjaman')) {
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

    // Add wheel scroll event for better user experience
    tableContainer.addEventListener("wheel", function (e) {
      e.preventDefault();
      tableContainer.scrollTop += e.deltaY;
    });

    // Add visual feedback when scrolling
    tableContainer.addEventListener("scroll", function () {
      if (tableContainer.scrollTop > 0) {
        tableContainer.style.boxShadow = "inset 0 5px 5px -5px rgba(0,0,0,0.2)";
      } else {
        tableContainer.style.boxShadow = "none";
      }
    });
  }

  /**
   * Initialize mobile interactions
   */
  function initMobileInteractions() {
    // Add touch interactions for mobile cards
    mobileCards.forEach(card => {
      // Add touch feedback
      card.addEventListener('touchstart', function() {
        this.style.transform = 'scale(0.98)';
        this.style.transition = 'transform 0.1s ease';
      });

      card.addEventListener('touchend', function() {
        this.style.transform = 'scale(1)';
        setTimeout(() => {
          this.style.transition = 'transform 0.2s ease, box-shadow 0.2s ease';
        }, 100);
      });

      card.addEventListener('touchcancel', function() {
        this.style.transform = 'scale(1)';
        this.style.transition = 'transform 0.2s ease, box-shadow 0.2s ease';
      });
    });

    // Smooth scrolling for mobile cards container
    if (mobileCardsContainer) {
      let isScrolling = false;
      
      mobileCardsContainer.addEventListener('scroll', function() {
        if (!isScrolling) {
          requestAnimationFrame(() => {
            // Add subtle shadow when scrolling
            if (mobileCardsContainer.scrollTop > 0) {
              mobileCardsContainer.style.boxShadow = "inset 0 5px 10px -5px rgba(0,0,0,0.1)";
            } else {
              mobileCardsContainer.style.boxShadow = "none";
            }
            isScrolling = false;
          });
          isScrolling = true;
        }
      });
    }
  }

  /**
   * Add hover effects for interactive elements
   */
  function initHoverEffects() {
    document.addEventListener('mouseover', function(e) {
      if (e.target.classList.contains('status-badge') || 
          e.target.classList.contains('verification-badge') ||
          e.target.classList.contains('id-peminjaman')) {
        e.target.style.transform = 'scale(1.05)';
        e.target.style.transition = 'transform 0.2s ease';
      }
    });
    
    document.addEventListener('mouseout', function(e) {
      if (e.target.classList.contains('status-badge') || 
          e.target.classList.contains('verification-badge') ||
          e.target.classList.contains('id-peminjaman')) {
        e.target.style.transform = 'scale(1)';
      }
    });
  }

  /**
   * Handle responsive behavior on window resize
   */
  function handleResize() {
    window.addEventListener('resize', function() {
      // Clear any ongoing drag operations
      isDown = false;
      if (tableContainer) {
        tableContainer.style.cursor = "grab";
      }
      
      // Reset search if switching between desktop and mobile views
      const isMobile = window.innerWidth <= 768;
      if (isMobile && mobileCardsContainer) {
        // Ensure mobile cards are properly filtered
        const searchTerm = searchInput.value.toLowerCase().trim();
        if (searchTerm) {
          mobileCards.forEach((card) => {
            const cardSearchContent = card.getAttribute('data-search-content') || '';
            if (cardSearchContent.includes(searchTerm)) {
              card.classList.remove("hidden");
            } else {
              card.classList.add("hidden");
            }
          });
        }
      }
    });
  }

  /**
   * Initialize all functionality
   */
  function init() {
    initSearch();
    initDragScroll();
    initMobileInteractions();
    initHoverEffects();
    handleResize();
    
    // Add loading animation fadeout
    setTimeout(() => {
      document.body.style.opacity = '1';
      document.body.style.transition = 'opacity 0.3s ease';
    }, 100);
  }

  // Initialize everything
  init();

  // Keyboard shortcuts
  document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + F to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
      e.preventDefault();
      searchInput.focus();
      searchInput.select();
    }
    
    // Escape to clear search and blur input
    if (e.key === 'Escape' && document.activeElement === searchInput) {
      searchInput.blur();
    }
  });

  // Improve accessibility
  searchInput.addEventListener('focus', function() {
    this.style.outline = '2px solid #cee397';
    this.style.outlineOffset = '2px';
  });

  searchInput.addEventListener('blur', function() {
    this.style.outline = 'none';
  });

  // Add swipe gesture support for mobile cards (optional enhancement)
  let touchStartX = 0;
  let touchStartY = 0;
  
  mobileCards.forEach(card => {
    card.addEventListener('touchstart', function(e) {
      touchStartX = e.touches[0].clientX;
      touchStartY = e.touches[0].clientY;
    });
    
    card.addEventListener('touchmove', function(e) {
      if (!touchStartX || !touchStartY) return;
      
      const touchEndX = e.touches[0].clientX;
      const touchEndY = e.touches[0].clientY;
      
      const diffX = touchStartX - touchEndX;
      const diffY = touchStartY - touchEndY;
      
      // Prevent horizontal scroll interference
      if (Math.abs(diffX) > Math.abs(diffY)) {
        e.preventDefault();
      }
    });
  });
});