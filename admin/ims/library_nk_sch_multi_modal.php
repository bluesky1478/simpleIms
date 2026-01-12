<script type="text/javascript">
    //모듈 적용방법
    //1. <section id="imsApp"> 안에 './admin/ims/library_nk_sch_multi_modal.php' 파일 include
    //2. ImsBoneService.setData() 할때 안에다가 schListMultiNk : schListMultiModalServiceNk.objDefault 넣기(모듈 안에서 선언한 객체변수들을 vueapp에 집어넣음)
    //3. 버튼에 다음 함수를 호출하도록 @click에 추가 : schListMultiModalServiceNk.popup(args1,args2,args3,args4,args5,args6);
    //3-1. args1(type=obj) : modal창 css설정. {title:'원단 검색', width:1300, height:700, top:100, left:400} 형태로 전달, {} 처럼 빈 obj로 보내주면 default로 설정한 값 반영함
    //3-2. args2(type=string) : 리스트를 가져오는 메소드명
    //3-3. args3(type=obj) : target. 붙여넣을 대상이 되는 1개row object or array (ex> v-for="(fabric, fabricIndex) in sampleView.fabric" 일때 fabric을 전달)
    //3-4. args4(type=obj) : 붙여넣을 target(4번째 arg)의 필드명과 DB컬럼명을 매칭한 obj {필드명1:컬럼명(or alias명)1, 필드명2:컬럼명(or alias명)2, ......}
    //3-5. args5(type=obj) : default검색필터값 {검색필터변수명1:검색값1(string or array), 검색필터변수명2:검색값2(string or array), .....}
    //3-6. args6(type=string) : 마지막단계(검색리스트에서 원하는 항목을 클릭) 때 호출하는 콜백함수명
    //4. 리스트 가져오는 backend 메소드 : 특정 배열($params['require_fld_list'])을 파라메터로 받으면 return [fieldData=>arr] 를 재구성하는 소스 추가필요

    
    const getListDataMultiNk = async ()=>{
        let onk = vueApp.schListMultiNk;
        //namku(chk) 내가 만든 리스트post 방식은 아래 if문에 추가하기
        let oPost = {};
        if (onk.sListMethodName == 'material' || onk.sListMethodName == 'basicFormProposalGuide') oPost = ImsNkService.getList(onk.sListMethodName, onk.searchCondition);
        else oPost = $.imsPost(onk.sListMethodName, onk.searchCondition);
        oPost.then((data)=>{
            $.imsPostAfter(data, (data)=> {
                onk.listTotal = data.page;
                onk.fieldData = data.fieldData;
                onk.listData = data.list;
                onk.pageHtml = data.pageEx;
                vueApp.$nextTick(function () {
                    $.each($('#sch-list-multi-nk-paging .pagination').find('a'), function(){
                        $(this).off('click').on('click',function(){
                            refreshListMultiNk($(this).data('page'));
                        });
                    });
                });
            });
        });
    };
    function refreshListMultiNk(iPage) {
        vueApp.schListMultiNk.searchCondition.page = iPage;
        getListDataMultiNk();
    }
    //검색결과리스트의 필드정리
    var oSchMultiListFlds = {};

    //레이어팝업 -> 리스트 검색 -> 1ROW 클릭하면 미리 설정한 변수에 값을 담는 모듈
    const schListMultiModalServiceNk = {
        init: ()=>{
            //default obj 선언 -> vue.js open할때 이 obj를 vueApp.schListMultiNk에 담는다
            schListMultiModalServiceNk.objDefault = {
                'modal_info': {
                    'title':'리스트 검색(다중선택)','width':1300,'height':950,'top':75,'left':0,
                },
                'sListMethodName': '', //리스트 가져오는 메소드명
                'aoTarget' : {}, //갖다붙일 배열/OBJ
                'oMatch' : {}, //컬럼명, 갖다붙일 필드명 match
                'sCallbackFnName' : '', //콜백함수명
                'searchCondition' : {}, //검색필터
                'listTotal' : {recode:{total:0}},
                'fieldData' : {},
                'listData' : {},
                'pageHtml' : '', //페이징 html내용 담을 변수
                'aoChooseRows' : [], //검색리스트에서 항목을 선택시 담게 되는 배열
                'bFlagSchExpand' : false, //자재리스트 : 검색필터에서 상세검색 show flag
            }
        },
        popup: (oModalProp={}, sListMethodNm='', aoTarget={}, oMatchFldNm={}, oDefaultSch={}, sCallbackFnNm='')=>{
            if (sListMethodNm == undefined || sListMethodNm == '') return false;

            if (sListMethodNm == 'material') {
                //자재검색 : 품목구분 가져오는 메소드 호출
                ImsNkService.getList('materialTypeDetail', {}).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        let oTmp = {};
                        $.each(data.list, function (key, val) {
                            if (oTmp[val.materialTypeByDetail] == undefined) oTmp[val.materialTypeByDetail] = val.materialTypeHan;
                        });
                        schListMultiModalServiceNk.objDefault.aoMaterialTypeList = oTmp;
                        schListMultiModalServiceNk.objDefault.aoMaterialTypeTextList = data.list;
                    });
                });
            }

            //전달받은 args들 vue객체변수에 넣기
            let onk = vueApp.schListMultiNk;
            onk.sListMethodName = sListMethodNm;
            onk.sCallbackFnName = sCallbackFnNm;
            $.each(onk.modal_info, function(key, val) {
                if (oModalProp[key] != undefined) onk.modal_info[key] = oModalProp[key];
            });
            onk.aoTarget = aoTarget; //list(선택한 항목들을 이 배열에 추가해줌)
            onk.oMatch = oMatchFldNm; //{타겟의필드명:DB컬럼명(or alias), .....}
            onk.aoChooseRows = []; //선택한 항목리스트

            //검색필터 초기화. namku(chkd) 검색대상에 따라 multiKey[].key 값 체크필요
            onk.searchCondition = { page : 1, pageNum : 10, sort : 'D,desc', multiKey:[{key: 'a.name', keyword: ''}], multiCondition:'OR', };
            //검색필터 초기값 세팅 and 검색결과리스트의 필드정리
            if (sListMethodNm == 'material') {
                onk.searchCondition.sRadioSchMaterialTypeByDetail = '';
                onk.searchCondition.sRadioSchMaterialTypeText = '';
                onk.searchCondition.aChkboxSumSchUsedStyle = [];
                onk.searchCondition.aChkboxSchMakeNational = [];
                onk.searchCondition.aChkboxSchOnHandYn = '';
                onk.searchCondition.sRadioSchBtYn = '';
                onk.searchCondition.sTextboxRangeStartSchUnitPrice = '';
                onk.searchCondition.sTextboxRangeEndSchUnitPrice = '';
                onk.searchCondition.sTextboxSchOrdererItemNo = '';
                onk.searchCondition.sTextboxSchOrdererItemName = '';
                onk.searchCondition.aChkboxSchMixRatio = [];
                onk.searchCondition.sTextboxRangeStartSchWeight = '';
                onk.searchCondition.sTextboxRangeEndSchWeight = '';
                onk.searchCondition.aChkboxSchAfterMake = [];
                onk.searchCondition.aChkboxSchFastness = [];
                onk.searchCondition.sTextboxSchMerit = '';
                onk.searchCondition.sTextboxSchDisadv = '';
                onk.searchCondition.sTextboxSchAfterIssue = '';

                //검색결과리스트의 필드정리
                oSchMultiListFlds = {'name':'자재명', 'materialTypeText':'품목 구분', 'unitPrice':'단가', 'mixRatio':'혼용율', 'customerName':'매입처'};
            } else if (sListMethodNm == 'basicFormProposalGuide') {
                oSchMultiListFlds = {'guideName':'가이드명', 'guideDesc':'설명', 'guideFileUrl':'이미지'};
            }

            //backend단(records select)에 보내줄 필요컬럼들 정리
            let aRequireFldList = [];
            onk.searchCondition.require_fld_list = [];
            $.each(onk.oMatch, function(key, val) {
                aRequireFldList.push(val);
            });
            onk.searchCondition.require_fld_list = aRequireFldList;

            //args5(default검색필터값) 적용(setSchTmpl() 함수에서 선언한 필드명만 유효) and 리스트 가져오는 메소드 실행
            if (Object.keys(oDefaultSch).length > 0) {
                $.each(oDefaultSch, function(key, val) {
                    if (onk.searchCondition[key] != undefined) onk.searchCondition[key] = val;
                });
                getListDataMultiNk();
            } else {
                //default 검색필터값 없어도 리스트 가져오기(전체리스트)
                getListDataMultiNk();
            }

            $('#modal_sch_list_multi_service').modal('show');
        },

        setMatch: () => {
            let onk = vueApp.schListMultiNk;
            let oTmpChoose = {};
            //z-index 때문에 msg창 안보임
            // if (onk.aoChooseRows.length == 0) {
            //     $.msg('좌측리스트에서 항목을 선택하셔야 완료됩니다.','','warning');
            //     return false;
            // }

            $.each(onk.aoChooseRows, function(key, val) {
                oTmpChoose = {};
                $.each(onk.oMatch, function(key2, val2) {
                    oTmpChoose[key2] = val[val2];
                });
                onk.aoTarget.push(oTmpChoose);
            });

            if (onk.sCallbackFnName != '') {
                eval(onk.sCallbackFnName)(onk.aoTarget);
            }
            $('#modal_sch_list_multi_service').modal('hide');
        },
        //검색필터에서 검색조건 추가시 실행
        addMultiKey : ()=>{
            vueApp.schListMultiNk.searchCondition.multiKey.push($.copyObject(vueApp.schListMultiNk.searchCondition.multiKey[0]));
        },
    };
    schListMultiModalServiceNk.init();
</script>

<!--리스트검색modal-->
<div class="modal fade" id="modal_sch_list_multi_service" tabindex="-1" role="dialog" aria-modal="true" style="z-index:9999;">
    <div class="modal-dialog" role="document" :style="{width:schListMultiNk.modal_info.width+'px', top:schListMultiNk.modal_info.top+'px', left:schListMultiNk.modal_info.left+'px'}">
        <div class="modal-content" :style="{width:schListMultiNk.modal_info.width+'px', height:schListMultiNk.modal_info.height+'px'}">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" style="color:#3f3f3f!important;"></i></button>
                <span class="modal-title">
                    <span v-show="schListMultiNk.modal_info.title!=undefined" class="font-17 font-bold"> {% schListMultiNk.modal_info.title %}</span>
                </span>
            </div>
            <div class="modal-body" style="overflow-y:scroll;" :style="{height:'calc('+schListMultiNk.modal_info.height+'px - 120px)'}">
                <div>
                    <!--검색 시작. namku(chkd) 검색대상에 따라 검색필터box 다르게 표시예정-->
                    <div v-if="schListMultiNk.sListMethodName == 'material'" class="search-detail-box form-inline">
                        <table class="table table-cols table-td-height0">
                            <colgroup>
                                <col class="w-10p">
                                <col class="w-40p">
                                <col class="w-10p">
                                <col class="">
                            </colgroup>
                            <tbody>
                            <tr>
                                <th>검색어</th>
                                <td>
                                    <div v-for="(keyCondition,multiKeyIndex) in schListMultiNk.searchCondition.multiKey" class="mgb5">
                                        검색조건{% multiKeyIndex+1 %} :
                                        <select v-model="keyCondition.key" class="form-control">
                                            <option value="a.name">자재명</option>
                                            <option value="a.code">품목코드</option>
                                            <option value="factoryName">매입처</option>
                                            <option value="">납품 고객사</option>
                                        </select>
                                        <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="refreshListMultiNk(1)" />
                                        <div class="btn btn-sm btn-red" @click="schListMultiModalServiceNk.addMultiKey" v-if="(multiKeyIndex+1) === schListMultiNk.searchCondition.multiKey.length ">+추가</div>
                                        <div class="btn btn-sm btn-gray" @click="schListMultiNk.searchCondition.multiKey.splice(multiKeyIndex, 1)" v-if="schListMultiNk.searchCondition.multiKey.length > 1 ">-제거</div>
                                    </div>
                                    <div class="mgb5">
                                        다중 검색 :
                                        <select class="form-control" v-model="schListMultiNk.searchCondition.multiCondition">
                                            <option value="AND">AND (그리고)</option>
                                            <option value="OR">OR (또는)</option>
                                        </select>
                                    </div>
                                </td>
                                <th>원단 생산국</th>
                                <td>
                                    <label class="checkbox-inline mgr10">
                                        <input type="checkbox" name="aChkboxSchMakeNational[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchMakeNational[]"  :checked="0 >= schListMultiNk.searchCondition.aChkboxSchMakeNational.length?'checked':''" @click="schListMultiNk.searchCondition.aChkboxSchMakeNational=[]"> 전체
                                    </label>
                                    <?php foreach( \Component\Ims\ImsCodeMap::PRD_NATIONAL_CODE as $k => $v){ ?>
                                        <label class="mgr10">
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchMakeNational[]" value="<?=$k?>"  v-model="schListMultiNk.searchCondition.aChkboxSchMakeNational"> <?=$v?>
                                        </label>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <th>품목 구분</th>
                                <td>
                                    <select v-model="schListMultiNk.searchCondition.sRadioSchMaterialTypeByDetail" @change="schListMultiNk.searchCondition.sRadioSchMaterialTypeText = ''; refreshListMultiNk(1)" class="form-control">
                                        <option value="">타입 전체</option>
                                        <option v-for="(val, key) in schListMultiNk.aoMaterialTypeList" :value="key">{% val %}</option>
                                    </select>
                                    <select v-model="schListMultiNk.searchCondition.sRadioSchMaterialTypeText" @change="refreshListMultiNk(1)" class="form-control">
                                        <option value="">품목 전체</option>
                                        <option v-show="val.materialTypeByDetail == schListMultiNk.searchCondition.sRadioSchMaterialTypeByDetail" v-for="(val, key) in schListMultiNk.aoMaterialTypeTextList" :value="val.materialTypeText">{% val.materialTypeText %}</option>
                                    </select>
                                </td>
                                <th>생지 보유 현황</th>
                                <td>
                                    <label class="checkbox-inline mgr10">
                                        <input type="checkbox" name="aChkboxSchOnHandYn[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchOnHandYn[]"  :checked="0 >= schListMultiNk.searchCondition.aChkboxSchOnHandYn.length?'checked':''" @click="schListMultiNk.searchCondition.aChkboxSchOnHandYn=[]"> 전체
                                    </label>
                                    <?php foreach( \Component\Ims\NkCodeMap::MATERIAL_ON_HAND as $k => $v){ ?>
                                        <label class="mgr10">
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchOnHandYn[]" value="<?=$k?>"  v-model="schListMultiNk.searchCondition.aChkboxSchOnHandYn"> <?=$v?>
                                        </label>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <th>사용 스타일</th>
                                <td>
                                    <div v-if="schListMultiNk.searchCondition.aChkboxSumSchUsedStyle !== undefined" class="checkbox">
                                        <div>
                                            <label class="checkbox-inline mgr10">
                                                <input type="checkbox" name="aChkboxSumSchUsedStyle[]" value="all" class="js-not-checkall" data-target-name="aChkboxSumSchUsedStyle[]"  :checked="0 >= schListMultiNk.searchCondition.aChkboxSumSchUsedStyle.length?'checked':''" @click="schListMultiNk.searchCondition.aChkboxSumSchUsedStyle=[]"> 전체
                                            </label>
                                            <?php foreach( \Component\Ims\NkCodeMap::MATERIAL_USED_STYLE as $k => $v){ ?>
                                                <label class="mgr10">
                                                    <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSumSchUsedStyle[]" value="<?=$k?>"  v-model="schListMultiNk.searchCondition.aChkboxSumSchUsedStyle"> <?=$v?>
                                                </label>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </td>
                                <th>BT 준비 현황</th>
                                <td>
                                    <label class="radio-inline ">
                                        <input type="radio" name="sRadioSchBtYn" value="all" v-model="schListMultiNk.searchCondition.sRadioSchBtYn"  />전체
                                    </label>
                                    <?php foreach( \Component\Ims\NkCodeMap::MATERIAL_BT_YN as $k => $v){ ?>
                                        <label class="radio-inline">
                                            <input type="radio" name="sRadioSchBtYn" value="<?=$k?>" v-model="schListMultiNk.searchCondition.sRadioSchBtYn"/><?=$v?>
                                        </label>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <span v-show="!schListMultiNk.bFlagSchExpand" @click="schListMultiNk.bFlagSchExpand = true;" class="btn btn-blue">상세검색 펼침 ▽</span>
                                    <span v-show="schListMultiNk.bFlagSchExpand" @click="schListMultiNk.bFlagSchExpand = false;" class="btn btn-blue-line">상세검색 접기 ▲</span>
                                </td>
                                <th>단가 검색</th>
                                <td>
                                    <input type="text" class="form-control" v-model="schListMultiNk.searchCondition.sTextboxRangeStartSchUnitPrice" placeholder="단가">이상 ~ <input type="text" class="form-control" v-model="schListMultiNk.searchCondition.sTextboxRangeEndSchUnitPrice" placeholder="단가">이하
                                </td>
                            </tr>
                            <tr v-show="schListMultiNk.bFlagSchExpand">
                                <th>발주처 ITEM NO</th>
                                <td>
                                    <input type="text" class="form-control" v-model="schListMultiNk.searchCondition.sTextboxSchOrdererItemNo" placeholder="발주처 ITEM NO 검색" />
                                </td>
                                <th>발주처 ITEM NAME</th>
                                <td>
                                    <input type="text" class="form-control" v-model="schListMultiNk.searchCondition.sTextboxSchOrdererItemName" placeholder="발주처 ITEM NAME 검색" />
                                </td>
                            </tr>
                            <tr v-show="schListMultiNk.bFlagSchExpand">
                                <th>혼용률</th>
                                <td colspan="3">
                                    <label class="checkbox-inline mgr10">
                                        <input type="checkbox" name="aChkboxSchMixRatio[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchMixRatio[]"  :checked="0 >= schListMultiNk.searchCondition.aChkboxSchMixRatio.length?'checked':''" @click="schListMultiNk.searchCondition.aChkboxSchMixRatio=[]"> 전체
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchMixRatio[]" value="폴리"  v-model="schListMultiNk.searchCondition.aChkboxSchMixRatio" /> 폴리
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchMixRatio[]" value="나일론"  v-model="schListMultiNk.searchCondition.aChkboxSchMixRatio" /> 나일론
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchMixRatio[]" value="면"  v-model="schListMultiNk.searchCondition.aChkboxSchMixRatio" /> 면
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchMixRatio[]" value="울(모)"  v-model="schListMultiNk.searchCondition.aChkboxSchMixRatio" /> 울(모)
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchMixRatio[]" value="레이온"  v-model="schListMultiNk.searchCondition.aChkboxSchMixRatio" /> 레이온
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchMixRatio[]" value="스판"  v-model="schListMultiNk.searchCondition.aChkboxSchMixRatio" /> 스판
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchMixRatio[]" value="기모"  v-model="schListMultiNk.searchCondition.aChkboxSchMixRatio" /> 기모
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchMixRatio[]" value="아크릴"  v-model="schListMultiNk.searchCondition.aChkboxSchMixRatio" /> 아크릴
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchMixRatio[]" value="캐시미어"  v-model="schListMultiNk.searchCondition.aChkboxSchMixRatio" /> 캐시미어
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchMixRatio[]" value="모달"  v-model="schListMultiNk.searchCondition.aChkboxSchMixRatio" /> 모달
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchMixRatio[]" value="린넨"  v-model="schListMultiNk.searchCondition.aChkboxSchMixRatio" /> 린넨
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchMixRatio[]" value="텐셀"  v-model="schListMultiNk.searchCondition.aChkboxSchMixRatio" /> 텐셀
                                    </label>
                                </td>
                            </tr>
                            <tr v-show="schListMultiNk.bFlagSchExpand">
                                <th>중량 검색</th>
                                <td colspan="3">
                                    <input type="text" class="form-control" v-model="schListMultiNk.searchCondition.sTextboxRangeStartSchWeight" placeholder="중량">g 이상 ~ <input type="text" class="form-control" v-model="schListMultiNk.searchCondition.sTextboxRangeEndSchWeight" placeholder="중량">g 이하
                                </td>
                            </tr>
                            <tr v-show="schListMultiNk.bFlagSchExpand">
                                <th>후가공</th>
                                <td>
                                    <label class="checkbox-inline mgr10">
                                        <input type="checkbox" name="aChkboxSchAfterMake[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchAfterMake[]"  :checked="0 >= schListMultiNk.searchCondition.aChkboxSchAfterMake.length?'checked':''" @click="schListMultiNk.searchCondition.aChkboxSchAfterMake=[]"> 전체
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchAfterMake[]" value="흡한속건"  v-model="schListMultiNk.searchCondition.aChkboxSchAfterMake" /> 흡한속건
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchAfterMake[]" value="TPU"  v-model="schListMultiNk.searchCondition.aChkboxSchAfterMake" /> TPU
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchAfterMake[]" value="WR"  v-model="schListMultiNk.searchCondition.aChkboxSchAfterMake" /> WR
                                    </label>
                                </td>
                                <th class="text-danger">견뢰도 (개발:확인필요)</th>
                                <td>
                                    <label class="checkbox-inline mgr10">
                                        <input type="checkbox" name="aChkboxSchFastness[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchFastness[]"  :checked="0 >= schListMultiNk.searchCondition.aChkboxSchFastness.length?'checked':''" @click="schListMultiNk.searchCondition.aChkboxSchFastness=[]"> 전체
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchFastness[]" value="안감"  v-model="schListMultiNk.searchCondition.aChkboxSchFastness" /> 안감
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchFastness[]" value="메쉬"  v-model="schListMultiNk.searchCondition.aChkboxSchFastness" /> 메쉬
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchFastness[]" value="웰론백"  v-model="schListMultiNk.searchCondition.aChkboxSchFastness" /> 웰론백
                                    </label>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchFastness[]" value="다운백"  v-model="schListMultiNk.searchCondition.aChkboxSchFastness" /> 다운백
                                    </label>
                                </td>
                            </tr>
                            <tr v-show="schListMultiNk.bFlagSchExpand">
                                <th>장점</th>
                                <td colspan="3">
                                    <input type="text" v-model="schListMultiNk.searchCondition.sTextboxSchMerit" placeholder="장점" class="form-control" style="width:100%;" />
                                </td>
                            </tr>
                            <tr v-show="schListMultiNk.bFlagSchExpand">
                                <th>단점</th>
                                <td colspan="3">
                                    <input type="text" v-model="schListMultiNk.searchCondition.sTextboxSchDisadv" placeholder="단점" class="form-control" style="width:100%;" />
                                </td>
                            </tr>
                            <tr v-show="schListMultiNk.bFlagSchExpand">
                                <th>납품 후 이슈</th>
                                <td colspan="3">
                                    <input type="text" v-model="schListMultiNk.searchCondition.sTextboxSchAfterIssue" placeholder="납품 후 이슈" class="form-control" style="width:100%;" />
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--검색 끝 -->
                    <div v-if="schListMultiNk.sListMethodName != 'basicFormProposalGuide'" style="text-align: center;">
                        <input type="submit" value="검색" class="btn btn-lg btn-black" @click="refreshListMultiNk(1)" />
                        <input type="submit" value="닫기" class="btn btn-lg btn-white" data-dismiss="modal" />
                    </div>
                </div>

                <table class="w-100p mgt30">
                    <colgroup>
                        <col class="w-50p">
                        <col class="w-10px">
                        <col class="">
                    </colgroup>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="">
                                <div class="flo-left mgb5">
                                    <div class="font-16 dp-flex" >
                                        <span style="font-size: 18px !important;">
                                            TOTAL <span class="bold text-danger pdl5">{% $.setNumberFormat(schListMultiNk.listTotal.recode.total) %}</span> 건
                                        </span>
                                    </div>
                                </div>
                                <div class="flo-right mgb5">
                                    <div class="" style="display: flex; ">
                                        <select v-show="schListMultiNk.sListMethodName != 'basicFormProposalGuide'" @change="refreshListMultiNk(1)" class="form-control mgl5" v-model="schListMultiNk.searchCondition.sort">
                                            <option value="D,asc">등록일시 ▲</option>
                                            <option value="D,desc">등록일시 ▼</option>
                                        </select>
                                        <select @change="refreshListMultiNk(1)" v-model="schListMultiNk.searchCondition.pageNum" class="form-control mgl5">
                                            <option value="5">5개 보기</option>
                                            <option value="10">10개 보기</option>
                                            <option value="20">20개 보기</option>
                                            <option value="50">50개 보기</option>
                                            <option value="100">100개 보기</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--list start-->
                            <div>
                                <table class="table table-rows table-default-center table-td-height30 mgt5 ">
                                    <colgroup>
                                        <col class="w-50px"/>
                                        <col v-for="(val, key) in oSchMultiListFlds" />
                                    </colgroup>
                                    <tr>
                                        <th>선택</th>
                                        <th v-for="(val, key) in oSchMultiListFlds">
                                            {% val %}
                                        </th>
                                    </tr>
                                    <tr v-if="schListMultiNk.listData.length == 0">
                                        <td colspan="99">
                                            데이터가 없습니다.
                                        </td>
                                    </tr>
                                    <tr v-else v-for="(val, key) in schListMultiNk.listData">
                                        <td>
                                            <span @click="schListMultiNk.aoChooseRows.push($.copyObject(val));" class="btn btn-white btn-sm">선택</span>
                                        </td>
                                        <td v-for="(val2, key2) in oSchMultiListFlds">
                                            <div v-if="key2 == 'name'" @click="openCommonPopup('upsert_material', 1400, 910, {'sno':val.sno, 'type':val.materialType});" class="ta-l sl-blue cursor-pointer hover-btn">{% val[key2] %}</div>
                                            <div v-else-if="key2 == 'guideFileUrl'" @click="$refs.textImageViewSchMulti.innerHTML=val.guideName; $refs.imageImageViewSchMulti.src=val[key2]; $('#modalImageViewSchMulti').modal('show')" class="cursor-pointer hover-btn"><img :src="val[key2]" style="height:100px;" /></div>
                                            <div v-else>{% val[key2] %}</div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <!--list end-->
                            <div id="sch-list-multi-nk-paging" v-html="schListMultiNk.pageHtml" class="ta-c"></div>
                        </td>
                        <td></td>
                        <td style="vertical-align: top;">
                            <div class="">
                                <div class="flo-left mgb5">
                                    <div class="font-16 dp-flex" >
                                        <span style="font-size: 18px !important;">
                                            선택 리스트 <span @click="schListMultiModalServiceNk.setMatch();" class="btn btn-red">선택 완료</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-rows table-default-center table-td-height30 mgt5 ">
                                <colgroup>
                                    <col class="w-50px"/>
                                    <col v-for="(val, key) in oSchMultiListFlds" />
                                </colgroup>
                                <tr>
                                    <th>제외</th>
                                    <th v-for="(val, key) in oSchMultiListFlds">
                                        {% val %}
                                    </th>
                                </tr>
                                <tr  v-if="0 >= schListMultiNk.aoChooseRows.length">
                                    <td colspan="99">
                                        좌측 리스트에서 선택해 주세요.
                                    </td>
                                </tr>
                                <tr v-for="(val, key) in schListMultiNk.aoChooseRows">
                                    <td>
                                        <span @click="deleteElement(schListMultiNk.aoChooseRows, key)" class="btn btn-white btn-sm">제외</span>
                                    </td>
                                    <td v-for="(val2, key2) in oSchMultiListFlds">
                                        <div v-if="key2 == 'guideFileUrl'" @click="$refs.textImageViewSchMulti.innerHTML=val.guideName; $refs.imageImageViewSchMulti.src=val[key2]; $('#modalImageViewSchMulti').modal('show')" class="cursor-pointer hover-btn"><img :src="val[key2]" style="height:100px;" /></div>
                                        <div v-else>{% val[key2] %}</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <span @click="schListMultiModalServiceNk.setMatch();" class="btn btn-red">선택 완료</span>
                <div class="btn btn-gray" data-dismiss="modal">닫기</div>
            </div>
        </div>
    </div>
</div>

<!--제안서가이드(basicFormProposalGuide) 이미지 크게보기-->
<div class="modal fade" id="modalImageViewSchMulti" tabindex="-1" role="dialog"  aria-hidden="true" style="z-index:999999999">
    <div class="modal-dialog" role="document" :style="{width:schListMultiNk.modal_info.width+'px', top:schListMultiNk.modal_info.top+'px', left:schListMultiNk.modal_info.left+'px'}">
        <div class="modal-content" :style="{width:schListMultiNk.modal_info.width+'px', height:schListMultiNk.modal_info.height+'px'}">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                <span ref="textImageViewSchMulti" class="modal-title font-18 bold" ></span>
            </div>
            <div class="modal-body ta-c">
                <img ref="imageImageViewSchMulti" src="" style="max-width:100%;" />
            </div>
            <div class="modal-footer ">
                <div class="btn btn-white hover-btn btn-lg mg5" data-dismiss="modal">닫기</div>
            </div>
        </div>
    </div>
</div>