{if $vitalisoft__products_groups}
    <div class="details_boxed__item" style="display:flex; flex-direction:column; gap:25px">
        {foreach $vitalisoft__products_groups->features as $feature}
            <div style="display:flex; flex-direction:column; gap:10px">
                {$feature->name}:
                <div style="display:flex; flex-wrap:wrap; gap:5px">
                    {foreach $feature->values as $value}
                        {strip}
                            {if $value->product_url && !$value->is_current}
                                <a href="{url_generator route='product' url=$value->product_url}"
                            {else}
                                <span
                            {/if}
                        {/strip} {strip}
                        {if $value->is_current}
                            title="{$lang->vitalisoft__products_groups__current_combination|escape}"
                        {elseif !$value->product_url}
                            title="{$lang->vitalisoft__products_groups__combination_unavailable|escape}"
                        {elseif $value->stock === '0'}
                            title="{if !$settings->is_preorder}{$lang->product_out_of_stock}{else}{$lang->product_pre_order}{/if}"
                        {/if}
                        class="vitalisoft__products_groups__value_button
                            {if $value->is_current} vpg__current
                            {elseif !$value->product_url} vpg__unavailable
                            {elseif $value->stock === '0'} vpg__out_of_stock
                            {/if}">{$value->value}
                    {if $value->product_url && !$value->is_current}
                        </a>
                    {else}
                        </span>
                    {/if}
                    {/strip}
                    {/foreach}
                </div>
            </div>
        {/foreach}
        {if $vitalisoft__products_groups->colors}
            <div style="display:flex; flex-direction:column; gap:10px">
                {$lang->vitalisoft__products_groups__color|escape}:
                <div style="display:flex; flex-wrap:wrap; gap:12px">
                    {foreach $vitalisoft__products_groups->colors as $color}
                        {strip}
                            {if $color->product_url && !$color->is_current}
                                <a href="{url_generator route='product' url=$color->product_url}"
                            {else}
                                <span
                            {/if}
                        {/strip} {strip}
                        {if $color->is_current}
                            title="{$lang->vitalisoft__products_groups__current_combination|escape}"
                        {elseif !$color->product_url}
                            title="{$lang->vitalisoft__products_groups__combination_unavailable|escape}"
                        {elseif $color->stock === '0'}
                            title="{if !$settings->is_preorder}{$lang->product_out_of_stock}{else}{$lang->product_pre_order}{/if}"
                        {/if}
                        class="vitalisoft__products_groups__color_button
                            {if $color->is_current} vpg__current
                            {elseif !$color->product_url} vpg__unavailable
                            {elseif $color->stock === '0'} vpg__out_of_stock
                            {/if}" style="{$color->style}">
                    {if $color->product_url && !$color->is_current}
                        </a>
                    {else}
                        </span>
                    {/if}
                    {/strip}
                    {/foreach}
                </div>
            </div>
        {/if}
    </div>
{/if}