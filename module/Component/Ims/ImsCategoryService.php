<?php
namespace Component\Ims;

use App;
use Component\Database\DBIms;
use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Sms\Code;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;
use LogHandler;
use Request;
use Session;
use SiteLabUtil\SlCommonUtil;
use SlComponent\Database\DBUtil2;
use SlComponent\Database\SearchVo;
use SlComponent\Util\PhpExcelUtil;
use SlComponent\Util\SitelabLogger;
use SlComponent\Util\SlLoader;
use SlComponent\Util\SlSmsUtil;

/**
 * IMS 카테고리 서비스
 * Class GoodsStock
 * @package Component\Goods
 */
class ImsCategoryService {

    private $sql;
    private $dpData;

    use ImsListTrait;

    public function __construct(){
        //$this->sql = SlLoader::sqlLoad(__CLASS__, false);
        $this->dpData = [
            'no' => ['name'=>'번호','col'=>'3','skip'=>true],
        ];
    }
    public function getDisplay($params){
        //SlCommonUtil::createHtmlTableTitle($this->dpData);
        return $this->dpData;
    }

    /**
     * 일괄 업로드 (카테고리)
     * @param $files
     * @throws \Exception
     */
    public function batchUpload($files){
        //DBUtil2::runSql("truncate table sl_imsCategory");
        $result = PhpExcelUtil::readToArray($files, 1);
        gd_debug('run batchUpload');

        foreach($result as $index => $val){
            $cate1 = $val[1];
            if(empty($cate1)) continue;

            $cate2 = $val[2];
            $cate1Info = DBUtil2::getOne(ImsDBName::CATEGORY, 'LENGTH(cateCd)=3 and cateName', $cate1); //1차 확인
            $cate1LastCode = DBUtil2::runSelect("select max(left(cateCd,3)) as lastNo from sl_imsCategory where cateType='material'")[0]['lastNo'];

            //1차 카테고리 등록
            if( empty($cate1Info) ){
                $cate1LastNo = (int)$cate1LastCode;
                $cate1LastNo++;
                $cate1Rslt = DBUtil2::insert(ImsDBName::CATEGORY, [
                    'cateType' => ImsCodeMap::CATE_TYPE_MATERIAL,
                    'cateCd' => str_pad($cate1LastNo,3,'0', STR_PAD_LEFT),
                    'cateName' => $cate1,
                ]);
                //gd_debug('cate1 insert : (' . str_pad($cate1LastNo,3,'0', STR_PAD_LEFT) . ') ' . $cate1Rslt);
            }

            //2차 카테고리 등록
            if( !empty($cate1Info) && !empty($cate2) ){
                $sql = "select MAX(SUBSTRING(cateCd, 4, 3))  as lastNo from sl_imsCategory where cateType='material' and cateCd <> '{$cate1Info['cateCd']}' and cateCd LIKE '{$cate1Info['cateCd']}%' ";
                $cate2LastCode = DBUtil2::runSelect($sql)[0]['lastNo'];
                //gd_debug($cate2LastCode);

                $cate2LastNo = (int)$cate2LastCode;
                $cate2LastNo++;
                $cate2Rslt = DBUtil2::insert(ImsDBName::CATEGORY, [
                    'cateType' => ImsCodeMap::CATE_TYPE_MATERIAL,
                    'cateCd' => $cate1Info['cateCd'].str_pad($cate2LastNo,3,'0', STR_PAD_LEFT),
                    'cateName' => $cate2,
                ]);
                //gd_debug('cate2 insert : (' . $cate1Info['cateCd'].str_pad($cate2LastNo,3,'0', STR_PAD_LEFT) . ') ' . $cate2Rslt);
            }

        }
        gd_debug('exit batchUpload');
    }

}

