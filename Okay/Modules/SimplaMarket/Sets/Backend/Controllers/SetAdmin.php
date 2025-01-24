<?php


namespace Okay\Modules\SimplaMarket\Sets\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;
use Okay\Modules\SimplaMarket\Sets\Helpers\SetsHelper;
use Okay\Modules\SimplaMarket\Sets\Requests\SetsRequest;

class SetAdmin extends IndexAdmin
{
    public function fetch(SetsHelper $setsHelper, SetsRequest $setsRequest)
    {
        if ($this->request->method('post')) {
            $set = $setsRequest->postSet();

            if (empty($set->id)) {
                $set->id = $setsHelper->add($set);
            } else {
                $setsHelper->update($set->id, $set);
            }

            $id = $set->id;
        } else {
            $id = $this->request->get('id', 'integer');
        }

        $set = $setsHelper->get((int) $id);
        $this->design->assign('set', $set);
        $this->response->setContent($this->design->fetch('set.tpl'));
    }
}