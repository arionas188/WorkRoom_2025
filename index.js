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

// face id for circle photo - section our team - START
async function centerFace() {
    await faceapi.nets.tinyFaceDetector.loadFromUri('/models'); // φόρτωσε τα μοντέλα
    const img = document.getElementById('profile-img');
  
    const detections = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions());
    if (detections) {
      const box = detections.box;
      const xPercent = (box.x + box.width / 2) / img.naturalWidth * 100;
      const yPercent = (box.y + box.height / 2) / img.naturalHeight * 100;
      img.style.objectPosition = `${xPercent}% ${yPercent}%`;
    }
  }
  
  centerFace();
// END - face id for circle photo - section our team - 

// allagi automata o xronos
const yearSpan = document.getElementById('year');
  yearSpan.textContent = new Date().getFullYear();