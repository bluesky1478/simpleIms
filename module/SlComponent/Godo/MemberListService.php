<?php
namespace SlComponent\Godo;

use Component\Member\Util\MemberUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;

/**
 * 회원 리스트 (FRONT) 서비스
 * Class SlCode
 * @package SlComponent\Godo
 */
class MemberListService {
    //private $sql;
    public function __construct(){
        //$this->sql = \App::load(\SlComponent\Godo\Sql\MemberSql::class);
    }

    public function getList($param){
        if( 'bcloud1478.godomall.com' === \Request::getDefaultHost() ){
            $fileName = 'memberLimitListDev.sql';
        }else{

            if( 8 == MemberUtil::getMemberScmNo(\Session::get('member.memNo')) ){
                //TKE
                $fileName = 'memberLimitList.sql';
            }
            if( 21 == MemberUtil::getMemberScmNo(\Session::get('member.memNo')) ){
                //OEK
                $fileName = 'memberLimitListOek.sql';
            }
        }

        $filePath = './module/SlComponent/Godo/Sql/'.$fileName;
        $sql = SlCommonUtil::getFileData($filePath);

        if( !empty($param['keyword']) ){
            $sql .= " AND {$param['key']} LIKE '%{$param['keyword']}%' ";
        }

        if( 'y' === $param['buyFl'] ){
            $sql .= " and ( usedCount1 > 0 or usedCount2 > 0 )  ";
        }
        if( 'n' === $param['buyFl'] ){
            $sql .= " and ( usedCount1 = 0 and usedCount2 = 0 )  ";
        }

        $sql .= " ORDER BY a.nickNm ";

        return DBUtil2::runSelect($sql);
    }

    public function getStatus($param){
        if( 'bcloud1478.godomall.com' === \Request::getDefaultHost() ){
            $fileName = 'memberOrderStatusDev.sql';
        }else{
            $fileName = 'memberOrderStatus.sql';
        }

        $filePath = './module/SlComponent/Godo/Sql/'.$fileName;
        $sql = SlCommonUtil::getFileData($filePath);

        if( !empty($param['keyword']) ){
            $sql .= " AND {$param['key']} LIKE '%{$param['keyword']}%' ";
        }
        $listData = DBUtil2::runSelect($sql);
        //gd_debug( $sql );

        $listMap = [];
        foreach($listData as $data){
            $listMap[$data['deliveryName']] = $data;
        }

        $sql = "
        select b.deliveryName, count(1) cnt 
from es_member a 
join sl_setMemberConfig b 
on a.memNo = b.memNo 
where b.memberType <> 2 
and ex1 = 'TKE(티센크루프)'
and a.memNo NOT IN (  1 , 4, 5469, 4991  )
group by b.deliveryName
        ";
        $deliveryMemberList = DBUtil2::runSelect($sql);
        foreach($deliveryMemberList as $key => $each){
            $each['teeCnt'] = $listMap[$each['deliveryName']]['teeCnt'];
            $each['pantsCnt'] = $listMap[$each['deliveryName']]['pantsCnt'];
            $deliveryMember[$key] = $each;
        }

        foreach($listData as $key => $each){
            $each['cnt'] = $deliveryMember[$each['deliveryName']];
            $listData[$key] = $each;
        }

        $result['list'] = $deliveryMember;

        $sql = "select count(1) cnt, 
                    sum( if ( d.goodsNo = '1000000328' OR d.goodsNo = '1000000330' , 1, 0)) as allCnt,		 
                    sum( if ( d.goodsNo = '1000000328', 1, 0)) as teeCnt,		 
                    sum( if ( d.goodsNo = '1000000330', 1, 0 )) as pantsCnt
            from es_member a 
            join sl_setMemberConfig b on a.memNo = b.memNo 
            join es_order c on a.memNo = c.memNo 
            join es_orderGoods d on c.orderNo = d.orderNo 
            where b.memberType <> 2 
            and ex1 = 'TKE(티센크루프)'
            and a.memNo NOT IN (  1 , 4, 5469, 4991  )
            and d.orderStatus = 'p3'
            and d.goodsNo in ('1000000328', '1000000330')";
        $totalRslt = DBUtil2::runSelect($sql);
        $result['total'] = $totalRslt[0];

        return $result;
    }



}
