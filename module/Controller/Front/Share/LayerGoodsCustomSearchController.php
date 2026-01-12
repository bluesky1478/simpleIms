<?php

namespace Controller\Front\Share;

use Component\Goods\Goods;
use Component\Member\Util\MemberUtil;
use Component\Storage\Storage;
use SlComponent\Util\SitelabLogger;

class LayerGoodsCustomSearchController extends \Controller\Front\Controller{
    public function index(){
        //parent::index();
        //$pageNum = 10, $displayOrder = 'g.regDt asc', $imageType = 'list'
        //SitelabLogger::logger($this->getData('list'));
        $getValue = \Request::get()->toArray();
        $goods = new Goods();
        $goodsData = $goods->getGoodsSearchList(9, 'g.regDt asc', 'add2');
        $page = \App::load('\\Component\\Page\\Page'); // 페이지 재설정
        $page->setUrl(rawurldecode(\Request::server()->get('QUERY_STRING')));
        $pagination = $page->getPage('goAjaxPaging(\'PAGELINK\')');
        $this->setData('list', $goodsData['listData']);
        $this->setData('total', $page->getTotal());
        $this->setData('pagination', $pagination);
        $this->setData('soldoutDisplay', gd_policy('soldout.pc'));

        $memNo = \Session::get('member.memNo');
        $this->setData('memberScmNo', MemberUtil::getMemberScmNo($memNo));


    }
}
