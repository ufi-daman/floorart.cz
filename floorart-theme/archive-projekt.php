<?php
/**
 * FloorArt — archive-projekt.php
 * Portfolio grid — all projects
 */
get_header();
?>

<!-- Hero -->
<div class="head-image-block"
     style="background-image: url('https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1920&q=80')">
  <h1>Portfolio</h1>
</div>

<main>
  <div class="container wide">
    <div class="portfolio-grid">

      <?php if (have_posts()) : while (have_posts()) : the_post();
        $type     = get_post_meta(get_the_ID(), '_projekt_type', true);
        $location = get_post_meta(get_the_ID(), '_projekt_location', true);
        $year     = get_post_meta(get_the_ID(), '_projekt_year', true);
        $parts    = array_filter([$type, trim($location . ' ' . $year)]);
        $desc     = implode(' · ', $parts);
      ?>
      <a href="<?php the_permalink(); ?>" class="portfolio-item">
        <?php if (has_post_thumbnail()) : ?>
        <img src="<?php echo esc_url(get_the_post_thumbnail_url(null, 'large')); ?>"
             alt="<?php the_title_attribute(); ?>"
             loading="lazy">
        <?php else : ?>
        <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80"
             alt="<?php the_title_attribute(); ?>"
             loading="lazy">
        <?php endif; ?>
        <div class="portfolio-item-overlay">
          <div class="portfolio-item-info">
            <h3><?php the_title(); ?></h3>
            <p><?php echo esc_html($desc); ?></p>
          </div>
        </div>
      </a>

      <?php endwhile; endif; ?>

    </div>
  </div>
</main>

<?php get_footer(); ?>
