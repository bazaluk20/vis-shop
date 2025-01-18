{$meta_title = $btr->sm_cart_last_name_required_title scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->sm_cart_last_name_required_title|escape}
            </div>
        </div>
    </div>
</div>

<div class="alert alert--icon">
    <div class="alert__content">
        <div class="alert__title">{$btr->general_module_description}</div>
        <p>{$btr->sm_cart_last_name_required_description}</p>
    </div>
</div>

