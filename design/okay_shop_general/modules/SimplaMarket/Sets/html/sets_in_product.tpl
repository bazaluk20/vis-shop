{if $sets}
    <div class="product_sets block ">
        <div class="product_sets__header">
            <h2 class="title title--basic">{$lang->simplamarket__sets__kit_cheaper}</h2>
        </div>
        <div class="fn_set_slider product_sets__container swiper-container">
            <ul class="swiper-wrapper product_sets__list">
                {foreach $sets as $set}
                    <li class="swiper-slide fn-set_block product_sets__item" >
                        <div class="product_set__item_wrapper">
                            {* если осн. товар в комплекте *}
                            {if $set->include}
                                <!-- <div class="product_set_item">
                                    <div class="product_set_item__image">
                                        <img src="{$product->image->filename|resize:100:140}" />
                                    </div>
                                    <div class="product_set_item__content">
                                        <a class="product_set_item__name item_var" href="{url_generator route="product" url=$set->target_product->url}">{$set->target_product->name}</a>
                                        {$product_author}
                                        <span class="product_set_item__price set_new_price">{$product->variant->price|convert} {$currency->sign|escape}</span>
                                    </div>
                                </div>      
                                <div class="product_set__sign">
                                    <svg style="display: block" width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M38 20H20V38H18V20H0V18H18V0H20V18H38V20Z" fill="#4F4F4F"/>
                                    </svg>
                                </div> -->
                            {/if}
                            {foreach $set->items as $item}
                                <div class="product_set_item">
                                    
                                    <div class="product_set_item__content">
                                        <a class="product_set_item__name item_var" href="{url_generator route="product" url=$item->product->url}">{$item->product->name}</a>
                                        {$product_author}
                                        <!-- <span class="product_set_item__price_col">
                                            <span class="product_set_item__price set_new_price  {if $set->total_discount }price--red{/if}">{$item->cur_price|convert} {$currency->sign|escape}</span>
                                            {if $item->cur_discount > 0}
                                                <span class="product_set_item__price set_old_price">{$item->variant->price|convert} {$currency->sign|escape}</span>
                                            {/if}
                                            {if $item->amount > 1}
                                            <span>{$lang->simplamarket__za} {$item->amount} {$lang->simplamarket__wt}</span>
                                            {/if}
                                        </span> -->
                                    </div>
                                    <div class="product_set_item__image">
                                        <img src="{$item->product->image->filename|resize:200:240}" />
                                    </div>
                                </div>
                                {if !$item@last}
                                    <div class="product_set__sign">
                                        <svg style="display: block" width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M38 20H20V38H18V20H0V18H18V0H20V18H38V20Z" fill="#4F4F4F"/>
                                        </svg>
                                    </div>
                                {/if}
                            {/foreach}
                            <!-- <div class="product_set__sign">
                                <svg style="display: block" width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0 29V27H38V29H0ZM0 8H38V10H0V8Z" fill="#4F4F4F"/>
                                </svg>                            
                            </div> -->
                            <div class="product_set_buy">
                                <div class="product_set_buy__prices">
                                    <span class="product_set_buy__price set_new_price  {if $set->total_discount } price--red{/if}">{$set->total_price|convert} {$currency->sign|escape}</span>
                                    {if $set->total_discount > 0}
                                        <span class="product_set_buy__price set_old_price">{($set->total_price+$set->total_discount)|convert} {$currency->sign|escape}</span>
                                    {/if}
            
                                </div>
                                {if $set->total_discount > 0}
                                    <div class="product_set_buy__difference">{$lang->simplamarket__sets__economy}: 
                                        <b>{$set->total_discount|convert} {$currency->sign|escape}</b>
                                    </div>
                                {/if}
        
                                <input id='buy_set' type="submit" class="product_set_buy__button button__product fn_add_set_to_cart product_preview__button" data-variant_id="set_{$product->variant->id}_{$set->id}" value="{$lang->simplamarket__sets__set_buy}" data-result-text="{$lang->simplamarket__sets__set_add}"/>
        
                            </div>
                        </div>
                    </li>
                {/foreach}
            </ul>
            <div class="swiper-button-next product_set-next"></div>
            <div class="swiper-button-prev product_set-prev"></div>
        </div>
    </div>


{/if}
{literal}   
<script>
    document.addEventListener('DOMContentLoaded', function(){
        if($('.fn_set_slider').length) {
            $('.fn_set_slider').each(function(){
                var swiper = new Swiper(this, {
                    slidesPerView: 2,
                    watchOverflow: true,
                    // autoHeight: true,  
                    simulateTouch:false, 
                    spaceBetween: 12,
                    pagination: {
                        el: this.querySelector('.swiper-pagination'),
                        clickable: true,
                    },
                    navigation: {
                        nextEl: this.querySelector('.swiper-button-next'),
                        prevEl: this.querySelector('.swiper-button-prev'),
                    },
                    breakpoints: {
                        768: {
                            slidesPerView: 2,
                            spaceBetween: 12,
                        },
                        1280: {
                            slidesPerView: 3,
                            spaceBetween: 30,
                        }
                    },
                });
            });
        }
    });
</script>
{/literal}