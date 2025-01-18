<script>
{if $settings->vitalisoft__analytics__pixel_id}
var fb_analytics = true;
{*    <!-- Meta Pixel Code -->
    {literal}!function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{/literal}{$settings->vitalisoft__analytics__pixel_id}');
    fbq('track', 'PageView');*}
{else}var fb_analytics = false;
{/if}
{if $settings->vitalisoft__analytics__gtm_id && $settings->vitalisoft__analytics__ga_measurement_id}
var ga4_analytics = true;
var measurement_id = '{$settings->vitalisoft__analytics__ga_measurement_id}';
{if !empty($user)}var user_id = {$user->id}{/if};
var ga_debug_mode = {if $settings->vitalisoft__analytics__ga_debug_mode}true{else}false{/if};
<!-- Google Tag Manager -->
{literal}(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});const f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!=='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{/literal}{$settings->vitalisoft__analytics__gtm_id}');
{else}var ga4_analytics = false;
{/if}
var route_name = '{$route_name}';
var controller = '{$controller}';
{if $order && $route_name === 'order'}
var order_url = '{$order->url}';
var order_analytics_sent = {$order->analytics_sent};
var order_value = parseFloat({$order->total_price});
var order_shipping = {if !$order->separate_delivery}parseFloat({$order->delivery_price}){else}0{/if};
var order_transaction_id = '{$order->id}';
{elseif controller === 'cart' && $cart}
var cart_total = parseFloat({$cart->total_price});
{/if}
</script>