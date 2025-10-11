// START Face id for circle photo - section our team - 
async function centerFace() {
    // Φορτώνει τα μοντέλα από CDN αντί για local server (αποφεύγει 404 errors)
    await faceapi.nets.tinyFaceDetector.loadFromUri('https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model');
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
// END - Face id for circle photo - section our team - 
