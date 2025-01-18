$(function () {

    $(document).on('change', 'select[name^="privat_number_of_months"]', function () {

        let merchant_type = $(this).data('merchant_type'),
            currency_id = $('input[name="payment_method_id"]:checked').data('currency_id'),
            months = $(this).children(':selected').val(),
            price = okay.convert($('input[name=delivery_id]:checked').data('total_price'), currency_id, false),
            resCalc = PP_CALCULATOR.calculatePhys(months, price),
            currency_sign = $(this).data('currency_sign'),
            payment_block = $(this).closest('.fn_payment_method__item'),
            pb_pp_value;

        if (merchant_type === 'PP') {
            pb_pp_value = resCalc.ppValue;
        } else {
            pb_pp_value = resCalc.ipValue;
        }

        payment_block.find('.fn_payment_price').text('{$lang->privat_first_pay|escape}: ' + pb_pp_value);

        payment_block.find('.pb_payment_description').text('{$lang->privat_first_pay|escape} ' + pb_pp_value + ' ' + currency_sign + ', {$lang->privat_first_pay_2|escape} ' + (resCalc.payCount - 1) + ' {$lang->privat_first_pay_3|escape} ' + pb_pp_value + ' ' + currency_sign);

        payment_block.find('input[name^="privat_pay_count"]').val(resCalc.payCount);
        payment_block.find('input[name^="privat_pp_value"]').val(pb_pp_value);

    });

    $('input[name=payment_method_id]:checked').trigger('change');
    $('select[name^=privat_number_of_months]').trigger('change');

    $(document).on('change', '[name="delivery_id"]', function () {
        $('select[name^=privat_number_of_months]').trigger('change');
    });
});

