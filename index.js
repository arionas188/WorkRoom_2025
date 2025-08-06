function updateWidth() {
    document.body.setAttribute('data-width', window.innerWidth);
  }
  window.addEventListener('resize', updateWidth);
  updateWidth();
