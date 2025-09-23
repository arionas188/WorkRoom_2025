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
// END - sto index.html typeWriter typeWriter
