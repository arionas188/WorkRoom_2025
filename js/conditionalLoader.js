// Conditional Loader - Lazy loading for non-critical scripts
// Author: WorkRoom W
// Date: 2025-10-11

/**
 * TailwindPlus Elements Conditional Loading
 * Φορτώνει το TailwindPlus Elements μόνο όταν ανοίξει το mobile menu
 * Εξοικονόμηση: ~23.6 KiB στο initial load
 */
function loadTailwindPlusElements() {
  const mobileMenuBtn = document.querySelector('[commandfor="mobile-menu"]');
  
  if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener('click', async () => {
      // Ελέγχει αν το custom element έχει ήδη φορτώσει
      if (!window.customElements.get('el-dialog')) {
        try {
          await import('https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1');
          console.log('✅ TailwindPlus Elements loaded on-demand');
        } catch (error) {
          console.error('❌ Failed to load TailwindPlus Elements:', error);
        }
      }
    }, { once: true }); // Εκτελείται μόνο μία φορά
  }
}

// Initialize conditional loaders
loadTailwindPlusElements();

