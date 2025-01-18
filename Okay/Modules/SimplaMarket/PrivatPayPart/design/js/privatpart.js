$(document).ready(function() {
    $('.fn_privat_paypart__wrapper').hover(
        function(event) {
            $(this).css('z-index', 11);
            const tooltip = $(this).find('.paypart__content');

            if (typeof tooltip != 'undefined') {
                const cursorX = event.clientX;
                const screenWidth = $('.main').width();
                const parentWidth = $(this).width();
                const edgeThreshold = screenWidth * 0.45; // Пороговое значение (45% от ширины экрана)

                // Проверка, если курсор находится ближе к правому краю
                if (cursorX > edgeThreshold) {
                    tooltip.addClass('right');
                    tooltip.removeClass('left');
                } else if (cursorX - parentWidth / 2 < edgeThreshold) { // Проверка, если курсор находится ближе к левому краю
                    tooltip.addClass('left');
                    tooltip.removeClass('right');
                } else {
                    tooltip.removeClass('left right'); // Убираем классы, если не находимся близко к краю
                }

                tooltip.fadeIn(100);
            }
        },
        function() {
            $(this).css('z-index', 0);
            const tooltip = $(this).find('.paypart__content');
            tooltip.fadeOut(100);
        }
    );
});