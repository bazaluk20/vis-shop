<?php

namespace Okay\Modules\Vitalisoft\Analytics\Backend\Controllers;

use Okay\Admin\Controllers\IndexAdmin;

class AnalyticsAdmin extends IndexAdmin
{
    public function fetch()
    {
        if ($this->request->method('post')) {
            $this->settings->set('vitalisoft__analytics__ga_stream_id', $this->request->post('vitalisoft__analytics_ga_stream_id'));
            $this->settings->set('vitalisoft__analytics__ga_measurement_id', $this->request->post('vitalisoft__analytics__ga_measurement_id'));
            $this->settings->set('vitalisoft__analytics__ga_api_secret', $this->request->post('vitalisoft__analytics__ga_api_secret'));
            $this->settings->set('vitalisoft__analytics__gtm_id', $this->request->post('vitalisoft__analytics__gtm_id'));
            $this->settings->set('vitalisoft__analytics__ga_debug_mode', $this->request->post('vitalisoft__analytics__ga_debug_mode'));
            $this->settings->set('vitalisoft__analytics__pixel_id', $this->request->post('vitalisoft__analytics__pixel_id'));
        }

        $this->response->setContent($this->design->fetch('analytics.tpl'));
    }
}