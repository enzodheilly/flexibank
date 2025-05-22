document.addEventListener('DOMContentLoaded', () => {
  // Auto-hide flash messages
  document.querySelectorAll('.auto-hide').forEach(flash => {
    setTimeout(() => {
      flash.style.opacity = '0'
      setTimeout(() => flash.remove(), 500)
    }, 4000)
  });

  // Animation au scroll
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

  // Dark/light theme toggle
  const toggleBtn = document.getElementById('theme-toggle');
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const savedTheme = localStorage.getItem('theme');
  const initialTheme = savedTheme || (prefersDark ? 'dark' : 'light');

  function applyTheme(theme) {
    document.body.classList.remove('dark', 'light');
    document.body.classList.add(theme);
    toggleBtn.innerHTML = theme === 'dark' ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
  }

  if (toggleBtn) {
    applyTheme(initialTheme);

    toggleBtn.addEventListener('click', () => {
      const isDark = document.body.classList.contains('dark');
      const newTheme = isDark ? 'light' : 'dark';
      localStorage.setItem('theme', newTheme);
      applyTheme(newTheme);
    });
  }

  // Newsletter email validation
  const emailInput = document.querySelector('#newsletter-form input[type="email"]');
  const button = document.querySelector('#newsletter-form button');

  if (emailInput && button) {
    const validateEmail = email => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

    emailInput.addEventListener('input', () => {
      const email = emailInput.value.trim();
      if (validateEmail(email)) {
        emailInput.style.border = '2px solid #00c853';
        emailInput.style.backgroundColor = '#1f2f1f';
        button.disabled = false;
      } else {
        emailInput.style.border = '2px solid #ff5252';
        emailInput.style.backgroundColor = '#2f1f1f';
        button.disabled = true;
      }
    });
  }

  // Newsletter cooldown submit
  const form = document.getElementById('newsletter-form');
  const feedback = document.getElementById('newsletter-feedback');
  let canSubmit = true;

  if (form && feedback) {
    form.addEventListener('submit', e => {
      if (!canSubmit) {
        e.preventDefault();
        feedback.style.display = 'block';
        feedback.textContent = '⏳ Merci de patienter avant de réessayer.';
        feedback.style.color = 'red';
        return;
      }

      canSubmit = false;
      feedback.style.display = 'none';

      setTimeout(() => {
        canSubmit = true;
      }, 10000);
    });
  }

  // ✅ Mobile menu (hamburger)
  const hamburger = document.getElementById('hamburger-btn');
  const navMenu = document.getElementById('nav-menu');

  if (hamburger && navMenu) {
    hamburger.addEventListener('click', () => {
      navMenu.classList.toggle('menu-open');
      hamburger.querySelector('i').classList.toggle('fa-bars');
      hamburger.querySelector('i').classList.toggle('fa-times');
    });
  }
});

// Preloader
window.addEventListener('load', () => {
  const preloader = document.getElementById('preloader');
  if (preloader) {
    preloader.style.opacity = '0';
    setTimeout(() => {
      preloader.style.display = 'none';
    }, 500);
  }
});