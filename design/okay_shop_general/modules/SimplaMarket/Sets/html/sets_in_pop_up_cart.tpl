{if $cart->sets}
    {foreach $cart->sets as $set}
        <div class="purchase__item d-flex align-items-start">
            <div class="purchase__image d-flex">
                {$lang->simplamarket__sets__set}
            </div>
            <div class="purchase__content">

                <div class="purchase__group">
                    <div class="purchase__price hidden-xs-down">
                        <div class="purchase__group_title hidden-xs-down">
                            <span data-language="cart_head_price">{$lang->cart_head_price}</span>
                        </div>
                        <div class="purchase__group_content">{($set->total_price / $set->amount)|convert} <span class="currency">{$currency->sign}</span> {if $set->target_variant->units}/ {$set->target_variant->units|escape}{/if}</div>
                    </div>

                    <div class="purchase__amount">
                        <div class="purchase__group_title hidden-xs-down">
                            <span data-language="cart_head_amoun">{$lang->cart_head_amoun}</span>
                        </div>
                        <div class="fn_product_amount purchase__group_content{if $settings->is_preorder} fn_is_preorder{/if} amount">
                            <span class="fn_minus amount__minus">&minus;</span>
                            <input class="amount__input" type="text" data-id="set_{$set->variant_id}_{$set->id}" name="amounts[{$set->target_variant->id}]" value="{$set->amount}" onblur="ajax_change_amount(this, {$set->variant->id});" data-max="{$purchase->variant->stock}">
                            <span class="fn_plus amount__plus">&plus;</span>
                        </div>
                    </div>
                    <div class="purchase__price_total">
                        <div class="purchase__group_title hidden-xs-down">
                            <span data-language="cart_head_total">{$lang->cart_head_total}</span>
                        </div>
                        <div class="purchase__group_content">{$set->total_price|convert} <span class="currency">{$currency->sign}</span></div>
                    </div>
                </div>


                {* Remove button *}
                <a class="purchase__remove" href="{url_generator route="cart_remove_item" variantId=$purchase->variant->id}" onclick="ajax_remove('set_{$set->variant_id}_{$set->id}');return false;" title="{$lang->cart_remove}">
                    {include file='svg.tpl' svgId='remove_icon'}
                </a>

                {*айтемы комплекта*}
                <div class="purchase__kit kit_cart">

                    {if $set->include}
                        <div class="kit_cart_item border_top">
                            <div class="kit_cart_item__image">
                                <a href="{url_generator route="product" url=$purchase->product->url}" class="kit_cart_item__image_link">
                                    {if $set->target_product->image}
                                        <img class="" alt="{$set->target_product->name|escape}" src="{$set->target_product->image->filename|resize:45:45}">
                                    {else}
                                        <div class="purchase__no_image d-flex align-items-start">
                                            {include file="svg.tpl" svgId="no_image"}
                                        </div>
                                    {/if}
                                </a>
                            </div>

                            <div class="kit_cart_item__content">
                                <div class="kit_cart_item__name">
                                    <a class="kit_cart_item__name_link" href="{url_generator route="product" url=$set->target_product->url}">{$set->target_product->name}</a>
                                    <i>{$set->target_variant->name|escape}</i>
                                </div>

                                <div class="kit_cart_item__price">
                                    {$set->target_variant->price|convert} <span class="kit_cart_item__currency">{$currency->sign}</span> x 1 {if $purchase->variant->units}/ {$purchase->variant->units|escape}{/if}
                                </div>
                            </div>
                        </div>
                    {/if}

                    {foreach $set->items as $item}
                        <div class="kit_cart_item {if $item@first && !$set->include}border_top{/if}">
                            <div class="kit_cart_item__image">
                                <a href="{url_generator route="product" url=$purchase->product->url}" class="kit_cart_item__image_link">
                                    {if $item->product->image}
                                        <img class="" alt="{$item->product->name|escape}" src="{$item->product->image->filename|resize:45:45}">
                                    {else}
                                        <div class="purchase__no_image d-flex align-items-start">
                                            {include file="svg.tpl" svgId="no_image"}
                                        </div>
                                    {/if}
                                </a>
                            </div>

                            <div class="kit_cart_item__content">
                                <div class="kit_cart_item__name">
                                    <a class="kit_cart_item__name_link" href="{url_generator route="product" url=$item->product->url}">{$item->product->name|escape}</a>
                                    <i>{$item->product->variant->name|escape}</i>
                                </div>

                                <div class="kit_cart_item__price">
                                    {$item->price_per_item|convert} <span class="kit_cart_item__currency">{$currency->sign}</span> x {$item->amount} {if $purchase->variant->units}/ {$purchase->variant->units|escape}{/if}
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
    {/foreach}
{/if}