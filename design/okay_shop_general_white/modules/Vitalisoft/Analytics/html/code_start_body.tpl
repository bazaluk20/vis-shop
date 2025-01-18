{if $settings->vitalisoft__analytics__pixel_id}
    <!-- Meta Pixel Code (noscript) -->
{*    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={$settings->vitalisoft__analytics__pixel_id}&ev=PageView&noscript=1"/></noscript>*}
{/if}
{if $settings->vitalisoft__analytics__gtm_id && $settings->vitalisoft__analytics__ga_measurement_id}
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={$settings->vitalisoft__analytics__gtm_id}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
{/if}