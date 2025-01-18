<?php


namespace Okay\Modules\SimplaMarket\PrivatPayPart\Plugins;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Module;
use Okay\Core\SmartyPlugins\Func;
use Okay\Entities\ModulesEntity;

class PrivatPayPartPlugin extends Func
{
    protected $tag = 'private_paypart_block';

    protected $design;
    protected $module;
    protected $entityFactory;

    public function __construct(Design $design, Module $module, EntityFactory $entityFactory)
    {
        $this->design = $design;
        $this->module = $module;
        $this->entityFactory = $entityFactory;
    }

    public function run($vars)
    {
        /* ModulesEntity $modulesEntity */
        $modulesEntity = $this->entityFactory->get(ModulesEntity::class);
        $module = $modulesEntity->mappedBy('module_name')->findOne(['enabled' => 1, 'vendor' => 'SimplaMarket', 'module_name' => ['PrivatPayPart', 'MonoPayPart']]);
        //  выясняем какой один из связанных молулей первый и чтобы срабатывать только у одного
        if ($module->module_name == 'PrivatPayPart') {
            if (!empty($vars['prod'])) {    //  прокидываем данные одного товара внутрь контейнера
                $this->design->assign('prod', $vars['prod']);
            }

            //  $vars['type'] => variants: [ 'product', 'list' ]
            if (!empty($vars['type']) && $vars['type'] == 'list') {
                return $this->design->fetch('list_smarty_plugin_pay_part_block.tpl');
            } elseif (!empty($vars['type']) && $vars['type'] == 'product') {
                return $this->design->fetch('product_smarty_plugin_pay_part_block.tpl');
            }

            return $this->design->fetch('product_smarty_plugin_pay_part_block.tpl');
        }
    }
}