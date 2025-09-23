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
