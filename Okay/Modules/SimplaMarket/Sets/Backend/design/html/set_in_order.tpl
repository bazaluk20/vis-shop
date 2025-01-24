{if $purchase->set}
    <div style="border: 1px solid lightgray; margin: 10px 0">
        <div class="okay_list_body">
            {if $purchase->set->include}
                <div class="okay_list_row">
                    <div class="okay_list_boding okay_list_photo">
                        {if $purchase->set->target_product->image}
                            <img class=product_icon src="{$purchase->set->target_product->image->filename|resize:50:50}">
                        {else}
                            <img width="50" src="design/images/no_image.png"/>
                        {/if}
                    </div>

                    <div class="okay_list_boding">
                        <a href="{url controller='ProductAdmin' id=$purchase->set->target_product->id}">{$purchase->set->target_product->name} {if $purchase->set->target_variant->name}({$purchase->set->target_variant->name}){/if} | <b>{$purchase->set->target_variant->price|convert} {$currency->sign} x 1</b></a>
                    </div>
                </div>
            {/if}

            {foreach $purchase->set->items as $item}
                <div class="okay_list_row">
                    <div class="okay_list_boding okay_list_photo">
                        {if $item->product->image}
                            <img class=product_icon src="{$item->product->image->filename|resize:50:50}">
                        {else}
                            <img width="50" src="design/images/no_image.png"/>
                        {/if}
                    </div>

                    <div class="okay_list_boding">
                        <a href="{url controller='ProductAdmin' id=$item->product->id}">{$item->product->name} {if $item->product->variant->name}({$item->product->variant->name}){/if} | <b>{$item->price_per_item|convert} {$currency->sign} x {$item->amount}</b></a>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
{/if}