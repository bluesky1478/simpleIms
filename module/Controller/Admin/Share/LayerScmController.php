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

namespace Controller\Admin\Share;

use Exception;
use Globals;
use Request;

/**
 * [관리자 모드] 레이어 상품 등록 페이지
 * 설명 : 공급사 정보가 필요한 페이지에서 선택할 공급사의 리스트
 *
 * @package Bundle\Controller\Admin\Share
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>
 */
class LayerScmController extends \Bundle\Controller\Admin\Share\LayerScmController
{
    /**
     * {@inheritdoc}
     *
     */
    public function index()
    {
        Request::get()->set('pageNum', '500');
        Request::get()->set('sort', ['name'=>'companyNm', 'mode'=>'asc']);
        parent::index();
    }
}
