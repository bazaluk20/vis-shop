{if $product->to_privat_pay_part && $product->privat_max_pay_pp}
    <div class="logo_paypart logo_paypart--product">
        <div class="fn_privat_paypart__wrapper paypart__wrapper">
            {include file="svg.tpl" svgId="privatpart_svg"}

            {* html текст уведомления с ссылкой *}
            {if $settings->sm__privat_pay_part__icon_text}
                <div class="paypart__content">
                    <div class="paypart__text">
                        {$settings->sm__privat_pay_part__icon_text}
                    </div>
                </div>
            {/if}
            {if $product->privat_max_pay_pp}
                <div class="paypart__count_month">
                    <span class="count_month"
                          title="{$lang->privat_pp_alt_title|escape}">{$product->privat_max_pay_pp}</span>
                </div>
            {/if}
        </div>

        {* {if $product->privat_max_pay_ii}<a title="{$lang->privat_ii_alt_title|escape}"><span
                class="count_month">{$product->privat_max_pay_ii}</span></a>{/if} *}
    </div>
{/if}