<?php

namespace Controller\Front\Download;

use Request;
use SlComponent\Download\SiteLabDownloadUtil;
use SlComponent\Util\SitelabLogger;

/**
 * 관리자 다운로드 컨트롤러
 * @package Controller\Admin\Share
 */
class SimpleDownloadController extends \Controller\Front\Controller{

	/**
	 * {@inheritdoc}
	 */
	public function index()
	{
	    $fileName = urldecode( \Request::get()->get('fileName') );
	    $filePath = urldecode( \Request::get()->get('filePath') );
        SiteLabDownloadUtil::download($filePath, $fileName);
        exit();
	}

}
