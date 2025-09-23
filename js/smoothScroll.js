// START - Smooth-scroll to hash targets and close mobile menu if open
document.addEventListener("DOMContentLoaded", function () {
    if (location.hash) {
        const target = document.querySelector(location.hash);
        if (target) {
            target.scrollIntoView({ behavior: "smooth", block: "start" });
        }
    }

    document.body.addEventListener("click", function (e) {
        const link = e.target.closest('a[href^="#"], a[href$="#about-us"]');
        if (!link) return;

        const href = link.getAttribute("href");
        if (href.startsWith("#")) {
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: "smooth", block: "start" });
            }
        }

        // Close Tailwind Elements dialog if open
        const dialog = document.getElementById("mobile-menu");
        if (dialog && typeof dialog.close === "function") {
            try { dialog.close(); } catch (_) {}
        }
    });
});
// END - Smooth-scroll to hash targets and close mobile menu if open
