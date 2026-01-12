<?php
namespace Controller\Front\Work;

use Component\Work\DocumentCodeMap;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlProjectCodeMap;

Trait WorkControllerTrait {
    public function index() {
        //로그인 여부 확인
        if( empty(\Session::get('manager'))){
            $linkUrl = \Request::getPhpSelf() . '?' . \Request::getQueryString();
            $this->redirect('login.php?linkUrl=' . $linkUrl);
            exit();
        }else{
            $workControllerService=SlLoader::cLoad('work','workControllerService','');
            $workControllerService->setControllerData($this);
            $this->workIndex();
        }
    }

    public function setMenu($docDept, $docType){

        $projectListMap = [
                1=>[
                    'name' => '고객사 리스트',
                    'accept' => 'y',
                    'href' => 'company_list.php',
                ],
                2=>[
                    'name' => '프로젝트 리스트',
                    'accept' => 'y',
                    'href' => 'project_list.php',
                ]
        ];
        $salesDocumentListMap = SlProjectCodeMap::PRJ_DOCUMENT['SALES']['typeDoc'];
        foreach($salesDocumentListMap as $docKey => $docData){
            $docData['href'] = "document_list.php?docDept=SALES&docType={$docKey}";
            $salesDocumentListMap[$docKey] = $docData;
        }

        if( 'PROJECT' === $docDept ){
            $projectListMap[$docType]['active']='active';
        }else{
            $salesDocumentListMap[$docType]['active']='active';
        }
        /*gd_debug( $docDept );
        gd_debug($projectListMap);
        gd_debug($salesDocumentListMap);*/

        $menuList = [
            'PROJECT' => [
                'title' => '프로젝트 관리',
                'subMenuList' => $projectListMap,
            ],
            'SALES' => [
                'title' => '영업관리 문서',
                'subMenuList' => $salesDocumentListMap,
            ]
        ];
        //gd_debug( $menuList  );

        //gd_debug( $menuList );
        $this->setData('title' , $menuList[$docDept]['title']);
        $this->setData('titleSub', $menuList[$docDept]['subMenuList'][$docType]['name']);

        $this->setData('menuList', $menuList);
    }

}
