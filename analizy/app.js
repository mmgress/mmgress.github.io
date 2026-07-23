/* ==========================================================================
   Sternik — wspólna logika: kontrola sesji, wylogowanie, menu modułów.

   UWAGA (dev): to jest wersja przed-MVP bez backendu. Sesja jest symulowana
   przez sessionStorage. W docelowej wersji zastąpić to realną autoryzacją
   (np. tokenem z API) i sprawdzaniem sesji po stronie serwera.
   ========================================================================== */

(function () {
  var AUTH_KEY = 'sternik_authenticated';

  function requireAuth() {
    if (document.body.dataset.protected === 'true' &&
        sessionStorage.getItem(AUTH_KEY) !== 'true') {
      window.location.href = 'sternik.html';
    }
  }

  function wireLogout() {
    var btn = document.getElementById('logout-btn');
    if (!btn) return;
    btn.addEventListener('click', function () {
      sessionStorage.removeItem(AUTH_KEY);
      window.location.href = 'sternik.html';
    });
  }

  function wireModulesDropdown() {
    var trigger = document.getElementById('modules-trigger');
    var menu = document.getElementById('modules-menu');
    if (!trigger || !menu) return;

    function closeMenu() {
      menu.classList.remove('is-open');
      trigger.setAttribute('aria-expanded', 'false');
    }
    function openMenu() {
      menu.classList.add('is-open');
      trigger.setAttribute('aria-expanded', 'true');
    }

    trigger.addEventListener('click', function (e) {
      e.stopPropagation();
      if (menu.classList.contains('is-open')) closeMenu(); else openMenu();
    });

    document.addEventListener('click', function (e) {
      if (!menu.contains(e.target) && e.target !== trigger) closeMenu();
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeMenu();
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    requireAuth();
    wireLogout();
    wireModulesDropdown();
  });
})();
