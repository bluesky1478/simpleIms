<?php
namespace Component\Ims;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Member\Manager;
use Component\Scm\ScmTkeService;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Sms\Code;
use SlComponent\Util\SlSmsUtil;

/**
 * IMS 상품관련 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
trait ImsServiceSampleTrait {

    /**
     * 샘플에 사용하는 파일 반환
     * @return string[]
     */
    public static function getSampleFileFieldList(){
        return [
            'sampleFile1', // 샘플 의뢰서
            'sampleFile2', // 샘플 실물
            'sampleFile3', // 샘플 패턴
            'sampleFile4', // 샘플 기타
            'sampleFile5', // 샘플 리뷰서
            'sampleFile6', // 샘플 확정서
        ];
    }


    /**
     * 샘플 저장
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function saveSample($params){
        DBTableField::checkRequired(ImsDBName::SAMPLE, $params); //필수값 체크.
        $params['fabric'] = json_encode($params['fabric']);
        $params['subFabric'] = json_encode($params['subFabric']);
        //저장
        $sno = $this->save(ImsDBName::SAMPLE, $params);
        //화면 갱신용 리스트
        $list = $this->getSampleList([
            'styleSno' => $params['styleSno']
        ]);
        $styleInfo = DBUtil2::getOne(ImsDBName::PRODUCT, 'sno', $params['styleSno']);
        if( -1 == $styleInfo['sampleConfirmSno'] ){
            //샘플 진행하지 않다가 진행하게 되면 0으로 변경
            DBUtil2::update(ImsDBName::PRODUCT, ['sampleConfirmSno'=>'0'], new SearchVo('sno=?', $params['styleSno']) );
        }
        $this->setSyncStatus($styleInfo['projectSno'], __CLASS__);

        return [
            'sno' => $sno,
            'list' => $list,
        ];
    }

    /**
     * 샘플 복사
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function copySample($params){
        foreach($params['snoList'] as $sno){
            $copyData = DBUtil2::getOne(ImsDBName::SAMPLE, 'sno', $sno);
            unset($copyData['sno']);
            unset($copyData['regManagerSno']);
            unset($copyData['lastManagerSno']);
            unset($copyData['regDt']);
            unset($copyData['modDt']);
            unset($copyData['sampleFactoryBeginDt']);
            unset($copyData['sampleFactoryEndDt']);
            unset($copyData['sampleConfirmDt']);
            unset($copyData['sampleConfirm']);
            unset($copyData['sampleFile1Approval']);

            $newSampleSno = $this->save(ImsDBName::SAMPLE, $copyData);
            //파일 정보 복사
            /*$copyFileList = self::getSampleFileFieldList();
            foreach($copyFileList as $copyFile){
                $this->copyReOrderFile([ //reorder trait 사용
                    'srcEachSno' => $sno, //검색용
                    'newEachSno' => $newSampleSno, //새로운 데이터
                ], $copyData['projectSno'], $copyFile, 'nothing');
            }*/
        }
        return $this->getSampleList([
            'styleSno' => $params['styleSno']
        ]);
    }

    /**
     * 샘플 삭제 (영구)
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function deleteSample($params){
        foreach($params['snoList'] as $sno){
            $this->delete(ImsDBName::SAMPLE, $sno);
        }
        return $this->getSampleList([
            'styleSno' => $params['styleSno']
        ]);
    }

    /**
     * 샘플 확정
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function confirmSample($params){

        DBUtil2::update(ImsDBName::SAMPLE, [
            'sampleConfirm' => 'n'
        ], new SearchVo('styleSno=?',$params['styleSno']));

        $this->save(ImsDBName::SAMPLE, [
            'sno' => $params['sampleSno'],
            'sampleConfirm' => $params['confirmYn'],
            'sampleConfirmManager' => SlCommonUtil::getManagerSno(),
            'sampleConfirmDt' => 'now()'
        ]);

        if( 'y' === $params['confirmYn'] ){
            $this->save(ImsDBName::PRODUCT, [
                'sno'=>$params['styleSno'],
                'sampleConfirmSno'=>$params['sampleSno'],
            ]);
        }else{
            $this->save(ImsDBName::PRODUCT, [
                'sno'=>$params['styleSno'],
                'sampleConfirmSno'=>0,
            ]);
        }

        //확정 샘플 원부자재 정보 등록 ( 아예 초기일 경우 )
        $materialCount = DBUtil2::getCount(ImsDBName::PRD_MATERIAL, new SearchVo('styleSno=?', $params['styleSno']));
        if( 0 >= $materialCount){
            $sampleInfo = $this->getSample(['sno'=>$params['sampleSno']]);
            $eworkService = SlLoader::cLoad('ims','ImsEworkService');
            $eworkService->copySampleMaterial($params, $sampleInfo);
        }

        return $this->getSampleList([
            'styleSno' => $params['styleSno']
        ]);
    }

    /**
     * 샘플리스트 조회
     * @param $params
     * @return mixed
     */
    public function getSampleList($params){
        $searchVo = new SearchVo('a.styleSno=?',$params['styleSno']);
        $searchVo->setOrder('a.sno desc');
        $list = DBUtil2::getComplexList($this->sql->getSampleListTable(), $searchVo);
        $list = SlCommonUtil::setEachData($list, $this, 'decorationSample');
        //$list = DBTableField::parseJsonFieldList(ImsDBName::SAMPLE, $list);
        //List에서 파일 정보 추가.
        foreach($list as $key => $each){
            $list[$key] = $this->setSampleFileDefault($each);
        }
        return $list;
    }

    /**
     * 스타일 연관 리스트
     * @param $params
     * @return mixed
     */
    public function getRelatedList($params){
        $searchVo = new SearchVo('a.sno=?',$params['styleSno']);
        $srcStyle = DBUtil2::getComplexList($this->sql->getProductTable(), $searchVo)[0];

        $targetSearchVo = new SearchVo(
            [
                'a.delFl=?',
                'a.customerSno=?',
                'a.prdSeason=?',
                'a.prdStyle=?',
                'a.sno != ?',
                //'a.prdGender=?',
            ],[
                'n',
                $srcStyle['customerSno'],
                $srcStyle['prdSeason'],
                $srcStyle['prdStyle'],
                $params['styleSno'],
                //$srcStyle['prdGender']
            ]
        );

        $targetSearchVo->setOrder('a.styleCode desc, a.sno desc');

        if( !empty($srcStyle['prdGender']) ){
            //$targetSearchVo->setWhere('a.prdGender=?');
            //$targetSearchVo->setWhereValue($srcStyle['prdGender']);
        }
        if( !empty($srcStyle['addStyleCode']) ){
            $targetSearchVo->setWhere('a.addStyleCode=?');
            $targetSearchVo->setWhereValue($srcStyle['addStyleCode']);
        }

        return DBUtil2::getComplexList($this->sql->getProductTable(), $targetSearchVo);
    }

    /**
     * 원단 관리 정보 꾸미기
     * @param $each
     * @param $key
     * @param $mixData
     * @return mixed
     */
    public function decorationSample($each, $key, $mixData){
        $each = DBTableField::parseJsonField(ImsDBName::SAMPLE, $each);
        $each = $this->setSampleFileDefault($each);
        $each['sampleTypeHan'] = NkCodeMap::SAMPLE_TYPE[$each['sampleType']];
        return SlCommonUtil::setDateBlank($each);
    }

    /**
     * 샘플 파일의 구조를 셋팅.
     * @param $each
     * @return mixed
     */
    public function setSampleFileDefault($each){
        $checkFileList = self::getSampleFileFieldList();
        return $this->setDefaultFile($checkFileList, $each);
    }


    /**
     * 샘플 기본 구조 가져오기
     * @param $src
     * @return array
     */
    public function getSampleViewDefaultData($src){
        $result = DBTableField::getTableKeyAndBlankValue('sl_imsSample');
        $result['sampleManagerSno'] = \Session::get('manager.sno');
        $result['fabric'] = [ImsJsonSchema::SUB_FABRIC_INFO];
        $result['subFabric'] = [ImsJsonSchema::SUB_FABRIC_INFO];

        $initFieldList = ['laborCost', 'marginCost', 'dutyCost', 'managementCost', 'prdMoq', 'priceMoq', 'addPrice' ,'deliveryType' , 'produceType', 'producePeriod'];
        foreach($initFieldList as $initField){
            $result[$initField] = 0;
        }

        $result['fileList'] = $this->setSampleFileDefault([
            'customerSno' => $src['product']['customerSno'],
            'projectSno' => $src['product']['projectSno'],
            'styleSno' => $src['product']['sno'],
            'eachSno' => -1,
        ])['fileList'];
        
        return $result;
    }


    /**
     * unused ?
     * @param $params
     * @return mixed|null
     */
    public function getFabric($params){
        $sampleView = DBUtil2::getOne(ImsDBName::SAMPLE, 'sno', $params['sno']);
        $sampleView = DBTableField::parseJsonField(ImsDBName::SAMPLE, $sampleView);

        if( !empty($sampleView) ){
            $rslt = $this->setSampleFileDefault($sampleView);
        }else{
            $rslt = null;
        }
        return $rslt;
    }


    /**
     * 샘플 정보 조회
     * @param $params
     * @return array|mixed
     * @throws \Exception
     *
     * $sampleView = DBUtil2::getOne(ImsDBName::SAMPLE, 'sno', $params['sno']);
       $sampleView = $this->decorationSample($sampleView);
       //$sampleView = DBTableField::parseJsonField(ImsDBName::SAMPLE, $sampleView);
     *
     */
    public function getSample($params){
        if(empty($params['sno'])){
            $data = DBTableField::getTableBlankData('tableImsSample'); //초기 데이터.
        }else{
            $condition = ['sno' => $params['sno'],];
            $data = $this->getListSample([
                'condition' => $condition
            ])['list'][0];
            //$data = DBTableField::parseJsonFieldList(ImsDBName::SAMPLE, $data);
        }


        return $data;
    }

    /**
     * 샘플 리스트 (신규추가)
     * @param $params
     * @return array
     */
    public function getListSample($params){
        $searchVo = new SearchVo('prd.delFl=?','n');
        $totalSearchVo = new SearchVo('prd.delFl=?','n');

        $this->setCommonCondition($params['condition'], $searchVo);
        $this->setCommonCondition($params['condition'], $totalSearchVo);
        $this->setListSort($params['condition']['sort'], $searchVo);

        $searchData = [
            'page' => gd_isset($params['condition']['page'],1),
            'pageNum' => gd_isset($params['condition']['pageNum'],100),
        ];
        $allData = DBUtil2::getComplexListWithPaging($this->sql->getSampleListTable(), $searchVo, $searchData);
        $list = SlCommonUtil::setEachData($allData['listData'], $this, 'decorationSample');

        //Rowspan 설정
        SlCommonUtil::setListRowSpan($list, [
            'style'  => ['valueKey' => 'styleSno']
        ], $params);

        $pageEx = $allData['pageData']->getPage('#');

        return [
            'pageEx' => $pageEx,
            'page' => $allData['pageData'],
            'list' => $list
        ];
    }


    /**
     * 프로젝트 샘플 수 가져오기
     * @param $params
     * @return mixed
     */
    public function getSampleCountByProject($params){
        return DBUtil2::getCount(ImsDBName::SAMPLE, new SearchVo('projectSno=?', $params['projectSno']));
    }

}


