if($(".fn_validate_cart").length>0) {
    var form_enter_last_name = "{$lang->form_enter_last_name|escape}";
    $('.fn_validate_cart [name="last_name"]').rules( "add", {
        required: true,
        messages: {
            required: "{$lang->form_enter_last_name|escape}",
        }
    });
}
