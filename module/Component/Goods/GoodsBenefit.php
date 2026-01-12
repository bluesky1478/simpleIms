<?php
namespace Component\Goods;

use App;
use Component\Database\DBTableField;
use LogHandler;
use Request;
use Exception;

/**
 * Class 상품 혜택 관리
 * @package Bundle\Component\Goods
 * @author  cjb3333@godo.co.kr
 */

class GoodsBenefit extends \Bundle\Component\Goods\GoodsBenefit{
    /*public function setBenefitOrderGoodsData($data, $dataKind ='discount'){

        $goodsBenefitData = parent::setBenefitOrderGoodsData($data, $dataKind);
        gd_debug("Benefit");
        gd_debug($goodsBenefitData);

        return $goodsBenefitData;
    }*/


}
