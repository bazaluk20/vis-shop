<?php


namespace Okay\Modules\SimplaMarket\Redirects\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;
use Okay\Modules\SimplaMarket\Redirects\Entities\RedirectsEntity;

class RedirectAdmin extends IndexAdmin
{
    public function fetch(RedirectsEntity $redirectsEntity)
    {
        if ($this->request->method('post')) {
            $redirect           = new \stdClass();
            $redirect->id       = $this->request->post('id');
            $redirect->name     = $this->request->post('name');
            $redirect->url_from = ltrim($this->request->post('url_from'), '/');
            $redirect->url_to   = ltrim($this->request->post('url_to'), '/');
            $redirect->enabled  = $this->request->post('enabled');
            $redirect->status   = $this->request->post('status');

            if ($error = $this->getRedirectError($redirect)) {
                $this->design->assign('message_error', $error);
            } else {
                if (empty($redirect->id)) {
                    $redirect->id = $redirectsEntity->add($redirect);
                    $this->postRedirectGet->storeMessageSuccess('added');
                    $this->postRedirectGet->storeNewEntityId($redirect->id);
                } else {
                    $redirect->id = $redirectsEntity->update($redirect->id, $redirect);
                    $this->postRedirectGet->storeMessageSuccess('updated');
                }

                $this->postRedirectGet->redirect();
            }
        } else {
            $redirectId = $this->request->get('id');
            $redirect   = $redirectsEntity->get((int) $redirectId);
        }

        $this->design->assign('redirect', $redirect);

        $this->response->setContent($this->design->fetch('redirect.tpl'));
    }

    private function getRedirectError($redirect)
    {
        $error = '';

        if (empty($redirect->url_from)) {
            $error = 'empty_url_from';
        } elseif (empty($redirect->url_to)) {
            $error = 'empty_url_to';
        } elseif (empty($redirect->name)) {
            $error = 'empty_name';
        }

        return $error;
    }
}