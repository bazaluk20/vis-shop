<?php

namespace Okay\Modules\Vitalisoft\ProductsGroups\Backend\Controllers;

use Okay\Admin\Controllers\IndexAdmin;
use Okay\Core\Request;
use Okay\Core\Response;
use Okay\Modules\Vitalisoft\ProductsGroups\Helpers\ProductsGroupsHelper;

class ProductsGroupsAdmin extends IndexAdmin
{
    public function fetch(ProductsGroupsHelper $helper)
    {
        if ($this->request->method('post')) {
            $_SESSION['vitalisoft__products_groups__error']
                = $helper->safeExecute(function () use ($helper) {
                $helper->updateData($this->request->post('groups'));
            });
            Response::redirectTo(
                Request::getCurrentQueryPath()
                . '?controller=Vitalisoft.ProductsGroups.ProductsGroupsAdmin'
            );
        } else {
            $data = null;
            $errors = implode(
                "\n\n", array_filter(
                    [$_SESSION['vitalisoft__products_groups__error'] ?? null,
                     $helper->safeExecute(function () use ($helper, &$data) {
                         $data = $helper->singleQuotesAttrJson(
                             $helper->getBackendData()
                         );
                     })]
                )
            );
            unset($_SESSION['vitalisoft__products_groups__error']);
            $this->design->assign('error', $errors);
            $this->design->assign('data', $data);
            $this->response->setContent($this->design->fetch('admin.tpl'));
        }
    }

    public function search(ProductsGroupsHelper $helper)
    {
        $this->response->setContent(
            json_encode(
                $helper->ajaxProductsSearch(),
                JSON_UNESCAPED_UNICODE
            ),
            RESPONSE_JSON
        );
    }
}