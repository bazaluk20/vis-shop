<div class="row mt-1">
    <div class="col-xs-12 col-md-6 col-lg-6">
        <div class="heading_label">
            <span>{$btr->privat_product_max_pay_num_ii|escape}</span>
            <i class="fn_tooltips" title="{$btr->privat_product_max_pay_num_placehold|escape}">
                {include file='svg_icon.tpl' svgId='icon_tooltips'}
            </i>
        </div>
        <div class="form-group">
            <input class="form-control" name="privat_max_pay_ii" placeholder="24" min="1" max="24" type="number" value="{$category->privat_max_pay_ii|escape}"/>
        </div>
    </div>
    <div class="col-xs-12 col-md-6 col-lg-6">
        <div class="heading_label">
            <span>{$btr->privat_product_max_pay_num_pp|escape}</span>
        </div>
        <div class="form-group">
            <input class="form-control" name="privat_max_pay_pp" placeholder="6" min="1" max="6" type="number" value="{$category->privat_max_pay_pp|escape}"/>
        </div>
    </div>
</div>