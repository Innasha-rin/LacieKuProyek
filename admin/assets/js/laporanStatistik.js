document.addEventListener("DOMContentLoaded", function () {
    // Scroll down button functionality
    const scrollDownBtn = document.getElementById("scrollDown");
    const backButton = document.getElementById("backButton");

    scrollDownBtn.addEventListener("click", function () {
      backButton.scrollIntoView({ behavior: "smooth" });
    });

    // Hide scroll button when near the bottom
    window.addEventListener("scroll", function () {
      const scrollPosition = window.scrollY;
      const windowHeight = window.innerHeight;
      const documentHeight = document.body.scrollHeight;

      if (scrollPosition + windowHeight > documentHeight - 100) {
        scrollDownBtn.style.opacity = "0";
        scrollDownBtn.style.pointerEvents = "none";
      } else {
        scrollDownBtn.style.opacity = "1";
        scrollDownBtn.style.pointerEvents = "auto";
      }
    });

    // Back button functionality
    backButton.addEventListener("click", function () {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });

    // Chart 1: Statistik Peminjaman
    const peminjamanCtx = document
      .getElementById("chartPeminjaman")
      .getContext("2d");
    const chartPeminjaman = new Chart(peminjamanCtx, {
      type: "line",
      data: {
        labels: [
          "0",
          "1",
          "2",
          "3",
          "4",
          "5",
          "6",
          "7",
          "8",
          "9",
          "10",
          "11",
        ],
        datasets: [
          {
            label: "Jumlah Peminjaman",
            data: [
              1400, 1200, 1000, 1100, 1500, 1050, 1000, 980, 800, 1300,
              1500, 1200,
            ],
            borderColor: "#1BF36D",
            backgroundColor: "rgba(27, 243, 109, 0.1)",
            borderWidth: 2,
            pointBackgroundColor: "white",
            pointBorderColor: "#1BF36D",
            pointBorderWidth: 2,
            pointRadius: 5,
            tension: 0.1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            max: 1600,
            ticks: {
              stepSize: 400,
              font: {
                family: "Inter",
              },
            },
            grid: {
              color: "#CCCCCC",
            },
          },
          x: {
            grid: {
              color: "#CCCCCC",
            },
            ticks: {
              font: {
                family: "Inter",
              },
            },
          },
        },
        plugins: {
          legend: {
            display: false,
          },
        },
      },
    });

    // Chart 2: Statistik Kategori Buku Terpopuler
    const kategoriCtx = document
      .getElementById("chartKategori")
      .getContext("2d");
    const chartKategori = new Chart(kategoriCtx, {
      type: "doughnut",
      data: {
        labels: [
          "Teknik Digital",
          "Teknik Mesin",
          "Teknik Sipil",
          "Manajemen",
          "Komputer",
          "Budi Daya",
          "Mesin",
          "Akuntansi",
          "Geografi",
          "Fiksi dan Cerita",
          "Metodologi Penelitian Bisnis",
          "Arsitektur Desain",
          "Bisnis Manajemen",
          "Bahasa",
          "Ilmu Sosial",
          "Teknologi Umum",
          "Teknik Kimia",
          "Elektro",
        ],
        datasets: [
          {
            data: [
              850, 720, 680, 750, 920, 580, 630, 540, 490, 880, 420, 650,
              510, 770, 430, 690, 380, 520,
            ],
            backgroundColor: [
              "#3f51b5", // Indigo - Teknik Digital
              "#ff5722", // Deep Orange - Teknik Mesin
              "#000000", // Black - Teknik Sipil
              "#ffc107", // Amber - Manajemen
              "#2196f3", // Blue - Komputer
              "#4caf50", // Green - Budi Daya
              "#ff9800", // Orange - Mesin
              "#e81e63", // Pink - Akuntansi
              "#8bc34a", // Light Green - Geografi
              "#9c27b0", // Purple - Fiksi dan Cerita
              "#00bcd4", // Cyan - Metodologi Penelitian Bisnis
              "#673ab7", // Deep Purple - Arsitektur Desain
              "#009688", // Teal - Bisnis Manajemen
              "#f44336", // Red - Bahasa
              "#cddc39", // Lime - Ilmu Sosial
              "#03a9f4", // Light Blue - Teknologi Umum
              "#ffeb3b", // Yellow - Teknik Kimia
              "#808080", // Gray - Elektro
            ],
            borderColor: "white",
            borderWidth: 2,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "right",
            labels: {
              font: {
                family: "Inter",
                size: 11,
              },
              color: "#000000",
              boxWidth: 15,
              padding: 10,
            },
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                const label = context.label || "";
                const value = context.raw || 0;
                const total = context.dataset.data.reduce(
                  (acc, data) => acc + data,
                  0,
                );
                const percentage = Math.round((value / total) * 100);
                return `${label}: ${value} (${percentage}%)`;
              },
            },
          },
        },
      },
    });

    // Chart 3: Statistik Denda
    const dendaCtx = document.getElementById("chartDenda").getContext("2d");
    const chartDenda = new Chart(dendaCtx, {
      type: "line",
      data: {
        labels: [
          "0",
          "1",
          "2",
          "3",
          "4",
          "5",
          "6",
          "7",
          "8",
          "9",
          "10",
          "11",
        ],
        datasets: [
          {
            label: "Jumlah Denda",
            data: [
              750, 600, 550, 650, 700, 100, 760, 80, 120, 600, 750, 100,
            ],
            borderColor: "#3C4EB9",
            backgroundColor: "rgba(60, 78, 185, 0.1)",
            borderWidth: 2,
            pointBackgroundColor: "white",
            pointBorderColor: "#3C4EB9",
            pointBorderWidth: 2,
            pointRadius: 5,
            tension: 0.1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            max: 800,
            ticks: {
              stepSize: 200,
              font: {
                family: "Inter",
              },
            },
            grid: {
              color: "#CCCCCC",
            },
          },
          x: {
            grid: {
              color: "#CCCCCC",
            },
            ticks: {
              font: {
                family: "Inter",
              },
            },
          },
        },
        plugins: {
          legend: {
            display: false,
          },
        },
      },
    });
  });