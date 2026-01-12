<?php

namespace Component\Imsv2;

use Component\Database\DBTableField;
use Component\Erp\ErpService;
use Component\Ims\ImsDBName;
use Component\Ims\ImsService;
use Component\Ims\ImsServiceConditionTrait;
use Component\Ims\ImsServiceTrait;
use Component\Ims\ImsServiceSortNkTrait;
use Component\Ims\ImsServiceSampleTrait;
use Component\Ims\ImsCodeMap;
use Component\Ims\NkCodeMap;
use Controller\Admin\Ims\ImsPsNkTrait;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;

/**
 * IMS 영업 서비스 (25/11월 개설)
 * Class ImsStockService
 * @package Component\Imsv2
 */
class ImsSalesService
{
    /**
     *
     * @param $condition
     * @param SearchVo $searchVo
     * @return mixed
     * @throws \Exception
     */
    public function updateSalesCustomerStat(){
        $sCurrDt = SlCommonUtil::getDateCalc(date('Y-m-d'), -1);

        $aSkipUpsertFlds = ['sno', 'regDt', 'modDt'];
        $aTmpFldList = DBTableField::callTableFunction(ImsDBName::SALES_CUSTOMER_STATS);
        $aUpsert = [];
        foreach ($aTmpFldList as $val) {
            if (!in_array($val['val'], $aSkipUpsertFlds)) {
                if (isset($val['json']) && $val['json'] === true) $aUpsert[$val['val']] = [];
                else {
                    if ($val['typ'] == 'i') {
                        $aUpsert[$val['val']] = $val['def'] == null ? 0 : $val['def'];
                    } else {
                        $aUpsert[$val['val']] = $val['def'] == null ? '' : $val['def'];
                    }
                }
            }
        }
        $aUpsert['statsDt'] = $sCurrDt;
        //영업이력 통계
        $oSchVo = new SearchVo();
        $oSchVo->setWhere('regDt >= "'.$sCurrDt.' 00:00:00"');
        $oSchVo->setWhere('regDt <= "'.$sCurrDt.' 23:59:59"');
        $oSchVo->setGroup('contentsType');
        $tableInfo=[
            'a' => ['data' => [ ImsDBName::SALES_CUSTOMER_CONTENTS ], 'field' => ["contentsType, count(a.sno) as cntContents, sum(contentsMinute) as sumMin"]],
        ];
        $aContentsList = DBUtil2::getComplexList(DBUtil2::setTableInfo($tableInfo,false), $oSchVo, false, false, true);
        foreach ($aContentsList as $val) {
            if ($val['contentsType'] == 1) {
                $aUpsert['cntTm'] = $val['cntContents'];
                $aUpsert['sumMinTm'] = $val['sumMin'];
            } else {
                $aUpsert['cntEm'] = $val['cntContents'];
                $aUpsert['sumMinEm'] = $val['sumMin'];
            }
        }
        //업체 통계
        $sYesterDay = date('Y-m-d', strtotime($sCurrDt.' -1 day'));
        $aYesterDayList = DBUtil2::getListBySearchVo(ImsDBName::SALES_CUSTOMER_STATS, new SearchVo('statsDt=?', $sYesterDay));
        $aYesterDayInfo = ['jsonCustomer1'=>[], 'jsonCustomer2'=>[], 'jsonCustomer3'=>[], 'jsonCustomer4'=>[]];
        if (count($aYesterDayList) > 0) {
            foreach ($aYesterDayInfo as $key => $val) $aYesterDayInfo[$key] = json_decode($aYesterDayList[0][$key], true);
        }
        $oSchVo = new SearchVo();
        $oSchVo->setGroup('customerType');
        $oSchVo->setOrder('customerType asc');
        $tableInfo=[
            'a' => ['data' => [ ImsDBName::SALES_CUSTOMER ], 'field' => ["customerType, count(customerType) as cntCustomerType, group_concat(sno) as snos"]],
        ];
        $aCustomerList = DBUtil2::getComplexList(DBUtil2::setTableInfo($tableInfo,false), $oSchVo, false, false, true);
        foreach ($aCustomerList as $val) {
            switch ($val['customerType']) {
                case '10':
                    $aUpsert['cntCustomer1'] = $val['cntCustomerType'];
                    $aUpsert['jsonCustomer1'] = explode(',', $val['snos']);
                    break;
                case '20':
                    $aUpsert['cntCustomer2'] = $val['cntCustomerType'];
                    $aUpsert['jsonCustomer2'] = explode(',', $val['snos']);
                    break;
                case '30':
                    $aUpsert['cntCustomer3'] = $val['cntCustomerType'];
                    $aUpsert['jsonCustomer3'] = explode(',', $val['snos']);
                    break;
//                case '40':
//                    //기타고객(40)인 경우 담당자정보 있냐없냐에 따라서 잠재고객 or 관심고객에 붙임 -> 기타고객 일괄update 예정
//                    if ($val['contactName'] != '' && $val['contactPhone'] != '' && $val['contactEmail'] != '') {
//                        $aUpsert['cntCustomer2']++;
//                        array_push($aUpsert['jsonCustomer2'], $val['sno']);
//                    } else {
//                        $aUpsert['cntCustomer1']++;
//                        array_push($aUpsert['jsonCustomer1'], $val['sno']);
//                    }
//                    break;
                case '50':
                case '80':
                case '90':
                case '99':
                    $aUpsert['cntCustomer4'] = $val['cntCustomerType'];
                    $aUpsert['jsonCustomer4'] = explode(',', $val['snos']);
                    break;
            }
        }
        //증감sno 구하기
        if (count($aYesterDayList) > 0) {
            for ($i = 1; $i <= 4; $i++) {
                $aUpsert['jsonIncCustomer'.$i] = array_values(array_diff($aUpsert['jsonCustomer'.$i], $aYesterDayInfo['jsonCustomer'.$i]));
                $aUpsert['jsonDecCustomer'.$i] = array_values(array_diff($aYesterDayInfo['jsonCustomer'.$i], $aUpsert['jsonCustomer'.$i]));
            }
        }
        //upsert 전에 배열인 것들 json_encode
        foreach ($aUpsert as $key => $val) {
            if (is_array($val)) $aUpsert[$key] = json_encode($val);
        }

        //통계 upsert
        $aExistInfo = DBUtil2::getOne(ImsDBName::SALES_CUSTOMER_STATS, 'statsDt', $sCurrDt);
        if (!isset($aExistInfo['sno'])) {
            DBUtil2::insert(ImsDBName::SALES_CUSTOMER_STATS, $aUpsert);
        } else {
            DBUtil2::update(ImsDBName::SALES_CUSTOMER_STATS, $aUpsert, new SearchVo('sno=?', (int)$aExistInfo['sno']));
        }
    }

}