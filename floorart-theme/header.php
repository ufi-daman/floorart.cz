<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ============ POPUP MENU ============ -->
<div class="popupmenu" id="popupmenu">
  <div class="popup-logo">
    <a href="<?php echo esc_url(home_url('/')); ?>">
      <img src="<?php echo esc_url(get_template_directory_uri() . '/img/logo.svg'); ?>"
           alt="<?php bloginfo('name'); ?>" height="32">
    </a>
  </div>
  <div id="popupClose" class="popup-close" aria-label="Zavřít menu">
    <span></span><span></span>
  </div>
  <nav>
    <ul>
      <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('studio'))); ?>">Studio</a></li>
      <li><a href="<?php echo esc_url(get_post_type_archive_link('projekt')); ?>">Portfolio</a></li>
      <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('sluzby'))); ?>">Služby</a></li>
      <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('jak-pracujeme'))); ?>">Jak pracujeme</a></li>
      <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('kontakt'))); ?>">Kontakt</a></li>
    </ul>
  </nav>
  <div class="popup-footer">
    <a href="tel:+420777000111">+420 777 000 111</a><br>
    <a href="mailto:info@floorart.cz">info@floorart.cz</a>
  </div>
</div>

<?php if (!is_front_page()) : ?>
<!-- ============ PAGE HEADER ============ -->
<header class="page-header">
  <div class="logoblock">
    <a href="<?php echo esc_url(home_url('/')); ?>">
      <img src="<?php echo esc_url(get_template_directory_uri() . '/img/logo.svg'); ?>"
           alt="<?php bloginfo('name'); ?>">
    </a>
  </div>
  <div class="header-right">
    <div class="lang-switcher"><a href="#">EN</a></div>
    <div class="burger" id="burger" role="button" tabindex="0" aria-label="Otevřít menu">
      <span></span><span></span><span></span>
    </div>
  </div>
</header>
<?php endif; ?>
