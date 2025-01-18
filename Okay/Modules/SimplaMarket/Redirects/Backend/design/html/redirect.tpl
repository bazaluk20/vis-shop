{if $redirect->id}
    {$meta_title = $redirect->name scope=global}
{else}
    {$meta_title = $btr->redirect_new scope=global}
{/if}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {if !$redirect->id}
                    {$btr->redirect_new|escape}
                {else}
                    {$redirect->name|escape}
                {/if}
            </div>
            {if $redirect->id}
                <div class="box_btn_heading">
                    <a class="btn btn_small btn-info add" target="_blank" href="../{$lang_link}{$redirect->url_from}">
                        {include file='svg_icon.tpl' svgId='icon_desktop'}
                        <span>{$btr->general_open|escape}</span>
                    </a>
                </div>
            {/if}
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
                    {if $message_success == 'added'}
                        {$btr->redirect_added|escape}
                    {elseif $message_success == 'updated'}
                        {$btr->redirect_updated|escape}
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
                    {if $message_error == 'url_exists'}
                        {$btr->general_exists|escape}
                    {elseif $message_error=='empty_url'}
                        {$btr->general_enter_url|escape}
                    {elseif $message_error == 'url_wrong'}
                        {$btr->general_not_underscore|escape}
                    {elseif $message_error == 'used_url'}
                        {$btr->redirect_used_url|escape}
                        <a href="{url module=CustomRedirectAdmin id=$used_redirect->id return=$smarty.server.REQUEST_URI}">{$used_redirect->name|escape}</a>
                        <b>{$btr->redirects_url_from}:</b><a style="text-decoration:none;" href="{url module=CustomRedirectAdmin id=$redirect->id return=$smarty.server.REQUEST_URI}">{$used_redirect->url_from|escape}</a>
                        <b>{$btr->redirects_url_to}:</b><a style="text-decoration:none;" href="{url module=CustomRedirectAdmin id=$redirect->id return=$smarty.server.REQUEST_URI}">{$used_redirect->url_to|escape}</a>
                    {else}
                        {$message_error|escape}
                    {/if}
                </div>
            </div>
        </div>
    </div>
{/if}

{*Главная форма страницы*}
<form method="post" enctype="multipart/form-data">
    <input type=hidden name="session_id" value="{$smarty.session.id}">
    <input type="hidden" name="lang_id" value="{$lang_id}" />

    <div class="row">
        <div class="col-xs-12 ">
            <div class="boxed match_matchHeight_true">
                {*Название элемента сайта*}
                <div class="row d_flex">
                    <div class="col-lg-10 col-md-9 col-sm-12">
                        <div class="heading_label">
                            {$btr->general_name|escape}
                        </div>
                        <div class="form-group">
                            <input class="form-control" name="name" type="text" value="{$redirect->name|escape}"/>
                            <input name="id" type="hidden" value="{$redirect->id|escape}"/>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-lg-12 col-md-12">
                                <div class="alert alert--icon alert--info">
                                    <div class="alert__content">
                                        <div class="alert__title">{$btr->general_caution|escape}</div>
                                        <p>{$btr->redirects_tooltip_url|escape}</p>
                                        <p>
                                            {sprintf(
                                                {$btr->redirects_tooltip_url_description},
                                                {url_generator route="page" url="some/page" absolute=true},
                                                {$btr->redirects_url_from|escape},
                                                {$btr->redirects_url_to|escape},
                                                'some/page'
                                            )}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-h mb-h">
                                    <div class="heading_label">
                                        {$btr->redirects_url_from|escape}
                                    </div>
                                    <div class="form-group">
                                        <input name="url_from" class="form-control" type="text" value="{$redirect->url_from|escape}" />
                                    </div>
                                </div>
                                <div class="mt-h mb-h">
                                    <div class="heading_label">
                                        {$btr->redirects_url_to|escape}
                                    </div>
                                    <div class="form-group">
                                        <input name="url_to" class="form-control" type="text" value="{$redirect->url_to|escape}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-12">
                        <div class="activity_of_switch" style="height: 50%;">
                            <div class="activity_of_switch_item">
                                <div class="okay_switch clearfix">
                                    <label class="switch_label">{$btr->general_enable|escape}</label>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="enabled" value='1' type="checkbox" id="visible_checkbox" {if $redirect->enabled}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="">
                                    <div class="heading_label" >{$btr->redirect_type|escape}</div>
                                    <select name="status" class="selectpicker mb-1">
                                        <option value="301" {if $redirect->status == 301}selected{/if}>301</option>
                                        <option value="302" {if $redirect->status == 302}selected{/if}>302</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 mt-1">
                        <button type="submit" class="btn btn_small btn_blue float-md-right">
                            {include file='svg_icon.tpl' svgId='checked'}
                            <span>{$btr->general_apply|escape}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>