<?php
namespace Component\Work;

use App;
use Session;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Database\TableVo;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlCommonTrait;

/**
 * 이노버 시스템 주문관리 서비스
 */
class WorkOrderService {

    private $workCodeMap;
    private $documentCodeMap;

    public function __construct(){
        $this->workCodeMap = new \ReflectionClass('\Component\Work\WorkCodeMap');
        $this->documentCodeMap = new \ReflectionClass('\Component\Work\DocumentCodeMap');
    }



}
