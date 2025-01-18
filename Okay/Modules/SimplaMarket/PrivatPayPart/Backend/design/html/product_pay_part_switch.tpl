<div class="fn_step-4 activity_of_switch_item"> {* row block *}
    <div class="okay_switch clearfix">
        <label class="switch_label">
            {$btr->privat_to_pay_part|escape}
            <i class="fn_tooltips" title="{$btr->privat_to_pay_part_tooltip|escape}">
                {include file='svg_icon.tpl' svgId='icon_tooltips'}
            </i>
        </label>
        <label class="switch switch-default">
            <input class="switch-input" name="to_privat_pay_part" value="1" type="checkbox" {if $product->to_privat_pay_part}checked=""{/if}/>
            <span class="switch-label"></span>
            <span class="switch-handle"></span>
        </label>
    </div>
</div>

<div class="fn_step-4 activity_of_switch_item">
    <div class="okay_switch clearfix">
        <label class="switch_label">
            {$btr->individual_privat_value|escape}
            <i class="fn_tooltips" title="{$btr->individual_privat_value_tooltip|escape}">
                {include file='svg_icon.tpl' svgId='icon_tooltips'}
            </i>
        </label>
        <label class="switch switch-default">
            <input class="switch-input" name="individual_privat_value" value="1" type="checkbox" {if $product->individual_privat_value}checked=""{/if}/>
            <span class="switch-label"></span>
            <span class="switch-handle"></span>
        </label>
    </div>
</div>