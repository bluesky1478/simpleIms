<?php

namespace Controller\Admin\Test;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Deposit\Deposit;
use Component\Erp\ErpCodeMap;
use Component\Erp\ErpService;
use Component\Goods\GoodsPolicy;
use Component\Ims\EnumType\APPROVAL_STATUS;
use Component\Ims\EnumType\PREPARED_TYPE;
use Component\Ims\EnumType\TODO_STATUS;
use Component\Ims\EnumType\TODO_TARGET_TYPE;
use Component\Ims\EnumType\TODO_TYPE;
use Component\Ims\EnumType\TODO_TYPE2;
use Component\Ims\ImsApprovalService;
use Component\Ims\ImsCodeMap;
use Component\Ims\ImsDBName;
use Component\Ims\ImsJsonSchema;
use Component\Ims\ImsSendMessage;
use Component\Ims\ImsService;
use Component\Ims\StatusValidService;
use Component\Imsv2\ImsScheduleUtil;
use Component\Member\Util\MemberUtil;
use Component\Scm\AlterCodeMap;
use Component\Scm\ScmAsianaCodeMap;
use Component\Scm\ScmHyundaeService;
use Component\Scm\ScmTkeService;
use Component\Sitelab\SiteLabSmsUtil;
use Component\Work\Code\DocumentDesignCodeMap;
use Component\Work\DocumentCodeMap;
use Component\Work\WorkCodeMap;
use Controller\Admin\Sales\ControllerService\SalesListService;
use Encryptor;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\NumberUtils;
use Globals;
use Request;
use SiteLabUtil\ImsUtil;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Api\ExchangeRateService;
use SlComponent\Database\DBUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Godo\SamYoungService;
use SlComponent\Mail\SiteLabMailUtil;
use SlComponent\Util\ApiTrait;
use SlComponent\Util\CUrlUtil;
use SlComponent\Util\DocumentStruct;
use SlComponent\Util\ExcelCsvUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SitelabUtil;
use SlComponent\Util\SlCode;
use SlComponent\Util\SlCodeMap;
use SlComponent\Util\SlKakaoUtil;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlPostRequestUtil;
use SlComponent\Util\SlProjectCodeMap;
use SlComponent\Util\SlSmsUtil;
use UserFilePath;
use Framework\Utility\StringUtils;
use Component\Storage\Storage;
use Framework\Security\Digester;
use Framework\Utility\GodoUtils;
use DateTime;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use Bundle\Component\CurrencyExchangeRate\CurrencyExchangeRate;
use Bundle\Component\CurrencyExchangeRate\CurrencyExchangeRateAdmin;
use Framework\Debug\Exception\LayerException;

/**
 * TEST 페이지
 */
class TestServiceController extends \Controller\Admin\Controller{

    private $stockInOutList;

    public function __construct(){
        parent::__construct();
        $this->stockInOutList = SlLoader::cLoad('imsv2\\Lst','stockInOutList');
    }

    /**
     * @throws \Exception
     */
    public function index(){
        gd_debug('서비스 테스트 시작');
        //재고 서비스 테스트
        $this->testStockInOutList();
        gd_debug('서비스 테스트 종료');
        exit();
    }

    public function testStockInOutList(){
        //입출고 이력 가져오기 테스트
        $condition['multiKey'] = [];
        $condition['multiCondition'] = 'OR';
        $condition['multiKey'][] = [
            'key'=>'og.goodsNo',
            'keyword'=>'',
        ];
        $condition['scmNo'] = 6;
        $condition['inOutType'] = 1;
        $condition['goodsNo'] = 1000002255;

        $params = [
            'condition' => $condition
        ];
        $list = $this->stockInOutList->getList($params);
        gd_debug($list);
    }

}

