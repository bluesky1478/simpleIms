<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
    .spec-modify td{ font-size:11px!important; }
</style>

<section id="imsApp">

    <form id="frm">
        <div class="page-header js-affix">
            <h3>{% mainData.customer.customerName %} {% mainData.product.styleFullName %} 작업지시서</h3>

            <div class="btn-group">

                <input type="button" value="저장" class="btn btn-red" v-if="'revision' !== tabMode && 'p' !== mainData.ework.data.mainApproval"
                       @click="ImsEworkService.saveEwork(mainData.product, mainData.ework, orgInitData)">

                <input type="button" value="인쇄용 작업지시서 보기" class="btn btn-white" @click="window.open(`<?=$eworkUrl?>?sno=<?=$requestParam['sno']?>`);">
                <input type="button" value="사양서 보기" class="btn btn-white" @click="window.open(`<?=$guideUrl?>?key=${projectKey}`);">
                <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
            </div>
        </div>
    </form>

    <!--탭화면-->
    <div id="tabViewDiv">
        <ul class="nav nav-tabs mgb15" role="tablist" >
            <!--
            <tab-component :data="{tabName:'작업지시서 내용 수정', tabValue:'modify', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component>
            -->
            <tab-component :data="{tabName:'Revision', tabValue:'revision', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component>
            <tab-component :data="{tabName:'작업지시서 메인', tabValue:'main', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component>
            <tab-component :data="{tabName:'바텍정보', tabValue:'batek', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component>
            <tab-component :data="{tabName:'마크정보', tabValue:'mark', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component>
            <tab-component :data="{tabName:'케어라벨', tabValue:'care', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component>
            <tab-component :data="{tabName:'사이즈스펙', tabValue:'spec', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component>
            <tab-component :data="{tabName:'원부자재', tabValue:'material', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component>
            <tab-component :data="{tabName:'포장정보', tabValue:'packing', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component>
            <tab-component :data="{tabName:'생산시 유의사항', tabValue:'warn', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component>
            <!--<tab-component :data="{tabName:'변경히스토리', tabValue:'revHistory', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component>-->
            <tab-component :data="{tabName:'일러작업/구버전 파일', tabValue:'oldFile', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component>
        </ul>
    </div>

    <div class="row">
        <?php foreach(\Component\Ims\ImsCodeMap::EWORK_TYPE as $eworkKey => $value) { ?>
            <section v-if="'p' !== mainData.ework.data.mainApproval || 'care' === '<?=$eworkKey?>' ">
                <?php include './admin/ims/popup/template_ework/_work'.ucfirst($eworkKey).'.php'?>
            </section>
            <section v-else>
                <?php include './admin/ims/popup/template_ework/_work'.ucfirst($eworkKey).'_p.php'?>
            </section>
        <?php } ?>

        <?php include './admin/ims/popup/template_ework/_workRevision.php'?>

        <!-- ############### 수정사항(미오픈) ############### -->
        <?php include './admin/ims/popup/template_ework/_workModify.php'?>

        <!-- ############### 변경 히스토리(미오픈) ############### -->
        <?php include './admin/ims/popup/template_ework/_workRevHistory.php'?>

        <!-- ############### 구 작업지시서 파일 ############### -->
        <?php include './admin/ims/popup/template_ework/_workFile.php'?>

        <div class="col-xs-12 text-center mgt10">
            <div class="btn btn-red btn-lg" v-if="'revision' !== tabMode && 'p' !== mainData.ework.data.mainApproval"
                 @click="ImsEworkService.saveEwork(mainData.product, mainData.ework, orgInitData)">저장</div>
            <div class="btn btn-white btn-lg" @click="self.close()">닫기</div>
        </div>
    </div>


    <!--탭화면-->
    <div id="tabViewDiv2" class="mgt40">
        <ul class="nav nav-tabs mgb15" role="tablist" style=" border-top:solid 1px #888888">
            <!--
            <tab-component2 :data="{tabName:'작업지시서 내용 수정', tabValue:'modify', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component2>
            -->
            <tab-component2 :data="{tabName:'Revision', tabValue:'revision', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component2>
            <tab-component2 :data="{tabName:'작업지시서 메인', tabValue:'main', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component2>
            <tab-component2 :data="{tabName:'바텍정보', tabValue:'batek', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component2>
            <tab-component2 :data="{tabName:'마크정보', tabValue:'mark', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component2>
            <tab-component2 :data="{tabName:'케어라벨', tabValue:'care', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component2>
            <tab-component2 :data="{tabName:'사이즈스펙', tabValue:'spec', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component2>
            <tab-component2 :data="{tabName:'원부자재', tabValue:'material', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component2>
            <tab-component2 :data="{tabName:'포장정보', tabValue:'packing', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component2>
            <tab-component2 :data="{tabName:'생산시 유의사항', tabValue:'warn', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component2>
            <!--<tab-component2 :data="{tabName:'변경히스토리', tabValue:'revHistory', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component2>-->
            <tab-component2 :data="{tabName:'일러작업/구버전 파일', tabValue:'oldFile', tabMode:tabMode, changeTab:changeTab, cookieName:'popEworkTabMode'}"></tab-component2>
        </ul>
    </div>

    <button class="floating-btn" v-if="'revision' !== tabMode && 'p' !== mainData.ework.data.mainApproval" @click="ImsEworkService.saveEwork(mainData.product, mainData.ework, orgInitData)">
        저장
    </button>

</section>


<script type="text/javascript">
    const styleSno = '<?=$requestParam['sno']?>';
    let tabMode = '<?=gd_isset($requestParam['tabMode'],'main')?>';

    <?php if( 'y' === $isReload) { ?>
        if( !$.isEmpty($.cookie('popEworkTabMode')) ){
            tabMode = $.cookie('popEworkTabMode');
        }
    <?php } ?>

    //console.log('탭모드 확인 : ', tabMode);
    //메인 AI 히스토리 필요.
    const mainAiUploadAfterAction = (tmpFile, dropzoneId)=>{
        const saveFileList = [];
        tmpFile.forEach((value)=>{
            saveFileList.push(value);
        });
        let promptValue = window.prompt("메모입력 : ");

        const saveData = {
            saveData : {
                customerSno : vueApp.mainData.project.customerSno,
                projectSno : vueApp.mainData.project.sno,
                styleSno : vueApp.mainData.product.sno,
                fileDiv : dropzoneId,
                fileList : saveFileList,
                memo : promptValue,
            }
        }
        $.imsPost('saveProjectFiles',saveData).then((data)=>{
            vueApp.mainData.fileList[dropzoneId] = data.data[dropzoneId];
        });
    }

    $(appId).hide();
    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData, {
            schListMultiNk : schListMultiModalServiceNk.objDefault,

            tabMode : tabMode,
            beforeRevision : [],
            approvalData : {
                <?php foreach( \Component\Ims\ImsCodeMap::EWORK_TYPE as $eworkType => $eworkName ){ ?>
                ework<?=ucfirst($eworkType)?> : [],
                <?php } ?>
            },
            specModify : false,
            projectKey : null,
            fabricModify : false,
            revisionModify : false,
            loadSampleNo : '',

            staticCurrDt : '<?=date('Y-m-d H:i')?>',
            staticLoginName : '<?=\Session::get('manager.managerNm')?>',
        });
        ImsBoneService.setMounted(serviceData, ()=>{
            //ProjectKey 가져오기 (사양서 보기 용)
            $.imsPost('getProjectKey',{
                'projectSno':vueApp.mainData.project.sno,
            }).then((data)=>{
                vueApp.projectKey = data.data;
            });

            $('.set-dropzone').addClass('dropzone');
            //ImsService.setDropzone(vueInstance, 'fileCareMark', uploadAfterAction); //캐어라벨

            const fileList = [
                '<?=implode('\',\'',\Component\Ims\ImsCodeMap::EWORK_FILE_LIST)?>'
            ];
            fileList.forEach((fileName)=>{
                ImsService.setDropzone(vueApp, fileName, (tmpFile)=>{
                    const saveFileList = [];
                    tmpFile.forEach((value)=>{
                        saveFileList.push(value);
                    });
                    vueApp.mainData.ework.fileList[fileName] = saveFileList; //Upload 부분...

                    //if('fileAi' === fileName){
                        mainAiUploadAfterAction(tmpFile, fileName);
                    //}
                    $.imsPost('saveEworkUpload',{
                        'styleSno':styleSno,
                        'type':fileName,
                        'fileInfo':JSON.stringify(saveFileList),
                    });
                });
            });

            const setApprovalData = (approvalType)=>{
                ImsTodoService.getApprovalData(approvalType, 0, styleSno, 0).then((data)=>{
                    vueApp.approvalData[approvalType].push({
                        'appTitle' : '기안',
                        'name' : data.regManagerNm,
                        'status' : '',
                        'statusKr' : '',
                        'completeDt' : data.regDt,
                    });

                    if( typeof data.targetManagerList !== 'undefined' ){
                        data.targetManagerList.forEach((target)=>{
                            vueApp.approvalData[approvalType].push({
                                'appTitle' : target.appTitle,
                                'name' : target.name,
                                'status' : target.status,
                                'statusKr' : target.statusKr,
                                'completeDt' : target.completeDt,
                                'approvalSno' : data.sno,
                                'managerSno' : target.sno,
                                'reason' : target.reason,
                            });
                        });
                    }
                });
            };
            <?php foreach( \Component\Ims\ImsCodeMap::EWORK_TYPE as $eworkType => $eworkName ){ ?>
                setApprovalData('ework<?=ucfirst($eworkType)?>');
            <?php } ?>

            vueApp.beforeRevision = $.copyObject(vueApp.mainData.ework.data.revision);

            //원부자재 - 등록한 원자재/부자재의 시험성적서 존재여부 확인
            let oSchParam = {customerSno:vueApp.mainData.customer.sno, aoSchMaterial:[]};
            $.each(vueApp.mainData.product.fabricList, function(key, val) {
                this.testReportYn = 'n';
                this.testSelfYn = 'n';
                if (this.materialSno != 0 && this.materialSno != '') {
                    oSchParam.aoSchMaterial.push({materialSno:this.materialSno, materialColor:this.color});
                }
            });
            $.each(vueApp.mainData.product.subFabricList, function(key, val) {
                this.testReportYn = 'n';
                this.testSelfYn = 'n';
                if (this.materialSno != 0 && this.materialSno != '') {
                    oSchParam.aoSchMaterial.push({materialSno:this.materialSno, materialColor:this.color});
                }
            });
            if (oSchParam.aoSchMaterial.length > 0) {
                ImsNkService.getList('testReport', oSchParam).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        if (data.length > 0) {
                            $.each(data, function(key, val) { //가져온 시험성적서작성 리스트 반복
                                $.each(vueApp.mainData.product.fabricList, function(key2, val2) { //원자재 반복
                                    if (val.materialSno == this.materialSno && val.materialColor == this.color) {
                                        if (val.testType == 1) this.testReportYn = 'y';
                                        else if (val.testType == 2) this.testSelfYn = 'y';
                                    }
                                });
                                $.each(vueApp.mainData.product.subFabricList, function(key2, val2) { //부자재 반복
                                    if (val.materialSno == this.materialSno && val.materialColor == this.color) {
                                        if (val.testType == 1) this.testReportYn = 'y';
                                        else if (val.testType == 2) this.testSelfYn = 'y';
                                    }
                                });
                            });
                            vueApp.$forceUpdate();
                        }
                    });
                });
            }
        });

        ImsBoneService.setMethod(serviceData, {
            //결재후 수정
            afterApprovalModify : ()=>{
                $.msgPrompt('해당 작업지시서를 수정할 수 있게 합니다.','','사유 필수.', (confirmMsg)=>{
                    if( confirmMsg.isConfirmed ){
                        if(!$.isEmpty(confirmMsg.value)){
                            ImsEworkService.afterApprovalModify(styleSno, 'n', confirmMsg.value);
                        }else{
                            $.msg('사유를 입력해주세요.','','warning');
                        }
                    }
                });
            },
            afterApprovalRecover : ()=>{
                $.msgConfirm('결재 상태를 복구 합니다.','').then((confirmMsg)=>{
                    if( confirmMsg.isConfirmed ){
                        ImsEworkService.afterApprovalModify(styleSno, 'p');
                    }
                });
            },
            delFile : (delFileType)=>{
                $.msgConfirm('정말 삭제 하시겠습니까?','복구불가.').then(function(result){
                    if( result.isConfirmed ){
                        vueApp.mainData.ework.fileList[delFileType] = '';
                        vueApp.eworkUpdate(delFileType, ''); //Empty 처리.
                    }
                });
            },
            eworkUpdate : (updateField, updateData)=>{
                let availField = ['fileMain'];
                <?php foreach(\Component\Ims\ImsCodeMap::EWORK_TYPE as $key => $value) { ?>
                availField.push('<?=$key?>Approval');
                <?php } ?>
                if(availField.includes(updateField)){
                    vueApp.saveRealTime('ework','styleSno',vueApp.mainData.product.styleSno,updateField,updateData);
                }
            },
            prdUpdate : (updateField, updateData)=>{
                /*console.log('실행:', updateField);
                vueApp.saveRealTime(
                    'projectProduct'
                    ,'sno'
                    ,vueApp.mainData.product.styleSno
                    ,updateField
                    ,updateData
                );*/
            },
            setNow : (updateObject, updateDateField)=>{
                const now = new Date();
                updateObject[updateDateField] = formatDate(now);
            },
            saveEworkFabric : ()=>{
                //console.log('원자재',vueApp.mainData.product.fabricList);
                //console.log('부자재',vueApp.mainData.product.subFabricList);
                const saveData = {
                    styleSno : styleSno,
                    'fabric' : vueApp.mainData.product.fabricList,
                    'subFabricList' : vueApp.mainData.product.subFabricList,
                };

                $.imsPost('saveEworkFabric',saveData).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        $.msg('원부자재 정보가 저장되었습니다.','','success').then(()=>{
                            location.reload();
                        });
                    });
                });
            },
            saveProc : (updateField, updateData)=>{
                //console.log('저장 시작');
                const refineUpdateData = $.copyObject(updateData);
                //console.log('저장할 데이터 확인', refineUpdateData);

                //필요 부분만 저장.
                for(const specKey in refineUpdateData){
                    //console.log('Loop :', specKey);

                    //필요 없는 스펙리스트 삭제
                    delete refineUpdateData[specKey].specList;
                    refineUpdateData[specKey].correction = new Object(); //보정 데이터 재구성

                    for(const correctionKey in refineUpdateData[specKey].correctionList){
                        //리스트에 값이 있나?
                        if( !$.isEmpty(refineUpdateData[specKey].correctionList) && !$.isEmpty(refineUpdateData[specKey].correctionList[correctionKey]) ){
                            refineUpdateData[specKey].correction[correctionKey] = refineUpdateData[specKey].correctionList[correctionKey];
                        }
                        delete refineUpdateData[specKey].correctionList[correctionKey];
                    }
                    delete refineUpdateData[specKey].correctionList;
                }
                //console.log('원본:',updateData);
                //console.log('정제:',refineUpdateData);

                const saveData = {
                    'mode':'saveRealTime',
                    'target':'ework',
                    'key':'styleSno', //key : warnBatek
                    'keyValue':vueApp.mainData.product.styleSno, //key : currentValue
                    'updateField':updateField,
                    'updateData':refineUpdateData,
                    'dataMerge':'y',
                }
                //console.log('저장:',saveData);

                /*$.post('<?=$imsAjaxUrl?>',saveData, (data)=>{
                    console.log('data:',data);
                    $.msg('저장되었습니다.','','success').then(()=>{
                        location.reload();
                    });
                });*/
            },
            removeMark : (idx)=>{
                console.log('삭제번호:', idx)
                if(idx > 1){
                    vueApp.mainData.ework.markCnt--;
                }

                for(let markInfoKey in vueApp.mainData.ework['data']['markInfo'+idx]){
                    vueApp.mainData.ework['data']['markInfo'+idx][markInfoKey] = '';
                }
                vueApp.mainData.ework.fileList['fileMark'+idx]='';
                vueApp.eworkUpdate('markInfo'+idx,'');
                vueApp.eworkUpdate('fileMark'+idx,'');
            },
            addSpec : (specData, guideSpecData, standard, index) =>{
                vueApp.addElement(specData, guideSpecData, 'down', index);
                specData[specData.length-1].specList = {};
                specData[specData.length-1].correctionList = {};
                specData[specData.length-1].specList[standard] = '';
            },
            copyMaterial : (styleSno, costSno)=>{
                $.msgConfirm('원부자재 내역을 확정견적 내역으로 변경 하시겠습니까?','').then(function(result){
                    if( result.isConfirmed ){
                        $.imsPost('copyMaterial',{
                            'styleSno' : styleSno,
                            'costSno' : costSno,
                        }).then((data)=>{
                            $.imsPostAfter(data,(data)=>{
                                $.msg('원부자재 내역이 변경되었습니다.','','success').then(()=>{
                                    location.reload();
                                });
                            });
                        });
                    }
                });
            },
            removeElementProduceWarn : (obj,index)=>{
                vueApp.deleteElement(obj, index);
                vueApp.eworkUpdate('produceWarning',vueApp.mainData.ework.data.produceWarning)
            },
            downloadSpec : ()=>{
                const originalTable = document.getElementById("spec-table");
                // 테이블 복제
                const clone = originalTable.cloneNode(true);
                // "no-export" 클래스가 붙은 요소 제거
                clone.querySelectorAll('.no-export').forEach(el => el.remove());
                // 워크북 생성
                const wb = XLSX.utils.table_to_book(clone, { sheet: "Sheet1" });
                // 파일 저장
                XLSX.writeFile(wb, 'style_spec.xlsx').then(()=>{
                    alert('test');
                });
            },
            addEworkRevision : ()=>{
                vueApp.revisionModify = true;
                $.imsPost2('addEworkRevision',{},(data)=>{
                    vueApp.mainData.ework.data.revision.push(data);
                });
            },
            saveEworkRevision : ()=>{
                $.imsPost2('saveEworkRevision',{
                    'styleSno' : vueApp.mainData.ework.data.styleSno,
                    'revision' : vueApp.mainData.ework.data.revision,
                },()=>{
                    $.msg('저장 완료','','success').then(()=>{
                        vueApp.revisionModify = false;
                        vueApp.beforeRevision = $.copyObject(vueApp.mainData.ework.data.revision); //현재를 저장
                    });
                });
            },
            /**
             * 리비전 수정 취소
             */
            cancelEworkRevision : ()=>{
                vueApp.revisionModify = false;
                vueApp.mainData.ework.data.revision = $.copyObject(vueApp.beforeRevision);
            },

        });

        ImsBoneService.serviceBegin(DATA_MAP.PRODUCT,{sno:styleSno},serviceData);
    });
</script>
