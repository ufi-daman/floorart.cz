/**
 * FloorArt.cz — Main JavaScript
 * Slider, burger menu, scroll behavior
 */

document.addEventListener('DOMContentLoaded', function () {

  /* ============================================================
     BURGER MENU TOGGLE
     ============================================================ */
  const burger = document.getElementById('burger');
  const popupmenu = document.getElementById('popupmenu');
  const popupClose = document.getElementById('popupClose');

  function openMenu() {
    if (!popupmenu) return;
    burger && burger.classList.add('active');
    popupmenu.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeMenu() {
    if (!popupmenu) return;
    burger && burger.classList.remove('active');
    popupmenu.classList.remove('active');
    document.body.style.overflow = '';
  }

  if (burger) {
    burger.addEventListener('click', function () {
      if (popupmenu.classList.contains('active')) {
        closeMenu();
      } else {
        openMenu();
      }
    });
  }

  if (popupClose) {
    popupClose.addEventListener('click', closeMenu);
  }

  // Close on ESC
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeMenu();
  });

  /* ============================================================
     HOMEPAGE SLIDER
     ============================================================ */
  const slidesContainer = document.getElementById('slider');
  if (slidesContainer) {
    const slides = slidesContainer.querySelectorAll('.slide-item');
    const numbersList = document.querySelector('.projectnumbers ul');
    const numberItems = numbersList ? numbersList.querySelectorAll('li') : [];
    let currentSlide = 0;
    let slideInterval = null;
    let isTransitioning = false;

    function goToSlide(index) {
      if (isTransitioning || slides.length === 0) return;
      isTransitioning = true;

      // Deactivate current
      slides[currentSlide].classList.remove('active');
      if (numberItems[currentSlide]) numberItems[currentSlide].classList.remove('active');

      // Update index
      currentSlide = ((index % slides.length) + slides.length) % slides.length;

      // Activate new
      slides[currentSlide].classList.add('active');
      if (numberItems[currentSlide]) numberItems[currentSlide].classList.add('active');

      // Update indicator position class
      if (numbersList) {
        numbersList.className = currentSlide === 0 ? '' : 'activestep' + currentSlide;
      }

      setTimeout(function () { isTransitioning = false; }, 1000);
    }

    function startAutoplay() {
      slideInterval = setInterval(function () {
        goToSlide(currentSlide + 1);
      }, 5000);
    }

    function resetAutoplay() {
      clearInterval(slideInterval);
      startAutoplay();
    }

    // Initialize first slide
    if (slides.length > 0) {
      slides[0].classList.add('active');
      if (numberItems[0]) numberItems[0].classList.add('active');
      startAutoplay();
    }

    // Click to advance slide
    slidesContainer.addEventListener('click', function () {
      resetAutoplay();
      goToSlide(currentSlide + 1);
    });

    // Touch/swipe support
    var touchStartX = 0;
    slidesContainer.addEventListener('touchstart', function (e) {
      touchStartX = e.touches[0].clientX;
    }, { passive: true });

    slidesContainer.addEventListener('touchend', function (e) {
      var diff = touchStartX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) {
        resetAutoplay();
        goToSlide(diff > 0 ? currentSlide + 1 : currentSlide - 1);
      }
    }, { passive: true });
  }

  /* ============================================================
     SCROLL BEHAVIOR (inner pages)
     Burger + lang switcher become fixed after scrolling
     Small logo appears
     ============================================================ */
  var burgerEl = document.getElementById('burger');
  var langSwitcher = document.querySelector('.lang-switcher');
  var logoSmall = document.querySelector('.logoblock-small');
  var logoMain = document.querySelector('.page-header .logoblock');

  if (burgerEl || langSwitcher) {
    var lastScrollY = 0;

    window.addEventListener('scroll', function () {
      var scrollY = window.scrollY || window.pageYOffset;
      var scrolled = scrollY > 60;

      if (burgerEl && !document.body.classList.contains('homepage')) {
        burgerEl.classList.toggle('scrolled', scrolled);
      }
      if (langSwitcher && !document.body.classList.contains('homepage')) {
        langSwitcher.classList.toggle('scrolled', scrolled);
      }
      if (logoSmall) {
        logoSmall.classList.toggle('show', scrolled);
      }
      if (logoMain) {
        logoMain.style.opacity = scrolled ? '0' : '1';
        logoMain.style.transition = 'opacity 0.2s';
      }

      lastScrollY = scrollY;
    }, { passive: true });
  }

  /* ============================================================
     PORTFOLIO — simple image hover effect already in CSS
     Add lightbox for portfolio items if needed
     ============================================================ */

  /* ============================================================
     CONTACT FORM — validation + WordPress AJAX submission
     ============================================================ */
  var contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var name    = contactForm.querySelector('[name="name"]');
      var email   = contactForm.querySelector('[name="email"]');
      var message = contactForm.querySelector('[name="message"]');
      var btn     = contactForm.querySelector('button[type="submit"]');

      if (!name.value.trim() || !email.value.trim() || !message.value.trim()) {
        alert('Prosím vyplňte všechna povinná pole.');
        return;
      }

      btn.textContent = 'Odesíláno...';
      btn.disabled = true;

      // WordPress AJAX — falls back to simulation if not in WP context
      if (typeof floorartData !== 'undefined' && floorartData.ajaxUrl) {
        var data = new FormData(contactForm);
        data.append('action', 'floorart_contact');
        data.append('nonce', floorartData.nonce);

        fetch(floorartData.ajaxUrl, { method: 'POST', body: data })
          .then(function (r) { return r.json(); })
          .then(function (res) {
            btn.textContent = res.success ? 'Odesláno ✓' : 'Chyba — zkuste znovu';
            if (res.success) contactForm.reset();
            setTimeout(function () {
              btn.textContent = 'Odeslat';
              btn.disabled = false;
            }, 3000);
          })
          .catch(function () {
            btn.textContent = 'Chyba spojení';
            setTimeout(function () {
              btn.textContent = 'Odeslat';
              btn.disabled = false;
            }, 3000);
          });
      } else {
        // Static site fallback — simulate
        setTimeout(function () {
          btn.textContent = 'Odesláno ✓';
          contactForm.reset();
          setTimeout(function () {
            btn.textContent = 'Odeslat';
            btn.disabled = false;
          }, 3000);
        }, 1200);
      }
    });
  }

});
