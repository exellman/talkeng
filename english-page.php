<?php
// Template name: English
use Timber\Timber;

$context = Timber::context();

$post = Timber::get_post();
$context['post'] = $post;

// Дані для банера
$context['banner'] = [
    'title' => get_field('banner_title') ?: 'Заголовок банера',
    'subtitle' => get_field('banner_subtitle') ?: 'Підзаголовок банера',
    'button_text' => get_field('banner_button_text') ?: 'Зворотній зв’язок',
    'popup_title' => get_field('popup_title') ?: 'Зворотній зв’язок',
    'submit_text' => get_field('popup_submit_text') ?: 'Відправити',
    'thank_you_message' => get_field('popup_thank_you_message') ?: 'Дякуємо за звернення!',
];

// Дані для блоку з курсами
$context['courses'] = [
    'title' => get_field('section_title') ?: 'Наші курси',
    'items' => array_map(function ($post) {
        $post->excerpt = get_the_excerpt($post->ID);
        return $post;
    },
    Timber::get_posts([
        'post_type' => 'courses',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ])),
];

// Дані для блоку з контактами
$context['popup'] = [
    'form_title' => get_field('form_title') ?: 'Замовлення курсу', // Заголовок форми
    'submit_text' => get_field('submit_text') ?: 'Відправити', // Текст кнопки
    'thank_you_text' => get_field('thank_you_text') ?: 'Дякуємо за звернення!', // Текст кнопки
];

// Рендеримо Twig-шаблон
Timber::render('pages/english.twig', $context);