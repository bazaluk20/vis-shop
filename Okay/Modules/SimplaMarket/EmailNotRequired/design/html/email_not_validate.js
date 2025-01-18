if($(".fn_validate_cart").length>0) {
    $('.fn_validate_cart [name="email"]').rules('remove');

    $('.fn_validate_cart [name="email"]').rules("add", {
        email: {
            required: false,
            email: true
        },
    });
    //Убираем символ * в плейсхолдере
    let email_input = $('input[name="email"]');
    let span =  email_input.closest('.form__group').find('.form__placeholder');
    span.html(span.html().replace('*', ''));

}


