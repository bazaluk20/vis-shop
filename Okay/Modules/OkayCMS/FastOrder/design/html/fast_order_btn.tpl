<a class="fn_fast_order_button fast_order_button fa fa-phone fn_is_stock"
   href="#fast_order" {if $fastOrderProduct->variant->stock < 1 && !$settings->is_preorder }style="display: none" {/if}
   title="{$lang->fast_order}" data-language="fast_order" data-name="{$fast_order_product_name}"><span>{$lang->fast_order}</span>
</a>