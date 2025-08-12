document.addEventListener("DOMContentLoaded", function () {
    const faqContainer = document.querySelector(".faq-grid");

    if (!faqContainer) return;

    let isDragging = false;
    let startY = 0;
    let scrollTop = 0;

    function addDraggingClass() {
        faqContainer.classList.add("dragging");
    }

    function removeDraggingClass() {
        faqContainer.classList.remove("dragging");
    }

    // Mouse Events
    faqContainer.addEventListener("mousedown", function (e) {
        isDragging = true;
        startY = e.pageY;
        scrollTop = faqContainer.scrollTop;
        addDraggingClass();
        e.preventDefault();
    });

    document.addEventListener("mousemove", function (e) {
        if (!isDragging) return;

        const y = e.pageY;
        const walk = (y - startY) * 2; // Adjust scrolling speed
        faqContainer.scrollTop = scrollTop - walk;
    });

    document.addEventListener("mouseup", function () {
        isDragging = false;
        removeDraggingClass();
    });

    // Touch Events (Mobile)
    faqContainer.addEventListener("touchstart", function (e) {
        isDragging = true;
        startY = e.touches[0].pageY;
        scrollTop = faqContainer.scrollTop;
        addDraggingClass();
    });

    faqContainer.addEventListener("touchmove", function (e) {
        if (!isDragging) return;

        const y = e.touches[0].pageY;
        const walk = (y - startY) * 2;
        faqContainer.scrollTop = scrollTop - walk;
        e.preventDefault(); // Prevents page scrolling
    });

    faqContainer.addEventListener("touchend", function () {
        isDragging = false;
        removeDraggingClass();
    });

    document.addEventListener("mouseleave", function () {
        if (isDragging) {
            isDragging = false;
            removeDraggingClass();
        }
    });

    // Scroll Indicator
    faqContainer.addEventListener("scroll", function () {
        faqContainer.classList.add("scrolling");
        clearTimeout(faqContainer.scrollTimeout);
        faqContainer.scrollTimeout = setTimeout(() => {
            faqContainer.classList.remove("scrolling");
        }, 150);
    });

    // Initial Scroll Animation
    setTimeout(() => {
        if (faqContainer.scrollHeight > faqContainer.clientHeight) {
            faqContainer.classList.add("scrollable");
            faqContainer.scrollTop = 10;
            setTimeout(() => {
                faqContainer.scrollTop = 0;
            }, 500);
        }
    }, 1000);
});
