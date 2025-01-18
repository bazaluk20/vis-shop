<?php

namespace Okay\Modules\Vitalisoft\Analytics\Helpers;

use Okay\Core\Settings;

class AnalyticsHelper
{
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function mp_collect($payload)
    {
        $api_secret = $this->settings->get('vitalisoft__analytics__ga_api_secret');
        $measurement_id = $this->settings->get('vitalisoft__analytics__ga_measurement_id');
        $debug_mode = $this->settings->get('vitalisoft__analytics__ga_debug_mode');
        if($debug_mode) $payload['events']['params']['debug_mode'] = true;
        if(!empty($api_secret) && !empty($measurement_id)){
            $curl = curl_init();
            $url = "https://www.google-analytics.com/mp/collect?api_secret=$api_secret&measurement_id=$measurement_id";
            $data = json_encode($payload, JSON_UNESCAPED_UNICODE);
            curl_setopt_array($curl, array(
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => array('Content-Type:application/json', "Content-Length:" . strlen($data)),
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
            ));
            curl_exec($curl);
            curl_close($curl);
        }
    }
}
