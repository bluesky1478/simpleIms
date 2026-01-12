<?php
/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */
namespace Widget\Front\Proc;

use App;
use Request;
use SlComponent\Util\SlSkinUtil;

/**
 * Class MypageQnaController
 *
 * @package Bundle\Controller\Front\Outline
 * @author  Young Eun Jung <atomyang@godo.co.kr>
 */

class CategorySideWidget extends \Bundle\Widget\Front\Proc\CategorySideWidget
{

    public function index()
    {
        parent::index();
        $otherSkin = SlSkinUtil::getOtherSkinName();
        if( 'selc' === $otherSkin ){
            $data = $this->getData('data');
            $newCateData = [];
            foreach($data[0]['children'] as $value){
                $newCateData[] = $value;
            }
            $this->setData('data', $newCateData);
        }

    }
}
