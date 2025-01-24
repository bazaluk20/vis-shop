$(document).on('click', '.fn_add_set_to_cart', function(e) {
    e.preventDefault();
    $.ajax({
        url: okay.router['cart_ajax'],
        data: {
            action: 'add_citem',
            variant_id: $(this).data('variant_id'),
            amount: 1,
        },
        dataType: 'json',
    }).done(function(data) {
        $('#cart_informer').html(data.cart_informer);
        $( '#fn_pop_up_cart' ).html( data.pop_up_cart );
        $.fancybox.open({
            'src': '#fn_pop_up_cart_wrap',
            opts: {
                touch: false
            }
        });
    });
});