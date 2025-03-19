<?php

function disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('emoji_svg_url', '__return_false');
}
add_action('init', 'disable_emojis');

function clean_wp_head() {
    remove_action('wp_head', 'rsd_link');                      
    remove_action('wp_head', 'wlwmanifest_link');              
    remove_action('wp_head', 'wp_generator');                  
    remove_action('wp_head', 'wp_shortlink_wp_head');          
    remove_action('wp_head', 'feed_links_extra', 3);           
    remove_action('wp_head', 'feed_links', 2);                 
    remove_action('wp_head', 'rest_output_link_wp_head');      
    remove_action('wp_head', 'wp_oembed_add_discovery_links'); 
    remove_action('template_redirect', 'wp_shortlink_header', 11);
}
add_action('init', 'clean_wp_head');

add_filter('xmlrpc_enabled', '__return_false');

function remove_css_js_ver($src) {
    if (strpos($src, '?ver=') !== false) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'remove_css_js_ver', 10, 2);
add_filter('script_loader_src', 'remove_css_js_ver', 10, 2);

function disable_embeds() {
    remove_action('wp_head', 'wp_oembed_add_host_js');
}
add_action('init', 'disable_embeds');

remove_action('wp_head', 'wp_resource_hints', 2);

add_filter('show_admin_bar', '__return_false'); // Це вимикає адмін бар!!!

function disable_gutenberg_editor() {
    // Відключаємо Gutenberg для всіх типів записів
    add_filter('use_block_editor_for_post', '__return_false', 10);
    add_filter('use_block_editor_for_post_type', '__return_false', 10);
}
add_action('init', 'disable_gutenberg_editor');

// Відключаємо стили Gutenberg на фронтенді
function disable_gutenberg_styles() {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-block-style'); // Якщо використовується WooCommerce
}
add_action('wp_enqueue_scripts', 'disable_gutenberg_styles', 100);

// Ввімкнення Timber
if (!class_exists('Timber')) {
    die("❌ Timber-плагін не активовано!");
}

class StarterSite extends Timber\Site {
    function __construct() {
        add_theme_support('menus');
        add_theme_support('post-thumbnails');
        add_theme_support('title-tag');
        parent::__construct();
    }
}

new StarterSite();

Timber::$dirname = ['templates', 'templates/pages', 'templates/components', 'templates/layouts'];

// Підключення стилів і скриптів
function courses_enqueue_assets() {
    wp_enqueue_style('main-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('slick-style', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
    wp_enqueue_script('jquery-script', get_template_directory_uri() . '/assets/js/jquery-3.7.1.min.js', [], '1.0', true);
    wp_enqueue_script('slick-script', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', [], '1.0', true);
    wp_enqueue_script('main-script', get_template_directory_uri() . '/assets/js/main.js', [], '1.0', true);
}
add_action('wp_enqueue_scripts', 'courses_enqueue_assets');


// Реєстрація меню
function register_my_menus() {
    register_nav_menus( array(
        'primary' => __( 'Головне меню' ),
    ));
}
add_action( 'init', 'register_my_menus' );

// Новий post type курсів
function create_course_post_type() {
    register_post_type( 'courses',
        array(
            'labels' => [
                'name' => __('Courses'),
                'singular_name' => __('Course'),
            ],
            'public' => true,
            'has_archive' => true,
            'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'rewrite' => ['slug' => 'courses'],
        )
    );
}
add_action( 'init', 'create_course_post_type' );

function create_course_inquiries_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'course_inquiries';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        course_name VARCHAR(255) NOT NULL,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('init', 'create_course_inquiries_table');

function handle_course_form_submission() {
    global $wpdb;

    if (!isset($_POST['action']) || $_POST['action'] !== 'submit_course_form') {
        wp_send_json_error(['message' => 'Invalid request']);
    }

    // Отримуємо дані з форми
    $course_name = sanitize_text_field($_POST['course_name']);
    $name = sanitize_text_field($_POST['name']);
    $phone = sanitize_text_field($_POST['phone']);
    $email = sanitize_email($_POST['email']);

    if (empty($name) || empty($phone) || empty($email)) {
        wp_send_json_error(['message' => 'Будь ласка, заповніть всі поля']);
    }

    // Зберігаємо дані в таблиці
    $table_name = $wpdb->prefix . 'course_inquiries';
    $result = $wpdb->insert($table_name, [
        'course_name' => $course_name,
        'name' => $name,
        'phone' => $phone,
        'email' => $email,
    ]);

    if (!$result) {
        wp_send_json_error(['message' => 'Не вдалося зберегти дані']);
    }

    wp_send_json_success(['message' => 'Дякуємо за заявку!']);
}
add_action('wp_ajax_submit_course_form', 'handle_course_form_submission');
add_action('wp_ajax_nopriv_submit_course_form', 'handle_course_form_submission');


function register_course_inquiries_menu() {
    add_menu_page(
        'Заявки на курси',
        'Заявки на курси',
        'manage_options',
        'course-inquiries',
        'render_course_inquiries_page',
        'dashicons-feedback',
        20
    );
}
add_action('admin_menu', 'register_course_inquiries_menu');

function render_course_inquiries_page() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'course_inquiries';

    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        $wpdb->delete($table_name, ['id' => $id]);
    
        // Перенумеровуємо всі записи
        $inquiries = $wpdb->get_results("SELECT id FROM $table_name ORDER BY id ASC");
        $new_id = 1;
        foreach ($inquiries as $inquiry) {
            $wpdb->update($table_name, ['id' => $new_id], ['id' => $inquiry->id]);
            $new_id++;
        }
    
        // Скидаємо AUTO_INCREMENT до наступного значення
        $wpdb->query("ALTER TABLE $table_name AUTO_INCREMENT = $new_id");
    
        // Перезавантажуємо сторінку
        echo '<script>window.location.href = "' . admin_url('admin.php?page=course-inquiries') . '";</script>';
        exit;
    }

    ob_start();

    // Отримуємо всі заявки, сортуємо за датою створення (найновіші зверху)
    $inquiries = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");

    // Виведення таблиці
    echo '<div class="wrap">';
    echo '<h1>Заявки на курси</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>
            <tr>
                <th>#</th>
                <th>Курс</th>
                <th>Ім\'я</th>
                <th>Телефон</th>
                <th>Email</th>
                <th>Дата</th>
                <th>Дії</th>
            </tr>
          </thead>';
    echo '<tbody>';
    if ($inquiries) {
        $counter = 1; // Лічильник для порядкових номерів
        foreach ($inquiries as $inquiry) {
            echo '<tr>';
            echo '<td>' . $counter . '</td>'; // Відображаємо порядковий номер
            echo '<td>' . esc_html($inquiry->course_name) . '</td>';
            echo '<td>' . esc_html($inquiry->name) . '</td>';
            echo '<td>' . esc_html($inquiry->phone) . '</td>';
            echo '<td>' . esc_html($inquiry->email) . '</td>';
            echo '<td>' . esc_html($inquiry->created_at) . '</td>';
            echo '<td><a href="' . esc_url(admin_url('admin.php?page=course-inquiries&delete=' . $inquiry->id)) . '" class="button button-danger" onclick="return confirm(\'Ви впевнені, що хочете видалити цю заявку?\')">Видалити</a></td>';
            echo '</tr>';
            $counter++; // Збільшуємо лічильник
        }
    } else {
        echo '<tr><td colspan="7">Заявок не знайдено</td></tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';

    ob_end_flush();
}