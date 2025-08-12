/**
 * Enhanced Mobile Responsive Notification System
 * Fixed mobile click/tap functionality
 */

document.addEventListener("DOMContentLoaded", function () {
    // Get notification containers
    const notificationTable = document.querySelector("#notificationRows");
    const notificationContainer = document.querySelector(".notification-container");
    
    // Create mobile notification list if it doesn't exist
    let mobileNotificationList = document.querySelector(".mobile-notification-list");
    if (!mobileNotificationList) {
      mobileNotificationList = document.createElement("div");
      mobileNotificationList.className = "mobile-notification-list";
      mobileNotificationList.innerHTML = '<div class="loading-indicator">Memuat notifikasi...</div>';
      notificationContainer.appendChild(mobileNotificationList);
    }
  
    // Variables to track dragging state
    let isDragging = false;
    let startY = 0;
    let scrollTop = 0;
    let startX = 0;
    let scrollLeft = 0;
    let currentContainer = null;
    
    // Enhanced touch tracking
    let touchStartTime = 0;
    let touchMoved = false;
    let touchThreshold = 15; // Increased threshold for better touch detection
    let tapTimeout = 500; // Increased timeout for better tap recognition
    let initialTouch = { x: 0, y: 0 };
    let currentTouch = { x: 0, y: 0 };
  
    // Detect if mobile view
    function isMobileView() {
      return window.innerWidth <= 768;
    }
  
    // Convert table row data to mobile card with proper click handling
    function createMobileCard(rowData, index) {
      const { number, time, date, status, link, notificationId } = rowData;
      const isUnread = status.toLowerCase().includes('belum') || status.toLowerCase().includes('unread');
      
      return `
        <div class="mobile-notification-card ${isUnread ? 'unread' : 'read'}" 
             data-index="${index}" 
             data-link="${link}"
             data-notification-id="${notificationId || ''}"
             style="animation-delay: ${index * 0.1}s">
          <div class="mobile-card-header">
            <div class="mobile-card-number">#${number}</div>
            <div class="mobile-card-status ${isUnread ? 'unread' : 'read'}">${status}</div>
          </div>
          <div class="mobile-card-content">
            <div class="mobile-card-time">${time}</div>
            <div class="mobile-card-date">${date}</div>
          </div>
          ${isUnread ? '<div class="mobile-card-icon"></div>' : ''}
        </div>
      `;
    }
  
    // Extract data from table rows with better data extraction
    function extractTableData() {
      const rows = document.querySelectorAll('.table-row');
      const data = [];
      
      rows.forEach((row, index) => {
        const cells = row.querySelectorAll('.table-cell');
        const link = row.querySelector('a')?.href || '#';
        
        // Try to extract notification ID from onclick or data attributes
        let notificationId = '';
        const onclickAttr = row.getAttribute('onclick');
        if (onclickAttr) {
          const match = onclickAttr.match(/viewNotificationDetail\((\d+)\)/);
          if (match) {
            notificationId = match[1];
          }
        }
        
        if (cells.length >= 4) {
          data.push({
            number: cells[0]?.textContent?.trim() || (index + 1),
            time: cells[1]?.textContent?.trim() || '-',
            date: cells[2]?.textContent?.trim() || '-',
            status: cells[3]?.textContent?.trim() || 'Unknown',
            link: link,
            notificationId: notificationId,
            rowElement: row // Keep reference to original row
          });
        }
      });
      
      return data;
    }
  
    // Handle mobile card click
    function handleMobileCardClick(card) {
      const notificationId = card.dataset.notificationId;
      const link = card.dataset.link;
      
      // Add visual feedback
      card.style.transform = 'scale(0.95)';
      card.style.transition = 'transform 0.1s ease';
      
      setTimeout(() => {
        card.style.transform = 'scale(1)';
        
        // Try to use the notification ID first
        if (notificationId) {
          // Call the global function if it exists
          if (typeof viewNotificationDetail === 'function') {
            viewNotificationDetail(notificationId);
          } else {
            // Fallback to direct navigation
            window.location.href = `notificationPage2.php?id=${notificationId}`;
          }
        } else if (link && link !== '#') {
          window.location.href = link;
        } else {
          // Find the corresponding table row and trigger its click
          const index = parseInt(card.dataset.index);
          const tableRows = document.querySelectorAll('.table-row');
          if (tableRows[index]) {
            tableRows[index].click();
          }
        }
      }, 100);
    }
  
    // Update mobile cards based on table data
    function updateMobileCards() {
      if (!isMobileView()) return;
      
      const tableData = extractTableData();
      
      if (tableData.length === 0) {
        mobileNotificationList.innerHTML = '<div class="no-notifications">Tidak ada notifikasi</div>';
        return;
      }
      
      const mobileCards = tableData.map((data, index) => createMobileCard(data, index)).join('');
      mobileNotificationList.innerHTML = mobileCards;
      
      // Add enhanced click event listeners to mobile cards
      const cards = mobileNotificationList.querySelectorAll('.mobile-notification-card');
      cards.forEach((card, index) => {
        // Variables for this specific card
        let cardTouchStart = { x: 0, y: 0, time: 0 };
        let cardTouchMoved = false;
        
        // Touch start
        card.addEventListener('touchstart', function(e) {
          cardTouchStart.x = e.touches[0].clientX;
          cardTouchStart.y = e.touches[0].clientY;
          cardTouchStart.time = Date.now();
          cardTouchMoved = false;
          
          // Visual feedback
          this.style.transform = 'scale(0.98)';
          this.style.transition = 'transform 0.1s ease';
        }, { passive: true });
        
        // Touch move
        card.addEventListener('touchmove', function(e) {
          const deltaX = Math.abs(e.touches[0].clientX - cardTouchStart.x);
          const deltaY = Math.abs(e.touches[0].clientY - cardTouchStart.y);
          
          if (deltaX > touchThreshold || deltaY > touchThreshold) {
            cardTouchMoved = true;
            // Reset visual feedback if moved too much
            this.style.transform = 'scale(1)';
          }
        }, { passive: true });
        
        // Touch end
        card.addEventListener('touchend', function(e) {
          e.preventDefault(); // Prevent double-tap zoom
          
          const touchDuration = Date.now() - cardTouchStart.time;
          
          // Reset visual feedback
          this.style.transform = 'scale(1)';
          this.style.transition = 'transform 0.2s ease';
          
          // Check if it's a valid tap
          if (!cardTouchMoved && touchDuration < tapTimeout) {
            handleMobileCardClick(this);
          }
        });
        
        // Mouse events for desktop when in mobile view
        card.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          
          // Only handle click if not on mobile or if touch events didn't fire
          if (!('ontouchstart' in window)) {
            handleMobileCardClick(this);
          }
        });
        
        // Prevent context menu on long press
        card.addEventListener('contextmenu', function(e) {
          e.preventDefault();
        });
      });
    }
  
    // Handle window resize
    function handleResize() {
      updateMobileCards();
      checkScrollability();
    }
  
    // Add visual indicator class when dragging
    function addDraggingClass(container) {
      container.classList.add("dragging");
      document.body.style.userSelect = 'none';
    }
  
    // Remove visual indicator class when not dragging
    function removeDraggingClass(container) {
      container.classList.remove("dragging");
      document.body.style.userSelect = '';
    }
  
    // Setup drag scrolling for a container with improved touch handling
    function setupDragScroll(container) {
      if (!container) return;
  
      // Mouse events (desktop)
      container.addEventListener("mousedown", function (e) {
        // Don't interfere with clickable elements
        if (e.target.closest('.table-row') || e.target.closest('.mobile-notification-card')) {
          return;
        }
        
        isDragging = true;
        currentContainer = container;
        startY = e.pageY;
        startX = e.pageX;
        scrollTop = container.scrollTop;
        scrollLeft = container.scrollLeft;
        addDraggingClass(container);
        e.preventDefault();
      });
  
      // Enhanced touch events for mobile - only for container scrolling
      container.addEventListener("touchstart", function (e) {
        // Don't handle touch on notification cards, let them handle their own touch events
        if (e.target.closest('.mobile-notification-card') || e.target.closest('.table-row')) {
          return;
        }
        
        touchStartTime = Date.now();
        touchMoved = false;
        
        initialTouch.x = e.touches[0].pageX;
        initialTouch.y = e.touches[0].pageY;
        startY = e.touches[0].pageY;
        startX = e.touches[0].pageX;
        scrollTop = container.scrollTop;
        scrollLeft = container.scrollLeft;
        
        isDragging = true;
        currentContainer = container;
        addDraggingClass(container);
      }, { passive: true });
  
      container.addEventListener("touchmove", function (e) {
        if (!isDragging || currentContainer !== container) return;
        
        currentTouch.x = e.touches[0].pageX;
        currentTouch.y = e.touches[0].pageY;
        
        const deltaY = Math.abs(currentTouch.y - initialTouch.y);
        const deltaX = Math.abs(currentTouch.x - initialTouch.x);
        
        if (deltaY > touchThreshold || deltaX > touchThreshold) {
          touchMoved = true;
          
          const walkY = (currentTouch.y - startY) * 1.2;
          const walkX = (currentTouch.x - startX) * 1.2;
          
          container.scrollTop = scrollTop - walkY;
          container.scrollLeft = scrollLeft - walkX;
          
          e.preventDefault();
        }
      }, { passive: false });
  
      container.addEventListener("touchend", function (e) {
        if (isDragging && currentContainer === container) {
          isDragging = false;
          currentContainer = null;
          removeDraggingClass(container);
        }
        
        touchMoved = false;
        touchStartTime = 0;
      });
    }
  
    // Global mouse events for desktop
    document.addEventListener("mousemove", function (e) {
      if (!isDragging || !currentContainer) return;
  
      const y = e.pageY;
      const x = e.pageX;
      const walkY = (y - startY) * 1.2;
      const walkX = (x - startX) * 1.2;
      
      currentContainer.scrollTop = scrollTop - walkY;
      currentContainer.scrollLeft = scrollLeft - walkX;
      e.preventDefault();
    });
  
    document.addEventListener("mouseup", function () {
      if (isDragging && currentContainer) {
        isDragging = false;
        removeDraggingClass(currentContainer);
        currentContainer = null;
      }
    });
  
    // Check scrollability
    function checkScrollability() {
      const containers = [notificationTable, mobileNotificationList].filter(Boolean);
      
      containers.forEach(container => {
        const isVerticalScrollable = container.scrollHeight > container.clientHeight;
        const isHorizontalScrollable = container.scrollWidth > container.clientWidth;
        
        if (isVerticalScrollable || isHorizontalScrollable) {
          container.classList.add("scrollable");
          if (isVerticalScrollable) container.classList.add("vertical-scrollable");
          if (isHorizontalScrollable) container.classList.add("horizontal-scrollable");
        } else {
          container.classList.remove("scrollable", "vertical-scrollable", "horizontal-scrollable");
        }
      });
    }
  
    // Optimize scroll performance
    function optimizeScrollPerformance() {
      const containers = [notificationTable, mobileNotificationList].filter(Boolean);
      
      containers.forEach(container => {
        container.addEventListener('scroll', function() {
          if (!container.scrolling) {
            container.scrolling = true;
            requestAnimationFrame(() => {
              container.scrolling = false;
            });
          }
        }, { passive: true });
  
        container.style.webkitOverflowScrolling = 'touch';
      });
    }
  
    // Initialize everything
    function initialize() {
      // Setup drag scrolling for containers only
      setupDragScroll(notificationTable);
      setupDragScroll(mobileNotificationList);
      
      // Initial mobile cards update
      updateMobileCards();
      
      // Optimize scroll performance
      optimizeScrollPerformance();
      
      // Initial scrollability check
      setTimeout(() => {
        checkScrollability();
      }, 300);
    }
  
    // Event listeners
    window.addEventListener('resize', handleResize);
    window.addEventListener('orientationchange', () => {
      setTimeout(handleResize, 100);
    });
  
    // Mutation observer to watch for content changes
    const mutationObserver = new MutationObserver((mutations) => {
      let shouldUpdate = false;
      
      mutations.forEach(mutation => {
        if (mutation.type === 'childList' && 
            (mutation.target === notificationTable || 
             mutation.target.closest('#notificationRows'))) {
          shouldUpdate = true;
        }
      });
      
      if (shouldUpdate) {
        setTimeout(() => {
          updateMobileCards();
          checkScrollability();
        }, 100);
      }
    });
  
    if (notificationTable) {
      mutationObserver.observe(notificationTable, {
        childList: true,
        subtree: true
      });
    }
  
    // Listen for content loaded events from backend
    document.addEventListener('contentLoaded', function(e) {
      setTimeout(() => {
        updateMobileCards();
        checkScrollability();
      }, 100);
    });
  
    // Initialize the system
    initialize();
  
    // Handle visibility change for performance
    document.addEventListener('visibilitychange', () => {
      if (!document.hidden) {
        setTimeout(checkScrollability, 100);
      }
    });
  
    console.log("Enhanced mobile responsive notification system initialized with improved touch handling");
});