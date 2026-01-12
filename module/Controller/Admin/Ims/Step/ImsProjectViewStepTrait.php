<?php

namespace Controller\Admin\Ims\Step;

use App;
use Component\Facebook\Facebook;
use Component\Godo\GodoPaycoServerApi;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
use Component\Ims\ImsService;
use Component\Member\MemberSnsService;
use Component\Member\MemberValidation;
use Component\Member\Util\MemberUtil;
use Component\Policy\SnsLoginPolicy;
use Component\SiteLink\SiteLink;
use Component\Storage\Storage;
use Controller\Front\Controller;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\AlertReloadException;
use Logger;
use Request;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlControllerTrait;
use SlComponent\Util\SlLoader;

/**
 * 상품관련 Trait
 */
trait ImsProjectViewStepTrait {

    //60. 발주대기
    public function getViewAreaList(){
        return [
            'area1' => [
                ['title'=>'입찰 형태'    ,'type'=>'select' ,'model'=>'bidType2'  ,'code'=>ImsCodeMap::PROJECT_BID_TYPE, 'event'=>'',], //입찰변경시
                ['title'=>'미팅(입찰)일자/정보','type'=>'mix',
                    'mixData' => [
                        [ 'title'=>'미팅일자','type'=>'picker' ,'model'=>'meetingInfoExpectedDt'],
                        [ 'title'=>'정보'   ,'type'=>'text'   ,'model'=>'meetingInfoMemo'],
                    ]
                ],
                
                ['title'=>'제안 형태'   ,'type'=>'select' ,'model'=>'recommend' ,'code'=>ImsCodeMap::RECOMMEND_TYPE],
                ['title'=>'제안서 제출','type'=>'mix',
                    'mixData' => [
                        [ 'title'=>'제출예정일'  ,'type'=>'picker'  ,'model'=>'custInformExpectedDt'],
                        [ 'title'=>'변경가능여부','type'=>'radio'   ,'model'=>'custInformFieldStatus'],
                        [ 'title'=>'제안서타입'  ,'type'=>'text'    ,'model'=>'custInformMemo'],
                    ]
                ],

                ['title'=>'샘플 제작'   ,'type'=>'text'   ,'model'=>'addedInfo.etc1' ,'code'=>ImsCodeMap::RECOMMEND_TYPE],
                ['title'=>'샘플 제출일'   ,'type'=>'mix',
                    'mixData' => [
                        [ 'title'=>'제출예정일'  ,'type'=>'picker'  ,'model'=>'custSampleInformExpectedDt'],
                        [ 'title'=>'변경가능여부','type'=>'radio'   ,'model'=>'custSampleInformFieldStatus'],
                    ]
                ],

                ['title'=>'고객 의사 결정' ,'type'=>'text'   ,'model'=>'addedInfo.etc2' ,'code'=>ImsCodeMap::RECOMMEND_TYPE],
                ['title'=>'현재 유니폼'   ,'type'=>'mix',
                    'mixData' => [
                        [ 'title'=>'확보여부'  ,'type'=>'select'   ,'model'=>'custSampleInformExpectedDt'],
                        [ 'title'=>'변경가능여부','type'=>'radio'   ,'model'=>'custSampleInformFieldStatus'],
                    ]
                ],
            ]
        ];
    }



}

