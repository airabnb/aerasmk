/**
 * SMK Bangun Nusa Bangsa - Frontend Interactive JS
 */
document.addEventListener('DOMContentLoaded', () => {
  // Mobile Navigation Toggle
  const hamburger = document.getElementById('navToggle');
  const navLinks = document.getElementById('navMenu');

  if (hamburger && navLinks) {
    hamburger.addEventListener('click', () => {
      const isOpen = navLinks.classList.toggle('open');
      hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  // Image Upload Preview (for Admin Forms)
  const fileInput = document.querySelector('input[type="file"][data-preview]');
  if (fileInput) {
    const previewId = fileInput.getAttribute('data-preview');
    const previewImg = document.getElementById(previewId);
    fileInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file && previewImg) {
        const reader = new FileReader();
        reader.onload = (ev) => {
          previewImg.src = ev.target.result;
          previewImg.style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // Auto-dismiss alerts after 5 seconds
  document.querySelectorAll('.alert[data-auto-dismiss]').forEach((el) => {
    setTimeout(() => {
      el.style.transition = 'opacity 0.4s ease';
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 400);
    }, 5000);
  });
});
