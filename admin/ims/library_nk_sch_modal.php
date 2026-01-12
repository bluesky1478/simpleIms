<script type="text/javascript">
    //모듈 적용방법
    //1. <section id="imsApp"> 안에 './admin/ims/library_nk_sch_modal.php' 파일 include
    //2. ImsBoneService.setData() 할때 안에다가 schListNk : schListModalServiceNk.objDefault 넣기(모듈 안에서 선언한 객체변수들을 vueapp에 집어넣음)
    //3. 버튼에 다음 함수를 호출하도록 @click에 추가 : schListModalServiceNk.popup(args1,args2,args3,args4,args5,args6);
    //3-1. args1(type=obj) : modal창 css설정. {title:'원단 검색', width:700, height:1300, top:100, left:400} 형태로 전달, {} 처럼 빈 obj로 보내주면 default로 설정한 값 반영함
    //3-2. args2(type=string) : 리스트를 가져오는 메소드명
    //3-3. args3(type=obj) : target. 붙여넣을 대상이 되는 1개row object or array (ex> v-for="(fabric, fabricIndex) in sampleView.fabric" 일때 fabric을 전달)
    //3-4. args4(type=obj) : 붙여넣을 target(4번째 arg)의 필드명과 DB컬럼명을 매칭한 obj {필드명1:컬럼명(or alias명)1, 필드명2:컬럼명(or alias명)2, ......}
    //3-5. args5(type=obj) : default검색필터값 {검색필터변수명1:검색값1(string or array), 검색필터변수명2:검색값2(string or array), .....}
    //3-6. args6(type=string) : 마지막단계(검색리스트에서 원하는 항목을 클릭) 때 호출하는 콜백함수명
    //4. 리스트별 검색필터 설정방법
    //4-1. schListModalServiceNk.setSchTmpl(sListMethodNm) 함수안에서 args2값을 switch조건값으로 검색필터 필드명을 선언해야함
    //4-2. 검색필터가 체크박스처럼 여러 항목들을 뿌려줘야 한다면 전역변수로 opt_arg2변수값_검색필터필드명 을 선언해야함
    //5. 리스트 가져오는 backend 메소드 : 특정 배열($params['require_fld_list'])을 파라메터로 받으면 return [fieldData=>arr] 를 재구성하는 소스 추가필요

    const getListDataNk = async ()=>{
        let onk = vueApp.schListNk;
        //namku(chk) 내가 만든 리스트post 방식은 아래 if문에 추가하기
        let oPost = {};
        //250827 공임/기타비용을 library_nk_sch_modal.php에 추가. git 소스참고
        if (onk.sListMethodName == 'material' || onk.sListMethodName == 'sampleEtcCost' || onk.sListMethodName == 'stylePlan' || onk.sListMethodName == 'customerNk' || onk.sListMethodName == 'stylePlanRef') oPost = ImsNkService.getList(onk.sListMethodName, onk.searchCondition);
        else oPost = $.imsPost(onk.sListMethodName, onk.searchCondition);
        oPost.then((data)=>{
            $.imsPostAfter(data, (data)=> {
                onk.listTotal = data.page;
                onk.fieldData = data.fieldData;
                onk.listData = data.list;
                onk.pageHtml = data.pageEx;
                vueApp.$nextTick(function () {
                    $.each($('#sch-list-nk-paging .pagination').find('a'), function(){
                        $(this).off('click').on('click',function(){
                            refreshListNk($(this).data('page'));
                        });
                    });
                });
            });
        });
    };
    function refreshListNk(iPage) {
        vueApp.schListNk.searchCondition.page = iPage;
        getListDataNk();
    }

    const opt_material_aChkboxSchMaterialType = {
        <?php foreach( \Component\Ims\NkCodeMap::MATERIAL_TYPE as $k => $v){ ?>
            '<?=$k?>':'<?=$v?>',
        <?php } ?>
    };
    const opt_material_aChkboxSchMaterialSt = {
        <?php foreach( \Component\Ims\NkCodeMap::MATERIAL_ST as $k => $v){ ?>
        '<?=$k?>':'<?=$v?>',
        <?php } ?>
    };

    const opt_sampleEtcCost_aChkboxSchCostType = {
        <?php foreach( \Component\Ims\NkCodeMap::SAMPLE_ETC_COST_TYPE as $k => $v){ ?>
        '<?=$k?>':'<?=$v?>',
        <?php } ?>
    };

    const opt_stylePlanRef_aChkboxSchRefGender = {};
    const opt_stylePlanRef_aChkboxSchRefSeason = {};
    const opt_stylePlanRef_aChkboxSumSchRefType = {};
    const opt_stylePlanRef_aChkboxSchRefStyle = {};
    const opt_stylePlanRef_aChkboxSchMate = {materialType:''}; //=== 'opt_stylePlanRef_aChkboxSchMate.materialType'

    //레이어팝업 -> 리스트 검색 -> 1ROW 클릭하면 미리 설정한 변수에 값을 담는 모듈
    const schListModalServiceNk = {
        init: ()=>{
            //default obj 선언 -> vue.js open할때 이 obj를 vueApp.schListNk에 담는다
            schListModalServiceNk.objDefault = {
                'modal_info': {
                    'title':'리스트 검색','width':800,'height':700,'top':75,'left':0,
                },
                'sListMethodName': '',
                'oTarget' : {},
                'oMatch' : {},
                'oSchFilter' : {},
                'oSchFilterHan' : {},
                'oSchFilterOpt' : {},
                'sCallbackFnName' : '',
                'searchCondition' : {},
                'listTotal' : {recode:{total:0}},
                'fieldData' : {},
                'listData' : {},
                'pageHtml' : '',
            }
        },
        popup: (oModalProp={}, sListMethodNm='', oTarget={}, oMatchFldNm={}, oDefaultSch={}, sCallbackFnNm='')=>{
            if (sListMethodNm == undefined || sListMethodNm == '') return false;

            //전달받은 args들 vue객체변수에 넣기
            let onk = vueApp.schListNk;
            onk.sListMethodName = sListMethodNm;
            onk.sCallbackFnName = sCallbackFnNm;
            $.each(onk.modal_info, function(key, val) {
                if (oModalProp[key] != undefined) onk.modal_info[key] = oModalProp[key];
            });
            onk.oTarget = oTarget; //1row obj
            onk.oMatch = oMatchFldNm; //{타겟의필드명:DB컬럼명(or alias), .....}
            //검색필터 초기화
            onk.searchCondition = { page : 1, pageNum : 5, sort : 'D,desc' };
            onk.oSchFilter = {};
            onk.oSchFilterHan = {}; //컬럼의 한글명. 검색필터에 쓰임
            onk.oSchFilterOpt = {}; //checkbox인 경우 opt들 배열로 담겨야 함
            $.each(schListModalServiceNk.setSchTmpl(sListMethodNm), function(key, val) {
                if (val.type === 'text') onk.searchCondition[key] = '';
                else if (val.type === 'radio') onk.searchCondition[key] = 'all';
                else {
                    onk.searchCondition[key] = [];
                    onk.oSchFilterOpt[sListMethodNm+'_'+key] = eval('opt_'+sListMethodNm+'_'+key);
                }
                onk.oSchFilter[key] = val.type;
                onk.oSchFilterHan[key] = val.title;
            });

            //backend단(records select)에 보내줄 필요컬럼들 정리
            let aRequireFldList = [];
            onk.searchCondition.require_fld_list = [];
            $.each(onk.oMatch, function(key, val) {
                aRequireFldList.push(val);
            });
            onk.searchCondition.require_fld_list = aRequireFldList;
            //args5(default검색필터값) 적용(setSchTmpl() 함수에서 선언한 필드명만 유효)
            if (Object.keys(oDefaultSch).length > 0) {
                $.each(oDefaultSch, function(key, val) {
                    if (onk.searchCondition[key] != undefined) onk.searchCondition[key] = val;
                });
            }
            getListDataNk();
            $('#modal_sch_list_service').modal('show');
        },
        //namku(chk) sListMethodNm 새로 추가하면 검색필드도 case문으로 추가해줘야함
        setSchTmpl: (sListMethodNm) => {
            let oReturn = {};
            switch(sListMethodNm) {
                case 'material':
                    oReturn = {
                        'sTextboxSchName':{'title':'자재명', 'type':'text'},
                        'aChkboxSchMaterialType':{'title':'타입', 'type':'checkbox'},
                        'aChkboxSchMaterialSt':{'title':'상태', 'type':'checkbox'},
                    };
                    break;
                case 'sampleEtcCost':
                    oReturn = {
                        'sTextboxSchCostName':{'title':'항목명', 'type':'text'},
                        'aChkboxSchCostType':{'title':'타입', 'type':'checkbox'},
                    };
                    break;
                case 'stylePlan':
                    oReturn = {
                        'sRadioSchStyleSno':{'title':'스타일 일련번호', 'type':'text'},
                        'sTextboxSchPlanConcept':{'title':'디자인 컨셉', 'type':'text'},
                    };
                    break;
                case 'customerNk':
                    oReturn = {
                        'sTextboxSchCustomerName':{'title':'고객사명', 'type':'text'},
                        'sTextboxSchSales.managerNm':{'title':'영업담당자', 'type':'text'},
                    };
                    break;
                case 'stylePlanRef':
                    oReturn = {
                        'sTextboxSchRefName':{'title':'레퍼런스명', 'type':'text'},
                        'aChkboxSchRefGender':{'title':'성별', 'type':'checkbox'},
                        'aChkboxSchRefSeason':{'title':'시즌', 'type':'checkbox'},
                        'aChkboxSumSchRefType':{'title':'타입', 'type':'checkbox'},
                        'aChkboxSchRefStyle':{'title':'스타일코드', 'type':'checkbox'},
                        'sTextboxRangeStartSchRefUnitPrice':{'title':'제품 단가', 'type':'text'},
                        'sTextboxRangeEndSchRefUnitPrice':{'title':'제품 단가', 'type':'text'},
                        'sTextboxRangeStartSchMainFabricUnitPrice':{'title':'메인원단 단가', 'type':'text'},
                        'sTextboxRangeEndSchMainFabricUnitPrice':{'title':'메인원단 단가', 'type':'text'},
                        'sRadioSchMainFabricOnHandYn':{'title':'생지유무', 'type':'radio'},
                        'sTextboxSchMate.materialName':{'title':'원부자재 자재명', 'type':'text'},
                        'aChkboxSchMate.materialType':{'title':'원부자재 타입', 'type':'checkbox'},
                        'sTextboxSchMate.fabricMix':{'title':'원부자재 혼용률', 'type':'text'},
                        'sTextboxSchAfterMake':{'title':'원부자재 후가공', 'type':'text'},
                    };
                    break;

                default:
                    oReturn = {};
                    break;
            }
            return oReturn;
        },
        setMatch: (iKey) => {
            let onk = vueApp.schListNk;
            $.each(onk.oMatch, function(key, val) { //popup()함수 실행시 던져준 args4의 key name에만 값 넣음
                // if (onk.oTarget[key] != undefined) //이 조건 걸면 제대로 값 안넣음. 왜 원단에서 함수호출했는데 onk.oTarget log찍어보면 부자재 구조임???(원단 필드명 없어보임)
                onk.oTarget[key] = onk.listData[iKey][val];
            });
            if (onk.sCallbackFnName != '') {
                eval(onk.sCallbackFnName)(onk.oTarget);
            }
            $('#modal_sch_list_service').modal('hide');
        },
    };
    schListModalServiceNk.init();
</script>

<!--리스트검색modal-->
<div class="modal fade" id="modal_sch_list_service" tabindex="-1" role="dialog" aria-modal="true" style="z-index:99999999999999999;">
    <div class="modal-dialog" role="document" :style="{width:schListNk.modal_info.width+'px', top:schListNk.modal_info.top+'px', left:schListNk.modal_info.left+'px'}">
        <div class="modal-content" :style="{width:schListNk.modal_info.width+'px', height:schListNk.modal_info.height+'px'}">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" style="color:#3f3f3f!important;"></i></button>
                <span class="modal-title">
                    <span v-show="schListNk.modal_info.title!=undefined" class="font-17 font-bold"> {% schListNk.modal_info.title %}</span>
                </span>
            </div>
            <div class="modal-body" style="overflow-y:scroll;" :style="{height:'calc('+schListNk.modal_info.height+'px - 120px)'}">
                <div>
<!--                    검색 시작-->
                    <div class="search-detail-box form-inline">
                        <table v-if="schListNk.sListMethodName == 'stylePlanRef'" class="table table-cols table-td-height0">
                            <colgroup>
                                <col class="w-120px">
                                <col class="">
                                <col class="w-120px">
                                <col class="">
                            </colgroup>
                            <tbody>
                            <tr>
                                <th>레퍼런스명</th>
                                <td>
                                    <input type="text" v-model="schListNk.searchCondition.sTextboxSchRefName" value="" class="form-control" placeholder="레퍼런스명" />
                                </td>
                                <th>성별</th>
                                <td >
                                    <label class="checkbox-inline " >
                                        <input type="checkbox" name="aChkboxSchRefGender[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchRefGender[]"
                                               :checked="0 >= schListNk.searchCondition.aChkboxSchRefGender.length?'checked':''" @click="schListNk.searchCondition.aChkboxSchRefGender=[]" /> 전체
                                    </label>
                                    <?php foreach( \Component\Ims\NkCodeMap::PRODUCT_PLAN_GENDER as $k => $v){ ?>
                                        <label class="mgl10" >
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchRefGender[]" value="<?=$k?>"  v-model="schListNk.searchCondition.aChkboxSchRefGender" />
                                            <?=$v?>
                                        </label>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <th>시즌</th>
                                <td>
                                    <label class="checkbox-inline " >
                                        <input type="checkbox" name="aChkboxSchRefSeason[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchRefSeason[]"
                                               :checked="0 >= schListNk.searchCondition.aChkboxSchRefSeason.length?'checked':''" @click="schListNk.searchCondition.aChkboxSchRefSeason=[]" /> 전체
                                    </label>
                                    <?php foreach($seasonList as $k => $v){ ?>
                                        <label class="mgl10" >
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchRefSeason[]" value="<?=$k?>"  v-model="schListNk.searchCondition.aChkboxSchRefSeason" />
                                            (<?=$k?>) <?=$v?>
                                        </label>
                                    <?php } ?>
                                </td>
                                <th>타입</th>
                                <td>
                                    <label class="checkbox-inline " >
                                        <input type="checkbox" name="aChkboxSumSchRefType[]" value="all" class="js-not-checkall" data-target-name="aChkboxSumSchRefType[]"
                                               :checked="schListNk.searchCondition.aChkboxSumSchRefType.length == 0?'checked':''" @click="schListNk.searchCondition.aChkboxSumSchRefType=[]" /> 전체
                                    </label>
                                    <?php foreach( \Component\Ims\NkCodeMap::REF_PRODUCT_PLAN_TYPE as $k => $v){ if ($k > 0) { ?>
                                        <label class="mgl10" >
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSumSchRefType[]" value="<?=$k?>"  v-model="schListNk.searchCondition.aChkboxSumSchRefType" />
                                            <?=$v?>
                                        </label>
                                    <?php }} ?>
                                </td>
                            </tr>
                            <tr>
                                <th>스타일코드</th>
                                <td colspan="3">
                                    <label class="checkbox-inline " >
                                        <input type="checkbox" name="aChkboxSchRefStyle[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchRefStyle[]"
                                               :checked="0 >= schListNk.searchCondition.aChkboxSchRefStyle.length?'checked':''" @click="schListNk.searchCondition.aChkboxSchRefStyle=[]" /> 전체
                                    </label>
                                    <?php foreach($codeStyle as $k => $v){ ?>
                                        <label class="mgl10" >
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchRefStyle[]" value="<?=$k?>"  v-model="schListNk.searchCondition.aChkboxSchRefStyle" />
                                            <?=$v?>
                                        </label>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <th>제품 단가</th>
                                <td colspan="3">
                                    <div class="dp-flex">
                                        <div class="mini-picker mgl5">
                                            <input type="number" v-model="schListNk.searchCondition.sTextboxRangeStartSchRefUnitPrice" class="form-control" />
                                        </div>
                                        <div>~</div>
                                        <div class="mini-picker">
                                            <input type="number" v-model="schListNk.searchCondition.sTextboxRangeEndSchRefUnitPrice" class="form-control" />
                                        </div>
                                        <div class="form-inline" >
                                            <div class="btn btn-sm btn-white" @click="schListNk.searchCondition.sTextboxRangeStartSchRefUnitPrice=''; schListNk.searchCondition.sTextboxRangeEndSchRefUnitPrice=''; refreshListNk(1);">전체</div>
                                            <div class="btn btn-sm btn-white" @click="schListNk.searchCondition.sTextboxRangeStartSchRefUnitPrice=''; schListNk.searchCondition.sTextboxRangeEndSchRefUnitPrice='20000'; refreshListNk(1);">2만원 이하</div>
                                            <div class="btn btn-sm btn-white" @click="schListNk.searchCondition.sTextboxRangeStartSchRefUnitPrice='20000'; schListNk.searchCondition.sTextboxRangeEndSchRefUnitPrice='30000'; refreshListNk(1);">2만원~3만원</div>
                                            <div class="btn btn-sm btn-white" @click="schListNk.searchCondition.sTextboxRangeStartSchRefUnitPrice='30000'; schListNk.searchCondition.sTextboxRangeEndSchRefUnitPrice='50000'; refreshListNk(1);">3만원~5만원</div>
                                            <div class="btn btn-sm btn-white" @click="schListNk.searchCondition.sTextboxRangeStartSchRefUnitPrice='50000'; schListNk.searchCondition.sTextboxRangeEndSchRefUnitPrice='80000'; refreshListNk(1);">5만원~8만원</div>
                                            <div class="btn btn-sm btn-white" @click="schListNk.searchCondition.sTextboxRangeStartSchRefUnitPrice='80000'; schListNk.searchCondition.sTextboxRangeEndSchRefUnitPrice='100000'; refreshListNk(1);">8만원~10만원</div>
                                            <div class="btn btn-sm btn-white" @click="schListNk.searchCondition.sTextboxRangeStartSchRefUnitPrice='100000'; schListNk.searchCondition.sTextboxRangeEndSchRefUnitPrice=''; refreshListNk(1);">10만원 이상</div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>메인원단 단가</th>
                                <td>
                                    <div class="dp-flex">
                                        <div class="mini-picker mgl5">
                                            <input type="number" v-model="schListNk.searchCondition.sTextboxRangeStartSchMainFabricUnitPrice" class="form-control" style="width:80px; padding:0px 5px;" />
                                        </div>
                                        <div>~</div>
                                        <div class="mini-picker">
                                            <input type="number" v-model="schListNk.searchCondition.sTextboxRangeEndSchMainFabricUnitPrice" class="form-control" style="width:80px; padding:0px 5px;" />
                                        </div>
                                        <div class="form-inline" >
                                            <div class="btn btn-sm btn-white" @click="schListNk.searchCondition.sTextboxRangeStartSchMainFabricUnitPrice=''; schListNk.searchCondition.sTextboxRangeEndSchMainFabricUnitPrice=''; refreshListNk(1);">전체</div>
                                            <div class="btn btn-sm btn-white" @click="schListNk.searchCondition.sTextboxRangeStartSchMainFabricUnitPrice=''; schListNk.searchCondition.sTextboxRangeEndSchMainFabricUnitPrice='3000'; refreshListNk(1);">3천원 이하</div>
                                            <div class="btn btn-sm btn-white" @click="schListNk.searchCondition.sTextboxRangeStartSchMainFabricUnitPrice='3000'; schListNk.searchCondition.sTextboxRangeEndSchMainFabricUnitPrice='4000'; refreshListNk(1);">3천원~4천원</div>
                                            <div class="btn btn-sm btn-white" @click="schListNk.searchCondition.sTextboxRangeStartSchMainFabricUnitPrice='4000'; schListNk.searchCondition.sTextboxRangeEndSchMainFabricUnitPrice='5000'; refreshListNk(1);">4천원~5천원</div>
                                            <div class="btn btn-sm btn-white" @click="schListNk.searchCondition.sTextboxRangeStartSchMainFabricUnitPrice='5000'; schListNk.searchCondition.sTextboxRangeEndSchMainFabricUnitPrice=''; refreshListNk(1);">5천원 이상</div>
                                        </div>
                                    </div>
                                </td>
                                <th>생지유무</th>
                                <td>
                                    <label class="radio-inline ">
                                        <input type="radio" name="sRadioSchMainFabricOnHandYn" value="all" v-model="schListNk.searchCondition.sRadioSchMainFabricOnHandYn"/>전체
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="sRadioSchMainFabricOnHandYn" value="O" v-model="schListNk.searchCondition.sRadioSchMainFabricOnHandYn"/>생지 有
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="sRadioSchMainFabricOnHandYn" value="X" v-model="schListNk.searchCondition.sRadioSchMainFabricOnHandYn"/>생지 無
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th>원부자재 자재명</th>
                                <td>
                                    <input type="text" v-model="schListNk.searchCondition['sTextboxSchMate.materialName']" value="" class="form-control" placeholder="자재명" />
                                </td>
                                <th>원부자재 타입</th>
                                <td>
                                    <label class="checkbox-inline " >
                                        <input type="checkbox" name="aChkboxSchMateMaterialType[]" value="all" class="js-not-checkall" data-target-name="aChkboxSchMateMaterialType[]"
                                               :checked="0 >= schListNk.searchCondition['aChkboxSchMate.materialType'].length?'checked':''" @click="schListNk.searchCondition['aChkboxSchMate.materialType']=[]" /> 전체
                                    </label>
                                    <?php foreach( \Component\Ims\NkCodeMap::REF_PRODUCT_PLAN_MATERIAL_TYPE as $k => $v){ ?>
                                        <label class="mgl10" >
                                            <input class="checkbox-inline chk-progress" type="checkbox" name="aChkboxSchMateMaterialType[]" value="<?=$k?>"  v-model="schListNk.searchCondition['aChkboxSchMate.materialType']" />
                                            <?=$v?>
                                        </label>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <th>원부자재 혼용률</th>
                                <td>
                                    <input type="text" v-model="schListNk.searchCondition['sTextboxSchMate.fabricMix']" value="" class="form-control" placeholder="혼용률" />
                                </td>
                                <th>원부자재 후가공</th>
                                <td>
                                    <input type="text" v-model="schListNk.searchCondition.sTextboxSchAfterMake" value="" class="form-control" placeholder="후가공" />
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <table v-else class="table table-cols table-td-height0">
                            <colgroup>
                                <col class="width-sm">
                                <col class="">
                            </colgroup>
                            <tbody>
                            <tr v-for="(val, key) in schListNk.oSchFilter">
                                <th>{% schListNk.oSchFilterHan[key] %}</th>
                                <td>
                                    <span v-if="val ==='text'">
                                        <input type="text" class="form-control" v-model="schListNk.searchCondition[key]" @keyup.enter="refreshListNk(1)" />
                                    </span>
                                    <span v-else-if="val ==='checkbox'">
                                        <div v-if="schListNk.searchCondition[key] !== undefined" class="checkbox">
                                            <div>
                                                <label class="checkbox-inline mgr10">
                                                    <input type="checkbox" :name="key+'[]'" value="all" class="js-not-checkall" :data-target-name="key+'[]'"  :checked="0 >= schListNk.searchCondition[key].length?'checked':''" @click="schListNk.searchCondition[key]=[]"> 전체
                                                </label>
                                                <label v-for="(val2, key2) in schListNk.oSchFilterOpt[schListNk.sListMethodName+'_'+key]" class="mgr10">
                                                    <input class="checkbox-inline chk-progress" type="checkbox" :name="key+'[]'" :value="key2" v-model="schListNk.searchCondition[key]"> {% val2 %}
                                                </label>
                                            </div>
                                        </div>
                                    </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
<!--                    검색 끝 -->
                    <div style="text-align: center;">
                        <input type="submit" value="검색" class="btn btn-lg btn-black" @click="refreshListNk(1)" />
                        <input type="submit" value="닫기" class="btn btn-lg btn-white" data-dismiss="modal">
                    </div>
                </div>
                <div class="">
                    <div class="flo-left mgb5">
                        <div class="font-16 dp-flex" >
                        <span style="font-size: 18px !important;">
                            TOTAL <span class="bold text-danger pdl5">{% $.setNumberFormat(schListNk.listTotal.recode.total) %}</span> 건
                        </span>
                        </div>
                    </div>
                </div>
<!--                list start-->
                <div>
                    <div v-if="schListNk.sListMethodName == 'stylePlanRef'" style="clear: both;">
                        <ul class="box_list">
                            <li v-for="(val, key) in schListNk.listData" @click="schListModalServiceNk.setMatch(key);" class="cursor-pointer hover-btn">
                                <div>
                                    <div><img :src="val.refThumbImg==null||val.refThumbImg==''?'/data/commonimg/ico_noimg_300.gif':val.refThumbImg" /></div>
                                    <div>{% val.refName %}</div>
                                    <div>{% $.setNumberFormat(val.refUnitPrice) %} 원</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <table v-else class="table table-rows table-default-center table-td-height30 mgt5 ">
                        <colgroup>
                            <col v-if="true != fieldRow.skip" :class="`w-${fieldRow.col}p`" v-for="fieldRow in schListNk.fieldData" />
                        </colgroup>
                        <tr>
                            <th v-for="fieldRow in schListNk.fieldData"  v-if="true != fieldRow.skip" :class="fieldRow.titleClass">
                                {% fieldRow.title %}
                            </th>
                        </tr>
                        <tr  v-if="0 >= schListNk.listData.length">
                            <td colspan="99">
                                데이터가 없습니다.
                            </td>
                        </tr>
                        <tr class="cursor-pointer hover-btn" v-for="(val, key) in schListNk.listData" @click="schListModalServiceNk.setMatch(key);">
                            <td v-for="fieldRow in schListNk.fieldData" v-if="true != fieldRow.skip" :class="fieldRow.titleClass">
                                <span v-if="fieldRow.type=='i'">{% $.setNumberFormat(val[fieldRow.name]) %}</span>
                                <span v-else>{% val[fieldRow.name] %}</span>
                            </td>
                        </tr>
                    </table>
                </div>
<!--                list end-->
                <div id="sch-list-nk-paging" v-html="schListNk.pageHtml" class="ta-c"></div>
            </div>
            <div class="modal-footer">
                <div class="btn btn-gray" data-dismiss="modal">닫기</div>
            </div>
        </div>
    </div>
</div>
