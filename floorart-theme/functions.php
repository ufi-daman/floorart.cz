<?php
/**
 * FloorArt WordPress Theme — functions.php
 */

// ── Theme Support ─────────────────────────────────────────────────────────
add_action('after_setup_theme', function () {
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('custom-logo');
});

// ── Enqueue Scripts & Styles ──────────────────────────────────────────────
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'floorart-style',
        get_template_directory_uri() . '/assets/css/floorart.css',
        [],
        '1.0.0'
    );

    wp_enqueue_script(
        'floorart-main',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        '1.0.0',
        true
    );

    // Pass site URL to JS for AJAX contact form
    wp_localize_script('floorart-main', 'floorartData', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('floorart_contact'),
    ]);
});

// ── Body Classes ──────────────────────────────────────────────────────────
// Add same classes as the static HTML site so CSS works without changes
add_filter('body_class', function (array $classes): array {
    if (is_front_page()) {
        $classes[] = 'homepage';
    } else {
        $classes[] = 'inner-page';

        if (is_singular('projekt')) {
            $classes[] = 'page-projekt';
        } elseif (is_post_type_archive('projekt')) {
            $classes[] = 'page-portfolio';
        } elseif (is_page()) {
            $slug = get_post_field('post_name', get_the_ID());
            $classes[] = 'page-' . $slug;
        }
    }
    return $classes;
});

// ── Custom Post Type: Projekt ─────────────────────────────────────────────
add_action('init', function () {
    register_post_type('projekt', [
        'labels' => [
            'name'               => 'Projekty',
            'singular_name'      => 'Projekt',
            'add_new'            => 'Přidat projekt',
            'add_new_item'       => 'Přidat nový projekt',
            'edit_item'          => 'Upravit projekt',
            'view_item'          => 'Zobrazit projekt',
            'all_items'          => 'Všechny projekty',
            'search_items'       => 'Hledat projekty',
            'not_found'          => 'Žádné projekty.',
            'not_found_in_trash' => 'Žádné projekty v koši.',
        ],
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_rest'       => true,
        'has_archive'        => true,
        'rewrite'            => ['slug' => 'portfolio'],
        'menu_icon'          => 'dashicons-images-alt2',
        'menu_position'      => 5,
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
    ]);
});

// ── Project Meta Fields ───────────────────────────────────────────────────
add_action('init', function () {
    foreach (['_projekt_type', '_projekt_location', '_projekt_year', '_projekt_gallery'] as $key) {
        register_post_meta('projekt', $key, [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => 'string',
            'auth_callback' => fn() => current_user_can('edit_posts'),
        ]);
    }
});

// ── Project Meta Box ──────────────────────────────────────────────────────
add_action('add_meta_boxes', function () {
    add_meta_box(
        'floorart_projekt_details',
        'Detail projektu',
        'floorart_projekt_meta_html',
        'projekt',
        'normal',
        'high'
    );
});

function floorart_projekt_meta_html(WP_Post $post): void {
    wp_nonce_field('floorart_projekt_save', 'floorart_projekt_nonce');
    $type     = get_post_meta($post->ID, '_projekt_type', true);
    $location = get_post_meta($post->ID, '_projekt_location', true);
    $year     = get_post_meta($post->ID, '_projekt_year', true);
    $gallery  = get_post_meta($post->ID, '_projekt_gallery', true);
    ?>
    <table class="form-table" style="margin-top:0">
        <tr>
            <th><label for="projekt_type">Typ podlahy</label></th>
            <td><input type="text" id="projekt_type" name="projekt_type"
                       value="<?php echo esc_attr($type); ?>" class="regular-text"
                       placeholder="Epoxidová podlaha"></td>
        </tr>
        <tr>
            <th><label for="projekt_location">Lokalita</label></th>
            <td><input type="text" id="projekt_location" name="projekt_location"
                       value="<?php echo esc_attr($location); ?>" class="regular-text"
                       placeholder="Praha"></td>
        </tr>
        <tr>
            <th><label for="projekt_year">Rok realizace</label></th>
            <td><input type="text" id="projekt_year" name="projekt_year"
                       value="<?php echo esc_attr($year); ?>" class="regular-text"
                       placeholder="2024"></td>
        </tr>
        <tr>
            <th><label for="projekt_gallery">Galerie (URL obrázků)</label></th>
            <td>
                <textarea id="projekt_gallery" name="projekt_gallery"
                          rows="5" class="large-text"
                          placeholder="https://example.com/foto1.jpg&#10;https://example.com/foto2.jpg"><?php
                    echo esc_textarea($gallery);
                ?></textarea>
                <p class="description">Každé URL na samostatný řádek.</p>
            </td>
        </tr>
    </table>
    <?php
}

add_action('save_post_projekt', function (int $post_id): void {
    if (!isset($_POST['floorart_projekt_nonce'])) return;
    if (!wp_verify_nonce($_POST['floorart_projekt_nonce'], 'floorart_projekt_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $map = [
        'projekt_type'     => '_projekt_type',
        'projekt_location' => '_projekt_location',
        'projekt_year'     => '_projekt_year',
        'projekt_gallery'  => '_projekt_gallery',
    ];

    foreach ($map as $post_key => $meta_key) {
        if (array_key_exists($post_key, $_POST)) {
            update_post_meta($post_id, $meta_key, sanitize_textarea_field(wp_unslash($_POST[$post_key])));
        }
    }
});

// ── Contact Form AJAX Handler ─────────────────────────────────────────────
add_action('wp_ajax_nopriv_floorart_contact', 'floorart_handle_contact');
add_action('wp_ajax_floorart_contact',        'floorart_handle_contact');

function floorart_handle_contact(): void {
    check_ajax_referer('floorart_contact', 'nonce');

    $name    = sanitize_text_field(wp_unslash($_POST['name']    ?? ''));
    $email   = sanitize_email(wp_unslash($_POST['email']        ?? ''));
    $phone   = sanitize_text_field(wp_unslash($_POST['phone']   ?? ''));
    $subject = sanitize_text_field(wp_unslash($_POST['subject'] ?? ''));
    $message = sanitize_textarea_field(wp_unslash($_POST['message'] ?? ''));

    if (!$name || !is_email($email) || !$message) {
        wp_send_json_error(['message' => 'Vyplňte všechna povinná pole.']);
    }

    $to      = get_option('admin_email');
    $subj    = $subject ?: 'Nová zpráva z webu FloorArt';
    $body    = "Jméno: $name\nE-mail: $email\nTelefon: $phone\n\n$message";
    $headers = ["From: $name <$email>", 'Content-Type: text/plain; charset=UTF-8'];

    if (wp_mail($to, $subj, $body, $headers)) {
        wp_send_json_success(['message' => 'Zpráva odeslána.']);
    } else {
        wp_send_json_error(['message' => 'Odesílání selhalo, zkuste to prosím znovu.']);
    }
}

// ── Flush Rewrite Rules on Activation ─────────────────────────────────────
add_action('after_switch_theme', function () {
    flush_rewrite_rules();
});
