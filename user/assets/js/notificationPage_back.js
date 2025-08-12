// Notification Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
    
    // Refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);
    
    // Update unread count initially and periodically
    updateUnreadCount();
    setInterval(updateUnreadCount, 30000);
});

async function loadNotifications() {
    const loadingIndicator = document.getElementById('loadingIndicator');
    const noNotifications = document.getElementById('noNotifications');
    const notificationRows = document.getElementById('notificationRows');
    
    try {
        loadingIndicator.style.display = 'block';
        noNotifications.style.display = 'none';
        
        const response = await fetch('../views/notificationPage_back.php?action=list');
        const data = await response.json();
        
        if (data.success && data.data.length > 0) {
            populateNotifications(data.data);
            noNotifications.style.display = 'none';
            
            // Trigger scroll feature check after content is loaded
            setTimeout(() => {
                const event = new CustomEvent('contentLoaded', {
                    detail: { container: notificationRows }
                });
                document.dispatchEvent(event);
            }, 100);
            
        } else {
            notificationRows.innerHTML = '';
            noNotifications.style.display = 'block';
        }
        
    } catch (error) {
        console.error('Error loading notifications:', error);
        notificationRows.innerHTML = '<div class="error-message">Gagal memuat notifikasi. <button onclick="loadNotifications()" style="margin-left: 10px; padding: 5px 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Coba Lagi</button></div>';
    } finally {
        loadingIndicator.style.display = 'none';
    }
}

function populateNotifications(notifications) {
    const notificationRows = document.getElementById('notificationRows');
    notificationRows.innerHTML = '';
    
    notifications.forEach((notification, index) => {
        const row = document.createElement('div');
        row.className = 'table-row';
        row.style.cursor = 'pointer';
        
        // Add visual indicator for unread notifications
        const statusClass = notification.status === 'terkirim' ? 'unread' : 'read';
        row.classList.add(statusClass);
        
        // Add animation delay for staggered appearance
        row.style.animationDelay = `${index * 0.1}s`;
        
        row.innerHTML = `
            <div class="table-cell">${index + 1}</div>
            <div class="table-cell cell-centered">${notification.waktu_formatted}</div>
            <div class="table-cell cell-right">${notification.tanggal_formatted}</div>
            <div class="table-cell cell-right">
                <span class="status-badge ${statusClass}">
                    ${notification.status === 'terkirim' ? 'Belum dibaca' : 'Dibaca'}
                </span>
            </div>
        `;
        
        // Add click event to view notification detail
        row.addEventListener('click', function(e) {
            // Prevent click if currently dragging
            if (e.target.closest('#notificationRows').classList.contains('dragging')) {
                return;
            }
            viewNotificationDetail(notification.id);
        });
        
        // Add hover effects
        row.addEventListener('mouseenter', function() {
            if (!this.closest('#notificationRows').classList.contains('dragging')) {
                this.style.backgroundColor = '#f8f9fa';
            }
        });
        
        row.addEventListener('mouseleave', function() {
            if (!this.classList.contains('unread')) {
                this.style.backgroundColor = '';
            }
        });
        
        notificationRows.appendChild(row);
    });
    
    // Add some padding at the bottom for better scrolling experience
    const paddingDiv = document.createElement('div');
    paddingDiv.style.height = '20px';
    paddingDiv.style.pointerEvents = 'none';
    notificationRows.appendChild(paddingDiv);
}

async function viewNotificationDetail(notificationId) {
    try {
        // Show loading state
        const clickedRow = event.target.closest('.table-row');
        if (clickedRow) {
            clickedRow.style.opacity = '0.7';
            clickedRow.style.pointerEvents = 'none';
        }
        
        // Redirect to detail page with notification ID
        window.location.href = `notificationPage2.php?id=${notificationId}`;
    } catch (error) {
        console.error('Error viewing notification:', error);
        alert('Gagal membuka detail notifikasi');
        
        // Restore row state
        if (clickedRow) {
            clickedRow.style.opacity = '';
            clickedRow.style.pointerEvents = '';
        }
    }
}

// Update unread count badge
async function updateUnreadCount() {
    try {
        const response = await fetch('../views/notificationPage_back.php?action=unread_count');
        const data = await response.json();
        
        if (data.success) {
            const badge = document.getElementById('notificationBadge');
            if (data.unread_count > 0) {
                if (badge) {
                    badge.textContent = data.unread_count;
                    // Add pulse animation for new notifications
                    badge.style.animation = 'pulse 0.5s ease-in-out';
                    setTimeout(() => {
                        badge.style.animation = '';
                    }, 500);
                }
            } else {
                if (badge) {
                    badge.remove();
                }
            }
        }
    } catch (error) {
        console.error('Error updating unread count:', error);
    }
}

// Add keyboard navigation support
document.addEventListener('keydown', function(e) {
    const notificationRows = document.getElementById('notificationRows');
    if (!notificationRows) return;
    
    const rows = notificationRows.querySelectorAll('.table-row:not([style*="height: 20px"])');
    const focusedElement = document.activeElement;
    
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault();
        
        let currentIndex = -1;
        rows.forEach((row, index) => {
            if (row === focusedElement || row.contains(focusedElement)) {
                currentIndex = index;
            }
        });
        
        let nextIndex;
        if (e.key === 'ArrowDown') {
            nextIndex = currentIndex < rows.length - 1 ? currentIndex + 1 : 0;
        } else {
            nextIndex = currentIndex > 0 ? currentIndex - 1 : rows.length - 1;
        }
        
        if (rows[nextIndex]) {
            rows[nextIndex].focus();
            rows[nextIndex].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
    
    if (e.key === 'Enter' && focusedElement.classList.contains('table-row')) {
        focusedElement.click();
    }
});

// Make rows focusable for keyboard navigation
document.addEventListener('DOMContentLoaded', function() {
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && node.classList.contains('table-row')) {
                        node.setAttribute('tabindex', '0');
                    }
                });
            }
        });
    });
    
    const notificationRows = document.getElementById('notificationRows');
    if (notificationRows) {
        observer.observe(notificationRows, {
            childList: true,
            subtree: true
        });
    }
});