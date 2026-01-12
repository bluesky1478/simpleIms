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
namespace Controller\Admin\Provider\Statistics;

use Component\VisitStatistics\VisitStatistics;
use DateTime;
use Framework\Debug\Exception\AlertBackException;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;
use Component\Member\Manager;
use Framework\Utility\SkinUtils;
use Component\Member\Member;

/**
 * scm_member_list
 * 회원관리
 * @author ssong
 */
class ScmMemberListController extends \Controller\Admin\Controller
{
    /**
     * index
     *
     * @throws \Exception
     */
    public function index()
    {
        // 공급사 정보 설정
        $isProvider = Manager::isProvider();
        $this->callMenu('statistics', 'accept', 'member');
        $this->setData('isProvider', $isProvider);
        $this->setData('scmNo', \Session::get('manager.scmNo'));
        $this->setData('companyNm', \Session::get('manager.companyNm'));
        $this->setData('hankookTypeMap', SlCodeMap::HANKOOK_TYPE);

        $request = \App::getInstance('request');
        $getValue = \Request::get()->toArray();
        if(  !empty($getValue['simple_excel_download'])  ){
            \Request::get()->set('pageNum', '10000');
            \Request::get()->set('page', '1');
            $getValue['pageNum'] = '10000';
            $getValue['page'] = '1';

            $request->get()->set('pageNum', '10000');
            $request->get()->set('page', '1');
        }

        $this->runMemberListLogic();

        $scmNo = \Session::get('manager.scmNo');
        $this->setData('scmConfig', DBUtil2::getOne('sl_setScmConfig','scmNo',$scmNo));

        $this->setData('requestUrl', basename(\Request::getPhpSelf()) .'?simple_excel_download=1&'.  \Request::getQueryString() );
        if(  !empty($getValue['simple_excel_download'])  ){
            $this->simpleExcelDownload();
            exit();
        }

        $hyundaeAcctY = DBUtil2::getCount(DB_MEMBER, new SearchVo("adultFl='y' and ex1=?", "현대엘리베이터"));
        $hyundaeAcctN = DBUtil2::getCount(DB_MEMBER, new SearchVo("adultFl='n' and ex1=?", "현대엘리베이터"));
        $total = $hyundaeAcctY+$hyundaeAcctN;
        $this->setData('hdTotal', $total);
        $this->setData('hdAcctY', $hyundaeAcctY);
        $this->setData('hdAcctN', $hyundaeAcctN);
        $this->setData('hdAcctPercent', round($hyundaeAcctY/$total*100));
    }

    public function runMemberListLogic(){

        $request = \App::getInstance('request');

        // 회원 아이디 검증 (영문, 숫자, 특수문자(-),(_),(.),(@)만 가능함)
        if ($request->get()->get('key') === 'memId' && !preg_match('/^[a-zA-Z0-9\.\-\_\@]*$/', $request->get()->get('keyword'))) {
            $request->get()->set('keyword', preg_replace("/([^a-zA-Z0-9\.\-\_\@])/", "", $request->get()->get('keyword')));
        } else {
            $request->get()->set('keyword', preg_replace("!<script(.*?)<\/script>!is", "", $request->get()->get('keyword')));
        }

        if (!$request->get()->has('mallSno')) {
            $request->get()->set('mallSno', '');
        }
        if (!$request->get()->has('page')) {
            $request->get()->set('page', 1);
        }
        if (!$request->get()->has('pageNum')) {
            $request->get()->set('pageNum', 10);
        }

        // ISMS 인증관련 추가
        if (array_search($request->get()->get('pageNum'), SkinUtils::getPageViewCount()) === false) {
            $request->get()->set('pageNum', 10);
        }

        $request->get()->set('scmFl',1);
        $request->get()->set('scmNo',[ \Session::get('manager.scmNo') ]);
        $request->get()->set('scmNoNm',[ \Session::get('manager.companyNm') ]);

        $memberService = \App::load(Member::class);
        $funcSkipOverTime = function () use ($memberService, $request) {
            $getAll = $request->get()->all();
            $page = $request->get()->get('page');
            $pageNum = $request->get()->get('pageNum');

            return $memberService->listsWithCoupon($getAll, $page, $pageNum);
        };

        $funcCondition = function () use ($request) {
            return \count($request->get()->all()) === 3
                && $request->get()->get('mallSno') === ''
                && $request->get()->get('page') === 1
                && $request->get()->get('pageNum') === 10;
        };

        //gd_debug($funcCondition);
        $getData = $this->skipOverTime($funcSkipOverTime, $funcCondition, [], $isSkip);

        $pageObject = new \Component\Page\Page($request->get()->get('page'), 0, 0, $request->get()->get('pageNum'));
        $pageTotal = \count($getData);
        $pageObject->setTotal($pageTotal);
        $pageObject->setCache(true);
        if ($pageTotal > 0 && $pageObject->hasRecodeCache('total') === false) {
            $total = $memberService->foundRowsByListsWithCoupon($request->get()->all());
            $pageObject->setTotal($total);
        }
        if ($pageObject->hasRecodeCache('amount') === false) {
            $amount = $memberService->getCount(DB_MEMBER, 'memNo', 'WHERE sleepFl=\'n\'');
            $pageObject->setAmount($amount);
        }

        $pageObject->setUrl($request->getQueryString());
        $pageObject->setPage();
        $checked = \Component\Member\Util\MemberUtil::checkedByMemberListSearch($request->get()->all());
        $selected = \Component\Member\Util\MemberUtil::selectedByMemberListSearch($request->get()->all());
        $this->setData('isSkip', $isSkip);
        $this->setData('page', $pageObject);
        $this->setData('data', $getData);
        $this->setData('search', $request->get()->all());
        $this->setData('groups', \Component\Member\Group\Util::getGroupName());
        $this->setData('combineSearch', \Component\Member\Member::getCombineSearchSelectBox());
        $this->setData('checked', $checked);
        $this->setData('selected', $selected);
        $this->addScript(
            [
                'member.js',
                'sms.js',
            ]
        );

    }

    public function getTitle($isNo = true){
        $scmNo = \Session::get('manager.scmNo');
        $titleList[] = '번호';
        $titleList[] = '아이디';
        $titleList[] = '닉네임';
        $titleList[] = '이름';
        $titleList[] = '회원가입일';
        $titleList[] = '가입승인';
        $titleList[] = '유/무료 구분';

        if (8 == $scmNo) {
            $titleList[] = '회원유형';
            $titleList[] = '구매제한';
        }
        if (6 == $scmNo) {
            $titleList[] = '매장유형';
        }
        if (32 == $scmNo) {
            $titleList[] = '약관동의';
        }
        return $titleList;
    }

    public function simpleExcelDownload(){
        $scmNo = \Session::get('manager.scmNo');

        $titleList = $this->getTitle(false);
        $data = $this->getData('data');
        $page = $this->getData('page');

        $excelBody = '';
        foreach ($data as $key => $val) {
            $excelBody .= "<tr>";
            $excelBody .= ExcelCsvUtil::wrapTd($page->idx--);
            $excelBody .= ExcelCsvUtil::wrapTd($val['memId']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['nickNm']);
            $excelBody .= ExcelCsvUtil::wrapTd($val['memNm']);
            $excelBody .= ExcelCsvUtil::wrapTd(substr($val['entryDt'], 2, 8));
            $excelBody .= ExcelCsvUtil::wrapTd( ($val['appFl'] == 'y' ? '승인' : '미승인') );
            $excelBody .= ExcelCsvUtil::wrapTd( ('y' == $val['freeFl'])? '무료회원' : '유료회원');
            if (8 == $scmNo) {
                $excelBody .= ExcelCsvUtil::wrapTd(SlCodeMap::MEMBER_TYPE[ $val['memberType'] ]);
                $excelBody .= ExcelCsvUtil::wrapTd($val['buyLimitCount'] );
            }
            if (6 == $scmNo) {
                $hankookType = [];
                foreach(  SlCodeMap::HANKOOK_TYPE as $hankookKey => $hankookValue  )  {
                    if( $hankookKey & $val['hankookType'] ){
                        $hankookType[] = $hankookValue;
                    }
                }
                $excelBody .= ExcelCsvUtil::wrapTd( implode('<br>', $hankookType  ) );
            }
            if (32 == $scmNo) {
                $excelBody .= ExcelCsvUtil::wrapTd(  'y' === $val['adultFl'] ? '예':'아니오' );
            }
            $excelBody .= "</tr>";
        }

        $simpleExcelComponent = SlLoader::cLoad('Excel','SimpleExcelComponent','sl');
        $simpleExcelComponent->simpleDownload('회원리스트', $titleList, $excelBody);
    }

}
