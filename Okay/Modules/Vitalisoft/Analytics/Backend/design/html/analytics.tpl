{$meta_title = $btr->vitalisoft__analytics__settings_heading|escape scope=global}
{* Название страницы *}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->vitalisoft__analytics__settings_heading|escape}
            </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-12 col-sm-12 float-xs-right"></div>
</div>

{* Главная форма страницы *}
<form method="post" enctype="multipart/form-data" class="fn_fast_button">
    <input type=hidden name="session_id" value="{$smarty.session.id}">
    
    {* Measurement Id *}
    <div class="row">
        <div class="col-lg-4 col-md-6 ">
            <div class="heading_label">
                <strong>{$btr->vitalisoft__analytics__ga_measurement_id}</strong>
            </div>
            <div class="mb-1">
                <input class="form-control" type="text" name="vitalisoft__analytics__ga_measurement_id" value="{$settings->vitalisoft__analytics__ga_measurement_id|escape}" />
            </div>
        </div>
    </div>
    {* GA API Secret *}        
    <div class="row">
        <div class="col-lg-4 col-md-6 ">
            <div class="heading_label">
                <strong>{$btr->vitalisoft__analytics__ga_api_secret}</strong>
            </div>
            <div class="mb-1">
                <input class="form-control" type="text" name="vitalisoft__analytics__ga_api_secret" value="{$settings->vitalisoft__analytics__ga_api_secret|escape}" />
            </div>
        </div>
    </div>
    {* GTM ID *}       
    <div class="row">
        <div class="col-lg-4 col-md-6 ">
            <div class="heading_label">
                <strong>{$btr->vitalisoft__analytics__gtm_id}</strong>
            </div>
            <div class="mb-1">
                <input class="form-control" type="text" name="vitalisoft__analytics__gtm_id" value="{$settings->vitalisoft__analytics__gtm_id|escape}" />
            </div>
        </div>
    </div>
    {* Google ANalytics Debug Mode *}
    <div class="row">
        <div class="col-lg-4 col-md-6 ">
            <div class="heading_label">
                <strong>{$btr->vitalisoft__analytics__ga_debug_mode}</strong>
            </div>
            <div class="mb-1">
                <input class="" type="checkbox" name="vitalisoft__analytics__ga_debug_mode" {if $settings->vitalisoft__analytics__ga_debug_mode}checked="checked"{/if} id="debug_mode">
                <label for="debug_mode">{$btr->vitalisoft__analytics__ga_debug_mode}</label>
            </div>
        </div>
    </div>
    {* Facebook Pixel *}        
    <div class="row">
        <div class="col-lg-4 col-md-6 ">
            <div class="heading_label">
                <strong>{$btr->vitalisoft__analytics__pixel_id}</strong>
            </div>
            <div class="mb-1">
                <input class="form-control" type="text" name="vitalisoft__analytics__pixel_id" value="{$settings->vitalisoft__analytics__pixel_id|escape}" />
            </div>
        </div>
    </div>

</form>