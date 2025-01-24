<?php


namespace Okay\Modules\SimplaMarket\Sets\Requests;


use Okay\Core\Request;

class SetsRequest
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function postSet()
    {
        $set                  = new \stdClass();
        $set->id              = $this->request->post('id', 'integer');
        $set->name            = $this->request->post('name');
        $set->visible         = $this->request->post('visible', 'integer');
        $set->annotation      = $this->request->post('annotation');
        $set->include         = $this->request->post('include', 'integer');
        $set->show_type       = $this->request->post('show_type');
        $set->date_to         = $this->formatTimeStamp($this->request->post('date_to'));
        $set->date_from       = $this->formatTimeStamp($this->request->post('date_from'));
        $set->items           = $this->postSetItems();
        $set->display_objects = $this->request->post('display_objects');
        return $set;
    }

    private function formatTimeStamp($datetime)
    {
        if (empty($datetime)) {
            return null;
        }

        list($date, $time) = explode(' ', $datetime);

        $preparedDate = date('Y-m-d', strtotime($date));
        $preparedTime = $time.":00";

        return $preparedDate . " " . $preparedTime;
    }

    private function postSetItems()
    {
        $requestSetItems = (array) $this->request->post('set_items');

        $setItems = [];
        $position = 0;
        foreach($requestSetItems['product_id'] as $key => $productId) {
            $setItem                = new \stdClass();
            $setItem->id            = $requestSetItems['id'][$key];
            $setItem->product_id    = $productId;
            $setItem->variant_id    = $requestSetItems['variant_id'][$key];
            $setItem->amount        = $requestSetItems['amount'][$key];
            $setItem->discount      = $requestSetItems['discount'][$key];
            $setItem->discount_type = $requestSetItems['discount_type'][$key];
            $setItem->position      = $position;

            $setItems[] = $setItem;
            $position++;
        }

        return $setItems;
    }
}