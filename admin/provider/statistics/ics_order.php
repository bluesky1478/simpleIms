<?php
use Component\Ims\ImsCodeMap;
use Component\Ims\NkCodeMap;
use SiteLabUtil\SlCommonUtil;

include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<style>
    input[type=number] {
        padding: 4px 6px!important;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<?php if ($sExcelUploadResultHtml !== '') { ?>
    <section class="project-view">
        <div class="page-header js-affix">
            <h3>담당자 엑셀 업로드 결과</h3>
        </div>
        <?=$sExcelUploadResultHtml?>
        <br/><br/><a href="ics_order.php" class="btn btn-blue hover-btn">배송지점관리 페이지로 이동</a>
    </section>
<?php } else { ?>

<section id="imsApp" class="project-view">
    <div class="page-header js-affix">
        <h3>담당자 리스트</h3>
        <div class="btn-group">
            <input type="button" value="일괄 등록" class="btn btn-red-line" @click="isExcelUpload==true?isExcelUpload=false:isExcelUpload=true;" />
        </div>
    </div>
    <div class="row" >
        <div class="col-xs-12" >
            <div v-show="isExcelUpload">
                <div class="table-title excel-upload-goods-info">
                    담당자 일괄등록
                </div>
                <div class="excel-upload-goods-info">
                    <form id="frmRegistReceiver" name="frmRegistReceiver" action="./ics_order.php" method="post" enctype="multipart/form-data" @submit.prevent="listUpload();">
                        <table class="table table-cols">
                            <colgroup>
                                <col class="width20p"/>
                                <col class="width-xl"/>
                            </colgroup>
                            <tbody>
                            <tr>
                                <th>담당자 업로드</th>
                                <td>
                                    <div class="form-inline">
                                        <input type="file" name="excel" value="" class="form-control width50p" />
                                        <input type="submit" class="btn btn-white btn-sm excel-submit" value="엑셀업로드 하기">
                                        <button type="button" class="btn btn-white btn-icon-excel simple-download" @click="location.href='<?=SlCommonUtil::getAdminHost()?>/provider/statistics/download.php?filePath=<?=urlencode('./data/template/customer_delivery_upload.xlsx')?>&fileName=<?=urlencode('담당자업로드양식.xlsx')?>'">담당자 등록양식 다운로드</button>
                                    </div>
                                    <div>
                                        <span class="notice-info">엑셀 파일은 반드시 &quot;Excel 97-2003 통합문서&quot;만 가능하며, csv 파일은 업로드가 되지 않습니다.</span>
                                        <br/><span class="notice-info">담당자명, 주소는 필수입력값입니다.</span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>


            <div class="">
                <div class="flo-left mgb5">
                    <div class="font-16 dp-flex" >
                        <span style="font-size: 18px !important;">
                            TOTAL <span class="bold text-danger pdl5">{% $.setNumberFormat(listData.length) %}</span> 명
                        </span>
                    </div>
                </div>
                <div class="flo-right mgb5">
                    <div class="" style="display: flex; ">
                        <span v-show="!isModify" @click="listDownload()" class="btn btn-white btn-icon-excel btn-excel mgr10">엑셀 다운로드</span>
                        <span v-show="!isModify" @click="isModify = true;" class="btn btn-red-line mgr10">담당자 설정</span>
                        <span v-show="isModify" @click="save_receiver()" class="btn btn-red mgr10">담당자 저장</span>
                        <span v-show="isModify" @click="isModify = false;" class="btn btn-white mgr10">담당자 설정취소</span>
                    </div>
                </div>
            </div>
            <!--list start-->
            <div>
                <input type="hidden" name="zonecode" id="zonecode" /><input type="hidden" name="address" id="address" /><!--우편번호찾기 : 이거 2개 필요-->
                <table v-if="!$.isEmpty(searchData)" class="table table-rows table-default-center table-td-height30">
                    <colgroup>
                        <col v-show="isModify" class="w-3p" />
                        <col class="w-5p" />
                        <col :class="`w-${fieldData.col}p`" v-for="fieldData in searchData.fieldData" v-if="fieldData.skip != true" />
                        <col v-if="isModify" class="w-5p" />
                    </colgroup>
                    <tr>
                        <th v-show="isModify">이동</th>
                        <th>번호</th>
                        <th v-for="fieldData in searchData.fieldData">{% fieldData.title %}</th>
                        <th v-if="isModify">기능 <span @click="addRow(-1)" class="btn btn-white btn-sm">+ 추가</span></th>
                    </tr>
                    <tbody is="draggable" :list="listData"  :animation="200" tag="tbody" handle=".handle">
                    <tr v-if="listData.length == 0">
                        <td colspan="99">
                            데이터가 없습니다.
                        </td>
                    </tr>
                    <tr v-else v-for="(val, key) in listData" class="hover-light">
                        <td v-show="isModify" class="handle">
                            <div class="cursor-pointer hover-btn">
                                <i class="fa fa-bars" aria-hidden="true"></i>
                            </div>
                        </td>
                        <td>{% key+1 %}</td>
                        <td v-if="fieldData.skip != true" v-for="fieldData in searchData.fieldData" :class="fieldData.class + ''">
                            <span v-if="fieldData.type === 'postcode'">
                                <span v-if="isModify">
                                    <input type="text" v-model="val[fieldData.name]" class="form-control w-100px" style="display: inline-block;" readonly="readonly" />
                                    <input type="button" @click="iKeyChooseSchAddr=key; postcode_search('zonecode', 'address', 'zipcode');" value="우편번호찾기" class="btn btn-gray btn-sm"/>
                                </span>
                                <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                            </span>
                            <span v-else-if="fieldData.type === 'html'" v-html="val[fieldData.name]"></span>
                            <span v-else-if="fieldData.type === 'i'">{% $.setNumberFormat(val[fieldData.name]) %}</span>
                            <span v-else>
                                <span v-if="isModify">
                                    <input type="text" v-model="val[fieldData.name]" class="form-control" />
                                </span>
                                <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                            </span>
                        </td>
                        <td v-if="isModify">
                            <span @click="addRow(key)" class="btn btn-white btn-sm">+ 추가</span>
                            <span @click="deleteRow(val.sno, key)" class="btn btn-red btn-sm">삭제</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <!--list end-->
            <div v-show="false" id="customer_receiver-page" v-html="pageHtml" class="ta-c"></div>
        </div>

    </div>

    <div style="margin-bottom:150px"></div>

</section>

<script type="text/javascript">
    var igstaticCustomerSno = <?=$customerSno?>;

    function postcode_callback() { //우편번호찾기 : 콜백함수 가로채기
        vueApp.listData[vueApp.iKeyChooseSchAddr].managerAddrPost = $('#zonecode').val();
        vueApp.listData[vueApp.iKeyChooseSchAddr].managerAddr = $('#address').val();
    }

    const mainListPrefix = 'customer_receiver';
    const listSearchDefaultData = {
        multiKey : [{
            key : 'cust.customerName',
            keyword : '',
        }],
        multiCondition : 'OR',
        page : 1,
        pageNum : 10000,
        sort : 'sortNum,asc' //정렬
    };

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.mode = 'getListCustomerReceiver';
        return ImsNkService.getList('customerReceiver', params);
    };

    $(()=>{
        $('title').html('배송지점 관리');

        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            isModify:false,
            iKeyChooseSchAddr:0, //우편번호찾기 : 담당자key
            ooDefaultJson : { //담당자 default form
                'jsonReceiver' : { sno:0, customerSno:0, regManagerSno:0, branchName:'', departmentName:'', managerName:'', managerPhone:'', managerEmail:'', managerAddrPost:'', managerAddr:'', },
            },
            isExcelUpload:false,
        });
        ImsBoneService.setMethod(serviceData, {
            addRow : (iKey)=>{
                if (iKey == -1) vueApp.addElement(vueApp.listData, vueApp.ooDefaultJson.jsonReceiver, 'after');
                else vueApp.addElement(vueApp.listData, vueApp.ooDefaultJson.jsonReceiver, 'down', iKey);
            },
            deleteRow : (sno, iKey)=>{
                if (sno == '' || sno == 0) {
                    vueApp.deleteElement(vueApp.listData, iKey);
                } else {
                    $.msgConfirm('정말 삭제 하시겠습니까? (복구 불가능)','').then(function(result){
                        if( result.isConfirmed ){
                            ImsNkService.setDelete('jjjjj', sno).then(()=>{
                                vueApp.deleteElement(vueApp.listData, iKey);
                            });
                        }
                    });
                }
            },
            save_receiver : ()=>{
                if (vueApp.listData.length == 0) {
                    $.msg('담당자를 추가해 주시기 바랍니다.','','warning');
                    return false;
                }
                let bFlagErr = false;
                $.each(vueApp.listData, function (key, val) {
                    if (val.managerName == '' || val.managerAddr == '') {
                        bFlagErr = true;
                        return false;
                    }
                });
                if (bFlagErr === true) {
                    $.msg('담당자명, 주소는 필수입력값입니다.','','warning');
                    return false;
                }

                $.imsPost('setCustomerReceiver', {'list':vueApp.listData}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        $.msg('저장 완료','','success').then(()=>{
                            vueApp.refreshList(1);
                            vueApp.isModify = false;
                        });
                    });
                });
            },
            //엑셀 업로드
            listUpload : ()=>{
                if (document.getElementsByName('excel')[0].value == '') {
                    $.msg('업로드할 엑셀파일을 첨부하세요','','error');
                    return false;
                }
                $.msgConfirm('기존에 저장된 담당자는 삭제됩니다 (복구 불가능)','진행하시겠습니까?').then(function(result){
                    if( result.isConfirmed ){
                        document.getElementsByName('frmRegistReceiver')[0].submit();
                    }
                });
            },
            //엑셀 다운로드
            listDownload : ()=>{
                location.href='ics_order.php?simple_excel_download=1';
            },

        });

        ImsBoneService.setMounted(serviceData, ()=>{
            $('.bootstrap-filestyle')[0].remove();
        });

        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData); //style , storedSearchCondition
        listService.init(serviceData);
    });

</script>
<?php } ?>