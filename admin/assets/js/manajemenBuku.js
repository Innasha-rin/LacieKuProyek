// Search functionality
const searchInput = document.getElementById("searchInput");
const tableBody = document.getElementById("tableBody");
const tableRows = tableBody.querySelectorAll(".table-row");

searchInput.addEventListener("input", function () {
  const searchTerm = this.value.toLowerCase().trim();

  tableRows.forEach((row) => {
    const titleCell = row.children[1]; // Index 1 is the title column
    const title = titleCell.textContent.toLowerCase();

    if (title.includes(searchTerm)) {
      row.classList.remove("hidden");
    } else {
      row.classList.add("hidden");
    }
  });
});

// Drag to scroll functionality
let isDown = false;
let startY;
let scrollTop;

tableBody.addEventListener("mousedown", (e) => {
  isDown = true;
  tableBody.style.cursor = "grabbing";
  startY = e.pageY - tableBody.offsetTop;
  scrollTop = tableBody.scrollTop;
  e.preventDefault();
});

tableBody.addEventListener("mouseleave", () => {
  isDown = false;
  tableBody.style.cursor = "grab";
});

tableBody.addEventListener("mouseup", () => {
  isDown = false;
  tableBody.style.cursor = "grab";
});

tableBody.addEventListener("mousemove", (e) => {
  if (!isDown) return;
  e.preventDefault();
  const y = e.pageY - tableBody.offsetTop;
  const walk = (y - startY) * 2; // Scroll speed multiplier
  tableBody.scrollTop = scrollTop - walk;
});

// Touch events for mobile devices
tableBody.addEventListener(
  "touchstart",
  (e) => {
    isDown = true;
    startY = e.touches[0].pageY - tableBody.offsetTop;
    scrollTop = tableBody.scrollTop;
  },
  { passive: false },
);

tableBody.addEventListener("touchend", () => {
  isDown = false;
});

tableBody.addEventListener("touchcancel", () => {
  isDown = false;
});

tableBody.addEventListener(
  "touchmove",
  (e) => {
    if (!isDown) return;
    const y = e.touches[0].pageY - tableBody.offsetTop;
    const walk = (y - startY) * 2;
    tableBody.scrollTop = scrollTop - walk;
    e.preventDefault();
  },
  { passive: false },
);