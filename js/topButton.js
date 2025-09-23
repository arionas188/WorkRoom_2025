// START - TOP button
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

    goTopBtn.addEventListener("click", function () {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
});
// END - TOP button