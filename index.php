<?php

use Timber\Timber;

$context = Timber::context();

// Рендеримо Twig-шаблон для головної сторінки
Timber::render('pages/index.twig', $context);