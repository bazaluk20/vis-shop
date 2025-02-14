<div class="products_advantages_block">
    <div class="products_advantages_title">{$lang->advantages}</div>
    <div class="products_advantages_block_inner">
        {foreach $product_advantages as $advantage}
            <div class="products_advantages_l">
                <div class="products_advantages_img_block">
                    <img class="products_advantages_img"src="{$advantage->filename|resize:600:380:false:$config->resized_products_advantages_dir:'center':'center'}" alt="{$advantage->text}">
                </div>
                <div class="products_advantages_txt_block">
                    {$advantage->text}
                </div>
            </div>
        {/foreach}
    </div>
</div>