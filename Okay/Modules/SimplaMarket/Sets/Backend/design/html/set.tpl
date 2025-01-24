{if $set->id}
    {$meta_title = $set->name scope=parent}
{else}
    {$meta_title = $btr->new_set scope=parent}
{/if}

<script type="text/javascript" src="{$rootUrl}/Okay/Modules/SimplaMarket/Sets/Backend/design/js/jquery.datetimepicker.js"></script>
<link rel="stylesheet" type="text/css" href="{$rootUrl}/Okay/Modules/SimplaMarket/Sets/Backend/design/css/jquery.datetimepicker.css"/>
<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>

{* On document load *}
{literal}
    <script>
        var new_display_object = '';
        $(function() {

            $('input[name="date_from"],input[name="date_to"]').datetimepicker({
                lang: 'ru',
                dayOfWeekStart: 1,
                format: 'd.m.Y H:i',
                step: 10
            });

            $(document).on('change', '[name="show_type"]', function() {
                $('.display_objects').html('');
                startAutoComplete();
            });

            $(document).on( "click", ".fn_remove_item", function() {
                $(this).closest(".fn_row").fadeOut(200, function() { $(this).remove(); });
                return false;
            });

            $(".set_items a.delete").on('click', function() {
                $(this).closest("div.fn_row").fadeOut(200, function() { $(this).remove(); });
                return false;
            });

            new_display_object = $('#new_display_object').clone(true);
            $('#new_display_object').remove();
            new_display_object.removeAttr('id');
            startAutoComplete();

            // содержимое комплекта
            var new_set_item = $('#new_set_item').clone(true);
            $('#new_set_item').remove();
            new_set_item.removeAttr('id');
            $("input#set_items").devbridgeAutocomplete({
                serviceUrl: '{/literal}{url_generator route="SimplaMarket.Sets.SearchProducts"}{literal}',
                minChars:0,
                noCache: false,
                onSelect:
                    function(suggestion){
                        $("input#set_items").val('').focus().blur();
                        new_item = new_set_item.clone().appendTo('.set_items');
                        new_item.removeAttr('id');
                        new_item.find('a.set_item_name').html(suggestion.data.name);
                        new_item.find('a.set_item_name').attr('href', 'index.php?module=ProductAdmin&id='+suggestion.data.id);
                        new_item.find('[name*="set_items"][name*="product_id"]').val(suggestion.data.id);
                        if(suggestion.data.image) {
                            new_item.find('img.product_icon').attr("src", suggestion.data.image);
                        } else {
                            new_item.find('img.product_icon').remove();
                        }

                        // Добавляем варианты нового товара
                        var variants_select = new_item.find('[name*=set_items][name*=variant_id]');
                        for(var i in suggestion.data.variants) {
                            var sku = suggestion.data.variants[i].sku == ''?'':' (арт. '+suggestion.data.variants[i].sku+')';
                            variants_select.append("<option value='"+suggestion.data.variants[i].id+"' data-price='"+suggestion.data.variants[i].price+"' data-stock='"+suggestion.data.variants[i].stock+"'>"+suggestion.data.variants[i].name+sku+"</option>");
                        }
                        if(suggestion.data.variants.length>1 || suggestion.data.variants[0].name != '') {
                            variants_select.closest('.fn-variant_block').show();
                        }
                        variants_select.trigger('change');
                        new_item.show();
                    },
                formatResult:
                    function(suggestions, currentValue){
                        var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
                        var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
                        return (suggestions.data.image?"<img align=absmiddle src='"+suggestions.data.image+"' style='max-width: 35px;'> ":'') + suggestions.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
                    }

            });

            // изменение варианта, кол-ва и скидки
            $(document).on('change', '[name*=set_items][name*=variant_id]', function() {
                var elem = $(this).find('option:selected'),
                    parent = elem.closest('div.okay_list_row');
                parent.find('[name*="set_items"][name*="amount"]').data('max', elem.data('stock'));
                parent.find('[name*="set_items"][name*="amount"]').trigger('blur');
                parent.find('.fn-price').html(parseFloat(elem.data('price')).toFixed(2));
                parent.find('input[name*="set_items"][name*="discount"]').data('price', elem.data('price'));
                parent.find('input[name*="set_items"][name*="discount"]').trigger('blur');
            });

            $(document).on('blur', '[name*=set_items][name*=amount]', function() {
                var elem = $(this),
                    max = parseInt(elem.data('max')),
                    amount = parseInt(elem.val());
                if (isNaN(amount) || amount < 1) {
                    elem.val(1);
                } else {
                    elem.val(amount);
                }
            });

            $(document).on('blur', 'input[name*=set_items][name*=discount]', function() {
                var price = 0,
                    parent = $(this).closest('div.okay_list_row'),
                    price_discount = parseFloat($(this).val().replace(',', '.'));
                if (isNaN(price_discount)) {
                    price_discount = 0;
                }
                if (parent.find('[name*=set_items][name*=discount_type]').val() == 'value') {
                    price = (parseFloat($(this).data('price')) - price_discount).toFixed(2);
                } else {
                    price = (parseFloat($(this).data('price')) * (1-price_discount/100)).toFixed(2);
                }
                parent.find('.fn-price_discount').html(price);
                $(this).val(price_discount);
            });

            $(document).on('change', '[name*=set_items][name*=discount_type]', function() {
                $(this).closest('div.okay_list_row').find('input[name*=set_items][name*=discount]').trigger('blur');
            });
            $('input[name*=set_items][name*=discount]').trigger('blur');
        });

        function startAutoComplete() {
            $("input#display_objects").devbridgeAutocomplete("destroy");
            let serviceUrl;
            if ($('[name="show_type"]').val() == 'product') {
                serviceUrl = '{/literal}{url_generator route="SimplaMarket.Sets.SearchProducts"}{literal}';
            } else {
                serviceUrl = '{/literal}{url_generator route="SimplaMarket.Sets.SearchCategories"}{literal}';
            }
            
            $("input#display_objects").devbridgeAutocomplete({
                serviceUrl: serviceUrl,
                minChars:0,
                noCache: false,
                onSelect:
                    function(suggestion){
                        $("input#display_objects").val('').focus().blur();
                        new_item = new_display_object.clone().appendTo('.display_objects');
                        new_item.removeAttr('id');
                        new_item.find('a.display_object_name').html(suggestion.data.name);
                        new_item.find('a.display_object_name').attr('href', 'index.php?module='+($('[name="show_type"]').val() == 'category' ? 'CategoryAdmin' : 'ProductAdmin')+'&id='+suggestion.data.id);
                        new_item.find('input[name*="display_objects"]').val(suggestion.data.id);
                        if(suggestion.data.image)
                            new_item.find('img.product_icon').attr("src", suggestion.data.image);
                        else
                            new_item.find('img.product_icon').remove();
                        new_item.show();
                    },
                formatResult:
                    function(suggestions, currentValue){
                        var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
                        var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
                        return (suggestions.data.image?"<img align=absmiddle src='"+suggestions.data.image+"' style='max-width: 35px;'> ":'') + suggestions.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
                    }

            });
        }
    </script>
    <style>
        .autocomplete-suggestions{
            background-color: #ffffff;
            overflow: hidden;
            border: 1px solid #e0e0e0;
            overflow-y: auto;
        }
        .autocomplete-suggestions .autocomplete-suggestion{cursor: default;}
        .autocomplete-suggestions .selected { background:#F0F0F0; }
        .autocomplete-suggestions div { padding:2px 5px; white-space:nowrap; }
        .autocomplete-suggestions strong { font-weight:normal; color:#3399FF; }
    </style>
{/literal}



{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {if !$set->id}
                    {$btr->set_add|escape}
                {else}
                    {$set->name|escape}
                {/if}
            </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-12 col-sm-12 float-xs-right"></div>
</div>

{*Вывод успешных сообщений*}
{if $message_success}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="boxed boxed_success">
                <div class="heading_box">
                    {if $message_success=='added'}
                        {$btr->product_added|escape}
                    {elseif $message_success=='updated'}
                        {$btr->product_updated|escape}
                    {else}
                        {$message_success|escape}
                    {/if}
                    {if $smarty.get.return}
                        <a class="btn btn_return float-xs-right" href="{$smarty.get.return}">
                            {include file='svg_icon.tpl' svgId='return'}
                            <span>{$btr->general_back|escape}</span>
                        </a>
                    {/if}
                </div>
            </div>
        </div>
    </div>
{/if}

{*Вывод ошибок*}
{if $message_error}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="boxed boxed_warning">
                <div class="heading_box">
                    {if $message_error=='empty_name'}
                        {$btr->general_enter_title|escape}
                    {else}
                        {$message_error|escape}
                    {/if}
                </div>
            </div>
        </div>
    </div>
{/if}

<!-- Основная форма -->
<form method="post" id="product" enctype="multipart/form-data">
    <input type="hidden" name="session_id" value="{$smarty.session.id}">
    <input name="id" type="hidden" value="{$set->id|escape}"/>
    <div class="row">
        <div class="col-xs-12">
            <div class="boxed">
                {*Название элемента сайта*}
                <div class="row d_flex">
                    <div class="col-lg-10 col-md-9 col-sm-12">
                        <div class="heading_label">
                            {$btr->general_name|escape}
                        </div>
                        <div class="form-group">
                            <input class="form-control" name="name" type="text" value="{$set->name|escape}"/>
                            <input name="id" type="hidden" value="{$set->id|escape}"/>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-12">
                        <div class="activity_of_switch">
                            <div class="activity_of_switch_item"> {* row block *}
                                <div class="okay_switch clearfix">
                                    <label class="switch_label">{$btr->general_enable|escape}</label>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="visible" value='1' type="checkbox" id="visible_checkbox" {if $set->visible}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {*Параметры элемента*}
    <div class="row">
        <div class="col-lg-6 col-md-12 pr-0">
            <div class="boxed fn_toggle_wrap min_height_210px">
                <div class="heading_box">
                    {$btr->options_set|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="heading_label mb-h"></div>
                <div class="row sets">
                    <div class="col-md-6 col-lg-6 pr-0 pl-0">
                        <div class="input-group">
                            <label class="input-group-addon-date">{$btr->set_show|escape} {$btr->general_from|escape}</label>
                            {if $is_mobile || $is_tablet}
                                <input type="date" class="fn_from_date form-control" name="date_from" value="{if $set->date_from}{$set->date_from|date:'d.m.Y H:i'}{/if}" autocomplete="off" >
                            {else}
                                <input type="text" class="fn_from_date form-control" name="date_from" value="{if $set->date_from}{$set->date_from|date:'d.m.Y H:i'}{/if}" autocomplete="off" >
                            {/if}
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 pr-0 pl-0">
                        <div class="input-group">
                            <span class="input-group-addon-date">{$btr->general_to|escape}</span>
                            {if $is_mobile || $is_tablet}
                                <input type="date" class="fn_to_date form-control" name="date_to" value="{if $set->date_to}{$set->date_to|date:'d.m.Y H:i'}{/if}" autocomplete="off" >
                            {else}
                                <input type="text" class="fn_to_date form-control" name="date_to" value="{if $set->date_to}{$set->date_to|date:'d.m.Y H:i'}{/if}" autocomplete="off" >
                            {/if}
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-1">
                    <div class="mb-q">{$btr->set_description|escape}</div>
                    <textarea name="annotation" class="form-control okay_textarea">{$set->annotation|escape}</textarea>
                </div>
                <div class="activity_of_switch_item mt-1">
                    <div class="okay_switch clearfix">
                        <label class="switch_label">{$btr->show_product_in_set|escape}</label>
                        <label class="switch switch-default">
                            <input class="switch-input" name="include" value='1' type="checkbox" id="visible_checkbox" {if $set->include}checked{/if}/>
                            <span class="switch-label"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>
                <div class="set_flex">
                    <span>{$btr->set_show_in|escape} &nbsp;</span>
                    <select name="show_type" class="selectpicker">
                        <option value="product" {if $set->show_type=='product'}selected{/if}>{$btr->set_show_in_poroduct|escape}</option>
                        <option value="category" {if $set->show_type=='category'}selected{/if}>{$btr->set_show_in_category|escape}</option>
                    </select>
                </div>
                <input type="text" name="display" id="display_objects" class="form-control" placeholder="{$btr->select_to_add}">
                <div class="okay_list" style="margin-top: 15px;">
                    <div id="list" class="display_objects">
                        {if $set->show_type=='category'}
                            {$object_module='CategoryAdmin'}
                        {else}
                            {$object_module='ProductAdmin'}
                        {/if}
                        {foreach $set->display_objects as $display_object}
                            <div class="okay_list_body_item fn_row">
                                <div class="okay_list_row">
                                    <div class="okay_list_boding okay_list_related_photo">
                                        <input type="hidden" name="display_objects[]" value="{$display_object->id}" style="max-width: 35px;">
                                        {if $set->show_type=='product' && $display_object->image}
                                            <img class=product_icon src="{$display_object->image->filename|resize:45:45}">
                                        {/if}
                                    </div>
                                    <div class="okay_list_boding okay_list_related_name okay_list_boding_set">
                                        <a class="link display_object_name" href="{url controller=$object_module id=$display_object->id}">{$display_object->name}</a>
                                    </div>
                                    <div class="okay_list_boding okay_list_close">
                                        <button data-hint="{$btr->general_delete_product|escape}" type="button" class="delete btn_close fn_remove_item hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                            {include file='svg_icon.tpl' svgId='delete'}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                        <div id="new_display_object" class="okay_list_body_item fn_row" style='display:none;'>
                            <div class="okay_list_row">
                                <div class="okay_list_boding okay_list_related_photo">
                                    <input type="hidden" name="display_objects[]" value="" style="max-width: 35px;">
                                    <img class=product_icon src="">
                                </div>
                                <div class="okay_list_boding okay_list_related_name okay_list_boding_set">
                                    <a class="link display_object_name" href=""></a>
                                </div>
                                <div class="okay_list_boding okay_list_close">
                                    <button data-hint="{$btr->general_delete_product|escape}" type="button" class="delete btn_close fn_remove_item hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                        {include file='svg_icon.tpl' svgId='delete'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="boxed min_height_210px">
                <div class="heading_box">
                    {$btr->products_in_set|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                {*Выбор товара*}
                <input  type=text name=items id='set_items' class="form-control" placeholder='{$btr->select_to_add}'>
                <div id="list_items" class="sortable set_items okay_list" style="margin-top: 15px;">
                {foreach $set->items as $set_item}
                    <div class="set_product_box fn_row" {if !$set_item->variant || !$set_item->product || $set_item->variant->stock<$set_item->amount}style="background: orange;" {/if}>
                        <div class="okay_list_body related_products">
                            <div class="fn_row okay okay_list_body_item fn_sort_item">
                                <div class="okay_list_row">
                                    <div class="okay_list_boding okay_list_drag move_zone">
                                        {include file='svg_icon.tpl' svgId='drag_vertical'}
                                    </div>

                                    <input type="hidden" name="set_items[id][]" value="{$set_item->id}" />

                                    {*Превьюшка товара*}
                                    <div class="okay_list_boding image cell">
                                        <input type=hidden name=set_items[product_id][] value='{$set_item->product->id}'>
                                        <a href="{url controller=ProductAdmin id=$set_item->product->id}">
                                            <img class="product_icon image_cell_item" src='{$set_item->product->image->filename|resize:100:100}' style='max-width: 35px;'>
                                        </a>
                                    </div>

                                    {*Название товара*}
                                    <div class="okay_list_boding set_info_variant">
                                        <div class="name cell">
                                            <a class="name_cell_title" href="{url controller=ProductAdmin id=$set_item->product->id}">{if $set_item->product}{$set_item->product->name}{else}{$set_item->product_name}{/if}</a>
                                        </div>
                                        {*Ценовой блок*}
                                        <div class="set_admin_price">
                                            {$btr->set_price}
                                            <span class="fn-price" style="font-weight: bold;">{if $set_item->variant}{$set_item->variant->price}{else}0{/if}</span>
                                            {$btr->item_with_discount}
                                            <span class="fn-price_discount" style="font-weight: bold;"></span>
                                        </div>

                                        {if !$set_item->product || !$set_item->variant || $set_item->variant->stock<$set_item->amount}
                                            <div class="message_error">
                                                {if !$set_item->product}
                                                    {$btr->selected_product_does_not_exist}
                                                {elseif !$set_item->variant}
                                                    {$btr->selected_item_does_not_exist}
                                                {elseif $set_item->variant->stock<$set_item->amount}
                                                    {$btr->shortage_of_goods_in_stock}
                                                {/if}
                                            </div>
                                        {/if}

                                        {*Блок вариантов*}
                                        <div class="fn-variant_block var_list" {if count($set_item->product->variants)==1 && $set_item->product->variants[0]->name=='' && $set_item->variant}style="display: none;" {/if}>
                                            <label>{$btr->item_variant}</label>
                                            <select name="set_items[variant_id][]">
                                                {if !$set_item->variant}
                                                    <option data-stock="0" data-price="0" value="{$set_item->variant_id}" selected>{$set_item->variant_name|escape}</option>
                                                {/if}
                                                {foreach $set_item->product->variants as $v}
                                                    <option value="{$v->id}" data-stock="{$v->stock}" data-price="{$v->price}" {if $set_item->variant->id==$v->id}selected{/if}>
                                                        {$v->name|escape}{if $v->sku}  (арт. {$v->sku|escape}){/if}
                                                    </option>
                                                {/foreach}
                                            </select>
                                        </div>

                                        {*Блок колличества*}
                                        <div class="set_admin_quanti">
                                            <div>
                                                <label>{$btr->set_item_amount}</label>
                                                <input name="set_items[amount][]" value="{$set_item->amount}" type="text" data-max="{if $set_item->variant}{$set_item->variant->stock}{else}0{/if}" style="width: 35px;" />
                                                <label>{$btr->discont_for_one}</label>
                                                <input name="set_items[discount][]" value="{$set_item->discount}" data-price="{if $set_item->variant}{$set_item->variant->price}{else}0{/if}" type="text" style="width: 70px;" />
                                            </div>
                                            <div>
                                                <label>{$btr->discount_type}</label>
                                                <select name="set_items[discount_type][]">
                                                    <option value="value" {if $set_item->discount_type=='value'}selected{/if}>{$btr->sets_value}</option>
                                                    <option value="percent" {if $set_item->discount_type=='percent'}selected{/if}>{$btr->sets_percent}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="okay_list_boding okay_list_close">
                                        <button data-hint="{$btr->general_delete_product|escape}" type="button" class="delete btn_close fn_remove_item hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                            {include file='svg_icon.tpl' svgId='delete'}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {/foreach}

                {*Добавленный товар до сохранения*}
                <div id="new_set_item" class="set_product_box fn_row okay_list" style='display:none;'>
                    <div class="okay_list_body related_products">
                        <div class="fn_row okay okay_list_body_item fn_sort_item">
                            <div class="okay_list_row">
                                <div class="okay_list_boding okay_list_drag move_zone">
                                    {include file='svg_icon.tpl' svgId='drag_vertical'}
                                </div>

                                <input type="hidden" name="set_items[id][]" value="" />

                                {*Превьюшка нового товара*}
                                <div class="okay_list_boding image cell">
                                    <input type=hidden name=set_items[product_id][] value=''>
                                    <img class="product_icon image_cell_item" src='' style='max-width: 35px;'>
                                </div>
                                <div class="okay_list_boding set_info_variant">
                                    {*Название нового товара*}
                                    <div class="name cell">
                                        <a class="set_item_name name_cell_title" href=""></a>
                                    </div>

                                    {*Блок вариантов нового товара*}
                                    <div class="fn-variant_block var_list" style="display: none;">
                                        <label>Вариант</label>
                                        <select name="set_items[variant_id][]"></select>
                                    </div>

                                    {*Ценовой блок нового товара*}
                                    <div class="set_admin_price">Цена: <span class="fn-price" style="font-weight: bold;"></span>, со скидкой:<span class="fn-price_discount" style="font-weight: bold;"></span></div>
                                    <div class="set_admin_quanti">
                                        <div>
                                            <label>Кол-во</label>
                                            <input name="set_items[amount][]" value="1" type="text" data-max="{$settings->max_order_amount}" style="width: 35px;" />
                                            <label>Скидка(за 1 ед.)</label>
                                            <input name="set_items[discount][]" value="0" type="text" data-price="0" style="width: 70px;" />
                                        </div>
                                        <div>
                                            <label>Тип</label>
                                            <select name="set_items[discount_type][]" class="selectpicker">
                                                <option value="value" selected>Значение</option>
                                                <option value="percent">Процент</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="okay_list_boding okay_list_close">
                                    <button data-hint="{$btr->general_delete_product|escape}" type="button" class="delete btn_close fn_remove_item hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                        {include file='svg_icon.tpl' svgId='delete'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 mb-2">
            <button type="submit" class="btn btn_small btn_blue float-md-right mx-2">
                {include file='svg_icon.tpl' svgId='checked'}
                <span>{$btr->general_apply|escape}</span>
            </button>
        </div>
    </div>
</form>
<!-- Основная форма (The End) -->
<script>
    if ($(".sortable").size()>0) {
        {literal}
        $(".sortable").each(function() {
            Sortable.create(this, {
                handle: ".move_zone",  // Drag handle selector within list items
                sort: true,  // sorting inside list
                animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation
                ghostClass: "sortable-ghost",  // Class name for the drop placeholder
                chosenClass: "sortable-chosen",  // Class name for the chosen item
                dragClass: "sortable-drag",  // Class name for the dragging item
                scrollSensitivity: 30, // px, how near the mouse must be to an edge to start scrolling.
                scrollSpeed: 10
            });
        });
        {/literal}
    }
</script>

<style>

    .okay_list .okay_list_related_name.okay_list_boding_set {
        width: calc(100% - 100px);
    }
    .set_info_variant {
        text-align: left;
        width: calc(100% - 145px);
    }
    .set_admin_quanti select {
        width: 100px;
    }
    #new_set_item {
        text-align: left;
        border: 1px solid transparent;
    }

    .okay_list .okay_list_sets_name > a {
        color: #48b0f7;
        font-size: 16px;
    }
    .set_flex {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    .sets {
        padding: 0 15px
    }
    .sets .input-group-addon-date {
        min-width: auto;
    }
    .sets .input-group .form-control {
        max-width: auto;
        flex: 1 1 100%;
    }
</style>