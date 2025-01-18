<?php


namespace Okay\Modules\SimplaMarket\PrivatPayPart\Extenders;


use Okay\Core\BackendTranslations;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Request;
use Okay\Entities\ProductsEntity;
use Okay\Modules\SimplaMarket\PrivatPayPart\Init\Init;

class BackendExtender implements ExtensionInterface
{
    
    private $request;
    private $backendTranslations;

    /* EntityFactory $entityFactory */
    private $entityFactory;

    /* ProductsEntity $productsEntity */
    private $productsEntity;

    public function __construct(
        Request             $request,
        BackendTranslations $backendTranslations,
        EntityFactory       $entityFactory
    )
    {
        $this->request              = $request;
        $this->backendTranslations  = $backendTranslations;
        $this->entityFactory        = $entityFactory;
        $this->productsEntity       = $entityFactory->get(ProductsEntity::class);
    }
    
    public function postProduct($product)
    {
        $product->to_privat_pay_part = $this->request->post('to_privat_pay_part', 'int');
        $product->privat_max_pay_ii  = $this->request->post('privat_max_pay_ii', 'int');
        $product->privat_max_pay_pp  = $this->request->post('privat_max_pay_pp', 'int');
        $product->{Init::INIDIVID_PRIVAT_VALUE_CHECKBOX} = $this->request->post(Init::INIDIVID_PRIVAT_VALUE_CHECKBOX, 'int', 0);
        
        if (!empty($product->privat_max_pay_ii)) {
            $product->privat_max_pay_ii = min(24, $product->privat_max_pay_ii);
        } else {
            $product->privat_max_pay_ii = null;
        }
        
        if (!empty($product->privat_max_pay_pp)) {
            $product->privat_max_pay_pp = min(6, $product->privat_max_pay_pp);
        } else {
            $product->privat_max_pay_pp = null;
        }
        
        return $product;
    }

    //  принимаем кол-во месяцев для категории
    public function postCategory($category)
    {
        if (!empty($category)) {
            $category->{Init::MAX_PAY_II_FIELD} = $this->request->post(Init::MAX_PAY_II_FIELD, 'integer');
            $category->{Init::MAX_PAY_PP_FIELD} = $this->request->post(Init::MAX_PAY_PP_FIELD, 'integer');
        }

        return $category;
    }

    //  если кол-во месяцев установлено, тогда проставляем из дочерним товарам без признака уникальности
    public function uploadCategoryImage($null, $category, $image)
    {
        if (!empty($category) && !empty($category->id)) {
            //  находим все товары категории без признака индивидуального значения для Моно
            $findCategoryProducts = $this->productsEntity->mappedBy('id')->cols(['id', 'individual_privat_value'])->find([
                'category_id' => $category->id,
                'individual_privat_value' => 0,
            ]);

            //  если список товаров есть то проходимся по им всем и устанавливаем значения
            if (!empty($findCategoryProducts)) {
                foreach ($findCategoryProducts as $product) {
                    if (empty($product->individual_privat_value)) {
                        $data = [];
                        if (!empty($category->{Init::MAX_PAY_II_FIELD})) {
                            $data[Init::MAX_PAY_II_FIELD] = $category->{Init::MAX_PAY_II_FIELD};
                        }
                        if (!empty($category->{Init::MAX_PAY_PP_FIELD})) {
                            $data[Init::MAX_PAY_PP_FIELD] = $category->{Init::MAX_PAY_PP_FIELD};
                        }

                        if (!empty($data)) {
                            $this->productsEntity->update($product->id, $data);
                        }
                    }
                }
            }
        }

        return null;
    }

    //  модифицируем фильт для сортировки товаров по признаку галочки индивидуального кол-во месяцев Privat
    public function buildFilter($filter)
    {
        if (!empty($individualPrivatValue = $this->request->get('filter')) && $individualPrivatValue == 'individual_privat_value') {  //  фильтруем по столбцу individual_privat_value и показываем только с индивидуальными данным
            $filter['individual_privat_value'] = 1;
        } elseif (!empty($noIndividualPrivatValue = $this->request->get('filter')) && $individualPrivatValue == 'no_individual_privat_value') {  //  фильтруем по столбцу individual_privat_value и показываем все не индивидуальные данные
            $filter['individual_privat_value'] = 0;
        }

        return $filter;
    }
    
    public function getProduct($product)
    {
        // todo в 3.8.0 можно будет заюзать
        if (empty($product->id)) {
            // Сразу разрешим в приват
            //$product->to_privat_pay_part = 1;
        }
        
        return $product;
    }

    public function parseProductData($product, $itemFromCsv)
    {
        // Признак можно ли покупать в кредит
        if (isset($itemFromCsv[Init::TO_PAY_PART_FIELD])) {
            $product[Init::TO_PAY_PART_FIELD] = trim($itemFromCsv[Init::TO_PAY_PART_FIELD]);
        }
        if (isset($itemFromCsv[$this->backendTranslations->getTranslation('privat_to_pay_part')])) {
            $product[Init::TO_PAY_PART_FIELD] = trim($itemFromCsv[$this->backendTranslations->getTranslation('privat_to_pay_part')]);
        }

        // Максимум месяцев по мгновенной рассрочке ПриватБанка
        if (isset($itemFromCsv[Init::MAX_PAY_II_FIELD])) {
            $product[Init::MAX_PAY_II_FIELD] = trim($itemFromCsv[Init::MAX_PAY_II_FIELD]);
        }
        if (isset($itemFromCsv[$this->backendTranslations->getTranslation('privat_product_max_pay_num_ii')])) {
            $product[Init::MAX_PAY_II_FIELD] = trim($itemFromCsv[$this->backendTranslations->getTranslation('privat_product_max_pay_num_ii')]);
        }
        
        // Максимум месяцев по оплате частями ПриватБанка
        if (isset($itemFromCsv[Init::MAX_PAY_PP_FIELD])) {
            $product[Init::MAX_PAY_PP_FIELD] = trim($itemFromCsv[Init::MAX_PAY_PP_FIELD]);
        }
        if (isset($itemFromCsv[$this->backendTranslations->getTranslation('privat_product_max_pay_num_pp')])) {
            $product[Init::MAX_PAY_PP_FIELD] = trim($itemFromCsv[$this->backendTranslations->getTranslation('privat_product_max_pay_num_pp')]);
        }
        return $product;
    }

    public function extendExportColumnsNames($product)
    {
        $product[Init::TO_PAY_PART_FIELD] = $this->backendTranslations->getTranslation('privat_to_pay_part');
        $product[Init::MAX_PAY_II_FIELD] = $this->backendTranslations->getTranslation('privat_product_max_pay_num_ii');
        $product[Init::MAX_PAY_PP_FIELD] = $this->backendTranslations->getTranslation('privat_product_max_pay_num_pp');
        //$product[Init::TO_PAY_PART_FIELD] = Init::TO_PAY_PART_FIELD;
        return $product;
    }

    public function getModulesColumnsNames($modulesColumnsNames)
    {
        $modulesColumnsNames[] = $this->backendTranslations->getTranslation('privat_to_pay_part');
        $modulesColumnsNames[] = $this->backendTranslations->getTranslation('privat_product_max_pay_num_ii');
        $modulesColumnsNames[] = $this->backendTranslations->getTranslation('privat_product_max_pay_num_pp');

        $modulesColumnsNames[] = Init::TO_PAY_PART_FIELD;
        $modulesColumnsNames[] = Init::MAX_PAY_II_FIELD;
        $modulesColumnsNames[] = Init::MAX_PAY_PP_FIELD;

        return $modulesColumnsNames;
    }
    
}