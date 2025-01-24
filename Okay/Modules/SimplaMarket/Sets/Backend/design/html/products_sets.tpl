{if $product->sets}
    <div class="col-lg-12 col-md-12">
        <div class="fn_step-13 boxed match fn_toggle_wrap">
            <div class="heading_box">
                <span>{$lang->sets_in_product}</span>
            </div>

            <div class="toggle_body_wrap on fn_card row">
                <div class="col-md-6">
                    {foreach $product->sets as $set}
                        <div>{$set->name}</div>
                        <div class="heading_label">{$set->annotation}</div>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
{/if}
