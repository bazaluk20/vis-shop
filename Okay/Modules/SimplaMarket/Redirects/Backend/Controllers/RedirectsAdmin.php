<?php


namespace Okay\Modules\SimplaMarket\Redirects\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;
use Okay\Modules\SimplaMarket\Redirects\Entities\RedirectsEntity;

class RedirectsAdmin extends IndexAdmin
{
    public function fetch(RedirectsEntity $redirectsEntity)
    {
        $filter = [];

        $filter['page'] = max(1, $this->request->get('page', 'integer'));

        if ($filter['limit'] = $this->request->get('limit', 'integer')) {
            $filter['limit'] = max(5, $filter['limit']);
            $filter['limit'] = min(100, $filter['limit']);
            $_SESSION['redirects_num_admin'] = $filter['limit'];
        } elseif (!empty($_SESSION['redirects_num_admin'])) {
            $filter['limit'] = $_SESSION['redirects_num_admin'];
        } else {
            $filter['limit'] = 25;
        }

        if ($this->request->method('post')) {
            // Действия с выбранными
            $ids = $this->request->post('check');
            if(!empty($ids)) {
                switch($this->request->post('action')) {
                    case 'disable': {
                        $redirectsEntity->update($ids, ['enabled' => 0]);
                        break;
                    }
                    case 'enable': {
                        $redirectsEntity->update($ids, ['enabled' => 1]);
                        break;
                    }
                    case 'delete': {
                        $redirectsEntity->delete($ids);
                        break;
                    }
                }
            }
        }

        $keyword = $this->request->get('keyword');
        if (!empty($keyword)) {
            $this->design->assign('keyword', $keyword);
            $filter['keyword'] = $keyword;
        }

        $filterType = $this->request->get('filter');
        if (!empty($filterType)) {
            if ($filterType == '301') {
                $filter['status'] = '301';
            } elseif ($filterType == '302') {
                $filter['status'] = '302';
            } elseif($filterType == "enabled") {
                $filter = ['enabled' => 1];
            } elseif($filterType == "disabled") {
                $filter = ['disabled' => 1];
            }

            $this->design->assign('filter', $filterType);
        }

        $redirectsCount = $redirectsEntity->count($filter);

        if ($this->request->get('page') == 'all') {
            $filter['limit'] = $redirectsCount;
        }
        if ($filter['limit'] > 0) {
            $pagesCount = ceil($redirectsCount/$filter['limit']);
        } else {
            $pagesCount = 0;
        }

        $filter['page'] = min($filter['page'], $pagesCount);

        $redirects = $redirectsEntity->find($filter);

        $this->design->assign('redirects', $redirects);
        $this->design->assign('redirects_count', $redirectsCount);
        $this->design->assign('pages_count',  $pagesCount);
        $this->design->assign('current_page', $filter['page']);
        $this->design->assign('current_limit', $filter['limit']);

        $this->response->setContent($this->design->fetch('redirects.tpl'));
    }
}