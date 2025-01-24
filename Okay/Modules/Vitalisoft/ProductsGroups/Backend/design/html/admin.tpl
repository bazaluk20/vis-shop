{$meta_title = $btr->vitalisoft__products_groups__admin_title scope=global}

<div class="wrap_heading">
    <div class="heading_page box_heading">
        {$btr->vitalisoft__products_groups__admin_title}
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="alert alert--icon alert--info">
            <div class="alert__content">{$btr->vitalisoft__products_groups__description}</div>
        </div>
    </div>
</div>
<div class="boxed">
    <form method="post" x-data='{$data}' x-init="groups?.forEach(group => group.collapsed = true)">
        <input type="hidden" name="session_id" value="{$smarty.session.id}">

        <template x-for="(group, groupIndex) in groups" :key="group.key ??= Math.random()">
            <div x-init="initGroup" class="mb-1">
                <div style="display:flex; align-items:center">
                    <button type="button" class="btn btn-outline-secondary fa"
                            :class="group.collapsed ? 'fa-chevron-down' : 'fa-chevron-up'"
                            @click="group.collapsed = !group.collapsed"></button>
                    <input class="form-control" style="flex:auto"
                           placeholder="{$btr->vitalisoft__products_groups__products_group|escape}" x-model="group.name"
                           :name="`groups[${ groupIndex }][name]`">
                </div>
                <div x-init="if (group.collapsed) $($el).hide(); group.getContentEl = () => $el"
                     class="pb-1" style="border-bottom:1px solid #eeeeee">
                    <div style="display:flex; align-items:center; justify-content: center" class="my-1">
                        <div class="okay_switch" style="flex-shrink:0; margin:0 20px">
                            <label class="switch_label">
                                {$btr->vitalisoft__products_groups__colors_enabled}
                            </label>
                            <label class="switch switch-default">
                                <input type="checkbox" x-model="group.colorsEnabled"
                                       :name="`groups[${ groupIndex }][colors_enabled]`" class="switch-input">
                                <span class="switch-label"></span>
                                <span class="switch-handle"></span>
                            </label>
                        </div>

                        <button type="button" @click="removeGroup"
                                class="btn btn-danger">{$btr->vitalisoft__products_groups__delete_group}</button>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:10px" class="mb-1">
                        <template x-for="(product, productIndex) in group.products" :key="product.id">
                            <div x-init="initProduct" style="display:flex;  justify-content:space-between">
                                <input type="hidden" :value="product.id"
                                       :name="`groups[${ groupIndex }][products][${ productIndex }][id]`">
                                <div style="width:35px; flex-shrink:0; display:flex; align-items:center;">
                                    <a x-show="product.image" :href="`index.php?controller=ProductAdmin&id=${ product.id }`" target="_blank">
                                        <img class="product_icon" :src="product.image">
                                    </a>
                                    <img x-show="!product.image" class="product_icon" src="design/images/no_image.png">
                                </div>
                                <div x-text="product.name" style="width:100%; display:flex; align-items:center; padding: 0 10px"></div>
                                <div x-show="group.colorsEnabled"
                                     {literal}x-data="{ wasUrlColor: !product.color?.match(/^$|^#[0-9a-f]{6}$/i), isUrlColor: !product.color?.match(/^$|^#[0-9a-f]{6}$/i) }"{/literal}
                                     x-transition>
                                    <div style="display:flex; align-items:center; padding:4px 20px">
                                        <input class="form-control" placeholder="URL"  title="лінк на мініатюру-зображення"
                                               @change="isUrlColor = !!$event.target.value"
                                               :value="wasUrlColor ? product.color : null"
                                               :name="isUrlColor ? `groups[${ groupIndex }][products][${ productIndex }][color]` : null">
                                        <input type="color"
                                               @change="isUrlColor = !!$event.target.previousElementSibling.value"
                                               :value="wasUrlColor ? null : product.color"
                                               :name="isUrlColor ? null : `groups[${ groupIndex }][products][${ productIndex }][color]`">
                                    </div>
                                </div>
                                <button type="button" data-hint="{$btr->general_delete_product}"
                                        @click="removeProduct"
                                        style="padding:5px"
                                        class="btn btn-outline-danger hint-bottom-right-t-info-s-small-mobile hint-anim">
                                    {include file='svg_icon.tpl' svgId='delete' width=15 height=15}
                                </button>
                            </div>
                        </template>
                    </div>
                    <div class="autocomplete_arrow mb-2">
                        <input type="text" class="form-control" x-init="initSearch"
                               placeholder="{$btr->okaycms__google_merchant__add_products}">
                    </div>
                    <div class="heading_box">
                        {$btr->vitalisoft__products_groups__group_features}
                    </div>
                    <select multiple :name="`groups[${ groupIndex }][features_ids][]`" x-init="initFeatures">
                        <template x-for="featureId in group.commonFeaturesIds" :key="featureId">
                            <option :value="featureId" :selected="group.featuresIds?.includes(featureId) ?? false"
                                    x-text="featuresNames?.[featureId]"></option>
                        </template>
                    </select>
                </div>
            </div>
        </template>

        <div style="display:flex; flex-wrap:wrap; gap:4px">
            <button type="button" class="btn btn_blue" @click="addGroup">
                {include file='svg_icon.tpl' svgId='plus'}
                {$btr->vitalisoft__products_groups__add_group}
            </button>
            <button class="btn btn-info">
                {include file='svg_icon.tpl' svgId='checked'}
                {$btr->general_apply}
            </button>
        </div>
    </form>

    {if $error}
        <div class="alert alert--icon alert--error mt-2 mb-0">
            <div class="alert__content">
                <div class="alert__title">{$btr->vitalisoft__products_groups__error}</div>
                <pre class="m-0" style="flex:auto">{$error}</pre>
            </div>
        </div>
    {/if}
</div>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.js"></script>
<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>
<script src="https://unpkg.com/deepmerge@4.2.2/dist/umd.js"></script>
{literal}
<script>
  function initGroup() {
    this.group.getEl = () => this.$el

    const updateCommonFeatures = (old, products) => {
      this.group.commonFeaturesIds = products?.[0]?.featuresIds?.filter(
        featureId => products.every(
          product => product.featuresIds?.includes(featureId),
        ),
      ) || []
    };
    updateCommonFeatures(undefined, this.group.products)
    this.$watch('group.products', updateCommonFeatures)

    this.$watch('group.collapsed', (old, collapsed) => {
      (collapsed ? expand : collapse)(this.group.getContentEl())
    })
  }

  function initProduct() {
    this.product.getEl = () => this.$el
    this.product.featuresIds = this.product.categoriesIds.flatMap(
      categoryId => this.categoriesFeaturesIds[categoryId]
    ).filter((v, i, a) => a.indexOf(v) === i)
  }

  function initFeatures() {
    const $this = $(this.$el)
    $this.selectpicker({
      noneSelectedText: "{/literal}{$btr->vitalisoft__products_groups__none_selected}{literal}",
    })
    this.$watch('group.featuresIds', () => $this.selectpicker('refresh'))
    this.$watch('group.commonFeaturesIds', () => $this.selectpicker('refresh'))
  }

  function initSearch() {
    // https://github.com/devbridge/jQuery-Autocomplete
    $(this.$el).devbridgeAutocomplete({
      serviceUrl: "{/literal}{url controller='Vitalisoft.ProductsGroups.ProductsGroupsAdmin@search'}{literal}",
      params: { session_id: "{/literal}{$smarty.session.id}{literal}" },
      type: 'POST',
      minChars: 1,
      orientation: 'auto',
      noCache: true,
      preserveInput: false,
      preventBadQueries: true,
      showNoSuggestionNotice: true, // doesn't seem to work
      noSuggestionNotice: "{/literal}{$lang->products_not_found}{literal}",
      transformResult: response => {
        this.ajax = JSON.parse(response)
        return {
          query: this.ajax.query,
          suggestions: !Array.isArray(this.ajax.suggestions) ? []
            : this.ajax.suggestions.filter(suggestion =>
              !this.group.products?.find(product =>
                product.id === suggestion.data.id)),
        }
      },
      formatResult: (suggestion, currentValue) =>
        `<div><img src="${suggestion.data.image ||
        'design/images/no_image.png'}"></div><span>${suggestion.value.replace(
          new RegExp(`(${currentValue.replace(/([\/.*+?|()\[\]{}\\])/g, '\\$1')})`, 'gi'),
          `<strong>$1</strong>`)}</span>`,
      onSelect: suggestion => {
        $(this.$el).val('').focus().blur()
        const product = suggestion.data
        if (this.ajax) {
          for (const categoryId of product.categoriesIds) {
            const featuresIds = this.ajax.categoriesFeaturesIds[categoryId]
            this.categoriesFeaturesIds[categoryId] = [...new Set(
              [...(this.categoriesFeaturesIds[categoryId] || []), ...(featuresIds || [])])]
            for (const featureId of featuresIds) {
              this.featuresNames[featureId] = this.ajax.featuresNames[featureId]
            }
          }
          delete this.ajax
        }
        this.group.products ??= [];
        this.group.products.push({
          id: product.id,
          name: product.name,
          image: product.image,
          categoriesIds: product.categoriesIds,
        })
      },
    })
  }

  function expand(element, complete = () => {}) {
    $(element)
      .slideDown()
      .animate(
        { opacity: 1 },
        { queue: false, complete }
      )
  }

  function collapse(element, complete = () => {}) {
    $(element)
      .slideUp()
      .animate(
        { opacity: 0 },
        { queue: false, complete }
      )
  }

  function addGroup() {
    this.groups.push({})
  }

  function removeGroup() {
    collapse(this.group.getEl(), () => {
      this.groups.splice(this.groups.indexOf(this.group), 1)
    });
  }

  function removeProduct() {
    collapse(this.product.getEl(), () => {
      this.group.products.splice(this.group.products.indexOf(this.product), 1)
    })
  }
</script>
{/literal}