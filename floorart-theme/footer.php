<!-- ============ FOOTER ============ -->
<footer>
  <span>&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?></span>
  <a href="<?php echo esc_url(home_url('/')); ?>">
    <img src="<?php echo esc_url(get_template_directory_uri() . '/img/logo.svg'); ?>"
         alt="<?php bloginfo('name'); ?>" style="height:20px; opacity:0.4;">
  </a>
  <span><a href="<?php echo esc_url(get_permalink(get_page_by_path('kontakt'))); ?>">Kontakt</a></span>
</footer>

<?php wp_footer(); ?>
</body>
</html>
