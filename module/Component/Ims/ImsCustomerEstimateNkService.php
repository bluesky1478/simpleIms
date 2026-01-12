<?php
namespace Component\Ims;

use App;


class ImsCustomerEstimateNkService {
    private $dpData;

    public function __construct(){
        $this->dpData = [
            ['type' => 'c', 'col' => 5, 'class' => '', 'name' => 'estimateDt', 'title' => '견적일', ],
            ['type' => 'c', 'col' => 4, 'class' => '', 'name' => 'estimateTypeHan', 'title' => '견적타입', ],
            ['type' => 'pop_detail_customer', 'col' => 8, 'class' => '', 'name' => 'customerName', 'title' => '고객명', ],
            ['type' => 'pop_detail_project', 'col' => 4, 'class' => 'text-danger', 'name' => 'projectSno', 'title' => '프로젝트', ],
            ['type' => 'pop_detail_estimate', 'col' => 0, 'class' => '', 'name' => 'subject', 'title' => '제목', ],
            ['type' => 'i', 'col' => 7, 'class' => 'ta-r', 'name' => 'supply', 'title' => '공급가', ],
            ['type' => 'i', 'col' => 6, 'class' => 'ta-r', 'name' => 'tax', 'title' => '세액', ],
            ['type' => 'i', 'col' => 8, 'class' => 'ta-r', 'name' => 'sum_amount', 'title' => '총금액', ],
            ['type' => 'html', 'col' => 10, 'class' => 'ta-l pdl5', 'name' => 'estimateMemo', 'title' => '고객메모', ],
            ['type' => 'html', 'col' => 10, 'class' => 'ta-l pdl5', 'name' => 'innoverMemo', 'title' => '이노버(내부)메모', ],
        ];
    }

    public function getDisplay(){
        return $this->dpData;
    }
}