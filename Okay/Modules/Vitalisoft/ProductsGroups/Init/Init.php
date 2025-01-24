<?php

namespace Okay\Modules\Vitalisoft\ProductsGroups\Init;

use Okay\Core\Modules\AbstractInit;
use Okay\Core\QueryFactory;
use Okay\Core\ServiceLocator;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Helpers\MainHelper;
use Okay\Modules\Vitalisoft\ProductsGroups\Helpers\ProductsGroupsHelper;

class Init extends AbstractInit
{
    
    public function install()
    {
        $this->setBackendMainController('ProductsGroupsAdmin');
        $query_factory = ServiceLocator::getInstance()
            ->getService(QueryFactory::class);
        
        $query_factory->newSqlQuery()->setStatement(
            <<<EOF
create table ok_vitalisoft__products_groups__products (
    group_id   int not null auto_increment,
    product_id int not null,
    color      varchar(1000) not null default '#7F7F7F',
    primary key (group_id, product_id)
) engine = InnoDB default charset = utf8mb4 collate = utf8mb4_unicode_ci
EOF
        )->execute();
        $query_factory->newSqlQuery()->setStatement(
            <<<EOF
create table ok_vitalisoft__products_groups__features (
    group_id   int not null auto_increment,
    feature_id int not null,
    primary key (group_id, feature_id)
) engine = InnoDB default charset = utf8mb4 collate = utf8mb4_unicode_ci
EOF
        )->execute();
        $query_factory->newSqlQuery()->setStatement(
            <<<EOF
create table ok_vitalisoft__products_groups__groups (
    id       int          not null auto_increment,
    name           varchar(255) not null default '',
    colors_enabled tinyint(1)   not null default 0,
    primary key (id)
) engine = InnoDB default charset = utf8mb4 collate = utf8mb4_unicode_ci
EOF
        )->execute();
    }
    
    public function init()
    {
        $this->addPermission('vitalisoft__products_groups');
        $this->registerBackendController('ProductsGroupsAdmin');
        $this->addBackendControllerPermission(
            'ProductsGroupsAdmin', 'vitalisoft__products_groups'
        );
        
        $this->registerQueueExtension(
            [MainHelper::class, 'commonAfterControllerProcedure'],
            [ProductsGroupsHelper::class, 'commonAfterControllerProcedure']
        );
        
        $this->registerQueueExtension(
            [ProductsEntity::class, 'delete'],
            [ProductsGroupsHelper::class, 'deleteProducts']
        );
        
        $this->registerQueueExtension(
            [FeaturesEntity::class, 'delete'],
            [ProductsGroupsHelper::class, 'deleteFeatures']
        );
    }
}