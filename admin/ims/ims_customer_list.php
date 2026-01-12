<?php
use Component\Ims\ImsCodeMap;
use Component\Ims\NkCodeMap;

include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>
<?php include 'library_nk.php'?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<section id="imsApp" class="project-view">
    <div class="page-header js-affix">
        <h3>고객 리스트</h3>
        <div class="btn-group">
            <input type="button" value="고객사 등록" class="btn btn-red btn-reg hover-btn" />
        </div>
    </div>
    <div class="row" >
        <div class="col-xs-12" >
            <div>
                <div class="table-title ">
                    검색
                </div>
                <!--검색 시작-->
                <div class="search-detail-box form-inline">
                    <table class="table table-cols table-td-height0 font-11">
                        <colgroup>
                            <col class="width-sm">
                            <col class="width-3xl">
                            <col class="width-sm">
                            <col class="width-3xl">
                            <col class="width-sm">
                            <col class="width-3xl">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th rowspan="2">검색어</th>
                            <td rowspan="2">
                                <div v-for="(keyCondition,multiKeyIndex) in searchCondition.multiKey" class="mgb5">
                                    검색조건{% multiKeyIndex+1 %} : <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                                    <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="refreshList(1)" />
                                    <div class="btn btn-sm btn-red" @click="addMultiKey" v-if="(multiKeyIndex+1) === searchCondition.multiKey.length ">+추가</div>
                                    <div class="btn btn-sm btn-gray" @click="searchCondition.multiKey.splice(multiKeyIndex, 1)" v-if="searchCondition.multiKey.length > 1 ">-제거</div>
                                </div>
                                <div class="mgb5">
                                    다중 검색 :
                                    <select class="form-control" v-model="searchCondition.multiCondition">
                                        <option value="AND">AND (그리고)</option>
                                        <option value="OR">OR (또는)</option>
                                    </select>
                                </div>
                            </td>
                            <th>3PL</th>
                            <td>
                                <label class="radio-inline ">
                                    <input type="radio" name="isCust3pl" value="all" v-model="searchCondition.chkCustUse3pl"/>전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="isCust3pl" value="y" v-model="searchCondition.chkCustUse3pl"/>사용
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="isCust3pl" value="n" v-model="searchCondition.chkCustUse3pl"/>미사용
                                </label>
                            </td>
                            <th>폐쇄몰</th>
                            <td>
                                <label class="radio-inline ">
                                    <input type="radio" name="isCustMall" value="all" v-model="searchCondition.chkCustUseMall"/>전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="isCustMall" value="y" v-model="searchCondition.chkCustUseMall"/>사용
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="isCustMall" value="n" v-model="searchCondition.chkCustUseMall"/>미사용
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>기성금액 제외</th>
                            <td >
                                <label class="radio-inline ">
                                    <input type="radio" name="isRtw" value="all" v-model="searchCondition.chkRtw"/>전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="isRtw" value="y" v-model="searchCondition.chkRtw"/>기성금액 제외
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="isRtw" value="n" v-model="searchCondition.chkRtw"/>기성복만
                                </label>
                            </td>
                            <th>매출 여부</th>
                            <td >
                                <label class="radio-inline ">
                                    <input type="radio" name="isContract" value="all" v-model="searchCondition.chkContract"/>전체
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="isContract" value="y" v-model="searchCondition.chkContract"/>있음
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="isContract" value="n" v-model="searchCondition.chkContract"/>없음
                                </label>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!--검색 끝-->
            </div>

            <div class="dp-flex dp-flex-center">
                <div class="btn btn-lg btn-black w-100px" @click="refreshList(1)">검색</div>
                <div class="btn btn-lg btn-white w-100px" @click="conditionReset()">초기화</div>
            </div>


            <div class="">
                <div class="flo-left mgb5">
                    <div class="font-16 dp-flex" >
                        <span style="font-size: 18px !important;">
                            TOTAL <span class="bold text-danger pdl5">{% $.setNumberFormat(listTotal.recode.total) %}</span> 건
                        </span>
                    </div>
                </div>
                <div class="flo-right mgb5">
                    <div class="" style="display: flex; ">
                        <button type="button" class="btn btn-white btn-icon-excel simple-download mgl5" @click="listDownload()">다운로드</button>

                        <select @change="refreshList(1)" class="form-control mgl5" v-model="searchCondition.sort">
                            <option value="D2,asc">고객명 ▲</option>
                            <option value="D2,desc">고객명 ▼</option>
                            <option value="D1,asc">등록일 ▲</option>
                            <option value="D1,desc">등록일 ▼</option>
                            <option value="D3,asc">총매출 ▲</option>
                            <option value="D3,desc">총매출 ▼</option>
                        </select>
                        <select @change="refreshList(1)" v-model="searchCondition.pageNum" class="form-control mgl5">
                            <option value="5">5개 보기</option>
                            <option value="20">20개 보기</option>
                            <option value="50">50개 보기</option>
                            <option value="100">100개 보기</option>
                        </select>
                    </div>
                </div>
            </div>
            <!--list start-->
            <div>
                <table class="table table-rows table-default-center table-td-height30" v-if="!$.isEmpty(searchData)" :id="'list-main-table'">
                    <colgroup>
                        <col class="w-1p" />
                        <col class="w-1p" />
                        <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData" v-if="true != fieldData.skip && true !== fieldData.subRow" />
                        <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData" v-if="true === fieldData.subRow" />
                    </colgroup>
                    <tr>
                        <th rowspan="2"><input type='checkbox' value='y' class='js-checkall' data-target-name='sno' /></th>
                        <th rowspan="2" >번호</th>
                        <th v-for="fieldData in searchData.fieldData" v-if="true != fieldData.subRow" :rowspan="fieldData.rowspan" :colspan="fieldData.colspan">
                            <div :class="['assort', 'prdPriceApproval', 'prdCostApproval'].includes(fieldData.name)?'font-9':''">
                                {% fieldData.title %}
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th v-for="fieldData in searchData.fieldData" v-if="true == fieldData.subRow" :rowspan="fieldData.rowspan" :colspan="fieldData.colspan">
                            <div >
                                {% fieldData.title %}
                            </div>
                        </th>
                    </tr>
                    <tbody v-if="0 >= listData.length">
                    <tr>
                        <td colspan="99">
                            데이터가 없습니다.
                        </td>
                    </tr>
                    </tbody>
                    <tbody v-for="(each, index) in listData" class="hover-light"  >
                    <!--예정일-->
                    <tr >
                        <td >
                            <input type="checkbox" name="sno[]" :value="each.sno" class="list-check" >
                        </td>
                        <td >
                            <div>
                                {% (listTotal.idx-index) %}
                                <?php if( in_array($managerId, \Component\Ims\ImsCodeMap::IMS_ADMIN) ) { ?>
                                <!--<span class="text-muted cursor-pointer hover-btn font-10 mgl10" @click="ImsService.deleteData('project' , each.sno, refreshList)">
                                    <i class="fa fa-trash-o" aria-hidden="true"></i>삭제
                                </span>-->
                                <?php } ?>
                            </div>
                        </td>
                        <td v-for="fieldData in searchData.fieldData"
                            v-if="true != fieldData.skip"
                            :class="fieldData.class + ''" :style="!$.isEmpty(each['tx'+$.ucfirst(fieldData.name)])?'background-color:#f0f0f0':''">
                            <?php include 'nlist/list_template.php'?>
                            <div v-if="'c' === fieldData.type" class="pd0">
                                <!--고객사명-->
                                <div v-if="'custNameAndCode' === fieldData.name" class="text-left pdl5 relative">
                                    <span class="tn-pop-customer-info sl-blue hover-btn cursor-pointer" :data-sno="each.sno" @click="openCustomer(each.sno,'project')">
                                        <span class="text-muted">#{% each.sno %}</span>
                                        {% each.customerName %}
                                        <span class="font-10 text-danger">
                                            {% each.styleCode %}
                                        </span>
                                    </span>
                                </div>
                                <!--춘추/기타 착용 스타일-->
                                <div v-if="'etcStyle' === fieldData.name" class="text-left pdl5 relative">
                                    <ul>
                                        <li v-for="(style) in each.etcStyle" class="font-11">
                                            {% style.styleName %}
                                        </li>
                                    </ul>
                                </div>
                                <!--하계 착용 스타일-->
                                <div v-if="'ssStyle' === fieldData.name" class="text-left pdl5 relative">
                                    <ul >
                                        <li v-for="(style) in each.ssStyle" class="font-11">
                                            {% style.styleName %}
                                        </li>
                                    </ul>
                                </div>
                                <!--동계 스타일-->
                                <div v-if="'fwStyle' === fieldData.name" class="text-left pdl5 relative">
                                    <ul>
                                        <li v-for="(style) in each.fwStyle" class="font-11 hover-btn cursor-pointer">
                                            {% style.styleName %}
                                        </li>
                                    </ul>
                                </div>
                                <!--담당자 정보-->
                                <div v-if="'contactInfo' === fieldData.name" class="text-left pdl5 relative  ">
                                    {% each.contactName %}
                                    <span class="font-10">({% each.contactMobile %} / {% each.contactEmail %})</span>
                                </div>

                                <!--총매입-->
                                <div v-if="'customerCost' === fieldData.name" class="ta-r text-blue">
                                    <div v-show="'y' === searchCondition.chkRtw">{% $.setNumberFormat(each.customerCost-each.customerRtwCost) %}</div>
                                    <div v-show="'n' === searchCondition.chkRtw">{% $.setNumberFormat(each.customerRtwCost) %}</div>
                                    <div v-show="'all' === searchCondition.chkRtw">{% $.setNumberFormat(each.customerCost) %}</div>
                                </div>
                                <!--총매출-->
                                <div v-if="'customerPrice' === fieldData.name" class="ta-r text-danger">
                                    <div v-show="'y' === searchCondition.chkRtw">{% $.setNumberFormat(each.customerPrice-each.customerRtwCost) %}</div>
                                    <div v-show="'n' === searchCondition.chkRtw">{% $.setNumberFormat(each.customerRtwCost) %}</div>
                                    <div v-show="'all' === searchCondition.chkRtw">{% $.setNumberFormat(each.customerPrice) %}</div>
                                </div>
                                <!--총마진 정보-->
                                <div v-if="'customerMargin' === fieldData.name" class="ta-r">
                                    <div v-show="'y' === searchCondition.chkRtw">{% $.getMargin(each.customerCost-each.customerRtwCost, each.customerPrice-each.customerRtwPrice) %}%</div>
                                    <div v-show="'n' === searchCondition.chkRtw">{% $.getMargin(each.customerRtwCost, each.customerRtwPrice) %}%</div>
                                    <div v-show="'all' === searchCondition.chkRtw">{% $.getMargin(each.customerCost, each.customerPrice) %}%</div>
                                </div>
                                <div v-for="(n, i) in 3" v-if="'sum'+(<?=$firstYear?>+i)+'Cost' === fieldData.name ">
                                    <div v-if="!$.isEmpty(each.customerYearPrice) && !$.isEmpty(each.customerYearPrice[<?=$firstYear?>+i])" class="sl-blue">
                                        <div v-show="'y' === searchCondition.chkRtw">
                                            {% $.setNumberFormat(each.customerYearPrice[<?=$firstYear?>+i]['customerCost']-each.customerYearPrice[<?=$firstYear?>+i]['customerRtwCost']) %}
                                        </div>
                                        <div v-show="'n' === searchCondition.chkRtw">
                                            {% $.setNumberFormat(each.customerYearPrice[<?=$firstYear?>+i]['customerRtwCost']) %}
                                        </div>
                                        <div v-show="'all' === searchCondition.chkRtw">
                                            {% $.setNumberFormat(each.customerYearPrice[<?=$firstYear?>+i]['customerCost']) %}
                                        </div>
                                    </div>
                                    <div v-else class="text-muted">
                                        -
                                    </div>
                                </div>
                                <div v-for="(n, i) in 3" v-if="'sum'+(<?=$firstYear?>+i)+'Price' === fieldData.name ">
                                    <div v-if="!$.isEmpty(each.customerYearPrice) && !$.isEmpty(each.customerYearPrice[<?=$firstYear?>+i])" class="text-danger">
                                        <div v-show="'y' === searchCondition.chkRtw">
                                            {% $.setNumberFormat(each.customerYearPrice[<?=$firstYear?>+i]['customerPrice']-each.customerYearPrice[<?=$firstYear?>+i]['customerRtwPrice']) %}
                                        </div>
                                        <div v-show="'n' === searchCondition.chkRtw">
                                            {% $.setNumberFormat(each.customerYearPrice[<?=$firstYear?>+i]['customerRtwPrice']) %}
                                        </div>
                                        <div v-show="'all' === searchCondition.chkRtw">
                                            {% $.setNumberFormat(each.customerYearPrice[<?=$firstYear?>+i]['customerPrice']) %}
                                        </div>
                                    </div>
                                    <div v-else class="text-muted">
                                        -
                                    </div>
                                </div>
                                <div v-for="(n, i) in 3" v-if="'sum'+(<?=$firstYear?>+i)+'Margin' === fieldData.name ">
                                    <div v-if="!$.isEmpty(each.customerYearPrice) && !$.isEmpty(each.customerYearPrice[<?=$firstYear?>+i])">
                                        <div v-if="!$.isEmpty(each.customerYearPrice) && !$.isEmpty(each.customerYearPrice[<?=$firstYear?>+i])" >
                                            <div v-show="'y' === searchCondition.chkRtw">
                                                {% $.getMargin(each.customerYearPrice[<?=$firstYear?>+i]['customerCost']-each.customerYearPrice[<?=$firstYear?>+i]['customerRtwCost'], each.customerYearPrice[<?=$firstYear?>+i]['customerPrice']-each.customerYearPrice[<?=$firstYear?>+i]['customerRtwPrice']) %}%
                                            </div>
                                            <div v-show="'n' === searchCondition.chkRtw">
                                                {% $.getMargin(each.customerYearPrice[<?=$firstYear?>+i]['customerRtwCost'], each.customerYearPrice[<?=$firstYear?>+i]['customerRtwPrice']) %}%
                                            </div>
                                            <div v-show="'all' === searchCondition.chkRtw">
                                                {% $.getMargin(each.customerYearPrice[<?=$firstYear?>+i]['customerCost'], each.customerYearPrice[<?=$firstYear?>+i]['customerPrice']) %}%
                                            </div>
                                        </div>
                                        <div v-else class="text-muted">
                                            -
                                        </div>

                                    </div>
                                    <div v-else class="text-muted">
                                        -
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <!--list end-->
            <div id="customer-page" v-html="pageHtml" class="ta-c"></div>
        </div>

    </div>

    <div style="margin-bottom:150px"></div>

</section>

<script type="text/javascript">
    const mainListPrefix = 'customer';
    const listSearchDefaultData = {
        multiKey : [{
            key : 'cust.customerName',
            keyword : '',
        }],
        multiCondition : 'OR',
        sRadioSchBtYn : 'all',
        page : 1,
        pageNum : 100,
        sort : 'D3,desc' //정렬
    };
    listSearchDefaultData.chkCustUseMall = 'all';
    listSearchDefaultData.chkCustUse3pl = 'all';
    listSearchDefaultData.chkContract = 'y';
    listSearchDefaultData.chkRtw = 'y';

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        return $.imsPost('getCustomerList', params);
    };
    //자식 팝업창에서 실행
    function refreshList() {
        vueApp.refreshList(vueApp.searchCondition.page);
    }

    $(()=>{

        $('title').html('고객사 리스트');

        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            isExcelUpload : false,
            excelUploadType : 1,
            isModify:false,
        });
        ImsBoneService.setMethod(serviceData, {
            //엑셀 다운로드
            listDownload : (type)=>{
                //Not Ajax.
                const downloadSearchCondition = $.copyObject( vueApp.searchCondition );
                const queryString = $.objectToQueryString(downloadSearchCondition);
                location.href=`ims_customer_list.php?simple_excel_download=${type}&` + queryString;
            },


            /*excelDownload : ()=>{
                // HTML 테이블 가져오기
                const table = document.getElementById('list-main-table');
                // 테이블 데이터를 엑셀 시트로 변환
                const workbook = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });

                for(let i=2; 100>=i; i++){
                    try{
                        delete workbook.Sheets.Sheet1['L'+i];
                    }catch (e){}
                }
                // 엑셀 파일 다운로드
                XLSX.writeFile(workbook, `IMS고객사리스트.xlsx`);
            }*/

        });

        ImsBoneService.setMounted(serviceData, ()=>{
            //고객 등록
            $('.btn-reg').click(()=>{
                location.href='./ims_customer_reg.php';
            });
        });

        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData); //style , storedSearchCondition
        listService.init(serviceData);
    });

</script>
