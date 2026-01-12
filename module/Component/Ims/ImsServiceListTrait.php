<?php
namespace Component\Ims;

use App;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Sms\Code;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlSmsUtil;

/**
 * IMS 상품관련 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
trait ImsServiceListTrait {

    /**
     * 테이블명을 별칭으로 받아올 때는 치환
     * @param $params
     * @param $type
     * @return mixed|string
     */
    /*public static function tableNameMap($alias){
        $map = ['customerIssue'=>'imsCustomerIssue'];
        return empty($map[$alias])?$alias:$map[$alias];
    }*/

    //일반적 리스트 및 데이터 가져오기
    //getEach + TableName ... 개별 데이터
    //getList + TableName ... 페이징 리스트
    //decoration + TableName ... 리스트 꾸미기
    public function getEachData($params, $type){
        $tableName = ucfirst($type);
        if(empty($params['sno'])){
            $data = DBTableField::getTableBlankData('table'.$tableName); //초기 데이터.
            $data = DBTableField::refineGetData('sl_'.$type, $data);
        }else{
            $fncName = 'getList'.$tableName;
            $data = $this->$fncName([
                'condition' => ['sno' => $params['sno'] ]
            ])['list'][0];
        }
        return $data;
    }
    public function getListCommon($params, $type, SearchVo $searchVo, $defaultListCount=100){
        $tableName = ucfirst($type);
        //(요청, 처리중 , 처리불가 , 반려 ===> 진행중 ) 와 완료 ( limit 50 )
        $this->setCommonCondition($params['condition'], $searchVo); //Request쪽에 있음.
        $this->setListSort($params['condition']['sort'], $searchVo);
        $searchData = [
            'page' => gd_isset($params['condition']['page'], 1),
            'pageNum' => gd_isset($params['condition']['pageNum'], $defaultListCount),
        ];
        $fncName = 'getTable'.$tableName; //ex : getTableImsCommentTable(), getTableImsCustomerIssue
        $allData = DBUtil2::getComplexListWithPaging($this->sql->$fncName(), $searchVo, $searchData, false, false);

        $mixData = $type;
        return [
            'pageEx' => $allData['pageData']->getPage('#'),
            'page' => $allData['pageData'],
            'list' => SlCommonUtil::setEachData($allData['listData'], $this, 'decoration'.$tableName, $mixData) // decorationImsComment
        ];
    }
    public function defaultDecoration($each, $key, $mixData){
        return DBTableField::refineGetData('sl_'.$mixData, $each, 'decode');
    }

    //--------

    /**
     * 코멘트 개별 가져오기
     * @param $params
     * @return string
     */
    public function getImsComment($params){
        /*if($rslt['customerName']){
            $rslt['customerName'] = DBUtil2::getOne(ImsDBName::CUSTOMER, 'sno', $params['customerSno'])['customerName'];
        }*/
        return $this->getEachData($params,str_replace('sl_','',ImsDBName::PROJECT_COMMENT));
    }

    /**
     * 코멘트 리스트 가져오기 ('imsComment')
     * @param $params
     * @return array
     */
    public function getListImsComment($params)
    {
        //$params, $type, SearchVo $searchVo, $defaultListCount=100
        return $this->getListCommon($params,str_replace('sl_','',ImsDBName::PROJECT_COMMENT), new SearchVo());
    }

    /**
     * 코멘트 개별 데이터 꾸미기
     * @param $eachData
     * @return mixed
     */
    public function decorationImsComment($eachData){
        return $eachData;
    }




    //---------

    /**
     * 고객 이슈 개별 가져오기
     * @param $params
     * @return string
     */
    public function getImsCustomerIssue($params){
        return $this->getEachData($params,str_replace('sl_','',ImsDBName::CUSTOMER_ISSUE));
    }
    /**
     * 고객 이슈 리스트 가져오기 ('imsCustomerIssue')
     * @param $params
     * @return array
     */
    public function getListImsCustomerIssue($params){
        $aResponse = $this->getListCommon($params,str_replace('sl_','',ImsDBName::CUSTOMER_ISSUE), new SearchVo());
        //각 코멘트별로 댓글갯수 가져오기 -> $aResponse['list']에 키값(cnt_reply)추가
        $aCntReplyByComment = $aCommentSnos = [];
        foreach ($aResponse['list'] as $val) $aCommentSnos[] = (int)$val['sno'];
        if (count($aCommentSnos) > 0) {
            $searchVo = new SearchVo('commentDiv=?', 'custCommentReply');
            $searchVo->setWhere("eachSno in (".implode(',',$aCommentSnos).")");
            $searchVo->setGroup('eachSno');
            $aTableInfoCntReply = [
                'a' => [
                    'data' => [ ImsDBName::PROJECT_COMMENT ],
                    'field' => [ 'eachSno, count(eachSno) as cntReply']
                ],
            ];
            $searchData = ['page' => 1, 'pageNum' => 15000,];
            $aReplyCntList = DBUtil2::getComplexListWithPaging(DBUtil2::setTableInfo($aTableInfoCntReply), $searchVo, $searchData, false, true);
            foreach ($aReplyCntList['listData'] as $val) $aCntReplyByComment[$val['eachSno']] = $val['cntReply'];
        }
        foreach ($aResponse['list'] as $key => $val) {
            $aResponse['list'][$key]['cnt_reply'] = isset($aCntReplyByComment[$val['sno']]) ? $aCntReplyByComment[$val['sno']] : 0;
        }

        return $aResponse;
    }

    /**
     * 고객 이슈 개별 데이터 꾸미기
     * @param $eachData
     * @param $key
     * @param $mixData
     * @return mixed
     */
    public function decorationImsCustomerIssue($eachData, $key, $mixData){
        $eachData['issueTypeKr'] = ImsCodeMap::ISSUE_TYPE[$eachData['issueType']];
        $eachData['issueTypeKr'] = empty($eachData['issueTypeKr'])?'미정':$eachData['issueTypeKr'];
        $eachData['inboundTypeKr'] = ImsCodeMap::INBOUND_TYPE[$eachData['inboundType']];
        $eachData['textContents'] =  SlCommonUtil::truncateText(html_entity_decode(strip_tags(str_replace('\\n','',$eachData['contents'])), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        return $this->defaultDecoration($eachData, $key, $mixData);
    }

}