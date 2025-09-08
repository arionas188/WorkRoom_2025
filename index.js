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

// START - sto index.html typeWriter
function typeWriter(elementId, text, speed, callback) {
    const el = document.getElementById(elementId);
    if (!el) {
        if (callback) callback();
        return;
    }
    el.textContent = "";
    let i = 0;
    function typing() {
        if (i < text.length) {
            el.textContent += text.charAt(i);
            i++;
            setTimeout(typing, speed);
        } else {
            if (callback) callback();
        }
    }
    typing();
}
  
document.addEventListener("DOMContentLoaded", function() {
    typeWriter("typewriter-h1", "WorkRoom", 150, function() {
        typeWriter("typewriter-p", "Architecture & Design", 80);
    });
});
// END - typeWriter

// START - Scroll-triggered animations via IntersectionObserver
document.addEventListener("DOMContentLoaded", function () {
    const animateTargets = document.querySelectorAll('.animate-on-scroll');
    if (animateTargets.length === 0) return;

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const anim = el.getAttribute('data-animate') || 'slideInLeft';
                // control animation speed and easing
                el.style.setProperty('--animate-duration', '1.2s');
                el.style.setProperty('--animate-delay', '0s');
                el.style.setProperty('--animate-repeat', '1');
                el.classList.add('animate__animated', `animate__${anim}`);
                obs.unobserve(el);
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -10% 0px' });

    animateTargets.forEach(el => observer.observe(el));
});
// END - Scroll-triggered animations
