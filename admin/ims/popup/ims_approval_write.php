<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
            <?php if( empty($requestParam['sno']) ) { ?>
                <h3 class="">결재 요청</h3>
            <?php }else { ?>
                <h3 class="">결재 수정</h3>
            <?php } ?>
            <div class="btn-group font-18 bold">
            </div>
        </div>
    </form>

    <div class="">
        <!-- 기본 정보 -->
        <div>
            <table class="table table-cols table-pd-5" style="border-top:none !important;">
                <colgroup>
                    <col class="width-sm">
                    <col>
                </colgroup>
                <tbody>
                <!--<tr>
                    <th>결재유형</th>
                    <td>
                        <label class="radio-inline">
                            <input type="radio" name="scmFl" value="y" checked />일반
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="scmFl" value="y1" />고객 자료
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="scmFl" value="y2" />프로젝트 자료
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="scmFl" value="y3" />스타일 자료
                        </label>
                    </td>
                </tr>-->
                <tr>
                    <th>결재유형</th>
                    <td class="bold font-16">
                        {% ImsTodoService.approvalType[document.approvalType].name  %}
                    </td>
                </tr>
                <tr>
                    <th>
                        고객/프로젝트
                    </th>
                    <td class="font-14">
                        {% customer.customerName %}
                        {% project.projectYear %}
                        {% project.projectSeason %}
                        <span class="text-danger cursor-pointer hover-btn" @click="openProjectView(project.sno)">{% project.sno %}</span>
                    </td>
                </tr>
                <tr v-if="!$.isEmptyObject(style)">
                    <th>
                        스타일
                    </th>
                    <td class="font-14">
                        <span @click="openProductReg2(project.sno, style.styleSno)" class="hover-btn cursor-pointer">
                            {% style.styleFullName %}
                            <span class="font-12">({% style.styleCode %})</span>
                        </span>
                        <div class="font-12 text-muted" v-if="'salePrice' === approvalType">스타일 정보의 판매가를 확인하시어 결재 바랍니다.</div>
                    </td>
                </tr>
                <tr>
                    <th>
                        제목
                    </th>
                    <td>
                        <input type="text" placeholder="제목" class="form-control" v-model="document.subject" style="height:30px">
                    </td>
                </tr>
                <tr>
                    <th>
                        결재라인
                   </th>
                    <td >

                        <table class="table table-borderless table-cols">
                            <colgroup>
                                <col style="width:100px">
                                <col>
                            </colgroup>
                            <tr>
                                <th>
                                    <span class="hover-btn cursor-pointer" @click="openApprovalLine()">
                                        결재라인 <i class="fa fa-search" aria-hidden="true"></i>
                                    </span>
                                </th>
                                <td>
                                    <!--{% selectedApprovalLineSno %} :-->
                                    <select2 class="js-example-basic-single" style="width:50%" v-model="selectedApprovalLineSno" @change="selectedApprovalLine(selectedApprovalLineSno)" id="sel-approval-line">
                                        <option :value="approvalLine.sno" v-for="approvalLine in approvalLineList">
                                            {% approvalLine.subject %}
                                        </option>
                                    </select2>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <span class="hover-btn cursor-pointer" @click="openApproval()">
                                        결재자 <i class="fa fa-search" aria-hidden="true"></i>
                                    </span>
                                </th>
                                <td>
                                    <ul class="dp-flex dp-flex-wrap" >
                                       <li class="dp-flex dp-flex-wrap" >
                                            <div class="sl-badge mg5 relative" style="background-color: #ffd15e">{% document.regManagerNm %} </div>
                                        </li>
                                       <li class="dp-flex dp-flex-wrap" v-for="(appManager, appManagerIndex) in document.appManagers">
                                           <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                           <div class="sl-badge mg5 relative">
                                               {% appManagerIndex + 1 %}. {% appManager.name %}
                                               <!--TODO : 기본 결재자가 없으면 가져올 디폴드 결재자 구조 필요-->
                                               <i class="fa fa-minus-circle ico-approval-delete hover-btn" aria-hidden="true"
                                                  @click="deleteElement(document.appManagers,appManagerIndex)" v-show="document.appManagers.length > 1"></i>

                                               <i class="fa fa-minus-circle ico-approval-delete " aria-hidden="true" style="color: #8a8a8a"
                                                  v-show="2 > document.appManagers.length"></i>
                                           </div>
                                       </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr class="display-none">
                                <th>
                                    <span class="hover-btn cursor-pointer" @click="openApprovalRef()">
                                        참조 <i class="fa fa-search" aria-hidden="true"></i>
                                    </span>
                                </th>
                                <td>
                                    <!--
                                    <select2 class="js-example-basic-single"   style="width:20%" >
                                        <?php foreach ($managerList as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select2>
                                    -->
                                    <!--<div class="dp-flex dp-flex-wrap" >
                                        <div v-for="(refManager, refManagerIndex) in document.refManagers" class="sl-badge mgt5 mgr5">
                                            {% refManager.name %}
                                            <i class="fa fa-minus-circle ico-approval-delete" @click="deleteElement(document.refManagers,refManagerIndex)"
                                               aria-hidden="true"></i>
                                        </div>
                                    </div>-->
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <tr v-if="!$.isEmpty( ImsTodoService.approvalType[document.approvalType].fileDiv)">
                    <th>
                        {% ImsTodoService.approvalType[document.approvalType].name %} 파일
                    </th>
                    <td>
                        <div v-if="!$.isEmpty(project.projectFile) && $.isObject(project.projectFile)">
                            <ul class="ims-file-list" >
                                <li class="hover-btn" v-for="(file, fileIndex) in project.projectFile[ImsTodoService.approvalType[document.approvalType].fileDiv].files">
                                    <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">
                                        {% fileIndex+1 %}. {% file.fileName %}
                                     </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php include './admin/ims/popup/template/_sample.php'?>
                <?php include './admin/ims/popup/template/_salePrice.php'?>
                <?php include './admin/ims/popup/template/_cost.php'?>
                <?php include './admin/ims/popup/template/_ework.php'?>
                <tr>
                    <th>
                        요청 내용
                    </th>
                    <td class="mgb150 mgt15" style="height:120px; vertical-align: top">
                        <textarea class="form-control h100" placeholder="요청내용 작성" v-model="document.contents"></textarea>
                    </td>
                </tr>
                <!--
                <tr >
                    <th>
                        추가 첨부
                    </th>
                    <td>
                        <ul class="ims-file-list" >
                            <li class="hover-btn" v-for="(file, fileIndex) in document.todoFile1">
                                <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                            </li>
                        </ul>

                        <form id="todoFile1" class="set-dropzone mgt5" @submit.prevent="uploadFiles" >
                            <div class="fallback">
                                <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                            </div>
                        </form>
                    </td>
                </tr>
                -->
                </tbody>
            </table>
        </div>

        <div class="dp-flex" style="justify-content: center">
            <?php if( empty($requestParam['sno']) ) { ?>
                <div class="btn btn-accept hover-btn btn-lg mg5" @click="save()">결재요청</div>
            <?php }else{ ?>
                <div class="btn btn-accept hover-btn btn-lg mg5" @click="save()">수정</div>
            <?php } ?>
            <div class="btn btn-white hover-btn btn-lg mg5" @click="self.close()">닫기</div>

            <?php if( empty($requestParam['sno']) ) { ?>
                <div class="btn btn-lg mg5 btn-gray" @click="saveSelf()">
                    자체결재하기
                </div>
            <?php } ?>

        </div>

    </div>


    <!-- 결재라인 레이어 -->
    <div class="modal fade xsmall-picker" id="layer-approval-line-list"  role="dialog"  aria-hidden="true"  >
        <div class="modal-dialog" role="document" >
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                    <span class="modal-title font-18 bold" >
                    결재라인 설정
                </span>
                </div>
                <div class="modal-body">

                    결재라인 목록
                    <select2 class="js-example-basic-single" style="width:50%" v-model="selectedPopApprovalLineSno" @change="selectedPopApprovalLine(selectedPopApprovalLineSno)">
                        <option value="0">신규추가</option>
                        <option :value="approvalLine.sno" v-for="approvalLine in popApprovalLineList">
                            {% approvalLine.subject %}
                        </option>
                    </select2>
                    
                    <div class="btn btn-blue" @click="selectedPopApprovalLineSno=0">결재라인 신규추가</div>

                    <hr>

                    <div v-if="null !== selectedPopApprovalLineObject" class="font-14">
                        <b>결재유형 : </b>{% ImsTodoService.approvalType[document.approvalType].name  %}
                        <br><b>결재라인 제목 : </b><input type="text" class="form-control w80 inline-block font-14" placeholder="결재라인 제목" v-model="selectedPopApprovalLineObject.subject">
                    </div>
                    <div class="mgt5">
                        <select2 class="js-example-basic-single" id="pop-sel-approval-manager"  style="width:30%"  @change="addPopApprovalManager()">
                            <?php foreach ($managerList as $key => $value ) { ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>
                        </select2>
                        <div class="btn btn-white" @click="addPopApprovalManager()">결재자추가</div>
                    </div>

                    <div class="table-title mgt15">
                        <draggable v-model="popAppManagers" :filter="'.non-draggable'" :preventOnFilter="true" @end="onEnd">
                            <transition-group>
                                <div v-for="(item, index) in popAppManagers" :key="item.sno" :class="['draggable-item']">
                                    <!--<i class="fa fa-bars" aria-hidden="true" style="font-weight: normal!important;"></i>-->
                                    {%index+1%}. {% item.name %}
                                    <i class="fa fa-minus-circle ico-approval-delete hover-btn" aria-hidden="true"
                                       @click="deleteElement(popAppManagers,index)" v-show="popAppManagers.length > 1"></i>

                                </div>
                            </transition-group>
                        </draggable>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="btn btn-red" @click="saveApprovalLine()">저장</div>
                    <div class="btn btn-gray" data-dismiss="modal">닫기</div>
                </div>
            </div>
        </div>
        <div class="mgt50"></div>
    </div>
    

    <!-- 결재자 레이어 -->
    <div class="modal fade xsmall-picker" id="layer-approval-list"  role="dialog"  aria-hidden="true"  >
        <div class="modal-dialog" role="document" >
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                    <span class="modal-title font-18 bold" >
                    결재자 설정
                </span>
                </div>
                <div class="modal-body">

                    <div>
                        <select2 class="js-example-basic-single" id="sel-approval-manager"  style="width:50%" >
                            <?php foreach ($managerList as $key => $value ) { ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php } ?>
                        </select2>
                        <div class="btn btn-red btn-red-line2" @click="addApprovalManager()">추가</div>
                    </div>

                    <div class="table-title mgt15">
                        <draggable v-model="document.appManagers" :filter="'.non-draggable'" :preventOnFilter="true" @end="onEnd">
                            <transition-group>
                                <div v-for="(item, index) in document.appManagers" :key="item.sno" :class="['draggable-item']">
                                    <!--<i class="fa fa-bars" aria-hidden="true" style="font-weight: normal!important;"></i>-->
                                    {%index+1%}. {% item.name %}
                                </div>
                            </transition-group>
                        </draggable>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="btn btn-gray" data-dismiss="modal">닫기</div>
                </div>
            </div>
        </div>
        <div class="mgt50"></div>
    </div>

    <!--참조자 레이어-->
    <div class="modal fade xsmall-picker" id="layer-ref-list"  role="dialog"  aria-hidden="true"  >
        <div class="modal-dialog" role="document" style="width:850px">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                    <span class="modal-title font-18 bold" >
                    참조자 설정
                </span>
                </div>
                <div class="modal-body">

                    <td class="pd0">
                        <table class="table table-cols mgt10 mg0">
                            <?php foreach($teamManagerList as $teamManagerKey => $teamManager) { ?>
                                <tr>
                                    <th>
                                        <?=$teamManager['teamName']?>
                                        <br>
                                        <label class="checkbox-inline">
                                            <input type='checkbox' value='y' class='js-checkall' data-target-name='teamManager<?=$teamManagerKey?>' @change="checkManager" />전체
                                        </label>
                                    </th>
                                    <td>
                                        <?php foreach($teamManager['managers'] as $eachManagerSno => $eachManager) { ?>
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="teamManager<?=$teamManagerKey?>" value="<?=$eachManagerSno?>" data-valkr="<?=$eachManager?>" class="chk-manager" @change="checkManager"> <?=$eachManager?>
                                            </label>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                    </td>

                </div>

                <div class="modal-footer">
                    <div class="btn btn-gray" data-dismiss="modal">닫기</div>
                </div>
            </div>
        </div>
        <div class="mgt50"></div>
    </div>

</section>

<script type="text/javascript">

    const sno = '<?=gd_isset($requestParam['sno'],0)?>';
    const approvalType = '<?=gd_isset($requestParam['approvalType'],'')?>';

    const projectSno = '<?=gd_isset($requestParam['projectSno'],0)?>';
    const styleSno = '<?=gd_isset($requestParam['styleSno'],0)?>'; //defre ?
    const eachSno = '<?=gd_isset($requestParam['eachSno'],'')?>';

    console.log('Approval Type:',approvalType);
    console.log('ProjectSno : ',projectSno);
    console.log('StyleSno : ',styleSno);
    console.log('EachSno : ',eachSno);

    $(appId).hide();

    $(()=>{
        //결재라인 기본 셋팅
        const approvalLineSearchCondition = {approvalType : '<?=gd_isset($requestParam['approvalType'],0)?>', sort : 'D,desc'}
        ImsService.getList('approvalLine',approvalLineSearchCondition).then((data)=>{
            approvalWriteInit(data.data.list);
        });
    });

    /**
     * 초기화
     * @param approvalLineList
     */
    const approvalWriteInit = (approvalLineList)=>{
        const initParams = {
            data : {
                document : {
                    approvalType : approvalType
                },
                customer : {},
                project : {},
                style : {},
                productList : {},
                sample : {},

                selectedApprovalLineObject : null, //선택된 결재라인
                selectedApprovalLineSno : 0, //선택된 결재라인 번호
                approvalLineList : $.copyObject(approvalLineList), //결재라인

                popApprovalLineList : $.copyObject(approvalLineList), //결재라인(pop)
                popAppManagers : {}, //결재자들.
                selectedPopApprovalLineObject : null, //선택된 결재라인(pop)
                selectedPopApprovalLineSno : 0, //선택된 결재라인 번호(pop)
                // popApprovalLineList
            },
            mounted : (vueInstance)=>{
                //기본 구조 가져오기 (or 수정내용 가져오기)
                ImsTodoService.getData(sno).then((data)=>{
                    vueInstance.document = $.copyObject(data);
                    vueInstance.document.todoType = 'approval';
                    //console.log('이게 문제.', vueInstance.document);

                    //프로젝트 셋팅
                    if(!$.isEmpty(projectSno) && projectSno > 0 ){
                        //프로젝트 정보 불러오기
                        ImsService.getData(DATA_MAP.PROJECT,projectSno).then((prjData)=>{
                            console.log('프로젝트 데이터', prjData.data);
                            if( styleSno > 0 ){
                                const styleInfo = prjData.data.productList.filter(style => styleSno == style.sno)[0];
                                /*console.log('스타일 데이터(프로젝트)', prjData.data.productList);
                                console.log('스타일 데이터(필턱값)', styleInfo);
                                console.log('스타일SNO', styleSno);*/
                                vueApp.style = styleInfo;
                                vueApp.document.styleSno = styleInfo.sno;
                            }
                            vueApp.project = prjData.data.project;
                            vueApp.customer = prjData.data.customer;
                            vueApp.project.projectFile = prjData.data.fileList;
                            vueApp.document.customerSno = prjData.data.customer.sno;
                            vueApp.document.projectSno = prjData.data.project.sno;
                            vueApp.productList = prjData.data.productList;

                            //console.log('고객 번호 셋팅 : ', vueApp.document.customerSno);
                            //console.log('프로젝트 번호 셋팅 : ', vueApp.document.projectSno);
                            //제목 셋팅
                            if( $.isEmpty(vueApp.document.subject) ){
                                vueApp.document.subject = vueApp.customer.customerName + ' ' + ImsTodoService.approvalType[vueApp.document.approvalType].name + ' 결재 요청 건';
                                vueApp.document.contents = vueApp.customer.customerName + ' ' + ImsTodoService.approvalType[vueApp.document.approvalType].name + ' 결재 요청 드립니다.';
                            }
                        });
                    }

                    //샘플 불러오기
                    //console.log(`샘플 불러올 정보 ${eachSno} / ${vueApp.document.approvalType}`);
                    if(!$.isEmpty(eachSno) && 'sampleFile1' === vueApp.document.approvalType ){
                        ImsService.getData(DATA_MAP.SAMPLE,eachSno).then((sampleData)=>{
                            if( 200 === sampleData.code ){
                                vueApp.sample = sampleData.data;
                                vueApp.document.styleSno = sampleData.data.styleSno;
                                vueApp.document.eachSno = sampleData.data.sno;
                                vueApp.document.eachDiv = 'sample';
                            }else{
                                console.log(sampleData.message);
                            }
                        });
                    }

                    //기본 결재 라인 셋팅
                    //결재라인 리스트 불러와서 가장 처음 값 자동 셋팅
                    vueApp.selectedApprovalLineSno = vueApp.approvalLineList[0].sno;
                    vueApp.selectedApprovalLine(vueApp.approvalLineList[0].sno);

                    //팝업용
                    vueApp.selectedPopApprovalLineSno = vueApp.popApprovalLineList[0].sno;
                    vueApp.selectedPopApprovalLine(vueApp.popApprovalLineList[0].sno);

                    $('.set-dropzone').addClass('dropzone');
                    ImsService.setDropzone(vueApp, 'todoFile1', (tmpFile)=>{
                        const saveFileList = [];
                        tmpFile.forEach((value)=>{
                            saveFileList.push(value);
                        });
                        vueApp.document.todoFile1 = saveFileList;

                        //TO-DO : 파일만 올려도 저장되고 싶은 니즈가 있다면 주석 풀기.
                        /*if( sno > 0 ){
                            $.imsPost('saveTodo',{
                                document : vueApp.document,
                                reqManagers : vueApp.reqManagers,
                            });
                        }*/
                    }); //QB의뢰서 파일

                }); //기본데이터 설정
            },
            methods : {
                openApproval:()=>{
                    $('#layer-approval-list').modal('show')
                },
                openApprovalLine:()=>{
                    $('#layer-approval-line-list').modal('show')
                },
                openApprovalRef:()=>{
                    //현재 등록된 참조자 연동.

                    $('.chk-manager').prop('checked', false);
                    $('.js-checkall').prop('checked', false);

                    $('.chk-manager').each(function(){
                        const managerSno = $(this).val();
                        const isChecked = vueApp.document.refManagers.filter(refManager => managerSno == refManager.sno);
                        console.log(managerSno,':',isChecked.length)
                        if(isChecked.length > 0 ){
                            $(this).prop('checked', true);
                        }
                    });

                    $('#layer-ref-list').modal('show');
                },
                //참조자 선택
                checkManager:()=>{
                    vueApp.refManagers = [];
                    $('.chk-manager').each(function(){
                        if( $(this).is(':checked') ){
                            const managerSno = $(this).val();
                            const managerName = $(this).data('valkr');
                            vueApp.refManagers.push({
                                name : managerName,
                                sno : managerSno,
                            });
                        }
                    });
                    vueApp.document.refManagers = [];
                    $('.chk-manager').each(function(){
                        if( $(this).is(':checked') ){
                            const managerSno = $(this).val();
                            const managerName = $(this).data('valkr');
                            vueApp.document.refManagers.push({
                                name : managerName,
                                sno : managerSno,
                            });
                        }
                    });
                },
                selectedApprovalLine:(sno)=>{
                    //vueApp.document.refManagers = vueApp.selectedApprovalLineObject.refManagers;
                    vueApp.selectedApprovalLineObject = vueApp.approvalLineList.filter(line => line.sno == sno )[0];
                    vueApp.document.appManagers = vueApp.selectedApprovalLineObject.appManagers;
                },
                selectedPopApprovalLine:(sno)=>{
                    //console.log('POP 결재라인 셋팅 : ',sno);
                    vueApp.selectedPopApprovalLineObject = vueApp.popApprovalLineList.filter(line => line.sno == sno )[0];

                    if($.isEmpty(vueApp.selectedPopApprovalLineObject)){
                        vueApp.selectedPopApprovalLineObject = {
                            sno : 0,
                            subject : '',
                            appManagers : [],
                            refManagers : [],
                            approvalType : vueApp.document.approvalType,
                        }
                        vueApp.selectedPopApprovalLineSno = 0;
                    }
                    vueApp.popAppManagers = vueApp.selectedPopApprovalLineObject.appManagers;
                },
                selectedCustomer:(customerSno)=>{
                    if( customerSno > 0 ){
                        vueApp.projectListSearchCondition.customerSno = customerSno;
                        ImsService.getList('project',vueApp.projectListSearchCondition).then((data)=>{
                            vueApp.projectList = data.data.list;
                        });
                    }else{
                        //select2 초기화
                        vueApp.projectList = [];
                    }
                },
                selectedProject:()=>{
                    const selectedProjectSno = vueApp.document.projectSno;

                    if( !$.isEmpty(selectedProjectSno) && selectedProjectSno > 0 ){
                        console.log('선택된 프로젝트 : ', selectedProjectSno);
                        const selectedProject = vueApp.projectList.filter(project => project.sno == selectedProjectSno);
                        selectedProject.forEach((prj)=>{
                            console.log(prj);
                        });
                    }
                },
                save:()=>{
                    $.msgConfirm('결재를 요청 하시겠습니까?', '.').then((confirmData)=> {
                        if (true === confirmData.isConfirmed) {
                            //생산가일 경우 견적 선택이 모두 완료 되어야 한다.
                            if( 'cost' === vueApp.document.approvalType && 2 !== Number(vueApp.project.estimateStatus) && 4 !== Number(vueApp.project.projectType) ){
                                $.msg('모든 스타일의 견적 가격이 필요합니다.','견적선택 필수','warning');
                                return false;
                            }
                            $.imsPost('saveApproval',{
                                document : vueApp.document,
                            }).then((data)=>{
                                if(200 === data.code){
                                    $.msg('저장 되었습니다.','','success').then(()=>{
                                        parent.opener.location.reload(); //부모창이 있다면 부모창도 갱신
                                        self.close();
                                    });
                                }else{
                                    $.msg(data.message,'','warning');
                                }
                            });
                            //후처리 (신규면 -> 닫고/리스트 셋팅)
                            //내용 수정이면 리스트 셋팅
                        }
                    });
                },
                saveSelf:()=>{
                    $.msgConfirm('자체 결재 하시겠습니까?', '.').then((confirmData)=> {
                        if (true === confirmData.isConfirmed) {
                            $.imsPost('saveApprovalSelf',{
                                document : vueApp.document,
                            }).then((data)=>{
                                if(200 === data.code){
                                    $.msg('저장 되었습니다.','','success').then(()=>{
                                        parent.opener.location.reload(); //부모창이 있다면 부모창도 갱신
                                        self.close();
                                    });
                                }else{
                                    $.msg(data.message,'','warning');
                                }
                            });
                            //후처리 (신규면 -> 닫고/리스트 셋팅)
                            //내용 수정이면 리스트 셋팅
                        }
                    });
                },
                addApprovalManager:()=>{
                    const addManager = $.copyAndInitListData(vueApp.document.appManagers[0]);
                    const selectedManagerSno = $('#sel-approval-manager').val();

                    if( !$.isEmpty(selectedManagerSno) ){
                        addManager.sno  = $('#sel-approval-manager').val();
                        addManager.name = $('#sel-approval-manager option:selected').text();
                        vueApp.document.appManagers.push(addManager);
                    }else{
                        $.msg('결재자를 선택해주세요.','','warning');
                    }

                },
                addPopApprovalManager:()=>{
                    //console.log(vueApp.popAppManagers);
                    const defaultScheme = {
                        approvalDt : '',
                        memo : '',
                        name : '',
                        sno : '',
                        status : '',
                    }

                    let addManager = null;
                    try{
                        addManager = $.copyAndInitListData(typeof vueApp.popAppManagers[0] != 'undefined' ? vueApp.popAppManagers[0] : defaultScheme );
                    }catch (e){
                        addManager = defaultScheme;
                    }
                    const selectedManagerSno = $('#pop-sel-approval-manager').val();
                    if( !$.isEmpty(selectedManagerSno) ){
                        if(null === vueApp.popAppManagers){
                            vueApp.popAppManagers = [];
                        }
                        addManager.sno  = $('#pop-sel-approval-manager').val();
                        addManager.name = $('#pop-sel-approval-manager option:selected').text();
                        vueApp.popAppManagers.push(addManager);
                    }else{
                        $.msg('결재자를 선택해주세요.','','warning');
                    }
                },
                /**
                 * 결재라인 저장
                 */  
                saveApprovalLine:()=>{
                    vueApp.selectedPopApprovalLineObject.appManagers = $.copyObject(vueApp.popAppManagers);

                    if( $.isEmpty(vueApp.selectedPopApprovalLineObject.subject) ){
                        $.msg('결재라인 제목을 입력해주세요.','','warning');
                        return false;
                    }

                    if( 0 >= vueApp.selectedPopApprovalLineObject.appManagers.length){
                        $.msg('결재자가 선택되지 않았습니다.','','warning');
                        return false;
                    }

                    $.imsPost('saveApprovalLine',vueApp.selectedPopApprovalLineObject).then((data)=>{
                        if(200===data.code){
                            $.msg(data.message,'','success').then(()=>{
                                location.reload()
                            });
                        }else{
                            $.msg(data.message,'','warning').then(()=>{location.reload()});
                        }
                    });
                },
            }
        };
        vueApp = ImsService.initVueApp(appId, initParams);
        console.log('Init OK');

    }

</script>
