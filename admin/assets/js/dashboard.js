// Dashboard JavaScript functionality with AJAX data loading

// Global variables
let borrowingChart = null;

// Function to fetch data from the backend API
function fetchData(action) {
  return fetch(`../views/api.php?action=${action}`)
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .catch(error => {
      console.error(`Error fetching ${action}:`, error);
      return null;
    });
}

// Chart.js implementation
function initBorrowingChart(labels, data) {
  const ctx = document.getElementById("borrowingChart").getContext("2d");
  
  // If chart already exists, destroy it first
  if (borrowingChart) {
    borrowingChart.destroy();
  }

  // Create the chart
  borrowingChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: labels,
      datasets: [
        {
          label: "Jumlah Peminjaman",
          data: data,
          backgroundColor: "#88B7DF",
          borderColor: "#88B7DF",
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 25,
          },
          grid: {
            color: "#CCCCCC",
          },
        },
        x: {
          grid: {
            color: "#CCCCCC",
          },
        },
      },
      plugins: {
        legend: {
          display: true,
          position: "top",
        },
        tooltip: {
          backgroundColor: "rgba(0, 0, 0, 0.7)",
          titleFont: {
            size: 14,
          },
          bodyFont: {
            size: 14,
          },
          callbacks: {
            title: function (tooltipItems) {
              return "Hari " + tooltipItems[0].label;
            },
            label: function (context) {
              return "Peminjaman: " + context.raw;
            },
          },
        },
      },
    },
  });

  return borrowingChart;
}

// Function to set statistics
function setStatistics(statistics) {
  const totalBooksElement = document.querySelector(
    ".stat-card:nth-child(1) .stat-value",
  );
  const totalUsersElement = document.querySelector(
    ".stat-card:nth-child(2) .stat-value",
  );
  const dailyLoansElement = document.querySelector(
    ".stat-card:nth-child(3) .stat-value",
  );
  const lateReturnsElement = document.querySelector(
    ".stat-card:nth-child(4) .stat-value",
  );

  if (totalBooksElement)
    totalBooksElement.textContent = statistics.totalBooks;
  if (totalUsersElement)
    totalUsersElement.textContent = statistics.totalUsers;
  if (dailyLoansElement)
    dailyLoansElement.textContent = statistics.dailyLoans;
  if (lateReturnsElement)
    lateReturnsElement.textContent = statistics.lateReturns;
}

// Function to populate popular books table
function populatePopularBooks(popularBooks) {
  const tableBody = document.querySelector(".popular-books-section tbody");
  if (!tableBody) return;

  // Clear existing rows
  tableBody.innerHTML = "";

  // Add new rows
  popularBooks.forEach((book) => {
    const row = document.createElement("tr");
    row.className = "table-row";

    row.innerHTML = `
      <td class="table-cell">${book.id}</td>
      <td class="table-cell">${book.title}</td>
      <td class="table-cell">${book.category}</td>
    `;

    tableBody.appendChild(row);
  });
}

// Function to populate active borrowers table
function populateActiveBorrowers(activeBorrowers) {
  const tableBody = document.querySelector(".active-borrowers-section tbody");
  if (!tableBody) return;

  // Ensure it's an array
  activeBorrowers = Array.isArray(activeBorrowers) ? activeBorrowers : [];

  // Clear existing rows
  tableBody.innerHTML = "";

  // Add new rows
  activeBorrowers.forEach((borrower) => {
    const row = document.createElement("tr");
    row.className = "table-row";

    row.innerHTML = `
      <td class="table-cell">${borrower.id}</td>
      <td class="table-cell">${borrower.nim}</td>
      <td class="table-cell">${borrower.name}</td>
    `;

    tableBody.appendChild(row);
  });
}


// Function to populate late returns table
function populateLateReturns(lateReturns) {
  const tableBody = document.querySelector(".late-returns-section tbody");
  if (!tableBody) return;

  // Ensure it's an array
  lateReturns = Array.isArray(lateReturns) ? lateReturns : [];

  // Clear existing rows
  tableBody.innerHTML = "";

  // Add new rows
  lateReturns.forEach((lateReturn) => {
    const row = document.createElement("tr");
    row.className = "table-row";

    row.innerHTML = `
      <td class="table-cell">${lateReturn.id}</td>
      <td class="table-cell">${lateReturn.nim}</td>
      <td class="table-cell">${lateReturn.name}</td>
      <td class="table-cell">${lateReturn.bookTitle}</td>
      <td class="table-cell">${lateReturn.fine}</td>
    `;

    tableBody.appendChild(row);
  });
}

// Function to add event listeners
function addEventListeners() {
  // Example: Add click event to statistics cards to show more details
  const statCards = document.querySelectorAll(".stat-card");
  statCards.forEach((card) => {
    card.addEventListener("click", function () {
      const statTitle = this.querySelector(".stat-title").textContent;
      
      // Determine which page to redirect to based on the card
      let redirectPage = '';
      
      switch(statTitle) {
        case 'Total Buku':
          redirectPage = 'manajemenBuku.php';
          break;
        case 'Total Pengguna':
          redirectPage = 'daftarUsers.php';
          break;
        case 'Total Peminjaman Hari Ini':
          redirectPage = 'riwayatPeminjaman.php';
          break;
        default:
          alert(`You clicked on ${statTitle}. More details would be shown here.`);
          return;
      }
      
      // Redirect to the appropriate page
      if (redirectPage) {
        window.location.href = redirectPage;
      }
    });
  });
  
  // Add refresh button functionality
  const refreshButton = document.getElementById('refreshData');
  if (refreshButton) {
    refreshButton.addEventListener('click', function() {
      loadAllDashboardData();
    });
  }
}

function updateUnverifiedPaymentsNotification(unverifiedPayments) {
  const notificationContainer = document.getElementById('unverifiedPaymentsNotification');
  const countElement = document.getElementById('unverifiedPaymentsCount');
  
  if (!notificationContainer || !countElement) return;
  
  // Update jumlah pembayaran yang belum diverifikasi
  const count = unverifiedPayments.count || 0;
  countElement.textContent = count;
  
}

// Function to load all dashboard data at once
function loadAllDashboardData() {
  // Show loading indicator if available
  const loadingIndicator = document.getElementById('loadingIndicator');
  if (loadingIndicator) {
    loadingIndicator.style.display = 'block';
  }
  
  // Fetch all data at once
  fetchData('getAllDashboardData')
    .then(data => {
      if (data && !data.error) {
        // Update statistics
        setStatistics(data.statistics);
        
        // Update chart
        if (data.borrowingStats && data.borrowingStats.labels && data.borrowingStats.data) {
          initBorrowingChart(data.borrowingStats.labels, data.borrowingStats.data);
        }
        
        // Update tables
        populatePopularBooks(data.popularBooks);
        populateActiveBorrowers(data.activeBorrowers);
        populateLateReturns(data.lateReturns);
        
        // Update notifikasi pembayaran yang belum diverifikasi
        if (data.unverifiedPayments) {
          updateUnverifiedPaymentsNotification(data.unverifiedPayments);
        }
        
        // Hide loading indicator
        if (loadingIndicator) {
          loadingIndicator.style.display = 'none';
        }
      } else {
        console.error('Error loading dashboard data:', data ? data.error : 'No data received');
        // Hide loading indicator
        if (loadingIndicator) {
          loadingIndicator.style.display = 'none';
        }
        // Show error message
        alert('Gagal memuat data dashboard. Silakan coba lagi nanti.');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      // Hide loading indicator
      if (loadingIndicator) {
        loadingIndicator.style.display = 'none';
      }
      // Show error message
      alert('Terjadi kesalahan saat memuat data. Silakan coba lagi nanti.');
    });
}

// Initialize the dashboard when the DOM is fully loaded
document.addEventListener("DOMContentLoaded", function() {
  // Initialize event listeners
  addEventListeners();
  
  // Load all dashboard data
  loadAllDashboardData();
  
  // Set up auto-refresh every 5 minutes (300000 ms)
  setInterval(loadAllDashboardData, 300000);
});