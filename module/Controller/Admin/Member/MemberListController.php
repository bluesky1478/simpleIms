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
 * @link      http://www.godo.co.kr
 */

namespace Controller\Admin\Member;

use Component\Member\Member;
use Framework\Utility\SkinUtils;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;

/**
 * Class 회원리스트
 * @package Bundle\Controller\Admin\Member
 * @author  yjwee
 */
class MemberListController extends \Bundle\Controller\Admin\Member\MemberListController
{
    /**
     * @inheritdoc
     */
    public function index()
    {
        $isFirstPage = false;
        $request = \App::getInstance('request');

        if( !$request->get()->has('entryDt') ){
            $isFirstPage = true;
        }

        // -- _GET 값
        $getValue = \Request::get()->toArray();
        if(  !empty($getValue['simple_excel_download'])  ){
            \Request::get()->set('pageNum', '10000');
            \Request::get()->set('page', '1');
            $getValue['pageNum'] = '10000';
            $getValue['page'] = '1';

            $request->get()->set('pageNum', '10000');
            $request->get()->set('page', '1');
        }

        parent::index();

        //gd_debug('2차');
        //gd_debug($request->get()->all());

        $scmAdmin = SlLoader::cLoad('scm','scmAdmin');
        $scmList = $scmAdmin->getSelectScmList();
        $refineScmList = array();
        foreach( $scmList as $val ){
            $refineScmList[$val] = $val;
        }
        $this->setData('scmList', $refineScmList);
        //gd_debug($refineScmList);

        if( $isFirstPage ){
            $search = $this->getData('search');
            $entryDate = $search['entryDt'];
            $entryDate[0] = '2020-01-01';
            $search['entryDt'] = $entryDate;
            $this->setData('search',$search);
        }

        $this->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );

        if(  !empty($getValue['simple_excel_download'])  ){
            $this->simpleExcelDownload();
            exit();
        }

    }


    public function getTitle($isNo = true){
        $titleList[] = '번호';
        $titleList[] = '신청 고객사';
        $titleList[] = '연결 고객사';
        $titleList[] = '아이디';
        $titleList[] = '닉네임';
        $titleList[] = '이름';
        $titleList[] = '폰번호';
        $titleList[] = '이메일';
        $titleList[] = '등급';
        //$titleList[] = '가입승인';
        //$titleList[] = '유/무료';
        $titleList[] = '회원유형';
        $titleList[] = '구매제한수량';
        $titleList[] = '팀';
        $titleList[] = '매장유형';
        return $titleList;
    }

    public function simpleExcelDownload(){
        $titleList = $this->getTitle(false);
        $data = $this->getData('data');
        $groups = $this->getData('groups');
        $page = $this->getData('page');

        $excelBody = '';
        foreach ($data as $key => $val) {
            $excelBody .= "<tr>";
            $excelBody .= ExcelCsvUtil::wrapTd($page->idx--);
            $excelBody .= ExcelCsvUtil::wrapTd($val['ex2']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['ex1']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['memId']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['nickNm']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['memNm']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['cellPhone']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['email']);
            $excelBody .= ExcelCsvUtil::wrapTd( gd_isset($groups[$val['groupSno']]) );
            //$txtAppFl = ($val['appFl'] == 'y' ? '승인' : '미승인');
            //$excelBody .= ExcelCsvUtil::wrapTd( $txtAppFl );
            //$excelBody .= ExcelCsvUtil::wrapTd( ('y' == $val['freeFl'])? '무료회원' : '유료회원' );
            $excelBody .= ExcelCsvUtil::wrapTd( \SlComponent\Util\SlCodeMap::MEMBER_TYPE[ $val['memberType'] ]);
            $excelBody .= ExcelCsvUtil::wrapTd( $val['buyLimitCount']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['ex3']);
            $hankookType = [];
            foreach(  \SlComponent\Util\SlCodeMap::HANKOOK_TYPE as $hankookKey => $hankookValue  )  {
                if( $hankookKey & $val['hankookType'] ){
                    $hankookType[] = $hankookValue;
                }
            }
            $excelBody .= ExcelCsvUtil::wrapTd( implode('<br>', $hankookType  ) );
            $excelBody .= "</tr>";
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('회원리스트', $titleList, $excelBody);
    }


}
