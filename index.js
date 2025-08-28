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


// typewriter h1 , p sto index.html
function typeWriter(elementId, text, speed, callback) {
    let i = 0;
    function typing() {
      if (i < text.length) {
        document.getElementById(elementId).innerHTML += text.charAt(i);
        i++;
        setTimeout(typing, speed);
      } else {
        if (callback) callback(); // μόλις τελειώσει → ξεκινάει το επόμενο
      }
    }
    typing();
  }
  
  // πρώτα γράφεται το h1
  typeWriter("typewriter-h1", "WorkRoom", 220, function() {
    // μετά γράφεται το p
    typeWriter("typewriter-p", "Architecture & Design", 100);
  });