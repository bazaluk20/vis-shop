{$meta_title = $btr->privat_module_name|escape scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->privat_module_name|escape}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="alert alert--icon alert--info">
            <div class="alert__content">
                <div class="alert__title">{$btr->alert_info|escape}</div>
                <p>{$btr->privat_description_part_1} <a href="https://payparts2.privatbank.ua/ipp/" target="_blank">https://payparts2.privatbank.ua/ipp/</a> {$btr->privat_description_part_2}</p>
                <p>{$btr->privat_description_part_3}</p>
                <p>{$btr->privat_description_part_4}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="alert alert--icon alert--warning">
            <div class="alert__content">
                <div class="alert__title">{$btr->sm__privat_pay_part__settings_title}</div>

                <form method="post" enctype="multipart/form-data" class="fn_fast_button">
                    <input type="hidden" name="session_id" value="{$smarty.session.id}"/>

                    {*Параметры элемента*}
                    <div class="mt-2">
                        <div class="heading_box">
                            {$btr->sm__privat_pay_part__icon_text|escape}
                            <i class="fn_tooltips" title="{$btr->sm__privat_pay_part__icon_text_tooltip|escape}">
                                {include file='svg_icon.tpl' svgId='icon_tooltips'}
                            </i>
                        </div>

                        <div class="toggle_body_wrap on fn_card">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="mb-1">
                                        <textarea name="sm__privat_pay_part__icon_text"
                                                  class="form-control okay_textarea editor_small">{$settings->sm__privat_pay_part__icon_text}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn_small btn_blue">
                        {include file='svg_icon.tpl' svgId='checked'}
                        <span>{$btr->general_apply|escape}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="boxed">
            <div>
                <img src="{$rootUrl}/Okay/Modules/SimplaMarket/PrivatPayPart/Backend/design/images/test_keys.png">
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="design/js/tinymce_jq/tinymce.min.js"></script>
{literal}
    <script>
        $(function () {
            tinyMCE.init({
                selector: "textarea.editor_small",
                height: '100',
                plugins: ["link", "code"],
                toolbar_items_size: 'small',
                menubar: '',
                toolbar1: "fontselect fontsizeselect | bold italic underline | link unlink | alignleft aligncenter alignright alignjustify | forecolor backcolor | code",
                statusbar: true,
                font_formats: "Andale Mono=andale mono,times;" +
                    "Arial=arial,helvetica,sans-serif;" +
                    "Arial Black=arial black,avant garde;" +
                    "Book Antiqua=book antiqua,palatino;" +
                    "Comic Sans MS=comic sans ms,sans-serif;" +
                    "Courier New=courier new,courier;" +
                    "Georgia=georgia,palatino;" +
                    "Helvetica=helvetica;" +
                    "Impact=impact,chicago;" +
                    "Open Sans=Open Sans,sans-serif;" +
                    "Symbol=symbol;" +
                    "Tahoma=tahoma,arial,helvetica,sans-serif;" +
                    "Terminal=terminal,monaco;" +
                    "Times New Roman=times new roman,times;" +
                    "Trebuchet MS=trebuchet ms,geneva;" +
                    "Verdana=verdana,geneva;" +
                    "Webdings=webdings;" +
                    "Wingdings=wingdings,zapf dingbats",
            });
        });
    </script>
{/literal}
