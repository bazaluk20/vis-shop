{$meta_title=$btr->general_sets scope=parent}

{*Название страницы*}
<div class="main_header">
    <div class="main_header__item">
        <div class="main_header__inner">
            {if $sets_count}
                <div class="box_heading heading_page">
                    {$btr->general_sets|escape} - {$sets_count}
                </div>
            {else}
                <div class="box_heading heading_page">{$btr->sets_no|escape}</div>
            {/if}
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info" href="{url controller="SimplaMarket.Sets.SetAdmin" return=$smarty.server.REQUEST_URI}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->set_add|escape}</span>
                </a>
            </div>
        </div>
    </div>
    <div class="main_header__item">
        <div class="main_header__inner">
            <form class="search" method="get">
                <input type="hidden" name="controller" value="SimplaMarket.Sets.SetsAdmin">
                <div class="input-group input-group--search">
                    <input name="keyword" class="form-control" placeholder="{$btr->general_search|escape}" type="text" value="{$keyword|escape}" >
                    <span class="input-group-btn">
                    <button type="submit" class="btn"><i class="fa fa-search"></i></button>
                </span>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="boxed fn_toggle_wrap">
    {if $sets}
        <form method="post" class="fn_form_list">
            <input type="hidden" name="session_id" value="{$smarty.session.id}" />
            <div class="okay_list products_list fn_sort_list">
                <div class="okay_list_head">
                    <div class="okay_list_boding okay_list_drag"></div>
                    <div class="okay_list_heading okay_list_check">
                        <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value="" />
                        <label class="okay_ckeckbox" for="check_all_1"></label>
                    </div>
                    <div class="okay_list_heading okay_list_sets_name">{$btr->general_name|escape}</div>
                    <div class="okay_list_heading okay_list_status">{$btr->general_enable|escape}</div>
                    <div class="okay_list_heading okay_list_close"></div>
                </div>
                {*Параметры элемента*}
                <div class="okay_list_body sortable">
                    {foreach $sets as $set}
                        <div class="fn_row okay_list_body_item fn_sort_item">
                            <div class="okay_list_row ">
                                <input type="hidden" name="positions[{$set->id}]" value="{$set->position}" />

                                <div class="okay_list_boding okay_list_drag move_zone">
                                    {include file='svg_icon.tpl' svgId='drag_vertical'}
                                </div>

                                <div class="okay_list_boding okay_list_check">
                                    <input class="hidden_check" type="checkbox" id="id_{$set->id}" name="check[]" value="{$set->id}" />
                                    <label class="okay_ckeckbox" for="id_{$set->id}"></label>
                                </div>


                                <div class="okay_list_boding okay_list_sets_name">
                                    <a href="{url controller="SimplaMarket.Sets.SetAdmin" id=$set->id return=$smarty.server.REQUEST_URI}">
                                        {$set->name|escape}
                                    </a>
                                    <div>
                                        {if $set->date_from && $set->date_to}
                                            <b>{$set->date_from|date:'d.m.Y H:i'}</b>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <b>{$set->date_to|date:'d.m.Y H:i'}</b>
                                        {/if}
                                        {$error = ''}
                                        {foreach $set->items as $item}
                                            {if !$item->product}
                                                {$error = {$btr->one_goods_does_not_exist|escape}}
                                            {elseif !$item->variant}
                                                {$error = {$btr->one_variants_does_not_exist|escape}}
                                            {elseif $item->variant->stock<$item->amount}
                                                {$error = {$btr->one_goods_not_stock|escape}}
                                            {/if}
                                        {/foreach}

                                        <ul class="cost_list">
                                            {if $error}
                                                <li><span style="background: orange;">{$error}</span></li>
                                            {/if}
                                            <li><span>{$btr->cost_without_discount|escape}</span> <b>{$set->total_price|number_format:2:'.':''}</b></li>
                                            <li><span>{$btr->cost_complete_set_with_discount|escape}</span> <b>{($set->total_price-$set->total_discount)|number_format:2:'.':''}</b></li>
                                            <li><span>{$btr->saving_when_buying_kit|escape}</span> <b>{$set->total_discount|number_format:2:'.':''}</b></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="okay_list_boding okay_list_status">
                                    <label class="switch switch-default ">
                                        <input class="switch-input fn_ajax_action {if $set->visible}fn_active_class{/if}" data-controller="SimplaMarket.Sets.SetsEntity" data-action="visible" data-id="{$set->id}" name="visible" value="1" type="checkbox"  {if $set->visible}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>

                                <div class="okay_list_boding okay_list_close">
                                    <button data-hint="{$btr->sets_delete_set|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
                                        {include file='svg_icon.tpl' svgId='delete'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>

                {*Блок массовых действий*}
                <div class="okay_list_footer fn_action_block">
                    <div class="okay_list_foot_left">
                        <div class="okay_list_boding okay_list_drag"></div>
                        <div class="okay_list_heading okay_list_check">
                            <input class="hidden_check fn_check_all" type="checkbox" id="check_all_2" name="" value=""/>
                            <label class="okay_ckeckbox" for="check_all_2"></label>
                        </div>
                        <div class="okay_list_option">
                            <select name="action" class="selectpicker">
                                <option value="enable">{$btr->general_do_enable|escape}</option>
                                <option value="disable">{$btr->general_do_disable|escape}</option>
                                <option value="set_dates_null">{$btr->stop_view_set|escape}</option>
                                <option value="delete">{$btr->general_delete|escape}</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn_small btn_blue">
                        {include file='svg_icon.tpl' svgId='checked'}
                        <span>{$btr->general_apply|escape}</span>
                    </button>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm 12 txt_center">
                {include file='pagination.tpl'}
            </div>
        </div>
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->sets_no|escape}</div>
        </div>
    {/if}
</div>

<style>

    .okay_list .okay_list_sets_name {
        width: calc(100% - 250px);
        position: relative;
        text-align: left;
    }
    .okay_list .okay_list_sets_name > a {
        color: #48b0f7;
        font-size: 16px;
    }

</style>