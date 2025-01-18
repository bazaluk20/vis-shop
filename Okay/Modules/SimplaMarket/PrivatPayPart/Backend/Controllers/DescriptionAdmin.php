<?php


namespace Okay\Modules\SimplaMarket\PrivatPayPart\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;

class DescriptionAdmin extends IndexAdmin
{
    public function fetch()
    {
        if ($this->request->method('post')) {

            $this->settings->update('sm__privat_pay_part__icon_text',
                strip_tags(
                    $this->request->post('sm__privat_pay_part__icon_text'),
                    ['strong', 'b', 'a', 'p', 'em', 'span'])
            );
        }

        $this->response->setContent($this->design->fetch('description.tpl'));
    }
}