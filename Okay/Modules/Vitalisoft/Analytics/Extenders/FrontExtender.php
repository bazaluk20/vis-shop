<?php

namespace Okay\Modules\Vitalisoft\Analytics\Extenders;

use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Request;
use Okay\Core\Router;
use Okay\Core\Settings;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\PurchasesEntity;
use Okay\Entities\UsersEntity;
use Okay\Modules\Vitalisoft\Analytics\Helpers\AnalyticsHelper;
use Okay\Modules\Vitalisoft\ParametersVariants\Entities\ParametersVariantsEntity;

class FrontExtender implements ExtensionInterface
{

    private $entityFactory;
    private $design;
    private $settings;
    private $request;
    private $analyticsHelper;
    private $clientId;

    public function __construct(
        EntityFactory   $entityFactory,
        Design          $design,
        Settings        $settings,
        Request         $request,
        AnalyticsHelper $analyticsHelper
    )
    {
        $this->entityFactory = $entityFactory;
        $this->design = $design;
        $this->settings = $settings;
        $this->request = $request;
        $this->analyticsHelper = $analyticsHelper;
        $this->clientId = substr(filter_input(INPUT_COOKIE, '_ga'), 6);
        $this->route_name = Router::getCurrentRouteName();
        $this->main_currency = $this->entityFactory->get(CurrenciesEntity::class)->getMainCurrency()->code;
    }

    public function addProductList($products, $filter = [])
    {
        if (empty($products))
            return;
        $filterLists = [
            'featured_products' => ($this->route_name =='main') ? 'Mainpage Featured' : 'Featured',
            'new_products' => ($this->route_name =='main') ? 'Mainpage New' : 'New',
            'discounted_products' => ($this->route_name =='main') ? 'Mainpage Discounted' : 'Discounted',
        ];
        if (isset($filter['var']) && isset($filterLists[$filter['var']])) {
            $list = $filterLists[$filter['var']];
        } else if (isset($filter['brand_id']) && $this->route_name == 'brand') {
            $list = 'Brand';
        } else if (isset($filter['category_id']) && $this->route_name == 'category') {
//            if($this->design->getVar('is_all_pages')) $page = ' ALL';
//            else $page = ' page-'.$filter['page'];
            $list = ucwords(str_replace('/', ' / ', str_replace('-', ' ', $this->design->getVar('category')->path_url)));
        } else if (isset($filter['keyword']) && $filter['keyword'] !=='') {
            $list = 'Search Results';
        } else if (isset($filter['other_filter'])) {
            if (in_array('featured', $filter['other_filter']) && !in_array('discounted', $filter['other_filter']))
                $list = 'Bestsellers';
            else if (!in_array('featured', $filter['other_filter']) && in_array('discounted', $filter['other_filter']))
                $list = 'Discounted';
            else return;
        } else if (isset($filter['discounted']) && $filter['discounted']){
            $list = 'Discounted';
        } else if (isset($filter['featured']) && $filter['featured']){
            $list = 'Bestsellers';
        } else if ((null !== $this->design->getVar('page') && $this->route_name == 'products') || ($this->route_name =='search' && !isset($filter['keyword']))) {
            $list = 'All Products';
        } else {
            return;
        }
        $this->extendProductsJsVar($products, $list);
    }

    public function addBrowsedProductsList($products)
    {
        if (!empty($products) && in_array($this->route_name, ['user', 'user_favorites', 'user_browsed']))
            $this->extendProductsJsVar($products, 'User Browsed Products');
    }

    public function addComparisonProductsList($comparison)
    {
        if (!empty($comparison->products) && $this->route_name == 'comparison')
            $this->extendProductsJsVar($comparison->products, 'Comparison');
    }

    public function addRelatedProductsList($products)
    {
        if (!empty($products) && $this->route_name == 'product')
            $this->extendProductsJsVar($products, 'Product Related');
        if (!empty($products) && $this->route_name == 'post')
            $this->extendProductsJsVar($products, 'Post Related');
    }

    public function addWishlistProductsList($wishlist)
    {
        if (!empty($wishlist->products) && $this->route_name == 'wishlist') {
            $this->extendProductsJsVar($wishlist->products, 'Wishlist');
        } else if (!empty($wishlist->products) && in_array($this->route_name, ['user', 'user_favorites', 'user_browsed'])) {
            $this->extendProductsJsVar($wishlist->products, 'User Wishlist');
        }
    }

    public function getAjaxFilterData($result)
    {
        $result->products = $_SESSION['common_js']['vars']['products'];
        return $result;
    }

    public function addProduct($products, $product)
    {
        if ($product->id) {
            $this->extendProductsJsVar([$product->id => $product], '');
        }
        /* code for ParameterVariants */
        if (!empty($product->variant->parameters)) {
            $parameters_features = $this->design->getVar('parameters_features');
            $feature_values = $this->design->getVar('parameters_features_values');
            $parametersVariantsEntity = $this->entityFactory->get(ParametersVariantsEntity::class);
            foreach ($parametersVariantsEntity->find(['product_id' => $product->id]) as $param) {
                if (!$param->is_available) continue;
                $combination_name = '';
                if ($product->variants[$param->variant_id]->name)
                    $combination_name = $product->variants[$param->variant_id]->name . ', ';
                foreach (explode('_', $param->parameter_key) as $key) {
                    $s[] = $parameters_features[$feature_values[$key]->feature_id]->name . ": " . $feature_values[$key]->value;
                }
                $combination_name .= implode(', ', $s);
                $s = [];
                $fpv[$param->variant_id . '_' . $param->parameter_key] = [
                    'name' => $combination_name,
                    'price' => floatval($param->price),
                    'compare_price' => floatval($param->compare_price),
                    'stock' => $param->stock,
                    'rate_from' => $param->rate_from,
                    'rate_to' => $param->rate_to
                ];
            }
            if (!isset($fpv)) {
                foreach ($product->variants as $variant) {
                    array_walk_recursive($variant->parameters, function ($value, $key) use ($variant, &$keys) {
                        if ($key == 'parameter_key')
                            $keys[] = $variant->id . '_' . $value;
                    });
                }
                foreach ($keys as $key) {
                    $combination_name = '';
                    if ($product->variants[(int)$key]->name)
                        $combination_name = $product->variants[(int)$key]->name . ', ';
                    foreach (explode('_', $key) as $i => $p) {
                        if ($i > 0)
                            $s[] = $parameters_features[$feature_values[$p]->feature_id]->name . ": " . $feature_values[$p]->value;
                    }
                    $combination_name .= implode(', ', $s);
                    $s = [];
                    $fpv[$key] = [
                        'name' => $combination_name,
                        'price' => floatval($product->variants[(int)$key]->price),
                        'compare_price' => floatval($product->variants[(int)$key]->compare_price),
                        'stock' => $product->variants[(int)$key]->stock,
                        'rate_from' => $product->variants[(int)$key]->rate_from,
                        'rate_to' => $product->variants[(int)$key]->rate_to
                    ];
                }
            }
            $this->design->assignJsVar('fpv', $fpv);
        }
    }

    private function extendProductsJsVar($products, $list)
    {
        $brandsEntity = $this->entityFactory->get(BrandsEntity::class);
        $categoriesEntity = $this->entityFactory->get(CategoriesEntity::class);
        if (!in_array($this->route_name, ['user', 'user_favorites', 'user_browsed'])) {
            $browsedProductsIds = !empty($_COOKIE['browsed_products']) ? array_reverse(explode(',', $_COOKIE['browsed_products'])) : [];
            if (count($browsedProductsIds) >= 1) {
                $catalogBrowsedProductsIds = array_slice($browsedProductsIds, 0, 6);
                $userBrowsedProductsIds = array_slice($browsedProductsIds, 0, 16);
                if ($catalogBrowsedProductsIds == array_keys($products) || $userBrowsedProductsIds == array_keys($products)) {
                    return;
                }
            }
        }

        foreach (array_values($products) as $i => $product) {
            $this->products[] = (object)[
                'item_list_name' => $list,
                'index' => $i + 1,
                'item_name' => $product->name,
                'product_id' => $product->variant->product_id,
                'item_id' => $product->variant->id,
                'sku' => $product->variant->sku,
                'item_brand' => $brandsEntity->col('name')->findOne(['id' => $product->brand_id]),
                'item_category' => $categoriesEntity->findOne(['id' => $product->main_category_id])->name,
                'currency' => $this->main_currency,
                'price' => floatval($product->variant->price),
                'compare_price' => floatval($product->variant->compare_price),
                'href' => Router::generateUrl('product', ['url' => $product->url]),
                'variants' => $product->variants,
            ];
        }
        $this->design->assignJsVar('products', $this->products);
    }

    public function setPurchases()
    {
        $this->design->assignJsVar('main_currency', $this->main_currency);
        $brandsEntity = $this->entityFactory->get(BrandsEntity::class);
        $categoriesEntity = $this->entityFactory->get(CategoriesEntity::class);
        $purchases = [];
        $cart = $this->design->getVar('cart');
        if (null !== $cart_purchases = $cart->purchases) {
            foreach ($cart_purchases as $key => $p) {
                $purchases[$p->variant_id] = [
                    'item_name' => $p->product_name,
                    'item_id' => $p->variant_id,
                    'product_id' => $p->product_id,
                    'item_category' => isset($p->product->main_category_id) ? $categoriesEntity->findOne(['id' => $p->product->main_category_id])->name : '',
                    'item_brand' => isset($p->product->brand_id) ? $brandsEntity->col('name')->findOne(['id' => $p->product->brand_id]) : '',
                    'price' => floatval($p->variant->price),
                    'currency' => $this->main_currency,
                    'undiscounted_price' => isset($p->variant->undiscounted_price) ? floatval($p->variant->undiscounted_price) : '',
                    'item_variant' => $p->variant_name,
                    'quantity' => (int)$p->amount,
                    'sku' => isset($p->sku) ? $p->sku : ''
                ];
            }
        }
        if ($cart_purchases_with_parameters = (isset($this->design->getVar('cart')->purchases_with_parameters)) ? $this->design->getVar('cart')->purchases_with_parameters : null) {
            foreach ($cart_purchases_with_parameters as $key => $p) {
                $purchases[$p->variant_id] = [
                    'item_name' => $p->product_name,
                    'item_id' => $p->variant_id,
                    'product_id' => $p->product_id,
                    'item_category' => isset($p->product->main_category_id) ? $categoriesEntity->findOne(['id' => $p->product->main_category_id])->name : '',
                    'item_brand' => isset($p->product->brand_id) ? $brandsEntity->col('name')->findOne(['id' => $p->product->brand_id]) : '',
                    'price' => floatval($p->variant->price),
                    'undiscounted_price' => isset($p->variant->undiscounted_price) ? floatval($p->variant->undiscounted_price) : '',
                    'item_variant' => $p->variant_name,
                    'quantity' => (int)$p->amount,
                    'sku' => isset($p->sku) ? $p->sku : '',
                    'parameter_key' => $p->parameter_key
                ];
            }
        }
        $this->design->assignJsVar('cart_total', $cart->total_price);
        $this->design->assignJsVar('purchases', (object)$purchases);
    }

    public function setOrderPurchases($order_purchases)
    {
        $this->design->assignJsVar('main_currency', $this->main_currency);
        if ($order_purchases) {
            $brandsEntity = $this->entityFactory->get(BrandsEntity::class);
            $categoriesEntity = $this->entityFactory->get(CategoriesEntity::class);
            foreach ($order_purchases as $p) {
                if (!empty($p->parameter_key)) $p->variant_id .= '_' . $p->parameter_key;
                $purchases[$p->variant_id] = [
                    'item_name' => $p->product_name,
                    'item_id' => $p->variant_id,
                    'item_category' => isset($p->product->main_category_id) ? $categoriesEntity->findOne(['id' => $p->product->main_category_id])->name : '',
                    'item_brand' => isset($p->product->brand_id) ? $brandsEntity->col('name')->findOne(['id' => $p->product->brand_id]) : '',
                    'price' => floatval($p->price),
                    'undiscounted_price' => floatval($p->undiscounted_price),
                    'item_variant' => $p->variant_name,
                    'quantity' => (int)$p->amount
                ];
            }
            $this->design->assignJsVar('order_purchases', (object)$purchases);
        }
    }

    public function set_cid($userId)
    {
        $uri = explode('/', Request::getRequestUri());
        $event = "login";
        if (array_search('register', $uri))
            $event = 'sign_up';
        if (!$this->clientId || !$event)
            return;
        $usersEntity = $this->entityFactory->get(UsersEntity::class);
        $usersEntity->update($userId, ['cid' => $this->clientId]);
        $payload = [
            "client_id" => $this->clientId,
            "user_id" => (string)$userId,
            "user_properties" => [
                "user_id_dimension" => [
                    "value" => (string)$userId
                ]
            ],
            "events" => [
                [
                    "name" => $event
                ]
            ]
        ];
        $this->analyticsHelper->mp_collect($payload);
    }

    public function post_cid($order)
    {
        if ($this->clientId)
            $order->cid = $this->clientId;
        return $order;
    }

    public function sendAnalyticsPurchase($null, array $ids, $state)
    {
        $ordersEntity = $this->entityFactory->get(OrdersEntity::class);
        $purchasesEntity = $this->entityFactory->get(PurchasesEntity::class);
        $brandsEntity = $this->entityFactory->get(BrandsEntity::class);
        $categoriesEntity = $this->entityFactory->get(CategoriesEntity::class);
        $productsEntity = $this->entityFactory->get(ProductsEntity::class);

        $id = $ids[0];
        $order = $ordersEntity->findOne(['id' => $id]);
        if (!$state || $order->analytics_sent)
            return;

        $purchases = $purchasesEntity->find(['order_id' => $id]);
        $currency = $this->main_currency;

        $items = array();
        foreach ($purchases as $purchase) {
            $product = $productsEntity->findOne(['id' => $purchase->product_id]);
            $data = [
                'item_id' => $purchase->variant_id,
                'item_name' => $purchase->product_name,
                'quantity' => (int)$purchase->amount,
                'price' => floatval($purchase->price)
            ];
            if (!empty($product->brand_id))
                $data['item_brand'] = $brandsEntity->col('name')->findOne(['id' => $product->brand_id]);
            if (!empty($product->main_category_id))
                $data['item_category'] = $categoriesEntity->col('name')->findOne(['id' => $product->main_category_id]);
            if (!empty($purchase->variant_name))
                $data['item_variant'] = $purchase->variant_name;
            $items[] = $data;
        }

        $payload = [
            'client_id' => $order->cid,
            'user_id' => $order->user_id,
            'events' => [
                [
                    'name' => 'purchase',
                    'params' => [
                        'items' => $items,
                        'currency' => $currency,
                        'transaction_id' => $order->id,
                        'shipping' => floatval($order->delivery_price),
                        'value' => floatval($order->total_price),
                        'affiliation' => 'Online store',
                        // 'coupon' => '',
                        // 'tax' => 1,
                    ],
                ],
            ],
        ];
        $this->analyticsHelper->mp_collect($payload);
        $ordersEntity->update($id, ['analytics_sent' => 1]);
    }

}