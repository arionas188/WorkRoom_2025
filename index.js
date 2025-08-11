document.addEventListener("DOMContentLoaded", function () {
    const goTopBtn = document.getElementById("goTopBtn");

    // Εμφάνιση/απόκρυψη κουμπιού στο scroll
    window.addEventListener("scroll", function () {
        if (window.scrollY > 0) {
            goTopBtn.style.display = "block";
        } else {
            goTopBtn.style.display = "none";
        }
    });

    // Scroll στην κορυφή όταν γίνει κλικ
    goTopBtn.addEventListener("click", function () {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
});

function updateWidth() {
    document.body.setAttribute('data-width', window.innerWidth);
  }
  window.addEventListener('resize', updateWidth);
  updateWidth();

