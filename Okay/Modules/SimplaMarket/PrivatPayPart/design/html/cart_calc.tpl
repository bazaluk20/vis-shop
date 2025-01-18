{if $payment_method->module == 'SimplaMarket/PrivatPayPart'}

    {$purchasesNotPayPart = []}
    {$maxPaymentsNum = []}
    {foreach $cart->purchases as $purchase}
        {if !$purchase->product->to_privat_pay_part}
            {$purchasesNotPayPart[] = $purchase}
        {/if}

        {if $payment_method->settings.merchant_type == 'PP' && !empty($purchase->product->privat_max_pay_pp)}
            {$maxPaymentsNum[] = $purchase->product->privat_max_pay_pp}
        {/if}

        {if $payment_method->settings.merchant_type == 'II' && !empty($purchase->product->privat_max_pay_ii)}
            {$maxPaymentsNum[] = $purchase->product->privat_max_pay_ii}
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
    {else}
        <div class="pb_pay_part_payment_settings">
            {$lang->privat_cart_months_title}

            {if $payment_method->settings.merchant_type == 'PP'}
                {$maxPaymentsNum[] = 6}
            {elseif $payment_method->settings.merchant_type == 'II'}
                {$maxPaymentsNum[] = 24}
            {/if}

            {* Выбираем минимальное количество платежей *}
            {$maxPaymentsNum = min($maxPaymentsNum)}
            <select name="privat_number_of_months[{$payment_method->settings.merchant_type}]"
                    data-merchant_type="{$payment_method->settings.merchant_type}"
                    data-currency_sign="{$all_currencies[$payment_method->currency_id]->sign}">
                {section name=pay_part start=2 loop=($maxPaymentsNum+1) step=1}
                    <option value="{$smarty.section.pay_part.index}"
                            {if $smarty.section.pay_part.index == $smarty.section.pay_part.loop-1}selected{/if}>{$smarty.section.pay_part.index}</option>
                {/section}
            </select>

            <div class="pb_payment_description"></div>
            {*<iframe height="185" width="300" style="border: none;" src="https://paypartslimit.privatbank.ua/pp-limit/widgetlimit?shopsource=1"></iframe>*}

            <input type="hidden" name="privat_pay_count[{$payment_method->settings.merchant_type}]" value="">
            <input type="hidden" name="privat_pp_value[{$payment_method->settings.merchant_type}]" value="">
        </div>
    {/if}
{/if}