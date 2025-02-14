<?php


namespace Okay\Modules\SimplaMarket\ProductsAdvantages\Extensions;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Router;
use Okay\Modules\SimplaMarket\ProductsAdvantages\Entities\ProductsAdvanatgesEntity;

class ProductControllerExtension implements ExtensionInterface
{
    private $design;

    private $productAdvantagesEntity;

    private $productController = false;

    public function __construct(
        EntityFactory $entityFactory,
        Design $design,
        Router $router
    )
    {
        $this->design = $design;
        $this->productAdvantagesEntity = $entityFactory->get(ProductsAdvanatgesEntity::class);
        if ($router->getCurrentRouteName() == 'product') {
            $this->productController = true;
        }
    }

    public function assignProductAdvantages($product)
    {
        if ($this->productController) {
            // Устанавливаем директорию HTML из модуля
            $this->design->setModuleDir(__CLASS__);

            $advantages = $this->productAdvantagesEntity->find(['product_id' => $product->id]);
            $this->design->assign('product_advantages', $advantages);
            $this->design->assign('product_advantages_html', $this->design->fetch('product_advantages.tpl'));

            // Вернём обратно стандартную директорию шаблонов
            $this->design->rollbackTemplatesDir();
        }
    }
}