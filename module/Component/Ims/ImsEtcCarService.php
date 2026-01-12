<?php
namespace Component\Ims;

use App;
use Component\Work\WorkCodeMap;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SlLoader;

use Controller\Admin\Ims\ImsPsNkTrait;
use Component\Ims\ImsServiceSortNkTrait;

class ImsEtcCarService {
    use ImsPsNkTrait;
    use ImsServiceSortNkTrait;

    private $dpDataCar;
    private $dpDataMaintain;
    private $dpDataAddr;
    private $dpDataDrive;


    public function __construct(){
        $this->dpDataCar = [
            ['type' => 'img', 'col' => 8, 'class' => 'ta-l', 'name' => 'carImage', 'title' => '이미지', ],
            ['type' => 'c', 'col' => 8, 'class' => 'ta-c font-15 bold', 'name' => 'carName', 'title' => '차량명', ],
            ['type' => 'btn', 'col' => 8, 'class' => 'ta-l', 'name' => 'carImage', 'title' => '운행/정비등록', ],
            ['type' => 'c', 'col' => 6, 'class' => 'ta-c font-15', 'name' => 'carNumber', 'title' => '차량번호', ],
            ['type' => 'i', 'col' => 8, 'class' => 'ta-c font-15', 'name' => 'totalDriveKm', 'title' => '총주행거리', 'appendChar'=>'Km' ],
            ['type' => 'd3', 'col' => 8, 'class' => '', 'name' => 'totalCheckDt', 'title' => '종합검사일자', ],
            ['type' => 'd2', 'col' => 8, 'class' => '', 'name' => 'changeEODt', 'title' => '엔진오일교체일', ],
            ['type' => 'd2', 'col' => 8, 'class' => '', 'name' => 'changeTireDt', 'title' => '타이어교체일', ],
            ['type' => 'd2', 'col' => 8, 'class' => '', 'name' => 'carWashDt', 'title' => '최근세차일', ],
            ['type' => 'd1', 'col' => 8, 'class' => '', 'name' => 'changeRepairDt', 'title' => '파손수리일', ],
        ];
        $this->dpDataMaintain = [
            ['type' => 'c', 'col' => 10, 'class' => 'ta-l', 'name' => 'carName', 'title' => '차종', ],
            ['type' => 'date', 'col' => 10, 'class' => '', 'name' => 'maintainDt', 'title' => '정비일자', ],
            ['type' => 'i', 'col' => 7, 'class' => 'ta-r', 'name' => 'currKm', 'title' => '정비 당시 Km', 'appendChar'=>'Km' ],
            ['type' => 'c', 'col' => 10, 'class' => 'ta-l pdl5', 'name' => 'maintainType', 'title' => '정비내역', ],
            ['type' => 'html', 'col' => 0, 'class' => 'ta-l pdl5', 'name' => 'maintainMemo', 'title' => '메모', ],
            ['type' => 'etc', 'col' => 7, 'class' => '', 'name' => '', 'title' => '수정/삭제', ],
            ['type' => 'c', 'col' => 5, 'class' => '', 'name' => 'regManagerName', 'title' => '등록자', ],
            ['type' => 'c', 'col' => 6, 'class' => '', 'name' => 'regDt', 'title' => '등록일자', ],
        ];
        $this->dpDataAddr = [
            ['type' => 'c', 'col' => 10, 'class' => '', 'name' => 'addrType', 'title' => '분류', ],
//            ['type' => 'c', 'col' => 10, 'class' => '', 'name' => 'topYn', 'title' => '상단고정Y/N', ],
            ['type' => 'title', 'col' => 20, 'class' => 'ta-l pdl5', 'name' => 'addrName', 'title' => '명칭', ],
            ['type' => 'c', 'col' => 0, 'class' => 'ta-l', 'name' => 'addrAddr', 'title' => '주소', ],
            ['type' => 'c', 'col' => 14, 'class' => '', 'name' => 'regDt', 'title' => '등록일시', ],
        ];
        $this->dpDataDrive = [
            ['type' => 'c', 'col' => 6, 'class' => 'ta-c', 'name' => 'carName', 'title' => '차종', ],
            ['type' => 'date', 'col' => 7, 'class' => '', 'name' => 'driveDt', 'title' => '운행일자', ],
            ['type' => 'c', 'col' => 6, 'class' => '', 'name' => 'driveTime', 'title' => '사용시간', ],
            ['type' => 'c', 'col' => 4, 'class' => '', 'name' => 'driveName', 'title' => '사용자', ],
            ['type' => 'c', 'col' => 9, 'class' => 'ta-l pdl5', 'name' => 'driveType', 'title' => '목적', ],
            ['type' => 'c', 'col' => 10, 'class' => 'ta-l', 'name' => 'startAddrInfo', 'title' => '출발지', ],
            ['type' => 'c', 'col' => 18, 'class' => 'ta-l', 'name' => 'arriveAddrInfo', 'title' => '도착지', ],
            ['type' => 'i', 'col' => 5, 'class' => '', 'name' => 'driveKm', 'title' => '주행Km(왕복)', ],
            ['type' => 'html', 'col' => 0, 'class' => 'ta-l pdl5', 'name' => 'driveMemo', 'title' => '비고', ],
            ['type' => 'etc', 'col' => 7, 'class' => '', 'name' => '', 'title' => '수정/삭제', ],
            ['type' => 'c', 'col' => 4, 'class' => '', 'name' => 'regManagerName', 'title' => '등록자', ],
            ['type' => 'c', 'col' => 6, 'class' => '', 'name' => 'regDt', 'title' => '등록일자', ],
        ];

    }

    public function getDisplayCar(){
        return $this->dpDataCar;
    }
    public function getDisplayMaintain(){
        return $this->dpDataMaintain;
    }
    public function getDisplayAddr(){
        return $this->dpDataAddr;
    }
    public function getDisplayDrive(){
        return $this->dpDataDrive;
    }


    public function getListEtcCar($params) {
        $sTableNm = ImsDBName::ETC_CAR;
        $tableInfo=[
            'a' => ['data' => [ $sTableNm ], 'field' => ["a.*"]],
        ];
        if (!isset($params['upsertSnoGet'])) { //리스트를 return받으려는 경우
            if (!isset($params['sort'])) $params['sort'] = 'sno,asc';
            //리스트에서 뿌려줄 필드리스트 정리
            $aFldList = $this->getDisplayCar();
        } else $aFldList = [];
        $aReturn = $this->fnRefineListUpsertForm($params, $sTableNm, $tableInfo, $aFldList);

        if (!isset($params['upsertSnoGet'])) {
            //리스트 정제
            if (isset($aReturn['list']) && count($aReturn['list']) > 0) {
                $aAppendFldByCar = [];
                //차량별 총 주행거리 구하기
                $oDriveSearchVo = new SearchVo();
                $oDriveSearchVo->setGroup('carSno');
                $aTmpDriveList = DBUtil2::getComplexList(DBUtil2::setTableInfo(['a' => ['data' => [ ImsDBName::ETC_CAR_DRIVE ], 'field' => ["carSno, sum(driveKm) as totalDriveKm"]]],false), $oDriveSearchVo, false, false, true);
                foreach ($aTmpDriveList as $val) $aAppendFldByCar[$val['carSno']]['totalDriveKm'] = $val['totalDriveKm'];

                //최근 정비내용 가져오기(엔진오일교체, 타이어교체, 세차, 파손수리)
                $oSVMaintain = new SearchVo(); //
                $oSVMaintain->setOrder('maintainDt desc, sno desc');
                $aMaintainList = DBUtil2::getListBySearchVo(ImsDBName::ETC_CAR_MAINTAIN, $oSVMaintain);
                if (count($aMaintainList) > 0) {
                    foreach ($aMaintainList as $val) {
                        if ($val['maintainType'] == '엔진오일교체' && !isset($aAppendFldByCar[$val['carSno']]['changeEODt'])) {
                            $aAppendFldByCar[$val['carSno']]['changeEODt'] = $val['maintainDt'];
                            $aAppendFldByCar[$val['carSno']]['currEODKm'] = $val['currKm'];
                        }
                        if ($val['maintainType'] == '타이어교체' && !isset($aAppendFldByCar[$val['carSno']]['changeTireDt'])) {
                            $aAppendFldByCar[$val['carSno']]['changeTireDt'] = $val['maintainDt'];
                            $aAppendFldByCar[$val['carSno']]['changeTireKm'] = $val['currKm'];
                        }
                        if ($val['maintainType'] == '세차' && !isset($aAppendFldByCar[$val['carSno']]['carWashDt'])) {
                            $aAppendFldByCar[$val['carSno']]['carWashDt'] = $val['maintainDt'];
                            $aAppendFldByCar[$val['carSno']]['carWashKm'] = $val['currKm'];
                        }
                        if ($val['maintainType'] == '파손수리' && !isset($aAppendFldByCar[$val['carSno']]['changeRepairDt'])) {
                            $aAppendFldByCar[$val['carSno']]['changeRepairDt'] = $val['maintainDt'];
                        }
                    }
                }

                foreach ($aReturn['list'] as $key => $val) {
                    $aReturn['list'][$key]['totalDriveKm'] =
                    $aReturn['list'][$key]['changeEODt'] =
                    $aReturn['list'][$key]['changeTireDt'] =
                    $aReturn['list'][$key]['carWashDt'] =
                    $aReturn['list'][$key]['changeRepairDt'] = '';

                    if (isset($aAppendFldByCar[$val['sno']]['totalDriveKm'])) $aReturn['list'][$key]['totalDriveKm'] = $aAppendFldByCar[$val['sno']]['totalDriveKm'];
                    if (isset($aAppendFldByCar[$val['sno']]['changeEODt'])) $aReturn['list'][$key]['changeEODt'] = $aAppendFldByCar[$val['sno']]['changeEODt'];
                    if (isset($aAppendFldByCar[$val['sno']]['changeTireDt'])) $aReturn['list'][$key]['changeTireDt'] = $aAppendFldByCar[$val['sno']]['changeTireDt'];
                    if (isset($aAppendFldByCar[$val['sno']]['carWashDt'])) $aReturn['list'][$key]['carWashDt'] = $aAppendFldByCar[$val['sno']]['carWashDt'];
                    if (isset($aAppendFldByCar[$val['sno']]['changeRepairDt'])) $aReturn['list'][$key]['changeRepairDt'] = $aAppendFldByCar[$val['sno']]['changeRepairDt'];
                    if (isset($aAppendFldByCar[$val['sno']]['currEODKm'])) $aReturn['list'][$key]['currEODKm'] = $aAppendFldByCar[$val['sno']]['currEODKm'];
                    if (isset($aAppendFldByCar[$val['sno']]['changeTireKm'])) $aReturn['list'][$key]['changeTireKm'] = $aAppendFldByCar[$val['sno']]['changeTireKm'];
                    if (isset($aAppendFldByCar[$val['sno']]['carWashKm'])) $aReturn['list'][$key]['carWashKm'] = $aAppendFldByCar[$val['sno']]['carWashKm'];
                }
            }
        } else {
            if ($params['upsertSnoGet'] > 0) { //수정(==상세)

            }
        }

        return $aReturn;
    }
    public function getListEtcCarMaintain($params) {
        $sTableNm = ImsDBName::ETC_CAR_MAINTAIN;
        $tableInfo=[
            'a' => ['data' => [ $sTableNm ], 'field' => ["a.*"]],
            'manager' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = manager.sno' ], 'field' => ["if(manager.sno is null, '미선택', manager.managerNm) as regManagerName"]],
            'car' => ['data' => [ ImsDBName::ETC_CAR, 'LEFT OUTER JOIN', 'a.carSno = car.sno' ], 'field' => ["carName"]],
        ];

        if (!isset($params['upsertSnoGet'])) { //리스트를 return받으려는 경우
            if (!isset($params['sort'])) $params['sort'] = 'CM,desc';
            //리스트에서 뿌려줄 필드리스트 정리
            $aFldList = $this->getDisplayMaintain();
        } else $aFldList = [];
        $aReturn = $this->fnRefineListUpsertForm($params, $sTableNm, $tableInfo, $aFldList);

        if (!isset($params['upsertSnoGet'])) {
            foreach ($aReturn['list'] as $key => $val) {
                $aReturn['list'][$key]['regDt'] = explode(' ', $val['regDt'])[0];
            }
        } else {
            if ($params['upsertSnoGet'] == 0) { //등록시 default값 정리
                $aReturn['info']['carSno'] = $params['sRadioSchCarSno'];
                $aReturn['info']['maintainType'] = '엔진오일교체';
                $aReturn['info']['maintainDt'] = date('Y-m-d');
                $aReturn['info']['maintainUser'] = \Session::get('manager.managerNm');
                //현재 주행Km 구하기
                $aReturn['info']['currKm'] = 0;
                //차량별 총 주행거리 구하기
                $oDriveSearchVo = new SearchVo('carSno=?', $aReturn['info']['carSno']);
                $oDriveSearchVo->setGroup('carSno');
                $aTmpDriveList = DBUtil2::getComplexList(DBUtil2::setTableInfo(['a' => ['data' => [ ImsDBName::ETC_CAR_DRIVE ], 'field' => ["carSno, sum(driveKm) as totalDriveKm"]]],false), $oDriveSearchVo, false, false, true);
                if (isset($aTmpDriveList[0]['totalDriveKm'])) {
                    $aReturn['info']['currKm'] = (int)$aTmpDriveList[0]['totalDriveKm'];
                }
            }
        }

        return $aReturn;
    }

    public function getListEtcCarAddr($params) {
        $sTableNm = ImsDBName::ETC_CAR_ADDR;
        $tableInfo=[
            'a' => ['data' => [ $sTableNm ], 'field' => ["a.*"]],
            'manager' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = manager.sno' ], 'field' => ["if(manager.sno is null, '미선택', manager.managerNm) as regManagerName"]],
        ];

        if (!isset($params['upsertSnoGet'])) { //리스트를 return받으려는 경우
            if (!isset($params['sort'])) $params['sort'] = 'topYn,asc,addrType,asc,addrName,asc';
            //리스트에서 뿌려줄 필드리스트 정리
            $aFldList = $this->getDisplayAddr();
        } else $aFldList = [];
        $aReturn = $this->fnRefineListUpsertForm($params, $sTableNm, $tableInfo, $aFldList);
        //리스트 정제
        if (isset($aReturn['list']) && count($aReturn['list']) > 0) {
            foreach ($aReturn['list'] as $key => $val) {
                $aReturn['list'][$key]['topYn'] = $val['topYn'] == 1 ? 'Y' : 'N';
            }
        }

        return $aReturn;
    }

    public function getListEtcCarDrive($params) {
        $sTableNm = ImsDBName::ETC_CAR_DRIVE;
        $tableInfo=[
            'a' => ['data' => [ $sTableNm ], 'field' => ["a.*"]],
            'manager' => ['data' => [ DB_MANAGER, 'LEFT OUTER JOIN', 'a.regManagerSno = manager.sno' ], 'field' => ["if(manager.sno is null, '미선택', manager.managerNm) as regManagerName"]],
            'b' => ['data' => [ ImsDBName::ETC_CAR_ADDR, 'LEFT OUTER JOIN', 'a.startAddrSno = b.sno' ], 'field' => ["b.addrType as startAddrType, b.addrName as startAddrName, b.addrAddr as startAddrAddr"]],
            'c' => ['data' => [ ImsDBName::ETC_CAR_ADDR, 'LEFT OUTER JOIN', 'a.arriveAddrSno = c.sno' ], 'field' => ["c.addrType as arriveAddrType, c.addrName as arriveAddrName, c.addrAddr as arriveAddrAddr"]],
            'car' => ['data' => [ ImsDBName::ETC_CAR, 'LEFT OUTER JOIN', 'a.carSno = car.sno' ], 'field' => ["carName"]],
        ];

        if (!isset($params['upsertSnoGet'])) { //리스트를 return받으려는 경우
            if (!isset($params['sort'])) $params['sort'] = 'CD,desc';
            //리스트에서 뿌려줄 필드리스트 정리
            $aFldList = $this->getDisplayDrive();
        } else $aFldList = [];
        $aReturn = $this->fnRefineListUpsertForm($params, $sTableNm, $tableInfo, $aFldList);

        if (isset($aReturn['list']) && count($aReturn['list']) > 0) { //리스트 정제
            foreach ($aReturn['list'] as $key => $val) {
                $aReturn['list'][$key]['driveTime'] = substr($val['driveStartTime'],0,-3).' ~ '.substr($val['driveEndTime'],0,-3);
                $aReturn['list'][$key]['startAddrInfo'] = '('.$val['startAddrType'].') '.$val['startAddrName'];
                $aReturn['list'][$key]['arriveAddrInfo'] = '('.$val['arriveAddrType'].') '.$val['arriveAddrName'];
                $aReturn['list'][$key]['regDt'] = explode(' ', $val['regDt'])[0];
            }
        } else if (isset($aReturn['info'])) {
            if ($aReturn['info']['sno'] == 0) { //등록시 default값 정리
                $aReturn['info']['carSno'] = $params['sRadioSchCarSno'];
                $aReturn['info']['driveType'] = '거래처방문';
                $aReturn['info']['driveDt'] = date('Y-m-d');
                $aReturn['info']['driveStartTimeHour'] = $aReturn['info']['driveStartTimeMin'] = $aReturn['info']['driveEndTimeHour'] = $aReturn['info']['driveEndTimeMin'] = '00';
                $aReturn['info']['driveDepartment'] = WorkCodeMap::DEPT_KR[WorkCodeMap::DEPT_STR[SlCommonUtil::getManagerInfo(\Session::get('manager.sno'))['departmentCd']]];
                $aReturn['info']['driveName'] = \Session::get('manager.managerNm');
                //구분이 '회사' 인 주소지 sno default로
                $aReturn['info']['startAddrSno'] = '0';
                $aCompanyAddrInfo = DBUtil2::getOneBySearchVo(ImsDBName::ETC_CAR_ADDR, new SearchVo('addrType=?', '회사'));
                if (isset($aCompanyAddrInfo['sno'])) $aReturn['info']['startAddrSno'] = $aCompanyAddrInfo['sno'];
                //현재 주행Km 구하기
                $aReturn['info']['driveBeforeCluster'] = 0;
                //차량 총운행km 계산방식 변경 (최근운행정보 -> sum(driveKm))
//                $oSVDrive = new SearchVo('carSno=?', $aReturn['info']['carSno']);
//                $oSVDrive->setOrder('driveDt desc, sno desc');
//                $aDriveInfo = DBUtil2::getOneBySearchVo(ImsDBName::ETC_CAR_DRIVE, $oSVDrive);
//                if (isset($aDriveInfo['sno'])) {
//                    $aReturn['info']['driveBeforeCluster'] = (int)$aDriveInfo['driveBeforeCluster'] + (int)$aDriveInfo['driveKm'];
//                }
                //차량별 총 주행거리 구하기
                $oDriveSearchVo = new SearchVo('carSno=?', $aReturn['info']['carSno']);
                $oDriveSearchVo->setGroup('carSno');
                $aTmpDriveList = DBUtil2::getComplexList(DBUtil2::setTableInfo(['a' => ['data' => [ ImsDBName::ETC_CAR_DRIVE ], 'field' => ["carSno, sum(driveKm) as totalDriveKm"]]],false), $oDriveSearchVo, false, false, true);
                if (isset($aTmpDriveList[0]['totalDriveKm'])) {
                    $aReturn['info']['driveBeforeCluster'] = (int)$aTmpDriveList[0]['totalDriveKm'];
                }
            } else {
                $aTmpTime = explode(':', $aReturn['info']['driveStartTime']);
                $aReturn['info']['driveStartTimeHour'] = $aTmpTime[0];
                $aReturn['info']['driveStartTimeMin'] = $aTmpTime[1];
                $aTmpTime = explode(':', $aReturn['info']['driveEndTime']);
                $aReturn['info']['driveEndTimeHour'] = $aTmpTime[0];
                $aReturn['info']['driveEndTimeMin'] = $aTmpTime[1];

                //운행전계기판값 운행기록으로 계산. 이렇게 해야 이전 주행건의 운행Km를 수정했을 때 꼬이지 않음. 단 이러면 항상 driveBeforeCluster값을 계산하므로 driveBeforeCluster컬럼이 필요가 없음. 필요없어도 될듯??(차api 쓰면 필요함)
                $oDriveSearchVo = new SearchVo('carSno=?', $aReturn['info']['carSno']);
                $oDriveSearchVo->setOrder('driveDt desc, driveStartTime desc');
                $aDriveList = DBUtil2::getListBySearchVo(ImsDBName::ETC_CAR_DRIVE, $oDriveSearchVo);
                $aReturn['info']['flagRecentDrive'] = false; //상세/수정 주행건이 차량의 마지막 주행건인지 flag
                $aReturn['info']['driveBeforeCluster'] = 0;
                if (count($aDriveList) > 0) {
                    if ($aReturn['info']['sno'] == $aDriveList[0]['sno']) $aReturn['info']['flagRecentDrive'] = true;
                    foreach ($aDriveList as $val) {
                        if (strtotime($aReturn['info']['driveDt'].' '.$aReturn['info']['driveStartTime']) > strtotime($val['driveDt'].' '.$val['driveStartTime'])) {
                            $aReturn['info']['driveBeforeCluster'] += (int)$val['driveKm'];
                        }
                    }
                } else $aReturn['info']['flagRecentDrive'] = true;
            }
        }

        return $aReturn;
    }

}