{if $purchase->set}
    {$set = $purchase->set}

    {if $set->items}
        {*айтемы комплекта*}
        <div class="purchase__kit kit_cart">
            {if $set->include && false}
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
                <div style="padding: 10px 0;">
                    <div style="float: left; width: 50px;">
                        <a href="{url_generator route="product" url=$purchase->product->url}" class="kit_cart_item__image_link">
                            {if $item->product->image}
                                <img alt="{$item->product->name|escape}" src="{$item->product->image->filename|resize:45:45}">
                            {else}
                                <div>
                                    {include file="svg.tpl" svgId="no_image"}
                                </div>
                            {/if}
                        </a>
                    </div>

                    <div style="float: left; width: calc(100% - 50px); padding-bottom: 10px">
                        <div>
                            <span>{$item->product->name|escape}</span>
                            <i>{$item->product->variant->name|escape}</i>
                        </div>

                        <div style="margin-top: 5px;">
                            <b>{$item->price_per_item|convert} <span class="kit_cart_item__currency">{$currency->sign}</span> x {$item->amount} {if $purchase->variant->units}/ {$purchase->variant->units|escape}{/if}</b>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    {/if}
{/if}