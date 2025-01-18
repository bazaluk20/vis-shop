<div style="margin-top: 15px;">
    <div class="row">
        <div class="col-xs-12 col-md-6 col-lg-6">
            <div class="heading_label">
                <span>{$btr->privat_product_max_pay_num_ii|escape}</span>
            </div>
            <div class="form-group">
                <input class="form-control" name="privat_max_pay_ii" placeholder="{$btr->sm__privat_pay_part__privat_product_max_pay_num|escape} 24" min="1" max="24" type="number" value="{$product->privat_max_pay_ii|escape}"/>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-6">
            <div class="heading_label">
                <span>{$btr->privat_product_max_pay_num_pp|escape}</span>
            </div>
            <div class="form-group">
                <input class="form-control" name="privat_max_pay_pp" placeholder="{$btr->sm__privat_pay_part__privat_product_max_pay_num|escape} 6" min="1" max="6" type="number" value="{$product->privat_max_pay_pp|escape}"/>
            </div>
        </div>
    </div>
</div>