if($(".fn_validate_cart").length>0) {
    $('.fn_validate_cart [name="phone"]').rules( "add", {
        required: true,
        messages: {
            required: "{$lang->form_enter_phone|escape}",
        }
    });
}