{* Title *}
{$meta_title=$btr->redirects_redirects scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-7 col-md-7">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {if $keyword && $redirects_count>0}
                    {$btr->redirects_redirects|escape} - {$redirects_count}
                {elseif $redirects_count>0}
                    {$btr->redirects_redirects|escape} - {$redirects_count}
                {/if}
            </div>
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info" href="{url controller="SimplaMarket.Redirects.RedirectAdmin" return=$smarty.server.REQUEST_URI}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->redirect_add|escape}</span>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-lg-5 col-xs-12 float-xs-right">
        <div class="boxed_search">
            <form class="search" method="get">
                <input type="hidden" name="controller" value="SimplaMarket.Redirects.RedirectsAdmin">
                <div class="input-group">
                    <input name="keyword" class="form-control" placeholder="{$btr->redirects_search|escape}" type="text" value="{$keyword|escape}" >
                    <span class="input-group-btn">
                    <button type="submit" class="btn btn_blue"><i class="fa fa-search"></i> <span class="hidden-md-down"></span></button>
                </span>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="boxed fn_toggle_wrap">
    {if $redirects}
        {*Блок фильтров*}
        <div class="row">
            <div class="col-lg-12 col-md-12 ">
                <div class="row mb-1">
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <select onchange="location = this.value;" class="selectpicker form-control">
                            <option value="{url limit=5}" {if $current_limit == 5}selected{/if}>{$btr->general_show_by|escape} 5</option>
                            <option value="{url limit=10}" {if $current_limit == 10}selected{/if}>{$btr->general_show_by|escape} 10</option>
                            <option value="{url limit=25}" {if $current_limit == 25}selected{/if}>{$btr->general_show_by|escape} 25</option>
                            <option value="{url limit=50}" {if $current_limit == 50}selected{/if}>{$btr->general_show_by|escape} 50</option>
                            <option value="{url limit=100}" {if $current_limit == 100}selected=""{/if}>{$btr->general_show_by|escape} 100</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <select name="status" class="selectpicker"  onchange="location = this.value;">
                            <option value="{url filter=301}" {if $filter == 301}selected=""{/if} >{$btr->redirects_301|escape}</option>
                            <option value="{url filter=302}" {if $filter == 302}selected=""{/if} >{$btr->redirects_302|escape}</option>
                            <option value="{url filter=enabled}" {if $filter == 'enabled'}selected=""{/if} >{$btr->redirects_enabled|escape}</option>
                            <option value="{url filter=disabled}" {if $filter == 'disabled'}selected=""{/if} >{$btr->redirects_disabled|escape}</option>
                            <option value="{url filter=null}" {if !$filter}selected=""{/if}>{$btr->general_all|escape}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {*Главная форма страницы*}
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form class="fn_form_list" method="post">
                    <input type="hidden" name="session_id" value="{$smarty.session.id}">

                    <div class="users_wrap okay_list products_list fn_sort_list">
                        <div class="okay_list_head">
                            <div class="okay_list_heading okay_list_check">
                                <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                                <label class="okay_ckeckbox" for="check_all_1"></label>
                            </div>
                            <div class="okay_list_heading okay_list_users_name">
                                <span>{$btr->general_name|escape}</span>
                            </div>

                            <div class="okay_list_heading okay_list_users_email" style="width: 235px;">
                                <span>{$btr->redirects_url_from|escape}</span>
                            </div>

                            <div class="okay_list_heading okay_list_users_email" style="width: 235px;">
                                <span>{$btr->redirects_url_to|escape}</span>
                            </div>

                            <div class="okay_list_heading okay_list_users_group" style="width: 90px;">
                                <span>{$btr->redirects_status|escape}</span>
                            </div>

                            <div class="okay_list_heading okay_list_count" style="width: 90px;">
                                <span>{$btr->redirects_enable|escape}</span>
                            </div>

                            <div class="okay_list_heading okay_list_close"></div>
                        </div>
                        {*Параметры элемента*}
                        <div class="okay_list_body sortable">
                            {foreach $redirects as $redirect}
                                <div class="fn_row okay_list_body_item fn_sort_item">
                                    <div class="okay_list_row ">
                                        <div class="okay_list_boding okay_list_check">
                                            <input class="hidden_check" type="checkbox" id="id_{$redirect->id}" name="check[]" value="{$redirect->id}"/>
                                            <label class="okay_ckeckbox" for="id_{$redirect->id}"></label>
                                        </div>

                                        <div class="okay_list_boding okay_list_users_name">
                                            <a href="{url controller="SimplaMarket.Redirects.RedirectAdmin" id=$redirect->id return=$smarty.server.REQUEST_URI}">
                                                {$redirect->name|escape}
                                            </a>
                                        </div>

                                        <div class="okay_list_boding okay_list_users_email" style="width: 235px;">
                                            <a href="{url controller="SimplaMarket.Redirects.RedirectAdmin" id=$redirect->id return=$smarty.server.REQUEST_URI}">
                                                {$redirect->url_from|escape}
                                            </a>
                                        </div>

                                        <div class="okay_list_boding okay_list_users_email" style="width: 235px;">
                                            <a href="{url controller="SimplaMarket.Redirects.RedirectAdmin" id=$redirect->id return=$smarty.server.REQUEST_URI}">
                                                {$redirect->url_to|escape}
                                            </a>
                                        </div>

                                        <div class="okay_list_boding okay_list_users_group" style="width: 90px;">
                                            {if $redirect->status == 301}
                                                <div class="tag tag-info">{$redirect->status}</div>
                                            {else}
                                                <div class="tag tag-warning">{$redirect->status}</div>
                                            {/if}
                                        </div>

                                        <div class="okay_list_boding okay_list_status" style="width: 90px;">
                                            <label class="switch switch-default ">
                                                <input class="switch-input fn_ajax_action {if $redirect->enabled}fn_active_class{/if}" data-controller="SimplaMarket.Redirects.RedirectsEntity" data-action="enabled" data-id="{$redirect->id}" name="enabled" value="1" type="checkbox"  {if $redirect->enabled}checked=""{/if}/>
                                                <span class="switch-label"></span>
                                                <span class="switch-handle"></span>
                                            </label>
                                        </div>

                                        <div class="okay_list_boding okay_list_close">
                                            <button data-hint="{$btr->redirects_delete|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
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
                                <div class="okay_list_heading okay_list_check">
                                    <input class="hidden_check fn_check_all" type="checkbox" id="check_all_2" name="" value=""/>
                                    <label class="okay_ckeckbox" for="check_all_2"></label>
                                </div>
                                <div class="okay_list_option">
                                    <select name="action" class="selectpicker fn_user_select">
                                        <option value="disable">{$btr->general_do_disable|escape}</option>
                                        <option value="enable">{$btr->general_do_enable|escape}</option>
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
            </div>
            <div class="col-lg-12 col-md-12 col-sm 12 txt_center">
                {include file='pagination.tpl'}
            </div>
        </div>
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->redirects_no|escape}</div>
        </div>
    {/if}
</div>
