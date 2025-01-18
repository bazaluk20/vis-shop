<?php


namespace Okay\Modules\SimplaMarket\Redirects\Init;


use Okay\Helpers\MainHelper;
use Okay\Core\Modules\EntityField;
use Okay\Core\Modules\AbstractInit;
use Okay\Modules\SimplaMarket\Redirects\Entities\RedirectsEntity;
use Okay\Modules\SimplaMarket\Redirects\Extensions\Redirects;

class Init extends AbstractInit
{
    public function install()
    {
        $this->setBackendMainController('RedirectsAdmin');

        $this->migrateEntityTable(RedirectsEntity::class, [
            (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('name'))->setTypeText()->setNullable(),
            (new EntityField('url_from'))->setTypeVarchar(511)->setDefault(''),
            (new EntityField('url_to'))->setTypeVarchar(511)->setDefault(''),
            (new EntityField('enabled'))->setTypeTinyInt(1, true)->setDefault(1)->setIndex(),
            (new EntityField('status'))->setTypeEnum(['301','302'])->setDefault('301'),
        ]);
    }

    public function init()
    {
        $this->addPermission('simpla-market__redirects');

        $this->extendBackendMenu('left_seo', [
            'left_custom_redirects_title' => [
                'RedirectsAdmin',
                'RedirectAdmin',
            ]
        ]);

        $this->registerBackendController('RedirectsAdmin');
        $this->addBackendControllerPermission('RedirectsAdmin', 'simpla-market__redirects');

        $this->registerBackendController('RedirectAdmin');
        $this->addBackendControllerPermission('RedirectAdmin', 'simpla-market__redirects');

        $this->extendUpdateObject('SimplaMarket.Redirects.RedirectsEntity', 'simpla-market__redirects', RedirectsEntity::class);

        $this->registerQueueExtension(
            [MainHelper::class, 'setDesignDataProcedure'],
            [Redirects::class, 'redirect']
        );
    }
}