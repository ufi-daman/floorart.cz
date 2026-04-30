<?php
/**
 * FloorArt — single-projekt.php
 * Individual project detail page
 */
get_header();

while (have_posts()) : the_post();
    $type     = get_post_meta(get_the_ID(), '_projekt_type', true);
    $location = get_post_meta(get_the_ID(), '_projekt_location', true);
    $year     = get_post_meta(get_the_ID(), '_projekt_year', true);
    $gallery  = get_post_meta(get_the_ID(), '_projekt_gallery', true);
    $hero_url = has_post_thumbnail()
        ? get_the_post_thumbnail_url(null, 'full')
        : 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1920&q=80';
?>

<!-- Hero -->
<div class="head-image-block projekt-hero"
     style="background-image: url('<?php echo esc_url($hero_url); ?>')">
  <h1><?php the_title(); ?></h1>
</div>

<main>
  <div class="container">
    <div class="section projekt-section">

      <!-- Zpět na portfolio -->
      <div class="projekt-back">
        <a href="<?php echo esc_url(get_post_type_archive_link('projekt')); ?>">&larr; Zpět na portfolio</a>
      </div>

      <!-- Metadata -->
      <?php if ($type || $location || $year) : ?>
      <div class="projekt-meta">
        <?php if ($type)     echo '<span>' . esc_html($type) . '</span>'; ?>
        <?php if ($location) echo '<span>' . esc_html($location) . '</span>'; ?>
        <?php if ($year)     echo '<span>' . esc_html($year) . '</span>'; ?>
      </div>
      <?php endif; ?>

      <!-- Popis -->
      <div class="projekt-detail">
        <?php the_content(); ?>
      </div>

      <!-- Galerie -->
      <?php
      if ($gallery) {
          $urls = array_filter(array_map('trim', explode("\n", $gallery)));
          if ($urls) : ?>
      <div class="projekt-gallery">
        <?php foreach (array_values($urls) as $i => $url) : ?>
        <img src="<?php echo esc_url($url); ?>"
             alt="<?php echo esc_attr(get_the_title() . ' — foto ' . ($i + 1)); ?>"
             loading="lazy">
        <?php endforeach; ?>
      </div>
      <?php endif;
      } ?>

      <!-- CTA -->
      <div class="projekt-cta">
        <a href="<?php echo esc_url(get_permalink(get_page_by_path('kontakt'))); ?>" class="btn">
          Mám zájem o podobný projekt
        </a>
      </div>

    </div>
  </div>
</main>

<?php endwhile; ?>

<?php get_footer(); ?>
