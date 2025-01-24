<?php

namespace Okay\Modules\Vitalisoft\ProductsGroups\Helpers;

use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Image;
use Okay\Core\Languages;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\QueryFactory;
use Okay\Core\Request;
use Okay\Core\Router;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesEntity;
use Okay\Entities\ImagesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\VariantsEntity;
use Psr\Log\LoggerInterface;

class ProductsGroupsHelper implements ExtensionInterface
{
    private LoggerInterface $logger;
    private Languages $languages;
    private Image $image;
    private Request $request;
    private Design $design;
    private QueryFactory $query_factory;
    private ProductsEntity $products_entity;
    private FeaturesEntity $features_entity;
    private FeaturesValuesEntity $features_values_entity;
    
    public function __construct(
        LoggerInterface $logger,
        Languages       $languages,
        Image           $image,
        Request         $request,
        Design          $design,
        QueryFactory    $query_factory,
        EntityFactory   $entity_factory
    )
    {
        $this->logger = $logger;
        $this->languages = $languages;
        $this->image = $image;
        $this->request = $request;
        $this->design = $design;
        $this->query_factory = $query_factory;
        $this->products_entity = $entity_factory->get(ProductsEntity::class);
        $this->features_entity = $entity_factory->get(FeaturesEntity::class);
        $this->features_values_entity = $entity_factory->get(
            FeaturesValuesEntity::class
        );
    }
    
    public function commonAfterControllerProcedure()
    {
        if (Router::getCurrentRouteName() === 'product') {
            $this->design->assign('vitalisoft__products_groups', $this->getFrontendData());
        }
    }
    
    public function getFrontendData()
    {
        $current_product = $this->design->getVar('product');
        $current_groups = $this->query_factory->newSelect()
            ->distinct()
            ->cols(['g.id', 'g.name', 'g.colors_enabled'])
            ->from('__vitalisoft__products_groups__groups AS g')
            ->innerJoin('__vitalisoft__products_groups__products AS gp', 'gp.group_id = g.id')
            ->where('gp.product_id = ?', $current_product->id)
            ->results(null, 'id');
        if (!$current_groups) return null;
        $select = $this->query_factory->newSelect()
            ->cols([
                'gp.group_id',
                'gp.product_id AS id',
                'p.url',
                'gp.color',
                'v.stock'
            ])
            ->from('__vitalisoft__products_groups__products AS gp')
            ->innerJoin(ProductsEntity::getTable() . ' AS p', 'p.id = gp.product_id')
            ->innerJoin(VariantsEntity::getTable() . ' AS v', 'p.id = v.product_id')
            ->where('gp.group_id IN (?)', array_keys($current_groups))
            ->groupBy(['p.id'])
            ->orderBy(['p.position']);
        $products = $select
            ->results(null, 'id');
        $features = $this->query_factory->newSelect()
            ->cols(['gf.group_id', 'gf.feature_id AS id', 'lf.name'])
            ->from('__vitalisoft__products_groups__features AS gf')
            ->innerJoin(
                '__lang_features AS lf',
                'lf.feature_id = gf.feature_id AND lf.lang_id = ' . $this->languages->getLangId()
            )
            ->innerJoin(FeaturesEntity::getTable() . ' AS f', 'f.id = gf.feature_id')
            ->where('gf.group_id IN (?)', array_keys($current_groups))
            ->orderBy(['f.position'])
            ->results(null, 'id');
        $current_features_values = !$features ? [] : $this->features_values_entity
            ->cols(['feature_id', 'GROUP_CONCAT(l.value ORDER BY fv.position SEPARATOR ", ") AS value'])
            ->getSelect([
                'product_id' => $current_product->id,
                'feature_id' => array_keys($features),
            ])
            ->innerJoin('__products_features_values AS pf', 'pf.value_id = fv.id')
            ->groupBy(['pf.product_id', 'fv.feature_id'])
            ->results('value', 'feature_id');
        $colors = $this->query_factory->newSelect()
            ->distinct()
            ->cols(['gp.color'])
            ->from('__vitalisoft__products_groups__products AS gp')
            ->innerJoin('__vitalisoft__products_groups__groups AS g', 'g.id = gp.group_id')
            ->innerJoin(ProductsEntity::getTable() . ' AS p', 'p.id = gp.product_id')
            ->where('g.colors_enabled')
            ->where('gp.group_id IN (?)', array_keys($current_groups))
            ->orderBy(['p.position'])
            ->results('color');
        $current_color = $this->query_factory->newSelect()
            ->cols(['gp.color'])
            ->from('__vitalisoft__products_groups__products AS gp')
            ->where('gp.product_id = ?', $current_product->id)
            ->result('color');
        foreach ($products as $product) {
            $product->features_values = !$features ? [] : $this->features_values_entity
                ->cols(['feature_id', 'GROUP_CONCAT(l.value ORDER BY fv.position SEPARATOR ", ") AS value'])
                ->getSelect([
                    'product_id' => $product->id,
                    'feature_id' => array_keys($features),
                ])
                ->innerJoin('__products_features_values AS pf', 'pf.value_id = fv.id')
                ->groupBy(['pf.product_id', 'fv.feature_id'])
                ->results('value', 'feature_id');
        }
        $data = array();
        foreach ($features as $feature) {
            $data['features'][$feature->id]['name'] = $feature->name;
            $feature_values = $this->features_values_entity
                ->cols(['GROUP_CONCAT(DISTINCT l.value ORDER BY fv.position SEPARATOR ", ") AS value'])
                ->getSelect([
                    'feature_id' => $feature->id,
                    'product_id' => array_keys($products),
                ])
                ->innerJoin('__products_features_values AS pf', 'pf.value_id = fv.id')
                ->groupBy(['pf.product_id', 'fv.feature_id'])
                ->results('value');
            foreach ($feature_values as $feature_value) {
                $is_current_product = false;
                $product_url = null;
                foreach ($products as $product) {
                    $current_clone = $current_features_values;
                    $product_clone = $product->features_values;
                    unset($current_clone[$feature->id]);
                    unset($product_clone[$feature->id]);
                    if ($product->features_values[$feature->id] === $feature_value
                        && $product_clone == $current_clone
                        && (!$current_groups[$product->group_id]->colors_enabled
                            || $product->color === $current_color)) {
                        $is_current_product = $product->id === $current_product->id;
                        $product_url = $product->url;
                        $variant_stock = $product->stock;
                    }
                }
                $data['features'][$feature->id]['values'][] = [
                    'value' => $feature_value,
                    'product_url' => $product_url,
                    'is_current' => $is_current_product,
                    'stock' => $variant_stock,
                ];
            }
        }
        foreach ($colors as $color) {
            $is_current_product = false;
            $product_url = null;
            foreach ($products as $product) {
                if ($product->color === $color
                    && $product->features_values == $current_features_values) {
                    $is_current_product = $product->id === $current_product->id;
                    $product_url = $product->url;
                    $variant_stock = $product->stock;
                }
            }
            $ordinary_color = preg_match('/#[0-9a-f]{6}/i', $color);
            if ($ordinary_color) $style = "background-color: $color;";
            else $style = 'background-image: url("' . strtr($color, ['"' => '\\"', '\\' => '\\\\']) . '");';
            $data['colors'][] = [
                'style' => htmlentities($style),
                'product_url' => $product_url,
                'is_current' => $is_current_product,
                'stock' => $variant_stock,
            ];
        }
        return json_decode(json_encode($data));
    }
    
    public function getBackendData(): array
    {
        $products = $this->query_factory->newSelect()
            ->cols([
                'gp.group_id',
                'gp.product_id AS id',
                'gp.color',
                'p.name',
                'i.filename AS image',
            ])
            ->from('__vitalisoft__products_groups__products AS gp')
            ->innerJoin(
                ProductsEntity::getTable() . ' AS p',
                'p.id = gp.product_id'
            )
            ->leftJoin(
                ImagesEntity::getTable() . ' AS i',
                'i.id = p.main_image_id'
            )
            ->results();
        $features = $this->query_factory->newSelect()
            ->cols(['group_id', 'feature_id'])
            ->from('__vitalisoft__products_groups__features')
            ->results();
        $groups = $this->query_factory->newSelect()
            ->cols(['id', 'name', 'colors_enabled'])
            ->from('__vitalisoft__products_groups__groups')
            ->results();
        $data = $categories_ids = array();
        foreach ($products ?? [] as $product) {
            if (!empty($product->image)) {
                $product->image = $this->image
                    ->getResizeModifier($product->image, 35, 35);
            }
            $categories_ids = array_merge(
                $categories_ids, $product->categoriesIds
                = $this->getProductCategories($product->id)
            );
            $group_id = $product->group_id;
            unset($product->group_id);
            $data[$group_id]['products'][] = $product;
        }
        foreach ($features ?? [] as $feature) {
            $data[$feature->group_id]['featuresIds'][]
                = $feature->feature_id;
        }
        foreach ($groups ?? [] as $group) {
            $data[$group->id]['name'] = $group->name;
            $data[$group->id]['colorsEnabled'] = (bool)$group->colors_enabled;
        }
        $data = array_values($data);
        
        list($categories_features_ids, $features_names)
            = $this->getCategoriesFeatures($categories_ids);
        
        return [
            'groups' => $data,
            'categoriesFeaturesIds' => $categories_features_ids,
            'featuresNames' => $features_names,
        ];
    }
    
    public function ajaxProductsSearch(): array
    {
        $products = $this->products_entity
            ->cols(['id', 'name', 'i.filename AS image'])
            ->getSelect([
                'limit' => 10,
                'keyword' => $this->request->post('query', 'string')
            ])
            ->leftJoin(
                ImagesEntity::getTable() . ' AS i',
                'i.id = p.main_image_id'
            )
            ->results();
        $suggestions = $categories_ids = array();
        foreach ($products as $index => $product) {
            if (!empty($product->image)) {
                $product->image = $this->image
                    ->getResizeModifier($product->image, 35, 35);
            }
            $categories_ids = array_merge(
                $categories_ids, $product->categoriesIds
                = $this->getProductCategories($product->id)
            );
            $suggestions[$index] = [
                'value' => $product->name,
                'data' => $product,
            ];
        }
        list($categories_features_ids, $features_names)
            = $this->getCategoriesFeatures($categories_ids);
        
        return [
            'query' => $this->request->post('query', 'string'),
            'suggestions' => $suggestions,
            'featuresNames' => $features_names,
            'categoriesFeaturesIds' => $categories_features_ids,
        ];
    }
    
    public function updateData(?array $data)
    {
        $groups_products = $groups_features = $groups = array();
        foreach ($data ?? [] as $group_index => $group) {
            foreach ($group['products'] ?? [] as $product) {
                $groups_products[] = [
                    'group_id' => $group_index + 1,
                    'product_id' => $product['id'],
                    'color' => $product['color'] ?? '#000000',
                ];
            }
            foreach ($group['features_ids'] ?? [] as $feature_id) {
                $groups_features[] = [
                    'group_id' => $group_index + 1,
                    'feature_id' => $feature_id,
                ];
            }
            $groups[] = [
                'id' => $group_index + 1,
                'name' => $group['name'] ?? null,
                'colors_enabled' => isset($group['colors_enabled']),
            ];
        }
        $this->query_factory
            ->newSqlQuery()
            ->setStatement(
                <<<EOF
TRUNCATE __vitalisoft__products_groups__products;
TRUNCATE __vitalisoft__products_groups__features;
TRUNCATE __vitalisoft__products_groups__groups;
EOF
            )->execute();
        if ($groups_products) {
            $insert = $this->query_factory
                ->newInsert()->ignore()
                ->into('__vitalisoft__products_groups__products')
                ->addRows($groups_products);
            $insert->getStatement();
            $insert->execute();
        }
        if ($groups_features) {
            $insert = $this->query_factory
                ->newInsert()->ignore()
                ->into('__vitalisoft__products_groups__features')
                ->addRows($groups_features);
            $insert->getStatement();
            $insert->execute();
        }
        if ($groups) {
            $insert = $this->query_factory
                ->newInsert()->ignore()
                ->into('__vitalisoft__products_groups__groups')
                ->addRows($groups);
            $insert->getStatement();
            $insert->execute();
        }
    }
    
    public function deleteProducts($success, $ids)
    {
        if ($success && $ids) $this->query_factory
            ->newDelete()
            ->from('__vitalisoft__products_groups__products')
            ->where('product_id IN (?)', $ids)
            ->execute();
    }
    
    public function deleteFeatures($success, $ids)
    {
        if ($success && $ids) $this->query_factory
            ->newDelete()
            ->from('__vitalisoft__products_groups__features')
            ->where('feature_id IN (?)', $ids)
            ->execute();
    }
    
    public function singleQuotesAttrJson($data): string
    {
        return strtr(
            json_encode($data, JSON_UNESCAPED_UNICODE),
            ["'" => '&apos;', '&' => '&amp;']
        );
    }
    
    public function safeExecute(callable $function): ?string
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            // error was suppressed with the @-operator
            if (0 === error_reporting()) {
                return false;
            }
            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
        try {
            $function();
            restore_error_handler();
            return null;
        } catch (\Throwable $error) {
            restore_error_handler();
            $message = sprintf(
                "%s(%d): %s\n%s",
                $error->getFile(),
                $error->getLine(),
                $error->getMessage(),
                $error->getTraceAsString()
            );
            $message = str_replace([
                dirname(__DIR__) . DIRECTORY_SEPARATOR, // module path
                dirname(__NAMESPACE__) . '\\', // module namespace
                dirname(__DIR__, 5), // document root
            ], '', $message);
            $this->logger->error($message);
            return $message;
        }
    }
    
    private function getProductCategories($product_id): array
    {
        return $this->query_factory->newSelect()
            ->cols(['category_id'])
            ->from('__products_categories')
            ->where('product_id = ?', $product_id)
            ->results('category_id');
    }
    
    private function getCategoriesFeatures($categories_ids): array
    {
        $categories_features_ids = $features_ids = array();
        foreach (array_unique($categories_ids) as $category_id) {
            $features_ids = array_merge(
                $features_ids, $categories_features_ids[$category_id]
                = $this->features_entity->col('id')
                ->find(['category_id' => $category_id])
            );
        }
        if ($features_ids) {
            $features_names = $this->features_entity->cols(['id', 'name'])
                ->getSelect(['id' => $features_ids])
                ->results('name', 'id');
        }
        return array($categories_features_ids, $features_names ?? []);
    }
}