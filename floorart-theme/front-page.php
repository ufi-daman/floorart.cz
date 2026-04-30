<?php
/**
 * FloorArt — front-page.php
 * Homepage with fullscreen slider showing latest 5 projects
 */

get_header();

$slider_query = new WP_Query([
    'post_type'      => 'projekt',
    'posts_per_page' => 5,
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

$slides = [];
if ($slider_query->have_posts()) {
    while ($slider_query->have_posts()) {
        $slider_query->the_post();
        $slides[] = [
            'title'    => get_the_title(),
            'type'     => get_post_meta(get_the_ID(), '_projekt_type', true),
            'location' => get_post_meta(get_the_ID(), '_projekt_location', true),
            'year'     => get_post_meta(get_the_ID(), '_projekt_year', true),
            'image'    => get_the_post_thumbnail_url(null, 'full') ?: '',
            'link'     => get_permalink(),
        ];
    }
    wp_reset_postdata();
}

// Fallback slide if no projects yet
if (empty($slides)) {
    $slides[] = [
        'title'    => get_bloginfo('name'),
        'type'     => 'Designové podlahy',
        'location' => 'Praha',
        'year'     => date('Y'),
        'image'    => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1920&q=80',
        'link'     => get_post_type_archive_link('projekt'),
    ];
}
?>

<div id="app">

  <!-- Logo -->
  <div class="logoblock">
    <a href="<?php echo esc_url(home_url('/')); ?>">
      <img src="<?php echo esc_url(get_template_directory_uri() . '/img/logo.svg'); ?>"
           alt="<?php bloginfo('name'); ?>">
    </a>
  </div>

  <!-- Burger -->
  <div class="burger" id="burger" aria-label="Otevřít menu" role="button" tabindex="0">
    <span></span><span></span><span></span>
  </div>

  <!-- Language switcher -->
  <div class="lang-switcher">
    <a href="#">EN</a>
  </div>

  <!-- Social links -->
  <div class="social">
    <ul>
      <li><a href="#" target="_blank" rel="noopener">Instagram</a></li>
      <li><a href="#" target="_blank" rel="noopener">Facebook</a></li>
    </ul>
  </div>

  <!-- Project numbers -->
  <div class="projectnumbers">
    <ul>
      <?php foreach ($slides as $i => $slide) : ?>
      <li class="<?php echo $i === 0 ? 'active' : ''; ?>">
        <?php echo esc_html(str_pad($i + 1, 2, '0', STR_PAD_LEFT)); ?>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <!-- Fullscreen slider -->
  <div class="slider">
    <div class="slidesHome" id="slider">
      <?php foreach ($slides as $i => $slide) : ?>
      <div class="slide-item"
           style="background-image: url('<?php echo esc_url($slide['image']); ?>')">
        <a href="<?php echo esc_url($slide['link']); ?>" class="slide-link" aria-label="<?php echo esc_attr($slide['title']); ?>"></a>
        <div class="slide-content">
          <span class="slide-number"><?php echo esc_html(str_pad($i + 1, 2, '0', STR_PAD_LEFT)); ?></span>
          <div class="slide-info">
            <h2><?php echo esc_html($slide['title']); ?></h2>
            <p>
              <?php echo esc_html($slide['type']); ?>
              <?php if ($slide['location'] || $slide['year']) : ?>
              &mdash; <?php echo esc_html(trim($slide['location'] . ', ' . $slide['year'], ', ')); ?>
              <?php endif; ?>
            </p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Bottom navigation -->
  <nav class="main-menu home" aria-label="Hlavní navigace">
    <ul>
      <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('studio'))); ?>">Studio</a></li>
      <li><a href="<?php echo esc_url(get_post_type_archive_link('projekt')); ?>">Portfolio</a></li>
      <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('jak-pracujeme'))); ?>">Jak pracujeme</a></li>
      <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('kontakt'))); ?>">Kontakt</a></li>
    </ul>
  </nav>

</div>

<?php get_footer(); ?>
