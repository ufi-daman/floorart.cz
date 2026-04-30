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

  function fetchJSON(url, callback, errCallback) {
    fetch(url)
      .then(function (r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
      })
      .then(callback)
      .catch(function (err) {
        console.warn('CMS fetch failed:', url, err);
        if (errCallback) errCallback(err);
      });
  }

  // ── Kontaktní údaje (všechny stránky — popup footer) ───────────────────
  function loadContact() {
    fetchJSON('content/contact.json', function (c) {
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

    fetchJSON('content/portfolio.json', function (data) {
      grid.innerHTML = data.projects.map(function (p) {
        var href = p.slug ? 'projekt.html?id=' + encodeURIComponent(p.slug) : (p.link || '#');
        return (
          '<a href="' + esc(href) + '" class="portfolio-item">' +
          '<img src="' + esc(p.image) + '" alt="' + esc(p.title) + '" loading="lazy">' +
          '<div class="portfolio-item-overlay"><div class="portfolio-item-info">' +
          '<h3>' + esc(p.title) + '</h3>' +
          '<p>' + esc(p.description) + '</p>' +
          '</div></div></a>'
        );
      }).join('');
    });
  }

  // ── Projekt detail ───────────────────────────────────────────────────────
  function loadProjekt() {
    if (!body.classList.contains('page-projekt')) return;

    var params = new URLSearchParams(window.location.search);
    var slug = params.get('id');

    function showProjektError(msg) {
      var titleEl = document.getElementById('projekt-title');
      var detailEl = document.getElementById('projekt-detail-text');
      if (titleEl) titleEl.textContent = msg;
      if (detailEl) detailEl.innerHTML = '<a href="portfolio.html">&larr; Zpět na portfolio</a>';
    }

    fetchJSON('content/portfolio.json', function (data) {
      var project = data.projects.filter(function (p) { return p.slug === slug; })[0];

      if (!project) {
        showProjektError('Projekt nenalezen');
        return;
      }

      // <title> a meta description
      document.getElementById('page-title').textContent = project.title + ' — FloorArt';
      document.getElementById('page-desc').setAttribute('content', project.description + '. ' + (project.detail || ''));

      // Hero
      var hero = document.getElementById('projekt-hero');
      if (hero) hero.style.backgroundImage = 'url(' + project.image + ')';
      document.getElementById('projekt-title').textContent = project.title;

      // Metadata
      var type = document.getElementById('projekt-type');
      var loc  = document.getElementById('projekt-location');
      var year = document.getElementById('projekt-year');
      if (type) type.textContent = project.type || '';
      if (loc)  loc.textContent  = project.location || '';
      if (year) year.textContent = project.year || '';

      // Detail text
      var detail = document.getElementById('projekt-detail-text');
      if (detail) detail.textContent = project.detail || '';

      // Galerie
      var gallery = document.getElementById('projekt-gallery');
      if (gallery && project.gallery && project.gallery.length) {
        gallery.innerHTML = project.gallery.map(function (src, i) {
          return '<img src="' + esc(src) + '" alt="' + esc(project.title) + ' — foto ' + (i + 1) + '" loading="lazy">';
        }).join('');
      }
    }, function () {
      showProjektError('Nepodařilo se načíst projekt');
    });
  }

  // ── Studio (texty + tým) ────────────────────────────────────────────────
  function loadStudio() {
    if (!body.classList.contains('page-studio')) return;

    fetchJSON('content/studio.json', function (d) {
      var el;
      if ((el = document.getElementById('studio-about-title')))      el.textContent = d.about_title;
      if ((el = document.getElementById('studio-about-p1')))         el.textContent = d.about_p1;
      if ((el = document.getElementById('studio-about-p2')))         el.textContent = d.about_p2;
      if ((el = document.getElementById('studio-philosophy-title'))) el.textContent = d.philosophy_title;
      if ((el = document.getElementById('studio-philosophy-p1')))    el.textContent = d.philosophy_p1;
      if ((el = document.getElementById('studio-philosophy-p2')))    el.textContent = d.philosophy_p2;
    });

    fetchJSON('content/team.json', function (data) {
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

    fetchJSON('content/sluzby.json', function (data) {
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

    fetchJSON('content/jak-pracujeme.json', function (data) {
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
  loadProjekt();
  loadStudio();
  loadSluzby();
  loadJakPracujeme();

})();
