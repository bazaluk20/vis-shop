{$purchasesNotPayPart = []}
{foreach $purchases as $purchase}
    {if !$purchase->product->to_privat_pay_part}
        {$purchasesNotPayPart[] = $purchase}
    {/if}
{/foreach}

{if !empty($purchasesNotPayPart)}
    {$lang->privat_unavailable_purchases|escape}:
    {foreach $purchasesNotPayPart as $purchase}
        <p><b>
                {$purchase->product->name|escape}
                {if !empty($purchase->variant->name)}
                    ({$purchase->variant->name|escape})
                {/if}
            </b></p>
    {/foreach}
{elseif $needSetPaidData}
    {* todo Здесь форма для просчета, если в корзине не указали месяцы *}
{else}
    
    {$lang->privat_first_paid} {$privatPaymentData->value}&nbsp;{$all_currencies[$payment_method->currency_id]->sign}
    
    <form method="post" action="{url_generator route='SimplaMarket_PrivatPayPart_create_payment' lang_id=1}">
        <input type="hidden" name="order_id" value="{$order_id|escape}">
        <input type="hidden" name="merchant_type" value="{$merchant_type|escape}">
        <input type="submit" class="button" value="{$lang->privat_pay_button|escape} &#8594;">
    </form>

{/if}