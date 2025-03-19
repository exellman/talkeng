jQuery(document).ready(function ($) {    

    const $popup = $('#course-popup');
    const $popupClose = $('#popup-close');
    const $thankYouMessage = $('#thank-you-message');
    const $popupForm = $('#course-form');

    // Відкрити попап і передати назву курсу
    $('.open-popup').on('click', function () {
        const courseName = $(this).data('course') || 'Просто консультація'; // Отримуємо назву курсу або "Просто консультація"
        $('#course-name').val(courseName); // Передаємо назву курсу в приховане поле
        $popupForm.show(); // Показуємо форму
        $thankYouMessage.hide(); // Ховаємо повідомлення
        $popup.fadeIn();
    });

    // Закрити попап
    $popupClose.on('click', function () {
        $popup.fadeOut();
    });

    // Закрити попап при кліку поза його межами
    $(document).on('click', function (e) {
        if ($(e.target).is('#course-popup')) {
            $popup.fadeOut();
        }
    });

    // Відправка форми через AJAX
    $popupForm.on('submit', function (e) {
        e.preventDefault();

        const formData = $popupForm.serialize() + '&action=submit_course_form'; // Додаємо action

        $.ajax({
            url: 'http://localhost/courses/wp-admin/admin-ajax.php', // URL для обробки AJAX-запитів у WordPress
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    $popupForm.hide(); // Ховаємо форму
                    $thankYouMessage.show(); // Показуємо повідомлення
                } else {
                    alert(response.data.message || 'Сталася помилка');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
                alert('Сталася помилка при відправці форми');
            },
        });
    });

    $('#phone').on('input', function () {
        $(this).val($(this).val().replace(/[^0-9+() -]/g, '')); // Видаляємо всі недозволені символи
    });

    // Slider з'являється <= 800px
    if ($(window).width() <= 800) {
        if (!$('.courses-holder').hasClass('slick-initialized')) {
            $('.courses-holder').slick({
                arrows: false,
                dots: true,
                infinite: true,
                speed: 300,
                slidesToShow: 1,
                adaptiveHeight: false
            });
        }
    }
    
    // Burger Menu
    $('.burger-menu').on('click', function () {
        $('.burger-menu').toggleClass('active'); // Додаємо/забираємо клас active
        $('.main-nav').toggleClass('show'); // Додаємо/забираємо клас show
    });

});