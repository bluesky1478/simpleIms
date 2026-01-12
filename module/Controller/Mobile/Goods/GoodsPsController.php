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
namespace Controller\Mobile\Goods;

use Component\Board\BoardList;
use Framework\Utility\GodoUtils;
use Message;
use Request;
use Framework\Debug\Exception\AlertCloseException;
use Framework\Debug\Exception\AlertBackException;
use League\Flysystem\Exception;

/**
 * 상품 상세 페이지 처리
 *
 * @author artherot
 * @version 1.0
 * @since 1.0
 * @copyright Copyright (c), Godosoft
 */
class GoodsPsController extends \Bundle\Controller\Mobile\Goods\GoodsPsController
{
    /**
     * {@inheritdoc}
     *
     * @author Jong-tae Ahn <qnibus@godo.co.kr>
     */
    public function index()
    {
        parent::index();

        // --- 각 배열을 trim 처리
        $post = Request::post()->toArray();
        $get = Request::get()->toArray();

        switch ($post['mode']) {
            case 'get_all_category':
                try {

                    $cateDepth = gd_isset($post['cateDepth'],4);
                    $category = \App::load('\\Component\\Category\\Category');
                    $getData = $category->getCategoryCodeInfo(null, $cateDepth, false, false, 'mobile');

                    if ($getData) {
                        $getData = array_chunk($getData, 6);
                    }

                    echo json_encode($getData);
                    exit;
                } catch (Exception $e) {
                    echo json_encode(array('message' => $e->getMessage()));
                }
                break;
        }
        exit();
    }
}
