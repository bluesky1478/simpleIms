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
namespace Controller\Admin\Board;

use Component\Board\BoardUtil;
use Component\Board\Board;
use Component\Board\ArticleListAdmin;
use Component\Board\BoardAdmin;
use Component\Goods\GoodsCate;
use Component\Page\Page;
use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertOnlyException;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlCodeMap;

class ArticleListController extends \Bundle\Controller\Admin\Board\ArticleListController
{
    /**
     * Description
     */
    public function index()
    {
        $bdId = \Request::get()->get('bdId');
        if(empty($bdId)){
            \Request::get()->set('bdId','qa');
        }

        $req = Request::get()->toArray();
        if ($req['searchPeriod'] != -1 && (!$req['rangDate'][0] && !$req['rangDate'][1])) {
            $arr[] = date('Y-m-d', strtotime('-364 day'));
            $arr[] = $req['rangDate'][1] = date('Y-m-d');
            Request::get()->set('rangDate', $arr);
        }

        if( empty($req['pageNum']) ){
            Request::get()->set('pageNum', 20);
        }

        if( empty($req['sort']) ){
            Request::get()->set('sort', 'b.regDt desc');
        }

        parent::index();
        if( !empty(SlCodeMap::CLAIM_BOARD[\Request::get()->get('bdId')]) ){
            $this->getView()->setPageName('board/article_list_qa.php');
        }else{
            $this->getView()->setPageName('board/article_list.php');
        }

        $availBoard = [
            'qa',
            'goodsqa',
            'workshare',
            'reqGoods',
            'sales',
            'design',
            'qcboard',
            'workReport',
        ];

        $boards = $this->getData('boards');
        $refineBoards = [];
        foreach($boards as $board){
            if(in_array($board['bdId'], $availBoard)){
                $refineBoards[] = $board;
            }
        }

        $this->setData('boards', $refineBoards);
    }
}
