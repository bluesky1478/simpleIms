<?php
namespace Component\Ims;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Scm\ScmTkeService;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Sms\Code;

/**
 * IMS 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsStyleServiceSql {

    /**
     * 스타일 리스트
     * @param $params
     * @return array
     */
    public function getStyleTable($params){

        $sampleCntSql = "( select count(1) from sl_imsSample where styleSno = a.sno) as sampleCnt";

        $tableInfo=[
            'a' => //메인
                [
                    'data' => [ ImsDBName::PRODUCT ]
                    , 'field' => ["a.*, {$sampleCntSql}"]
                ]
            , 'b' => //가견적 확정자
            [
                'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.estimateConfirmManagerSno = b.sno' ]
                , 'field' => ['b.managerNm as estimateConfirmManagerNm']
            ]
            , 'c' => //확정가 확정자
            [
                'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.prdCostConfirmManagerSno = c.sno' ]
                , 'field' => ['c.managerNm as prdCostConfirmManagerNm']
            ]
            , 'prj' => //프로젝트정보
            [
                'data' => [ ImsDBName::PROJECT, 'JOIN', 'a.projectSno = prj.sno' ]
                , 'field' => ['prj.sno as projectNo', 'prj.projectType', 'prj.projectStatus', 'prj.packingYn', 'prj.projectSeason', 'prj.projectYear' , 'prj.msOrderDt', 'prj.use3pl', 'prj.useMall']
            ]
            , 'cust' => //고객정보
            [
                'data' => [ ImsDBName::CUSTOMER, 'JOIN', 'prj.customerSno = cust.sno' ]
                , 'field' => ['cust.customerName']
            ]
            , 'factory' => //생산처 정보
            [
                'data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.produceCompanySno = factory.sno' ]
                , 'field' => ['factory.managerNm as reqFactoryNm']
            ]
            , 'ework' => //전산 작지 정보
            [
                'data' => [ 'sl_imsEwork', 'LEFT OUTER JOIN', 'a.sno = ework.styleSno' ]
                , 'field' => [
                    'ework.sno as eworkSno'
                    , 'ework.fileMain, if(LENGTH(fileMain)>35,1,0) as eworkMainFl'
                    , 'ework.fileBatek, if(\'n\'=useBatek, 2, if(LENGTH(fileBatek)>35,1,0)) as eworkBatekFl'
                    , 'ework.fileMark1, if(\'n\'=useMark, 2, if(LENGTH(fileMark1)>35,1,0)) as eworkMarkFl'
                    , 'ework.filePosition, if(LENGTH(filePosition)>35,1,0) as eworkPositionFl'
                    , 'ework.fileSpec, if(LENGTH(fileSpec)>35,1,0) as eworkSpecFl , if(LENGTH(specData)>35,1,0) as eworkSpec2Fl  '
                    , 'ework.filePacking1, if(\'n\'=usePacking, 2, if(LENGTH(filePacking1)>35,1,0)) as eworkPackingFl'
                    , '(select count(1) from sl_imsPrdMaterial mat where mat.styleSno=a.sno) as eworkMaterialFl'
                    , 'ework.markInfo1' , 'ework.markInfo2', 'ework.markInfo3', 'ework.markInfo4', 'ework.markInfo5'
                    , 'ework.markInfo6' , 'ework.markInfo7', 'ework.markInfo8', 'ework.markInfo9', 'ework.markInfo10'
                    , 'ework.warnMaterial'
                    , 'ework.warnBatek'
                    , 'ework.warnMark'
                    , 'ework.warnPosition'
                    , 'ework.warnSpec'
                    , 'ework.warnPacking'
                    , 'ework.useBatek'
                    , 'ework.useMark'
                    , 'ework.usePacking'
                ]
            ]
        ];


        if( !empty($params['condition']['addJoinTable']) ){
            $tableInfo = array_merge($tableInfo, $params['condition']['addJoinTable']);
        }

        return DBUtil2::setTableInfo($tableInfo,false);
    }

}

