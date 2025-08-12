/**
 * Enhanced Riwayat Peminjaman - Mobile Cards and Desktop Table with Touch/Scroll Functionality
 */

document.addEventListener("DOMContentLoaded", function () {
  // Get DOM elements
  const searchInput = document.getElementById("searchInput");
  const tableContainer = document.getElementById("tableContainer");
  const tableRows = document.querySelectorAll(".table-row:not(.table-header-row)");

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

  // Variables for drag/touch scrolling
  let isDown = false;
  let startY;
  let startX;
  let scrollTop;
  let scrollLeft;
  let lastTouchY;
  let lastTouchX;

  // Touch detection
  const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

  // Initialize functionality
  initSearch();
  initScrolling();
  initMobileOptimizations();
  createMobileCards();

  /**
   * Function to create mobile cards from table data
   */
  function createMobileCards() {
    mobileCardView.innerHTML = "";
    
    tableRows.forEach((row, index) => {
      const cells = row.querySelectorAll("td");
      if (cells.length === 0) return;

      const card = document.createElement("div");
      card.className = "borrowing-card";
      card.setAttribute("data-row-index", index);

      const cardHeader = document.createElement("div");
      cardHeader.className = "borrowing-card-header";

      const cardNumber = document.createElement("div");
      cardNumber.className = "borrowing-card-number";
      cardNumber.textContent = `#${cells[0].textContent.trim()}`;

      const cardTitle = document.createElement("div");
      cardTitle.className = "borrowing-card-title";
      cardTitle.textContent = cells[1].textContent.trim();

      cardHeader.appendChild(cardNumber);
      cardHeader.appendChild(cardTitle);

      const cardDetails = document.createElement("div");
      cardDetails.className = "borrowing-card-details";

      // Create detail items for borrowing history (4 columns: No, Judul, Tanggal Pinjam, Jatuh Tempo)
      const details = [
        { label: "Tanggal Pinjam", value: cells[2].textContent.trim() },
        { label: "Jatuh Tempo", value: cells[3].textContent.trim() }
      ];

      details.forEach(detail => {
        const detailDiv = document.createElement("div");
        detailDiv.className = "borrowing-card-detail";

        const labelDiv = document.createElement("div");
        labelDiv.className = "borrowing-card-label";
        labelDiv.textContent = detail.label;

        const valueDiv = document.createElement("div");
        valueDiv.className = "borrowing-card-value";
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

  /**
   * Initialize search functionality with mobile optimizations
   */
  function initSearch() {
    // Debounce search for better performance
    let searchTimeout;
    
    searchInput.addEventListener("input", function () {
      clearTimeout(searchTimeout);
      const searchTerm = this.value;
      
      searchTimeout = setTimeout(() => {
        performSearch(searchTerm);
      }, 300); // Debounce for 300ms
    });

    // Clear search when ESC key is pressed
    searchInput.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        this.value = "";
        showAllRows();
      }
    });

    // Prevent zoom on iOS when focusing search input
    if (isTouchDevice) {
      searchInput.addEventListener('touchstart', function() {
        searchInput.style.fontSize = '16px';
      });
    }
  }

  /**
   * Enhanced search functionality for both desktop and mobile
   */
  function performSearch(searchTerm) {
    const normalizedSearch = searchTerm.toLowerCase().trim();
    let hasResults = false;

    // Filter desktop table rows
    tableRows.forEach((row) => {
      const rowText = row.textContent.toLowerCase();
      const shouldShow = normalizedSearch === "" || rowText.includes(normalizedSearch);
      
      if (shouldShow) {
        row.classList.remove("hidden");
        hasResults = true;
      } else {
        row.classList.add("hidden");
      }
    });

    // Filter mobile cards
    const mobileCards = document.querySelectorAll(".borrowing-card");
    mobileCards.forEach((card) => {
      const cardText = card.textContent.toLowerCase();
      const shouldShow = normalizedSearch === "" || cardText.includes(normalizedSearch);

      card.style.display = shouldShow ? "block" : "none";

      if (shouldShow) {
        hasResults = true;
      }
    });

    // Show/hide no results message
    noResultsElement.style.display = hasResults ? "none" : "block";
  }

  /**
   * Show all table rows and cards
   */
  function showAllRows() {
    tableRows.forEach((row) => row.classList.remove("hidden"));
    
    // Show all mobile cards
    const mobileCards = document.querySelectorAll(".borrowing-card");
    mobileCards.forEach((card) => {
      card.style.display = "block";
    });
    
    noResultsElement.style.display = "none";
  }

  /**
   * Initialize enhanced scrolling functionality
   */
  function initScrolling() {
    if (isTouchDevice) {
      initTouchScrolling();
    } else {
      initMouseScrolling();
    }
    
    // Enhanced wheel scrolling for all devices
    initWheelScrolling();
    
    // Add scroll indicators
    initScrollIndicators();
  }

  /**
   * Initialize touch scrolling for mobile devices
   */
  function initTouchScrolling() {
    let touchStartTime;
    
    tableContainer.addEventListener("touchstart", function (e) {
      // Only enable drag scrolling on desktop table, not on mobile cards
      if (window.innerWidth <= 768) return;
      
      isDown = true;
      touchStartTime = Date.now();
      const touch = e.touches[0];
      startY = touch.pageY - tableContainer.offsetTop;
      startX = touch.pageX - tableContainer.offsetLeft;
      scrollTop = tableContainer.scrollTop;
      scrollLeft = tableContainer.scrollLeft;
      lastTouchY = touch.pageY;
      lastTouchX = touch.pageX;
      
      // Stop any ongoing momentum scrolling
      tableContainer.style.overflow = 'hidden';
      setTimeout(() => {
        if (tableContainer) {
          tableContainer.style.overflow = 'auto';
        }
      }, 10);
    }, { passive: true });

    tableContainer.addEventListener("touchend", function (e) {
      if (window.innerWidth <= 768) return;
      
      isDown = false;
      const touchEndTime = Date.now();
      const touchDuration = touchEndTime - touchStartTime;
      
      // Implement momentum scrolling for quick swipes
      if (touchDuration < 200 && e.changedTouches) {
        const touch = e.changedTouches[0];
        const deltaY = lastTouchY - touch.pageY;
        const deltaX = lastTouchX - touch.pageX;
        
        if (Math.abs(deltaY) > 20 || Math.abs(deltaX) > 20) {
          implementMomentumScroll(deltaY, deltaX);
        }
      }
    }, { passive: true });

    tableContainer.addEventListener("touchmove", function (e) {
      if (!isDown || window.innerWidth <= 768) return;
      
      const touch = e.touches[0];
      const y = touch.pageY - tableContainer.offsetTop;
      const x = touch.pageX - tableContainer.offsetLeft;
      const walkY = (y - startY) * 1.5;
      const walkX = (x - startX) * 1.5;
      
      tableContainer.scrollTop = scrollTop - walkY;
      tableContainer.scrollLeft = scrollLeft - walkX;
      
      lastTouchY = touch.pageY;
      lastTouchX = touch.pageX;
      
      // Prevent page scrolling when scrolling table
      if (Math.abs(walkY) > 10 || Math.abs(walkX) > 10) {
        e.preventDefault();
      }
    }, { passive: false });
  }

  /**
   * Initialize mouse scrolling for desktop
   */
  function initMouseScrolling() {
    tableContainer.addEventListener("mousedown", function (e) {
      // Only enable drag scrolling on the desktop table, not on mobile cards
      if (window.innerWidth <= 768) return;
      
      isDown = true;
      tableContainer.classList.add("grabbing");
      startY = e.pageY - tableContainer.offsetTop;
      startX = e.pageX - tableContainer.offsetLeft;
      scrollTop = tableContainer.scrollTop;
      scrollLeft = tableContainer.scrollLeft;
      e.preventDefault();
    });

    tableContainer.addEventListener("mouseleave", function () {
      isDown = false;
      tableContainer.classList.remove("grabbing");
    });

    tableContainer.addEventListener("mouseup", function () {
      isDown = false;
      tableContainer.classList.remove("grabbing");
    });

    tableContainer.addEventListener("mousemove", function (e) {
      if (!isDown || window.innerWidth <= 768) return;
      e.preventDefault();
      
      const y = e.pageY - tableContainer.offsetTop;
      const x = e.pageX - tableContainer.offsetLeft;
      const walkY = (y - startY) * 2;
      const walkX = (x - startX) * 2;
      
      tableContainer.scrollTop = scrollTop - walkY;
      tableContainer.scrollLeft = scrollLeft - walkX;
    });
  }

  /**
   * Enhanced wheel scrolling
   */
  function initWheelScrolling() {
    tableContainer.addEventListener("wheel", function (e) {
      e.preventDefault();
      
      // Smooth scrolling with acceleration
      const delta = e.deltaY || e.detail || e.wheelDelta;
      const scrollAmount = Math.abs(delta) > 100 ? delta : delta * 2;
      
      tableContainer.scrollTop += scrollAmount;
    }, { passive: false });
  }

  /**
   * Implement momentum scrolling for touch devices
   */
  function implementMomentumScroll(deltaY, deltaX) {
    let momentumY = deltaY * 3;
    let momentumX = deltaX * 3;
    const friction = 0.95;
    const minMomentum = 1;
    
    function animateMomentum() {
      if (Math.abs(momentumY) < minMomentum && Math.abs(momentumX) < minMomentum) {
        return;
      }
      
      tableContainer.scrollTop += momentumY;
      tableContainer.scrollLeft += momentumX;
      
      momentumY *= friction;
      momentumX *= friction;
      
      requestAnimationFrame(animateMomentum);
    }
    
    requestAnimationFrame(animateMomentum);
  }

  /**
   * Initialize scroll indicators
   */
  function initScrollIndicators() {
    tableContainer.addEventListener("scroll", function () {
      const scrollTop = tableContainer.scrollTop;
      const scrollLeft = tableContainer.scrollLeft;
      const maxScrollTop = tableContainer.scrollHeight - tableContainer.clientHeight;
      const maxScrollLeft = tableContainer.scrollWidth - tableContainer.clientWidth;
      
      // Add shadow effects based on scroll position
      let boxShadow = '';
      
      if (scrollTop > 0) {
        boxShadow += 'inset 0 5px 5px -5px rgba(0,0,0,0.2)';
      }
      
      if (scrollLeft > 0) {
        boxShadow += (boxShadow ? ', ' : '') + 'inset 5px 0 5px -5px rgba(0,0,0,0.2)';
      }
      
      if (scrollTop < maxScrollTop) {
        boxShadow += (boxShadow ? ', ' : '') + 'inset 0 -5px 5px -5px rgba(0,0,0,0.2)';
      }
      
      if (scrollLeft < maxScrollLeft) {
        boxShadow += (boxShadow ? ', ' : '') + 'inset -5px 0 5px -5px rgba(0,0,0,0.2)';
      }
      
      tableContainer.style.boxShadow = boxShadow || 'none';
    });
  }

  /**
   * Initialize mobile-specific optimizations
   */
  function initMobileOptimizations() {
    // Improve touch responsiveness
    if (isTouchDevice) {
      // Add touch feedback to table rows (desktop)
      tableRows.forEach(row => {
        row.addEventListener('touchstart', function() {
          this.style.backgroundColor = 'rgba(243, 243, 232, 0.7)';
        }, { passive: true });
        
        row.addEventListener('touchend', function() {
          setTimeout(() => {
            this.style.backgroundColor = '';
          }, 150);
        }, { passive: true });
      });
      
      // Add touch feedback to mobile cards
      setTimeout(() => {
        const mobileCards = document.querySelectorAll(".borrowing-card");
        mobileCards.forEach(card => {
          card.addEventListener('touchstart', function() {
            this.style.backgroundColor = 'rgba(206, 227, 151, 0.2)';
          }, { passive: true });
          
          card.addEventListener('touchend', function() {
            setTimeout(() => {
              this.style.backgroundColor = '';
            }, 150);
          }, { passive: true });
        });
      }, 100);
      
      // Optimize scroll performance on mobile
      tableContainer.style.webkitOverflowScrolling = 'touch';
      tableContainer.style.overflowScrolling = 'touch';
    }
    
    // Handle orientation changes
    window.addEventListener('orientationchange', function() {
      setTimeout(() => {
        // Recalculate dimensions after orientation change
        updateContainerDimensions();
        // Recreate mobile cards if needed
        if (window.innerWidth <= 768) {
          createMobileCards();
          // Reapply current search filter
          if (searchInput && searchInput.value) {
            performSearch(searchInput.value);
          }
        }
      }, 100);
    });
    
    // Handle window resize
    let resizeTimeout;
    window.addEventListener('resize', function() {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(() => {
        // Update container dimensions on resize
        updateContainerDimensions();
        // Recreate mobile cards if needed
        if (window.innerWidth <= 768) {
          createMobileCards();
          // Reapply current search filter
          if (searchInput && searchInput.value) {
            performSearch(searchInput.value);
          }
        }
      }, 250);
    });
  }

  /**
   * Update container dimensions based on screen size
   */
  function updateContainerDimensions() {
    const screenWidth = window.innerWidth;
    const screenHeight = window.innerHeight;
    
    // Adjust table container height based on screen size
    let newHeight;
    if (screenWidth <= 480) {
      newHeight = Math.min(screenHeight * 0.5, 350);
    } else if (screenWidth <= 768) {
      newHeight = Math.min(screenHeight * 0.6, 450);
    } else if (screenWidth <= 991) {
      newHeight = Math.min(screenHeight * 0.65, 500);
    } else {
      newHeight = 602; // Default desktop height
    }
    
    tableContainer.style.height = newHeight + 'px';
  }

  // Initialize container dimensions
  updateContainerDimensions();

  // Performance monitoring for mobile
  if (isTouchDevice) {
    let performanceCheckInterval = setInterval(() => {
      // Monitor performance and adjust scroll sensitivity if needed
      const scrollHeight = tableContainer.scrollHeight;
      const clientHeight = tableContainer.clientHeight;
      
      if (scrollHeight > clientHeight * 10) {
        // Very long table, reduce scroll sensitivity
        clearInterval(performanceCheckInterval);
        console.log('Large table detected, optimizing scroll performance');
      }
    }, 5000);
  }
});