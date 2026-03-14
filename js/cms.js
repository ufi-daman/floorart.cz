/**
 * FloorArt CMS — dynamické načítání obsahu z JSON souborů
 * Spravováno přes Decap CMS (admin/)
 */
(function () {
  'use strict';

  var body = document.body;

  function esc(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function fetchJSON(url, callback) {
    fetch(url)
      .then(function (r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
      })
      .then(callback)
      .catch(function () { /* zachováme statický fallback */ });
  }

  // ── Kontaktní údaje (všechny stránky — popup footer) ───────────────────
  function loadContact() {
    fetchJSON('/content/contact.json', function (c) {
      var phoneHref = 'tel:' + c.phone.replace(/\s/g, '');

      // Popup footer — telefon a e-mail na všech stránkách
      document.querySelectorAll('[data-cms="phone"]').forEach(function (el) {
        el.href = phoneHref;
        el.textContent = c.phone;
      });
      document.querySelectorAll('[data-cms="email"]').forEach(function (el) {
        el.href = 'mailto:' + c.email;
        el.textContent = c.email;
      });

      // Kontaktní stránka — detailní údaje
      if (body.classList.contains('page-kontakt')) {
        var el;
        if ((el = document.getElementById('contact-phone'))) {
          el.href = phoneHref;
          el.textContent = c.phone;
        }
        if ((el = document.getElementById('contact-email'))) {
          el.href = 'mailto:' + c.email;
          el.textContent = c.email;
        }
        if ((el = document.getElementById('contact-address'))) {
          el.innerHTML =
            esc(c.company) + '<br>' +
            esc(c.address) + '<br>' +
            esc(c.city);
        }
        if ((el = document.getElementById('contact-hours'))) {
          el.innerHTML = esc(c.hours).replace(/\n/g, '<br>');
        }
        if ((el = document.getElementById('contact-ico'))) {
          el.textContent = c.ico;
        }
      }
    });
  }

  // ── Portfolio ───────────────────────────────────────────────────────────
  function loadPortfolio() {
    if (!body.classList.contains('page-portfolio')) return;
    var grid = document.getElementById('portfolio-grid');
    if (!grid) return;

    fetchJSON('/content/portfolio.json', function (data) {
      grid.innerHTML = data.projects.map(function (p) {
        return (
          '<a href="' + esc(p.link || '#') + '" class="portfolio-item">' +
          '<img src="' + esc(p.image) + '" alt="' + esc(p.title) + '" loading="lazy">' +
          '<div class="portfolio-item-overlay"><div class="portfolio-item-info">' +
          '<h3>' + esc(p.title) + '</h3>' +
          '<p>' + esc(p.description) + '</p>' +
          '</div></div></a>'
        );
      }).join('');
    });
  }

  // ── Studio (texty + tým) ────────────────────────────────────────────────
  function loadStudio() {
    if (!body.classList.contains('page-studio')) return;

    fetchJSON('/content/studio.json', function (d) {
      var el;
      if ((el = document.getElementById('studio-about-title')))      el.textContent = d.about_title;
      if ((el = document.getElementById('studio-about-p1')))         el.textContent = d.about_p1;
      if ((el = document.getElementById('studio-about-p2')))         el.textContent = d.about_p2;
      if ((el = document.getElementById('studio-philosophy-title'))) el.textContent = d.philosophy_title;
      if ((el = document.getElementById('studio-philosophy-p1')))    el.textContent = d.philosophy_p1;
      if ((el = document.getElementById('studio-philosophy-p2')))    el.textContent = d.philosophy_p2;
    });

    fetchJSON('/content/team.json', function (data) {
      var grid = document.getElementById('team-grid');
      if (!grid) return;
      grid.innerHTML = data.members.map(function (m) {
        return (
          '<div class="team-member">' +
          '<img src="' + esc(m.photo) + '" alt="' + esc(m.name) + '" loading="lazy">' +
          '<h3>' + esc(m.name) + '</h3>' +
          '<p>' + esc(m.role) + '</p>' +
          '</div>'
        );
      }).join('');
    });
  }

  // ── Služby ──────────────────────────────────────────────────────────────
  function loadSluzby() {
    if (!body.classList.contains('page-sluzby')) return;
    var list = document.getElementById('service-list');
    if (!list) return;

    fetchJSON('/content/sluzby.json', function (data) {
      list.innerHTML = data.services.map(function (s, i) {
        var num = String(i + 1).padStart(2, '0');
        return (
          '<li class="service-item">' +
          '<span class="service-number">' + num + '</span>' +
          '<div class="service-content">' +
          '<h3>' + esc(s.title) + '</h3>' +
          '<p>' + esc(s.description) + '</p>' +
          '</div></li>'
        );
      }).join('');
    });
  }

  // ── Jak pracujeme ───────────────────────────────────────────────────────
  function loadJakPracujeme() {
    if (!body.classList.contains('page-jak-pracujeme')) return;

    fetchJSON('/content/jak-pracujeme.json', function (data) {
      var intro = document.getElementById('process-intro');
      if (intro) intro.textContent = data.intro;

      var list = document.getElementById('process-list');
      if (!list) return;
      list.innerHTML = data.steps.map(function (s, i) {
        var num = String(i + 1).padStart(2, '0');
        return (
          '<li class="process-step">' +
          '<span class="step-number">' + num + '</span>' +
          '<div class="step-content">' +
          '<h3>' + esc(s.title) + '</h3>' +
          '<p>' + esc(s.description) + '</p>' +
          '</div></li>'
        );
      }).join('');
    });
  }

  // ── Init ────────────────────────────────────────────────────────────────
  loadContact();
  loadPortfolio();
  loadStudio();
  loadSluzby();
  loadJakPracujeme();

})();
