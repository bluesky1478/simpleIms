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

namespace Controller\Admin\Provider\Statistics;

use Component\Member\Member;
use Component\Page\Page;
use Framework\Utility\ArrayUtils;
use Framework\Utility\StringUtils;
use SlComponent\Godo\ScmService;

/**
 * Class 마일리지 일괄 지급/차감 관리
 * @package Bundle\Controller\Admin\Member
 * @author  yjwee
 */
class PointAddController extends \Controller\Admin\Controller
{
    public function index()
    {
        //튜닝 추가
        $this->setData('scmNo', \Session::get('manager.scmNo'));
        $this->setData('companyNm', \Session::get('manager.companyNm'));
        $request = \App::getInstance('request');
        //튜닝 추가

        $request = \App::getInstance('request');

        //튜닝 추가
        $request->get()->set('scmFl',1);
        $request->get()->set('scmNo',[ \Session::get('manager.scmNo') ]);
        $request->get()->set('scmNoNm',[ \Session::get('manager.companyNm') ]);

        /** @var \Bundle\Controller\Admin\Controller $this */
        $this->callMenu('statistics', 'accept', 'point_list');

        $request->get()->set('mallSno', DEFAULT_MALL_NUMBER);
        if (!$request->get()->has('page')) {
            $request->get()->set('page', 1);
        }
        if (!$request->get()->has('pageNum')) {
            $request->get()->set('pageNum', 10);
        }
        $getParams = $request->get()->all();
        //gd_debug($request->get()->all());
        //gd_debug($getParams);

        $memberService = \App::load(Member::class);
        $funcSkipOverTime = function () use ($memberService, $request) {
            $page = $request->get()->get('page');
            $pageNum = $request->get()->get('pageNum');
            //gd_debug($request->get()->all());
            return $memberService->listsWithCoupon($request->get()->all(), $page, $pageNum);
        };
        $funcCondition = function () use ($request) {
            return \count($request->get()->all()) === 3
                && $request->get()->get('mallSno') === DEFAULT_MALL_NUMBER
                && $request->get()->get('page') === 1
                && $request->get()->get('pageNum') === 10;
        };
        $getData = $this->skipOverTime($funcSkipOverTime, $funcCondition, [], $isSkip);

        $pageTotal = \count($getData);
        if ($pageTotal > 0) {
            $pageTotal = $memberService->foundRowsByListsWithCoupon($getParams);
        }
        $pageAmount = $memberService->getCount(DB_MEMBER, 'memNo', 'WHERE sleepFl=\'n\'');
        /** @var \Bundle\Component\Page\Page $pageObject */
        $pageObject = new Page($request->get()->get('page'), $pageTotal, $pageAmount, $request->get()->get('pageNum'));
        $pageObject->setPage();
        $pageObject->setUrl($request->getQueryString());

        $checked = \Component\Member\Util\MemberUtil::checkedByMemberListSearch($getParams);
        $selected = \Component\Member\Util\MemberUtil::selectedByMemberListSearch($getParams);

        // 지급/차감여부
        $search['mileageCheckFl'] = $request->get()->get('mileageCheckFl', 'add');
        $checked['mileageCheckFl'][$search['mileageCheckFl']] = 'checked="checked"';
        // 마일리지 부족 시 차감방법
        $search['removeMethodFl'] = $request->get()->get('removeMethodFl', 'minus');
        $checked['removeMethodFl'][$search['removeMethodFl']] = 'checked="checked"';
        // 회원안내 설정
        $search['guideSend'] = $request->get()->get('guideSend');
        if (\count($search['guideSend']) > 0) {
            foreach ($search['guideSend'] as $index => $item) {
                $checked['guideSend'][$item] = 'checked="checked"';
            }
        }
        // 대상회원 선택
        $search['targetMemberFl'] = $request->get()->get('targetMemberFl', 'search');
        $checked['targetMemberFl'][$search['targetMemberFl']] = 'checked="checked"';

        $searchItem = ArrayUtils::removeEmpty($getParams);
        if (empty($searchItem) === false) {
            $searchJson = htmlspecialchars(json_encode($searchItem));
        }

        $this->setData('isSkip', $isSkip);
        $this->setData('page', $pageObject);
        $this->setData('data', $getData);
        $this->setData('search', $getParams);
        $this->setData('searchKindASelectBox', \Component\Member\Member::getSearchKindASelectBox());
        $this->setData('groups', \Component\Member\Group\Util::getGroupName());
        $this->setData('combineSearch', \Component\Member\Member::getCombineSearchSelectBox());
        $this->setData('combineSearch', \Component\Member\Member::COMBINE_SEARCH);
        $this->setData('searchJson', StringUtils::strIsSet($searchJson, ''));
        $this->setData('mileageReasons', \Component\Code\Code::getGroupItems('01005'));
        $this->setData('checked', $checked);
        $this->setData('selected', $selected);
        $this->setData('disableGlobalSearch', true);
        $this->addScript(['member.js']);

        //튜닝
        /*
        [01005001]	=> 상품구매 시 포인트 사용
        [01005501]	=> 취소 시 사용 마일리지 환불
        [01005003]	=> 환불 시 사용 마일리지 환불
        [01005011]	=> 기타
        [10059997]	=> 정기지급
        */
        //gd_debug($this->getData('mileageReasons'));
        $this->setData('mileageReasons', ScmService::POINT_REASON);
    }
}
