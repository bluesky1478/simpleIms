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

namespace Controller\Front\Wcustomer;

use App;
use Component\Work\WorkCodeMap;
use Framework\Debug\Exception\AlertCloseException;
use Globals;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Util\SlLoader;
use UserFilePath;

/**
 * 고객 - 포트폴리오
 */
class PortfolioViewOldController extends \Controller\Front\Controller
{
    public function index() {
        $workCustomerService = SlLoader::cLoad('workCustomer','workCustomerService','');
        $workCustomerService->setListData($this, 'DESIGN', 20);




        $this->setData('portfolioFullData', $portfolioData);
        //$this->setData('portfolioData', $portfolioData['docData']);

        $portfolioRefineData = [];
        $styleType = [];
        foreach( WorkCodeMap::STYLE_TYPE as $key => $value ){
            $isPass = false;
            foreach($portfolioData['docData']['portData'][$key] as $portValue) {
                if( !empty($portValue['imageThumbnail']) ){
                    $portfolioRefineData[$key][] = $portValue;
                    $isPass = true;
                }
            }
            if( $isPass ){
                $styleType[$key] = $value;
            }
        }

        $this->setData('portfolioRefineData', $portfolioRefineData);

        foreach( $portfolioRefineData as $eachKey1 => $eachValue1 ){
            foreach( $eachValue1 as $eachKey2 => $eachValue2 ){
                if(empty($eachValue2['commentList'])){
                    $portfolioRefineData[$eachKey1][$eachKey2]['commentList'] = [];
                }
                foreach( $eachValue2['commentList'] as $commentKey => $comment ){
                    //gd_debug($comment); gd_htmlspecialchars_stripslashes , gd_htmlspecialchars_stripslashes
                    $comment['contents'] =  base64_encode($comment['contents']);
                    $portfolioRefineData[$eachKey1][$eachKey2]['commentList'][$commentKey] = $comment;
                }
            }
        }

        $this->setData('portfolioRefineJson', json_encode($portfolioRefineData, JSON_UNESCAPED_UNICODE) );
        $this->setData('styleType', $styleType);
        $this->setData('styleTypeJson', json_encode($styleType, JSON_UNESCAPED_UNICODE) );



    }

}

