<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
        <h3 class="">고객사 담당자 관리</h3>
        <div class="btn-group font-18 bold">
            <span @click="modifyMainContact(1);" class="btn btn-accept hover-btn btn-lg mg5" style="line-height: 38px;">메인담당자로 지정</span>
        </div>
    </div>
    <div>
        <table class="table table-rows table-default-center table-td-height30 mgt5 font-11" v-if="!$.isEmpty(searchData)" :id="'list-main-table'">
            <colgroup>
                <col class="w-4p" />
                <col class="w-5p" />

                <col class="w-8p" />
                <col class="w-7p" />
                <col class="w-13p" />
                <col class="w-9p" />
                <col class="w-25p" />
                <col class="w-15p" />
                <col class="w-11p" />
            </colgroup>
            <tr>
                <th>메인</th>
                <th>번호</th>
                <th v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip" :class="fieldData.titleClass">
                    {% fieldData.title %}
                </th>
                <th>관리</th>
            </tr>
            <tr  v-if="0 >= listData.length">
                <td :colspan="searchData.fieldData.length+3">
                    데이터가 없습니다.
                </td>
            </tr>
            <tr v-for="(val , key) in listData">
                <td>
                    <input type="radio" v-model="sRadioChooseMain" :value="val.sno" name="listRadioMainContact" />
                </td>
                <td >{% (key+1) %}</td>
                <td v-for="fieldData in searchData.fieldData"  v-if="true != fieldData.skip" :class="fieldData.class">
                    <span v-if="fieldData.type === 'modal_contact_detail'">
                        <div class="hover-btn cursor-pointer sl-blue" @click="fillDetailInfo(val);">
                            {% val[fieldData.name] %}
                        </div>
                    </span>
                    <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                </td>
                <td><span @click="fillDetailInfo(val); isModify=true;" class="btn btn-white btn-sm">수정</span> <span @click="fillDetailInfo(val); removeCustomerContact();" class="btn btn-red btn-sm">삭제</span></td>
            </tr>
        </table>
    </div>
    <div class="dp-flex" style="justify-content: center">
        <div class="btn btn-accept hover-btn btn-lg mg5" @click="clearDetailInfo();">등록</div>
        <div class="btn btn-white hover-btn btn-lg mg5" @click="self.close()">닫기</div>
    </div>

    <div class="modal fade" id="modalCustomerContact" tabindex="-1" role="dialog"  aria-hidden="true" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                    <span class="modal-title font-18 bold" >
                    고객사 담당자 상세
                </span>
                </div>
                <div class="modal-body">
                    <table class="table table-cols table-pd-5" >
                        <colgroup>
                            <col class="w-15p">
                            <col class="w-35p">
                            <col class="w-15p">
                            <col class="w-35p">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>담당자명</th>
                            <td>
                                <input type="hidden" v-model="oUpsertContactDetail.sno" />
                                <input type="hidden" v-model="oUpsertContactDetail.customerSno" />
                                <?php $model='oUpsertContactDetail.cContactName'; $placeholder='담당자명' ?>
                                <?php include './admin/ims/template/basic_view/_text.php'?>
                            </td>
                            <th>직함</th>
                            <td>
                                <?php $model='oUpsertContactDetail.cContactPosition'; $placeholder='직함' ?>
                                <?php include './admin/ims/template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>연락처</th>
                            <td>
                                <?php $model='oUpsertContactDetail.cContactMobile'; $placeholder='연락처' ?>
                                <?php include './admin/ims/template/basic_view/_text.php'?>
                            </td>
                            <th>담당자 성향</th>
                            <td>
                                <?php $model='oUpsertContactDetail.cContactPreference'; $placeholder='담당자 성향' ?>
                                <?php include './admin/ims/template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>이메일</th>
                            <td>
                                <?php $model='oUpsertContactDetail.cContactEmail'; $placeholder='이메일' ?>
                                <?php include './admin/ims/template/basic_view/_text.php'?>
                            </td>
                            <th>담당자 비고</th>
                            <td>
                                <?php $model='oUpsertContactDetail.cContactMemo'; $placeholder='담당자 비고' ?>
                                <?php include './admin/ims/template/basic_view/_text.php'?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer ">
                    <div class="btn btn-blue" v-show="!isModify" @click="modifyMainContact(2);">메인담당자 지정</div>
                    <div class="btn btn-red" v-show="!isModify" @click="isModify=true">수정하기</div>
                    <div class="btn btn-black" v-show="!isModify" @click="removeCustomerContact()">삭제하기</div>
                    <div class="btn btn-red" v-show="isModify" @click="save()">저장</div>
                    <div class="btn btn-black" v-show="isModify && oUpsertContactDetail.sno != 0" @click="isModify=false">취소</div>
                    <div class="btn btn-gray" data-dismiss="modal">닫기</div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    const mainListPrefix = 'customer_contact_list';
    const listSearchDefaultData = {
        page : 1,
        pageNum : 999,
        sort : 'D,asc'
    };
    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.mode = 'getListCustomerContact';
        params.customerSno = <?=$iCustomerSno?>;
        return ImsNkService.getList('customerContact', params);
    };
    const setMainContact = async ()=>{
        $.each(vueApp.listData, function (key, val) {
            if (this.mainContactYn == 1) {
                vueApp.sRadioChooseMain = this.sno;
                return false;
            }
        });
    };

    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            isModify : false,
            oUpsertContactDetail : {
                <?php foreach ($aFldNames as $val) { /* 이렇게 setData할때부터 박아놔야 안꼬임(ImsNkService.getList() imsPostAfter에서 이 객체내용 정의하면 값을 재깍재깍 못받아냄) */ ?>
                    <?=$val?>:'',
                <?php } ?>
            },
            sRadioChooseMain : '',
        });

        ImsBoneService.setMethod(serviceData,{
            clearDetailInfo : ()=>{
                $.each(vueApp.oUpsertContactDetail, function(key, val) {
                    vueApp.oUpsertContactDetail[key] = '';
                });
                vueApp.oUpsertContactDetail['customerSno'] = <?=$iCustomerSno?>;

                vueApp.isModify=true;
                $('#modalCustomerContact').modal('show');
            },
            fillDetailInfo : (oTarget)=>{
                $.each(oTarget, function(key, val) {
                    if (vueApp.oUpsertContactDetail[key] != undefined) vueApp.oUpsertContactDetail[key] = val;
                });

                vueApp.isModify=false;
                $('#modalCustomerContact').modal('show');
            },
            save : ()=>{
                if (vueApp.oUpsertContactDetail.cContactName === null || vueApp.oUpsertContactDetail.cContactName == '') {
                    $.msg('담당자명을 입력하세요','','error');
                    return false;
                }
                $.imsPost('setCustomerContact', {'data':vueApp.oUpsertContactDetail}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        listService.refreshList(vueApp.searchCondition.page);
                        isModify=false;
                        $('#modalCustomerContact').modal('hide');
                    });
                });
            },
            //namku(chk) 모든 페이지(고객상세, 프로젝트상세)에서 overwriteCustomerContact 함수 필요없어짐
            // overwriteCustomerContact : ()=>{
            //     parent.opener.overwriteCustomerContact(vueApp.oUpsertContactDetail);
            //     $.msg('선택하신 담당자 정보를 고객정보에 반영했습니다.<br/>고객정보 수정을 하셔야 저장이 완료됩니다.','','success').then(()=>{
            //         self.close();
            //     });
            // },

            //메인담당자로 지정
            modifyMainContact : (iType)=>{
                let iChooseContactSno = 0;
                if (iType == 1) {
                    if (vueApp.sRadioChooseMain == null || vueApp.sRadioChooseMain == '' || Number(vueApp.sRadioChooseMain) == '') {
                        $.msg('담당자를 선택하세요','','error');
                        return false;
                    }
                    iChooseContactSno = Number(vueApp.sRadioChooseMain);
                } else iChooseContactSno = Number(vueApp.oUpsertContactDetail.sno);

                $.msgConfirm('메인담당자로 지정하시겠습니까?', '').then((confirmData)=> {
                    if (true === confirmData.isConfirmed) {
                        //251224 : 버튼 누르면 바로 update로 변경
                        $.imsPost('setCustomerMainContact', {'sno':iChooseContactSno}).then((data)=>{
                            $.imsPostAfter(data,(data)=>{
                                parent.opener.location.reload();
                                $.msg('메인담당자로 지정했습니다.','','success').then(()=>{
                                    self.close();
                                });
                            });
                        });
                    }
                });

                //예전방식 : opner(고객사 수정창)의 값을 변경시키기만 함(고객사정보 수정해야 메인담당자정보 반영)
                // let oTarget = {};
                // $.each(vueApp.listData, function(key, val) {
                //     if (this.sno == vueApp.sRadioChooseMain) oTarget = val;
                // });
                // $.each(oTarget, function(key, val) {
                //     if (vueApp.oUpsertContactDetail[key] != undefined) vueApp.oUpsertContactDetail[key] = val;
                // });
                // parent.opener.overwriteCustomerContact(vueApp.oUpsertContactDetail);
                // $.msg('선택하신 담당자 정보를 고객정보에 반영했습니다.<br/>고객정보 수정을 하셔야 저장이 완료됩니다.','','success').then(()=>{
                //     self.close();
                // });
            },
            removeCustomerContact : ()=>{
                $.msgConfirm('해당 담당자를 정말 삭제하시겠습니까?', '복구가 불가능합니다.').then((confirmData)=> {
                    if (true === confirmData.isConfirmed) {
                        ImsNkService.setDelete('ccccc', vueApp.oUpsertContactDetail.sno).then(()=>{
                            listService.refreshList(vueApp.searchCondition.page);
                            $('#modalCustomerContact').modal('hide');
                        });
                    }
                });
            },
        });
        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData, setMainContact);
        listService.init(serviceData);
    });
</script>



