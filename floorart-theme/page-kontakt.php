<?php
/**
 * FloorArt — page-kontakt.php
 * Contact page (no hero image — matches bekarch style)
 */
get_header();
?>

<main>
  <div class="container">
    <div class="section" style="padding-top: 100px;">

      <p class="kontakth">Kontakt</p>

      <div class="contact-grid">

        <!-- Kontaktní info -->
        <div class="contact-info">
          <h3>Telefon</h3>
          <a href="tel:+420777000111">+420 777 000 111</a>

          <h3>E-mail</h3>
          <a href="mailto:info@floorart.cz">info@floorart.cz</a>

          <h3>Adresa</h3>
          <p>
            FloorArt s.r.o.<br>
            Vinohradská 28<br>
            120 00 Praha 2
          </p>

          <h3>Provozní doba</h3>
          <p>Po–Pá: 8:00–17:00<br>Realizace i o víkendech</p>

          <h3>IČO</h3>
          <p>12345678</p>
        </div>

        <!-- Kontaktní formulář -->
        <div>
          <form id="contactForm" class="contact-form" novalidate>
            <div class="form-group">
              <input type="text" name="name" placeholder="Jméno a příjmení *" required>
            </div>
            <div class="form-group">
              <input type="email" name="email" placeholder="E-mail *" required>
            </div>
            <div class="form-group">
              <input type="tel" name="phone" placeholder="Telefon">
            </div>
            <div class="form-group">
              <input type="text" name="subject" placeholder="Předmět">
            </div>
            <div class="form-group">
              <textarea name="message" placeholder="Zpráva *" required></textarea>
            </div>
            <button type="submit">Odeslat</button>
          </form>
        </div>

      </div>
    </div>
  </div>
</main>

<?php get_footer(); ?>
