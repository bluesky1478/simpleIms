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
namespace Controller\Admin\Board;

use Component\Board\ArticleViewAdmin;
use Component\Board\ArticleWriteAdmin;
use Component\Board\Board;
use Component\Board\BoardAdmin;
use Component\Goods\GoodsCate;
use Component\Page\Page;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\AlertBackException;
use Request;
use SlComponent\Util\ApiTrait;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlLoader;

class ArticleViewController extends \Bundle\Controller\Admin\Board\ArticleViewController
{
    use ApiTrait;

    /**
     * Description
     */
    public function index(){
        parent::index();
        $this->addScript([
            '../../script/vue.js',
        ]);
        $this->addCss([
            '../../css/preloader.css',
        ]);
        $this->setClaimApiUrl($this);
        $bdId = \Request::get()->get('bdId');
        $bdSno = \Request::get()->get('sno');
        $frontUrl = URI_HOME."board/write.php?isAdmin=y&mode=modify&bdId={$bdId}&sno={$bdSno}";

        $bdView = $this->getData('bdView');
        if( 'y' === $bdView['data']['isMobile'] ){
            $frontUrl = str_replace('http://','http://m.',$frontUrl);
            $frontUrl = str_replace('https://','https://m.',$frontUrl);
            //$frontUrl .= '&noheader=y';
        }
        $this->setData('frontUrl', $frontUrl);

    }
}
