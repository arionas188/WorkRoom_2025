(function () {
    const box = document.createElement("div");
    box.style.position = "fixed";
    box.style.top = "10px";          // πάνω
    box.style.right = "10px";        // δεξιά
    box.style.padding = "5px 10px";
    box.style.background = "rgba(0,0,0,0.7)";
    box.style.color = "#fff";
    box.style.fontFamily = "monospace";
    box.style.fontSize = "12px";
    box.style.borderRadius = "4px";
    box.style.zIndex = "999999";
    document.body.appendChild(box);
  
    function update() {
      box.textContent = `${window.innerWidth} × ${window.innerHeight}`;
    }
  
    window.addEventListener("resize", update);
    update();
  })();