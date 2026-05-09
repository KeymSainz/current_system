/**
 * Fix&Go — Theme Manager
 * Handles dark/light mode toggle with localStorage persistence.
 */
(function () {
  'use strict';

  const STORAGE_KEY = 'fg_theme';

  function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    const icon = document.getElementById('themeIcon');
    if (icon) {
      icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
    }
  }

  function getSavedTheme() {
    return localStorage.getItem(STORAGE_KEY) || 'light';
  }

  // Apply on load
  applyTheme(getSavedTheme());

  // Wire toggle button after DOM is ready
  document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('themeToggle');
    if (!btn) return;

    btn.addEventListener('click', function () {
      const current = document.documentElement.getAttribute('data-theme') || 'light';
      const next = current === 'dark' ? 'light' : 'dark';
      applyTheme(next);
      localStorage.setItem(STORAGE_KEY, next);
    });
  });
})();
