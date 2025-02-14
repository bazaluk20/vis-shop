<?php


namespace Okay\Modules\SimplaMarket\ProductsAdvantages\Backend\Requests;


use Okay\Core\Request;

class BackendProductsAdvantagesRequest
{
    private $request;

    public function __construct(
        Request $request
    )
    {
        $this->request = $request;
    }

    public function filesProductAdvantagesImages()
    {
        $images = (array) $this->request->files('product_advantages_image');

        if (empty($images['name'])) {
            return [];
        }

        $advantagesImages = [];
        foreach($images['name'] as $advantageId => $imageName) {
            $advantageImage = [];
            $advantageImage['name']        = $imageName;
            $advantageImage['tmp_name']    = $images['tmp_name'][$advantageId];
            $advantagesImages[$advantageId] = $advantageImage;
        }

        return $advantagesImages;
    }

    public function postProductAdvantagesUpdates()
    {
        $advantages = [];

        $position = 0;
        foreach((array) $this->request->post('product_advantages_text') as $id => $advantageText) {
            $advantage           = new \stdClass();
            $advantage->text     = $advantageText;
            $advantage->position = $position;

            $advantages[$id] = $advantage;

            $position++;
        }

        return $advantages;
    }

    public function postProductAdvantagesImagesToDelete()
    {
        $deleteAdvantagesImages = (array) $this->request->post('product_advantages_images_to_delete');
        $preparedDeleteAdvantagesImages = [];
        foreach($deleteAdvantagesImages as $advantageId => $deleteAdvantageImage) {
            if (!empty($deleteAdvantageImage)) {
                $preparedDeleteAdvantagesImages[] = $advantageId;
            }
        }

        return $preparedDeleteAdvantagesImages;
    }

    public function postPositionsProductAdvantages()
    {
        $positions = $this->request->post('product_advantages_positions');
        return $positions;
    }

    public function postCheckProductAdvantages()
    {
        $ids = $this->request->post('product_advantages_check');
        return $ids;
    }

    public function postActionProductAdvantages()
    {
        $action = $this->request->post('product_advantages_action');
        return $action;
    }

    public function postNewProductAdvantages($productId)
    {
        $newAdvantages = $this->request->post('new_product_advantages');

        if (empty($newAdvantages)) {
            return [];
        }

        $preparedNewAdvantages = [];
        foreach($newAdvantages['text'] as $key => $text) {
            $newAdvantage = new \stdClass();
            $newAdvantage->product_id = $productId;
            $newAdvantage->text = $text;
            $preparedNewAdvantages[] = $newAdvantage;
        }

        return $preparedNewAdvantages;
    }

    public function filesNewProductAdvantagesImages()
    {
        $images = $this->request->files('new_product_advantages_images');

        if (empty($images)) {
            return [];
        }

        $newAdvantagesImages = [];
        foreach($images['name'] as $key => $imageName) {
            $newAdvantageImage = [];
            $newAdvantageImage['name']     = $imageName;
            $newAdvantageImage['tmp_name'] = $images['tmp_name'][$key];
            $newAdvantagesImages[$key]      = $newAdvantageImage;
        }

        return $newAdvantagesImages;
    }
}