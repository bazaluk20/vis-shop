/* VIEW_ITEM_LIST */
if(route_name == 'user_favorites'){
    if (okay.products && okay.products.length) {
        const list = okay.products.filter(function(val) { return val.item_list_name == 'User Wishlist'; })
        view_item_list(list);
    }
}
if(route_name == 'user_browsed'){
    if (okay.products && okay.products.length) {
        const list = okay.products.filter(function(val) { return val.item_list_name == 'User Browsed Products'; })
        view_item_list(list);
    }
}
if(route_name == 'user' || route_name == 'user_browsed'){
    $(document).one('click', 'a[href="#user_wishlist"]', function(e) {
        if (okay.products && okay.products.length) {
            const list = okay.products.filter(function(val) { return val.item_list_name == 'User Wishlist'; })
            view_item_list(list);
        }
    });
}
if(route_name == 'user' || route_name == 'user_favorites'){
    $(document).one('click', 'a[href="#user_browsed"]', function(e) {
        if (okay.products && okay.products.length) {
            const list = okay.products.filter(function(val) { return val.item_list_name == 'User Browsed Products'; })
            view_item_list(list);
        }
    });
}
if(['order','cart','user','user_browsed','user_favorites'].indexOf(route_name) < 0 && okay.products && okay.products.length){
    const list = okay.products.filter(function(val) { return val.item_list_name; })
    if (list.length)
        view_item_list(list);
}
function view_item_list(list){
    if(ga4_analytics)
        dataLayer.push({
            event: 'view_item_list',
            ecommerce: {
                items: list.map(function(val) {
                    return {
                        item_name: val.item_name,
                        item_id: val.item_id,
                        price: parseFloat(val.price),
                        currency: val.currency,
                        item_brand: val.item_brand,
                        item_category: val.item_category,
                        item_list_name: val.item_list_name,
                        index: val.index
                    };
                })
            }
        });
}

/* VIEW_ITEM */
if(route_name === 'product'){
    if(okay.products) {
        const product = okay.products.find(function (val) {
            return !val.item_list_name;
        });
        if (ga4_analytics && product) {
            dataLayer.push({'ecommerce': undefined});
            dataLayer.push({
                event: 'view_item',
                ecommerce: {
                    items: [{
                        item_name: product.item_name,
                        item_id: product.item_id,
                        price: parseFloat(product.price),
                        currency: product.currency,
                        item_brand: product.item_brand,
                        item_category: product.item_category,
                    }]
                }
            });
        }
        /* Facebook */
        /*fbq('track', 'ViewContent', {
            content_name: product[0].name,
            content_category: product[0].category,
            content_ids: product[0].id,
            content_type: 'product',
            value: parseFloat(product[0].price),
            currency: main_currency
        });*/
    }
}

/* SELECT_ITEM - клик по товару */
if(ga4_analytics && ['order','cart'].indexOf(route_name) < 0){
    $(document).on('click', '.product_preview__image a, .product_preview__name_link, a[data-product], a[href^="/products/"]:not(.fn_wishlist)', function(e) {
        if(!$(this).parents('.block__popup_cart').length){
            let clicked_list = false;
            if($(this).closest('.main-products__featured').length)
                clicked_list = 'Mainpage Featured';
            if($(this).closest('.main-products__new').length)
                clicked_list = 'Mainpage New';
            if($(this).closest('.main-products__sale').length)
                clicked_list = 'Mainpage Discounted';
            if($(this).closest('#user_wishlist').length)
                clicked_list = 'User Wishlist';
            if($(this).closest('#user_browsed').length)
                clicked_list = 'User Browsed Products';
            e.preventDefault();
            const prod_href = $(this)[0].pathname;
            if (okay.products) {
                const clicked = okay.products.find(function (val) {
                    if (clicked_list)
                        return (val.href == prod_href && val.item_list_name == clicked_list);
                    else
                        return val.href == prod_href;
                });
                if (clicked) {
                    dataLayer.push({ecommerce: undefined});
                    dataLayer.push({
                        event: 'select_item',
                        ecommerce: {
                            items: [{
                                item_name: clicked.item_name,
                                item_id: clicked.item_id,
                                price: parseFloat(clicked.price),
                                currency: clicked.currency,
                                item_brand: clicked.item_brand,
                                item_category: clicked.item_category,
                                item_list_name: clicked.item_list_name,
                                index: clicked.index
                            }]
                        }
                    });
                }
            }
            window.location.href = prod_href;
        }
    });
}

/* ADD_TO_WISHLIST */
$(document).on('click', '.fn_wishlist', function(){
    if(!$(this).hasClass('selected')){
        prod_id = $(this).data('id');
        if(okay.products) {
            const clicked = okay.products.find(function (val) {
                return val.product_id == prod_id;
            });
            if (ga4_analytics && clicked) {
                dataLayer.push({ecommerce: undefined});
                dataLayer.push({
                    event: 'add_to_wishlist',
                    ecommerce: {
                        items: [{
                            item_name: clicked.item_name,
                            item_id: clicked.item_id,
                            price: parseFloat(clicked.price),
                            currency: clicked.currency,
                            item_brand: clicked.item_brand,
                            item_category: clicked.item_category,
                            item_list_name: clicked.item_list_name,
                            index: clicked.index
                        }]
                    }
                });
            }
            /* FB add to wishlist track */
//        fbq('track', 'AddToWishlist', {
//            value: products[idx].price,
//            currency: currency_code,
//            content_name: products[idx].name,
//            content_category: categories[products[idx].main_category_id].path_url,
//            content_ids: products[idx].id,
//            content_type: 'product'
//        });
        }
    }
});

/* VIEW_CART, BEGIN_CHECKOUT */
if(controller=='CartController' && !jQuery.isEmptyObject(okay.purchases)){
    if(ga4_analytics){
        dataLayer.push({ 'ecommerce': undefined });
        dataLayer.push({
            event: 'view_cart',
            ecommerce: {
                currency: okay.main_currency,
                value: okay.cart_total,
                items: Object.values(okay.purchases)
            }
        });

        $('input[name="name"]').one('keyup', function(e) {
            const total_value = parseInt(Object.values(okay.purchases).map(val=>{
                return parseFloat(val.price)*parseInt(val.quantity);
            }).reduce((a, b) => a + b, 0)*100)/100;
            dataLayer.push({ ecommerce: undefined });
            dataLayer.push({
                event: 'begin_checkout',
                ecommerce: {
                    currency: okay.main_currency,
                    value: total_value,
                    items: Object.values(okay.purchases)
                }
            });
        });
    }
    /*contents = okay.purchases.map(val=>{
        return {
            id: val.id,
            quantity: val.quantity
        }
    });
    fbq('track', 'InitiateCheckout', {
        content_type: 'product',
        contents: contents,
        currency: okay.main_currency,
        value: value
    });*/
}

if (controller === 'CategoryController' || controller === 'ProductsController'){
    /*prodid = products.map(function(val){
        return parseInt(val.id);
    });*/
    if (route_name === 'search'){
        /* facebook */
        /*fbq('track', 'Search', {
            content_type: 'product',
            content_ids: prodid,
            search_string: "{$smarty.get.keyword}"
        });*/
    }
}

$(document).ajaxSuccess(function(event, xhr, settings, data) {
    let searchParams = new URLSearchParams(settings.url);
    if (settings.url.includes("infinit_scroll=1")) {
        Object.assign(okay.products, data.products);
    } else if (settings.url.includes("ajax=1")){
        okay.products = data.products;
    } else if (settings.url.includes("add_item") || settings.url.includes("add_citem")){
        let varId = searchParams.get('variant_id')
        if(searchParams.get('parameter_key'))
            varId += searchParams.get('parameter_key');
        const qty = parseInt(searchParams.get('amount'));
        if(okay.purchases[varId]){
            okay.purchases[varId].quantity += qty;
            analyticsChangedCart(okay.purchases[varId], varId, qty);
        } else {
            const added = okay.products.find(p=>Object.keys(p.variants).some(v=>v == searchParams.get('variant_id')));
            added.item_id = varId;
            added.quantity = qty;
            if(varId.includes('_')){
                added.price = parseFloat(okay.fpv[varId].price);
                added.item_variant = okay.fpv[varId].name;
            } else {
                added.price = parseFloat(added.variants[varId].price);
                added.item_variant = added.variants[varId].name;
            }
            okay.purchases[String(varId)] = added;
            analyticsChangedCart(added, varId, qty);
        }
    } else if (settings.url.includes("update_item") || settings.url.includes("update_citem")){
        if(okay.purchases[searchParams.get('variant_id')]){
            const varId = searchParams.get('variant_id');
            const new_qty = parseInt(searchParams.get('amount'));
            const change_qty = new_qty - okay.purchases[varId].quantity;
            analyticsChangedCart(okay.purchases[varId], varId, change_qty);
            okay.purchases[varId].quantity = new_qty;
        }
    } else if (settings.url.includes("remove_item") || settings.url.includes("remove_citem")){
        if(okay.purchases[searchParams.get('variant_id')]){
            const varId = searchParams.get('variant_id');
            const remove_qty = -parseInt(okay.purchases[varId].quantity);
            analyticsChangedCart(okay.purchases[varId], varId, remove_qty);
            delete okay.purchases[varId];
        }
    }
});

/* ADD_TO_CART, REMOVE_FROM_CART function */
function analyticsChangedCart(item, variantId, qty, event = 'add_to_cart'){
    if(qty < 0)
        event = 'remove_from_cart';
    if(ga4_analytics){
        citem = [{
            item_name: item.item_name,
            item_id: variantId,
            price: parseFloat(item.price),
            currency: okay.main_currency,
            item_variant: item.item_variant,
            item_brand: item.item_brand,
            item_category: item.item_category,
            quantity: Math.abs(qty),
            ...(item.item_list_name ? {item_list_name:item.item_list_name, index: item.index} : {} )
        }];
        dataLayer.push({ecommerce: undefined});
        dataLayer.push({
            event: event,
            ecommerce: {
                items: citem
            }
        });
    }
    /* добавление товара в корзину событие FB Pixel */
//    fbq('track', 'AddToCart', {
//        content_name: p.name,
//        content_category: categories[p.main_category_id].path_url,
//        content_ids: p.id,
//        content_type: 'product',
//        value: parseFloat(p.price),
//        currency: currency
//    });
}

/* PURCHASE */
if(route_name === 'order' && order_url && !order_analytics_sent){
    if(ga4_analytics){
        dataLayer.push({ecommerce: undefined});
        dataLayer.push({
            event: 'purchase',
            ecommerce: {
                transaction_id: order_transaction_id,
                value: order_value,
                shipping: order_shipping,
                currency: okay.main_currency,
                //coupon: 'free_back_rub',
                items: Object.values(okay.order_purchases)
            }
        });
        $.ajax({
            url: okay.router['Vitalisoft_Analytics_purchase'] + '/' + order_url,
            success: function() {
                console.log('GA4 purchase sent');
            }
        });
    }
}