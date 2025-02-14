<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="boxed fn_toggle_wrap min_height_210px">
            <div class="heading_box">
                {$btr->settings_advantages}
            </div>

            <div class="okay_list products_list">
                {*Шапка таблицы*}
                <div class="fn_step_sorting okay_list_head">
                    <div class="okay_list_boding okay_list_drag"></div>
                    <div class="okay_list_heading okay_list_check">
                        <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value="" />
                        <label class="okay_ckeckbox" for="check_all_1"></label>
                    </div>
                    <div class="okay_list_heading okay_list_advantage_image">{$btr->advantage_image_title}</div>

                    <div class="okay_list_heading okay_list_advantage_description">{$btr->advantage_description_title}</div>

                    <div class="okay_list_heading okay_list_close hidden-sm-down"></div>
                </div>

                <div class="okay_list_body sort_extended fn_advantage_list">
                    {foreach $product_advantages as $advantage}
                        <div class="fn_step-1 fn_row okay_list_body_item fn_sort_item">
                            <div class="okay_list_row">
                                <input type="hidden" name="product_advantages_positions[{$advantage->id}]" value="{$advantage->position}">

                                <div class="okay_list_boding okay_list_drag move_zone">
                                    {include file='svg_icon.tpl' svgId='drag_vertical'}
                                </div>

                                <div class="okay_list_boding okay_list_check">
                                    <input class="hidden_check" type="checkbox" id="id_{$advantage->id}" name="product_advantages_check[]" value="{$advantage->id}"/>
                                    <label class="okay_ckeckbox" for="id_{$advantage->id}"></label>
                                </div>

                                <div class="okay_list_boding okay_list_advantage_image">
                                    <div class="fn_image_block">

                                        {if $advantage->filename}
                                            <input type="hidden" class="fn_accept_delete" name="product_advantages_images_to_delete[{$advantage->id}]" value="">
                                            <div class="fn_parent_image">
                                                <div class="advantage__image image_wrapper fn_image_wrapper text-xs-center">
                                                    <a href="javascript:;" class="fn_delete_item remove_image"></a>
                                                    <img src="{$advantage->filename|resize:120:120:false:$config->resized_products_advantages_dir}" alt="" />
                                                </div>
                                            </div>
                                        {else}
                                            <div class="fn_parent_image"></div>
                                        {/if}

                                        <div class="fn_upload_image dropzone_block_image {if $advantage->filename} hidden{/if}">
                                            <i class="fa fa-plus font-5xl" aria-hidden="true"></i>
                                            <input class="dropzone_image" name="product_advantages_image[{$advantage->id}]" type="file" />
                                        </div>

                                        <div class="brand_image image_wrapper fn_image_wrapper fn_new_image text-xs-center hidden">
                                            <a href="javascript:;" class="fn_delete_item remove_image"></a>
                                            <img style="max-height: 73px;" src="" alt="" />
                                        </div>
                                    </div>
                                </div>

                                <div class="okay_list_boding okay_list_advantage_description">
                                    <textarea class="editor_small advantage_textarea form-control short_textarea" name="product_advantages_text[{$advantage->id}]">{$advantage->text}</textarea>
                                </div>

                                <div class="okay_list_boding okay_list_close hidden-sm-down">
                                    {*delete*}
                                    <button data-hint="{$btr->general_delete_product|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
                                        {include file='svg_icon.tpl' svgId='trash'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                    <div class="okay_list_body_item">
                        <button class="fn_add_new_advantage btn btn_small btn-info" type="button">
                            {include file='svg_icon.tpl' svgId='plus'}
                            <span>Добавить преимущество</span>
                        </button>
                    </div>
                    <div class="heading_label" style="margin-top: 12px;">
                        <span>Рекомендуемый размер изображения 600х380px.</span>
                    </div>
                </div>


                <div id="new_advantage" class="fn_step-1 fn_row okay_list_body_item fn_sort_item hidden">
                    <div class="okay_list_row">
                        <div class="okay_list_boding okay_list_drag "></div>
                        <div class="okay_list_boding okay_list_check"></div>
                        <div class="okay_list_boding okay_list_advantage_image">
                            <div class="fn_image_block">
                                <div class="fn_parent_image"></div>
                                <div class="fn_upload_image dropzone_block_image">
                                    <i class="fa fa-plus font-5xl" aria-hidden="true"></i>
                                    <input class="dropzone_image" name="new_product_advantages_images[]" type="file" disabled />
                                </div>

                                <div class="advantage__image image_wrapper fn_image_wrapper fn_new_image text-xs-center hidden">
                                    <a href="javascript:;" class="fn_delete_item remove_image"></a>
                                    <img style="max-height: 73px;" src="" alt="" />
                                </div>
                            </div>
                        </div>

                        <div class="okay_list_boding okay_list_advantage_description">
                            <textarea class="advantage__textarea form-control short_textarea" disabled name="new_product_advantages[text][]"></textarea>
                        </div>

                        <div class="okay_list_boding okay_list_close hidden-sm-down">
                            {*delete*}
                            <button data-hint="{$btr->general_delete_product|escape}" type="button" class="btn_close fn_remove_new hint-bottom-right-t-info-s-small-mobile  hint-anim"">
                            {include file='svg_icon.tpl' svgId='trash'}
                            </button>
                        </div>
                    </div>
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
                            <select name="product_advantages_action" class="selectpicker form-control products_action">
                                <option value="delete">{$btr->general_delete|escape}</option>
                            </select>
                        </div>

                        <div class="fn_additional_params">
                            <div class="fn_move_to_page col-lg-12 col-md-12 col-sm-12 hidden fn_hide_block">
                                <select name="target_page" class="selectpicker form-control dropup">
                                    {section target_page $pages_count}
                                        <option value="{$smarty.section.target_page.index+1}">{$smarty.section.target_page.index+1}</option>
                                    {/section}
                                </select>
                            </div>
                            <div class="fn_move_to_category col-lg-12 col-md-12 col-sm-12 hidden fn_hide_block">
                                <select name="target_category" class="selectpicker form-control dropup" data-live-search="true" data-size="10">
                                    {function name=category_select_btn level=0}
                                        {foreach $categories as $category}
                                            <option value='{$category->id}'>{section sp $level}&nbsp;&nbsp;&nbsp;&nbsp;{/section}{$category->name|escape}</option>
                                            {category_select_btn categories=$category->subcategories selected_id=$selected_id level=$level+1}
                                        {/foreach}
                                    {/function}
                                    {category_select_btn categories=$categories}
                                </select>
                            </div>
                            <div class="fn_move_to_brand col-lg-12 col-md-12 col-sm-12 hidden fn_hide_block">
                                <select name="target_brand" class="selectpicker form-control dropup" data-live-search="true" data-size="{if $brands|count<10}{$brands|count}{else}10{/if}">
                                    <option value="0">{$btr->general_not_set|escape}</option>
                                    {foreach $all_brands as $b}
                                        <option value="{$b->id}">{$b->name|escape}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn_small btn_blue">
                        {include file='svg_icon.tpl' svgId='checked'}
                        <span>{$btr->general_apply|escape}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $(document).on('click', '.fn_remove_new', function() {
            $(this).closest('.fn_row').remove();
        });

        $('.fn_add_new_advantage').on('click', function() {
            const new_advantage = $('#new_advantage').clone();
            new_advantage.removeAttr('id');
            new_advantage.find('[name]').prop('disabled', false);
            new_advantage.removeClass('hidden');
            $('.fn_advantage_list').append(new_advantage);
        });

        $(document).on("mouseenter click", ".fn_color", function () {
            var elem = $(this);
            elem.ColorPicker({
                onChange: function (hsb, hex, rgb) {
                    elem.css('backgroundColor', '#' + hex);
                    elem.next().val('#' + hex);
                },
                onBeforeShow: function () {
                    $(this).ColorPickerSetColor($(this).next().val());
                }
            });
        });

        $(".fn_submit_delete").on("click",function () {
            setTimeout(function(){
                $("form#product").submit();
            }, 1)
        });
    });
</script>