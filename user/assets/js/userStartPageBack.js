// advanced_dashboard.js - Modified with auto-refresh as default

class DashboardManager {
    constructor() {
        this.autoRefreshInterval = null;
        this.isAutoRefreshEnabled = true; // Default to true
        this.refreshIntervalMs = 30000; // 30 seconds
        this.overdueCheckInterval = 300000; // 5 minutes
        this.connectionCheckInterval = 60000; // 1 minute
        this.isOnline = navigator.onLine;
        this.lastUpdateTime = null;
        this.retryCount = 0;
        this.maxRetries = 3;
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.setupConnectionMonitoring();
        this.loadSettings();
        this.initialRefresh();
        this.startPeriodicChecks();
        // Start auto-refresh by default
        this.startAutoRefresh();
    }
    
    setupEventListeners() {
        // Remove auto-refresh toggle - no longer needed since it's always enabled
        
        // Manual refresh button
        const refreshBtn = document.querySelector('.refresh-btn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => this.refreshDashboard());
        }
        
        // Page visibility change
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && this.isAutoRefreshEnabled) {
                this.refreshDashboard();
            }
        });
        
        // Window focus
        window.addEventListener('focus', () => {
            if (this.isAutoRefreshEnabled) {
                this.refreshDashboard();
            }
        });
        
        // Online/offline events
        window.addEventListener('online', () => this.handleConnectionChange(true));
        window.addEventListener('offline', () => this.handleConnectionChange(false));
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                this.refreshDashboard();
            }
        });
    }
    
    setupConnectionMonitoring() {
        this.updateConnectionStatus();
        setInterval(() => this.checkConnection(), this.connectionCheckInterval);
    }
    
    loadSettings() {
        // Load settings from sessionStorage (since localStorage is not available)
        const settings = sessionStorage.getItem('dashboardSettings');
        if (settings) {
            const parsed = JSON.parse(settings);
            // Auto-refresh is always enabled, but we can still store interval preference
            this.refreshIntervalMs = parsed.refreshInterval || 30000;
        }
    }
    
    saveSettings() {
        const settings = {
            autoRefresh: true, // Always true now
            refreshInterval: this.refreshIntervalMs,
            lastSaved: new Date().toISOString()
        };
        sessionStorage.setItem('dashboardSettings', JSON.stringify(settings));
    }
    
    // Remove toggleAutoRefresh method since auto-refresh is always enabled
    
    startAutoRefresh() {
        this.stopAutoRefresh(); // Clear any existing interval
        this.autoRefreshInterval = setInterval(() => {
            if (this.isOnline && !document.hidden) {
                this.refreshDashboard(true); // Silent refresh
            }
        }, this.refreshIntervalMs);
    }
    
    stopAutoRefresh() {
        if (this.autoRefreshInterval) {
            clearInterval(this.autoRefreshInterval);
            this.autoRefreshInterval = null;
        }
    }
    
    async initialRefresh() {
        await this.refreshDashboard();
    }
    
    async refreshDashboard(silent = false) {
        if (!this.isOnline) {
            if (!silent) {
                this.showNotification('Tidak ada koneksi internet', 'error');
            }
            return;
        }
        
        this.showLoading(true);
        
        try {
            const response = await fetch('../views/userStartPageBack.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
                cache: 'no-cache'
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                this.updateDashboardData(data);
                this.lastUpdateTime = new Date();
                this.retryCount = 0; // Reset retry count on success
                
                if (!silent) {
                    this.showNotification('Data berhasil diperbarui', 'success');
                }
                
                // Update last refresh time display
                this.updateLastRefreshTime();
                
            } else {
                throw new Error(data.message || 'Gagal memuat data');
            }
            
        } catch (error) {
            console.error('Error refreshing dashboard:', error);
            
            if (this.retryCount < this.maxRetries) {
                this.retryCount++;
                setTimeout(() => this.refreshDashboard(silent), 2000 * this.retryCount);
                
                if (!silent) {
                    this.showNotification(`Mencoba ulang... (${this.retryCount}/${this.maxRetries})`, 'warning');
                }
            } else {
                if (!silent) {
                    this.showNotification('Gagal memuat data setelah beberapa percobaan', 'error');
                }
                this.retryCount = 0;
            }
        } finally {
            this.showLoading(false);
        }
    }
    
    updateDashboardData(data) {
        // Update book count with animation
        const bookCountEl = document.getElementById('bookCount');
        if (bookCountEl) {
            this.animateNumberChange(bookCountEl, parseInt(bookCountEl.textContent) || 0, data.bookCount);
        }
        
        // Update book cards
        this.updateBookCards(data.books);
        
        // Update fine status
        this.updateFineStatus(data.fine);
        
        // Add status indicators if needed
        this.addStatusIndicators(data);
    }
    
    animateNumberChange(element, from, to) {
        const duration = 500;
        const steps = 30;
        const stepTime = duration / steps;
        const increment = (to - from) / steps;
        let current = from;
        let step = 0;
        
        const timer = setInterval(() => {
            step++;
            current += increment;
            
            if (step >= steps) {
                element.textContent = to;
                clearInterval(timer);
            } else {
                element.textContent = Math.round(current);
            }
        }, stepTime);
    }
    
    updateBookCards(books) {
        const bookGrid = document.getElementById('bookGrid');
        if (!bookGrid) return;
        
        // Add updating class for animation
        const cards = bookGrid.querySelectorAll('.book-card');
        cards.forEach(card => card.classList.add('updating'));
        
        setTimeout(() => {
            bookGrid.innerHTML = '';
            
            // Always show exactly 2 cards
            for (let i = 0; i < 2; i++) {
                const bookCard = document.createElement('article');
                bookCard.className = 'book-card';
                
                if (books[i]) {
                    const dueDate = new Date(books[i].tanggal_jatuh_tempo);
                    const today = new Date();
                    const daysUntilDue = Math.ceil((dueDate - today) / (1000 * 60 * 60 * 24));
                    
                    bookCard.innerHTML = `
                        <a href="riwayatPeminjaman.php" class="book-card-link">
                            <div class="book-content">
                                <h2 class="book-title-main" title="${this.escapeHtml(books[i].judul)}">
                                    ${this.escapeHtml(books[i].judul)}
                                </h2>
                            </div>
                            <div class="book-deadline">
                                <h2 class="book-title-deadline">
                                    Tenggat: ${this.formatDate(books[i].tanggal_jatuh_tempo)}
                                </h2>
                            </div>
                        </a>
                    `;
                    
                    // Add status indicator
                    const indicator = document.createElement('div');
                    indicator.className = 'status-indicator';
                    
                    if (daysUntilDue < 0) {
                        indicator.classList.add('danger');
                        indicator.title = 'Terlambat dikembalikan';
                    } else if (daysUntilDue <= 3) {
                        indicator.classList.add('warning');
                        indicator.title = 'Akan jatuh tempo dalam 3 hari';
                    } else {
                        indicator.title = 'Status normal';
                    }
                    
                    bookCard.appendChild(indicator);
                } else {
                    bookCard.innerHTML = `
                        <div class="empty-book-placeholder">
                            <span class="empty-book-dash">-</span>
                        </div>
                    `;
                }
                
                bookGrid.appendChild(bookCard);
            }
            
            // Add animation class
            setTimeout(() => {
                bookGrid.querySelectorAll('.book-card').forEach(card => {
                    card.classList.add('updated');
                });
            }, 50);
        }, 200);
    }
    
    updateFineStatus(fine) {
        const fineEl = document.getElementById('fineStatus');
        if (fineEl) {
            fineEl.textContent = `Denda: ${fine}`;
            
            // Add overdue class if fine > 0
            const fineAmount = parseInt(fine.replace(/[^\d]/g, ''));
            if (fineAmount > 0) {
                fineEl.classList.add('overdue');
            } else {
                fineEl.classList.remove('overdue');
            }
        }
    }
    
    addStatusIndicators(data) {
        // This method can be extended to add more status indicators
        // based on the data received from the server
    }
    
    updateLastRefreshTime() {
        if (!this.lastUpdateTime) return;
        
        // Find or create the last refresh time display - positioned in center below collection button
        let timeDisplay = document.querySelector('.last-refresh-time');
        if (!timeDisplay) {
            timeDisplay = document.createElement('div');
            timeDisplay.className = 'last-refresh-time';
            timeDisplay.style.cssText = `
                text-align: center;
                margin: 10px auto;
                font-size: 12px;
                color: #666;
                width: 100%;
                max-width: 400px;
            `;
            
            // Try to insert after the collection button or main content
            const collectionButton = document.querySelector('.collection-button')
            
            if (collectionButton) {
                collectionButton.parentNode.insertBefore(timeDisplay, collectionButton.nextSibling);
            } else {
                // Fallback: append to main container
                const mainContainer = document.querySelector('.main-container');
                if (mainContainer) {
                    mainContainer.appendChild(timeDisplay);
                }
            }
        }
        
        timeDisplay.textContent = `Terakhir diperbarui: ${this.lastUpdateTime.toLocaleTimeString()}`;
    }
    
    startPeriodicChecks() {
        // Check for overdue books initially and then periodically
        setTimeout(() => this.checkOverdueBooks(), 2000);
        setInterval(() => this.checkOverdueBooks(), this.overdueCheckInterval);
    }
    
    async checkOverdueBooks() {
        if (!this.isOnline) return;
        
        try {
            const response = await fetch('../views/userStartPageBack_Overdue.php');
            const data = await response.json();
            
            if (data.success) {
                if (data.hasOverdue && data.count > 0) {
                    this.showNotification(
                        `Anda memiliki ${data.count} buku yang terlambat dikembalikan!`, 
                        'error'
                    );
                } else if (data.hasWarning && data.warningCount > 0) {
                    this.showNotification(
                        `${data.warningCount} buku akan jatuh tempo dalam 3 hari`, 
                        'warning'
                    );
                }
            }
        } catch (error) {
            console.error('Error checking overdue books:', error);
        }
    }
    
    handleConnectionChange(isOnline) {
        this.isOnline = isOnline;
        this.updateConnectionStatus();
        
        if (isOnline) {
            this.showNotification('Koneksi internet kembali', 'success');
            if (this.isAutoRefreshEnabled) {
                this.refreshDashboard(true);
            }
        } else {
            this.showNotification('Koneksi internet terputus', 'error');
        }
    }
    
    async checkConnection() {
        try {
            const response = await fetch('../views/userStartPageBack.php', {
                method: 'HEAD',
                cache: 'no-cache'
            });
            this.isOnline = response.ok;
        } catch {
            this.isOnline = false;
        }
        
        this.updateConnectionStatus();
    }
    
    updateConnectionStatus() {
        let statusEl = document.querySelector('.connection-status');
        
        if (!statusEl) {
            statusEl = document.createElement('div');
            statusEl.className = 'connection-status';
            document.body.appendChild(statusEl);
        }
        
        statusEl.className = `connection-status ${this.isOnline ? 'online' : 'offline'}`;
        statusEl.textContent = this.isOnline ? 'Online' : 'Offline';
        
        // Hide status if online after 3 seconds
        if (this.isOnline) {
            setTimeout(() => {
                if (statusEl && this.isOnline) {
                    statusEl.style.opacity = '0';
                }
            }, 3000);
        } else {
            statusEl.style.opacity = '1';
        }
    }
    
    showLoading(isLoading) {
        const container = document.querySelector('.main-container');
        if (container) {
            container.classList.toggle('loading', isLoading);
        }
    }
    
    showNotification(message, type = 'success', duration = 3000) {
        // Create or get notification element and position it in the center
        let notification = document.getElementById('notification');
        
        if (!notification) {
            notification = document.createElement('div');
            notification.id = 'notification';
            notification.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: #fff;
                color: #333;
                padding: 15px 25px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                z-index: 10000;
                font-size: 14px;
                font-weight: 500;
                min-width: 200px;
                text-align: center;
                opacity: 0;
                transition: all 0.3s ease;
                pointer-events: none;
            `;
            document.body.appendChild(notification);
        }
        
        // Set message and type
        notification.textContent = message;
        notification.className = `notification ${type}`;
        
        // Apply type-specific styles
        switch (type) {
            case 'success':
                notification.style.background = '#4CAF50';
                notification.style.color = '#fff';
                break;
            case 'error':
                notification.style.background = '#f44336';
                notification.style.color = '#fff';
                break;
            case 'warning':
                notification.style.background = '#ff9800';
                notification.style.color = '#fff';
                break;
            default:
                notification.style.background = '#2196F3';
                notification.style.color = '#fff';
        }
        
        // Show notification
        notification.style.opacity = '1';
        notification.style.pointerEvents = 'auto';
        
        // Hide notification after duration
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.pointerEvents = 'none';
        }, duration);
    }
    
    // Utility methods
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    formatDate(dateString) {
        const date = new Date(dateString);
        return `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()}`;
    }
    
    // Cleanup method
    destroy() {
        this.stopAutoRefresh();
        clearInterval(this.overdueCheckInterval);
        clearInterval(this.connectionCheckInterval);
    }
}

// Initialize dashboard manager when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.dashboardManager = new DashboardManager();
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (window.dashboardManager) {
        window.dashboardManager.destroy();
    }
});