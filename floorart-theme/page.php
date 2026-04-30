<?php
/**
 * FloorArt — page.php
 * Generic WordPress page template (Studio, Sluzby, Jak pracujeme, …)
 */
get_header();

while (have_posts()) : the_post();
    $hero_url = has_post_thumbnail()
        ? get_the_post_thumbnail_url(null, 'full')
        : 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1920&q=80';
?>

<!-- Hero -->
<div class="head-image-block"
     style="background-image: url('<?php echo esc_url($hero_url); ?>')">
  <h1><?php the_title(); ?></h1>
</div>

<main>
  <div class="container">
    <div class="section">
      <div class="infoblock text page-content">
        <?php the_content(); ?>
      </div>
    </div>
  </div>
</main>

<?php endwhile; ?>

<?php get_footer(); ?>
