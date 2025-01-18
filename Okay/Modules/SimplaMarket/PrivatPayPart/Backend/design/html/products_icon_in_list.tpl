{* Privat individuality period *}
<button data-hint="{$btr->individual_privat_value|escape}" type="button"
        class="setting_icon setting_icon_featured fn_ajax_action {if $product->individual_privat_value}fn_active_class{/if} hint-bottom-middle-t-info-s-small-mobile  hint-anim"
        data-controller="product" data-action="individual_privat_value" data-id="{$product->id}">
    {include file='svg_icon.tpl' svgId='check'}
</button>