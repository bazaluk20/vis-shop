<?php


namespace Okay\Modules\SimplaMarket\Sets\Controllers;


use Okay\Core\Image;
use Okay\Core\Managers;
use Okay\Core\QueryFactory;
use Okay\Entities\ImagesEntity;
use Okay\Entities\ManagersEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Controllers\AbstractController;
use Okay\Helpers\ProductsHelper;

class SearchController extends AbstractController
{
    public function searchCategories(
        CategoriesEntity $categoriesEntity,
        ManagersEntity   $managersEntity,
        Managers         $managers,
        QueryFactory     $queryFactory
    ){
        $manager = $managersEntity->get($_SESSION['admin']);
        if (!$managers->access('products', $manager)) {
            exit();
        }

        $keyword     = $this->request->get('query', 'string');
        $categoryIds = $this->getCategoryIdsByKeyword($queryFactory, $keyword);

        $fields      = ['id', 'name'];
        $suggestions = [];
        foreach ($categoriesEntity->cols($fields)->find(['id' => $categoryIds]) as $c) {
            $category       = new \stdClass();
            $category->id   = $c->id;
            $category->name = $c->name;

            $suggestion        = new \stdClass();
            $suggestion->value = $category->name;
            $suggestion->data  = $category;
            $suggestions[]     = $suggestion;
        }

        $result              = new \stdClass;
        $result->query       = $keyword;
        $result->suggestions = $suggestions;
        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }

    public function searchProducts(
        ManagersEntity $managersEntity,
        ProductsEntity $productsEntity,
        ImagesEntity   $imagesEntity,
        Image          $imagesCore,
        Managers       $managers,
        ProductsHelper $productsHelper
    ){
        $manager = $managersEntity->get($_SESSION['admin']);
        if (!$managers->access('products', $manager)) {
            exit();
        }

        $keyword   = $this->request->get('query', 'string');
        $imagesIds = [];
        $products  = [];

        foreach ($productsEntity->find(['keyword' => $keyword, 'limit' => 10]) as $product) {
            $products[$product->id] = $product;
            $imagesIds[] = $product->main_image_id;
        }

        $products = $productsHelper->attachVariants($products);
        foreach($products as $product) {
            $product->variants = array_values($product->variants);
        }

        foreach ($imagesEntity->find(['id' => $imagesIds]) as $image) {
            if (isset($products[$image->product_id])) {
                $products[$image->product_id]->image = $image->filename;
            }
        }

        $suggestions = [];
        foreach ($products as $product) {
            if (!empty($product->image)) {
                $product->image = $imagesCore->getResizeModifier($product->image, 35, 35);
            }

            $suggestion = new \stdClass();
            $suggestion->value = $product->name;
            $suggestion->data  = $product;
            $suggestions[]     = $suggestion;
        }

        $result              = new \stdClass;
        $result->query       = $keyword;
        $result->suggestions = $suggestions;
        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }

    private function getCategoryIdsByKeyword(QueryFactory $queryFactory, $keyword)
    {
        return $queryFactory->newSqlQuery()
            ->setStatement("
                SELECT
                    category_id
                FROM ".CategoriesEntity::getLangTable()."
                WHERE name LIKE :keyword
                UNION
                SELECT
                    id AS category_id
                FROM ".CategoriesEntity::getTable()."
                WHERE name LIKE :keyword
            ")
            ->bindValue('keyword', "%{$keyword}%")
            ->results('category_id');
    }
}