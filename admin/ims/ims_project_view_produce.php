<?php include 'library_all.php'?>
<?php include 'library.php'?>

<section id="imsApp" class="project-view">

    <form id="frm">
        <div class="page-header js-affix">
            <h3>
                <span class="text-blue">{% items.customerName %} {% project.projectYear %} {% project.projectSeason %}</span> 프로젝트 상세정보
                <span class="text-danger" style="font-weight:normal" v-show="!$.isEmpty(project.projectNo)">({% project.projectStatusKr %}-{% project.projectNo %})</span>
            </h3>
            <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
            <!--
            <input type="button" value="저장" class="btn btn-red btn-register" @click="save(items, project)">
            -->
        </div>
    </form>
    <div class="row ">
        <div class="col-xs-12" >
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span style="font-size:15px;font-weight: bold">프로젝트번호 : </span>
                    <span style="font-size:15px;font-weight: bold" class="text-danger">{% project.projectNo %} (신규)</span>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="font-16"><b class="text-danger">고객납기일 : {% project.customerDeliveryDt %} ( {% project.customerDeliveryRemainDt %} )</b></span>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span><b>고객발주일: {% project.customerOrderDt %}</b></span>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    등록일시 : <span>{% project.regDt %}</span>

                    <div class="pull-right">
                        <div class="form-inline">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12" >

            <div class="table-title gd-help-manual">
                <div class="flo-left">스타일 정보</div>
                <div class="flo-right"></div>
            </div>
            <div id="tabOrderStatus ">
                <div class="tab-content">
                    <div class="table-action" style="margin-bottom: 0px !important; border-top:solid 1px #888888">
                        <div class="pull-right form-inline" style="height: 26px;">
                            <div class="display-inline-block"></div>
                        </div>
                    </div>

                    <div role="tab-status-order" class="tab-pane in active" id="tab-status-order">
                        <div id="layer-wrap">
                            <div id="inc_order_view" class="table-responsive">
                                <table class="table table-rows">
                                    <colgroup>
                                        <col style="width:2%"  /><!--체크-->
                                        <col style="width:2%"  /><!--번호-->
                                        <col style="width:6%"  /><!--이미지-->
                                        <col style="width:15%"  /><!--스타일-->
                                        <col style="width:15%"  /><!--스타일코드-->
                                        <col style="width:6%"  /><!--제작수량-->
                                        <col  /><!--원단정보-->
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="allCheck" value="y" class="js-checkall" data-target-name="bundle[statusCheck]">
                                        </th>
                                        <th>번호</th>
                                        <th>이미지</th>
                                        <th>스타일명</th>
                                        <th>스타일코드</th>
                                        <th>제작수량</th>
                                        <th>원단정보</th>
                                    </tr></thead>
                                    <tbody>
                                    <tr class="text-center" v-for="(prd, prdIndex) in productList">
                                        <td class="center">
                                            <div class="display-block">
                                                <input type="checkbox" name="bundle[statusCheck][52782]" value="52782" class="">
                                            </div>
                                        </td>
                                        <td>{% prdIndex+1 %}</td>
                                        <td>
                                            <span class="hover-btn "  >
                                                <img src="/data/commonimg/ico_noimg_75.gif" v-show="$.isEmpty(prd.fileThumbnail)" class="middle" width="40">
                                                <img :src="prd.fileThumbnail" v-show="!$.isEmpty(prd.fileThumbnail)" class="middle" width="40">
                                            </span>
                                        </td>
                                        <td><!--스타일명-->
                                            <span class="font-16 text-blue " @click="openProductReg(project.sno, prd.sno)" >{% prd.productName %}</span>
                                        </td>
                                        <td style="padding-left:10px; text-align: left"><!--스타일코드-->
                                            <span class="font-16">{% prd.styleCode %}</span>
                                        </td>
                                        <td class="font-16"><!--제작수량-->
                                            {% $.setNumberFormat(prd.prdExQty) %}장
                                        </td>
                                        <td class="text-left font-15" style="padding-left:20px;">
                                            <ul>
                                                <li v-for="prdFabric in prd.fabric" v-if="!$.isEmpty(prdFabric.fabricName) && !$.isEmpty(prdFabric.color)">
                                                    {% prdFabric.no %} : {% prdFabric.fabricName %} / {% prdFabric.color %}
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="table-action" style="display: none">
                                <div class="pull-left form-inline">
                                    <span class="action-title">선택한 상품을</span>
                                    <select class=" form-control js-status-change" id="bundleOrderStatus" name="bundle[orderStatus]"><option value="">==상품상태==</option><option value="p1">결제완료</option><option value="p2">결제완료(발송대기)</option><option value="p3">결제완료(출고대기)</option><option value="g1">상품준비중</option><option value="g2">회수대기</option><option value="d1">배송중</option><option value="d2">배송완료</option><option value="s1">구매확정</option></select>
                                    <select class=" form-control" id="applyDeliverySno" name=""><option value="0">= 배송 업체 =</option><option value="5" selected="selected">한진택배</option><option value="6">경동택배</option><option value="8">CJ대한통운</option><option value="37">기타 택배</option><option value="40">등기, 소포</option><option value="41">화물배송</option><option value="42">방문수령</option><option value="43">퀵배송</option><option value="44">기타</option></select>                        <input type="text" id="applyInvoiceNo" value="" class="form-control input-lg width-lg">
                                    <button type="button" class="btn btn-red js-order-status-delivery">일괄적용</button>
                                </div>

                                <div class="pull-right form-inline">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="table-title gd-help-manual">생산처 코멘트 </div>
            <table class="table table-cols">
                <colgroup>
                    <col width="150px">
                    <col>
                    <col width="150px">
                    <col width="80px">
                </colgroup>
                <tbody><tr>
                    <th class="text-center">작성</th>
                    <th class="text-center">내용</th>
                    <th class="text-center">등록일</th>
                    <th class="text-center">삭제</th>
                </tr>
                <tr>
                    <td colspan="4" style="padding:5px 0">
                        <div class="comment_area">
                            <textarea name="comment" id="designDraftComment" rows="2" class="form-control" style="width:70%;float:left"></textarea>
                            <button type="button" class="btn btn-red btn-sm js-save-sian js-orderViewInfoSave" data-submit-mode="modifyOrderInfo" style="float:left;height:44px;margin-left:10px">코멘트등록</button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>

    <div style="margin-bottom:150px"></div>

</section>

<script type="text/javascript">
    const sno = '<?=$requestParam['sno']?>';
    let currentStatus = null;
    let currentCustomerConfirm = null;
    $(appId).hide();

    const autoSetStatus = (projectSno, status, reason)=>{
        $.imsPost('setStatus',{
            projectSno : projectSno
            , reason : reason
            , projectStatus : status
        }).then((data)=>{
            if(200 === data.code){
                $.msg('상태가 변경되었습니다.','', "success").then(()=>{
                    if( 80 === Number(status) || 90 === Number(status) ){
                        location.href=`ims_produce_view.php?sno=${sno}&status=step${status}`;
                    }else{
                        location.href=`ims_project_view.php?sno=${sno}&status=step${status}`;
                    }
                });
            }
        });
    }

    const setAccept = async (acceptDiv, confirmStatus, memo)=>{
        return $.imsPost('setAccept', {
            projectSno: sno,
            acceptDiv: acceptDiv,
            confirmStatus: confirmStatus,
            memo : memo
        });
    }

    /**
     * 업로드 후 처리
     * @param tmpFile
     * @param dropzoneId
     */
    const uploadAfterAction = (tmpFile, dropzoneId)=>{
        const saveFileList = [];
        tmpFile.forEach((value)=>{
            saveFileList.push(value);
        });

        const promptValue = window.prompt("메모입력 : ");

        $.imsPost('saveProjectFiles',{
            saveData : {
                projectSno : sno,
                fileDiv : dropzoneId,
                fileList : saveFileList,
                memo : promptValue,
            }
        }).then((data)=>{
            if(200 === data.code){
                vueApp.fileList[dropzoneId] = data.data[dropzoneId];
                const acceptDivMap = {
                    'filePlan' : 'planConfirm',
                    'fileProposal' : 'proposalConfirm',
                    'fileSampleConfirm' : 'sampleConfirm',
                    'fileWork' : 'workConfirm',
                };
                if( !$.isEmpty(acceptDivMap[dropzoneId]) ){
                    const acceptDiv = acceptDivMap[dropzoneId];
                    setAccept(acceptDiv, 'r', '파일등록으로 자동 승인요청').then((data)=>{
                        if(200 === data.code){
                            if( !$.isEmpty(data.data) && 'false' !== data.data && false !== data.data  ){
                                vueApp.project[acceptDiv+'Kr'] = data.data.project[acceptDiv+'Kr'];
                                vueApp.project[acceptDiv] = data.data.project[acceptDiv];
                            }
                            $.msg('저장 되었습니다.', "", "success");
                        }
                    });
                }

                //샘플 제작회차 +1
                if( 'fileSample' === dropzoneId ){
                    //제작의뢰서 등록 , 승인/반려가 되지 않은 상태.
                    /*
                    $.imsPost('addSampleCount',{
                        sno : sno,
                        count : vueApp.project.sampleCount,
                    }).then((data)=>{
                        if(200 === data.code){
                            //vueApp.project.sampleCount = Number(vueApp.project.sampleCount) + 1;
                        }
                    });
                    */
                }

                const completeCheckFieldMap = {
                    'filePlan'      : 'planEndDt',
                    'fileProposal'  : 'proposalEndDt',
                    'fileWork'      : 'workEndDt',
                    'fileSampleConfirm' : 'sampleEndDt',
                };
                for(let key in completeCheckFieldMap){
                    if( key === dropzoneId ){
                        $.imsPost('setCompleteDt',{
                            sno : sno,
                            field : completeCheckFieldMap[key] ,
                        }).then((data)=>{
                            if(200 === data.code){
                                vueApp.project[completeCheckFieldMap[key]] = data.data.project[completeCheckFieldMap[key]];
                            }
                        });
                    }
                }
            }
        });
    }

    $(()=>{
        //Load Data.
        ImsService.getData(DATA_MAP.PROJECT,sno).then((data)=>{
            if( 200 !== data.code  ){
                return false;
            }

            console.log('초기 데이터 : ',data.data);
            currentCustomerConfirm = data.data.project.customerOrderConfirm;

            const initParams = {
                data : {
                    swEtcFile : false,
                    items : data.data.customer,
                    project : data.data.project,
                    productList : data.data.productList,
                    fileList: data.data.fileList,
                    prepared: data.data.prepared,
                    preparedList: data.data.preparedList,
                    meeting : data.data.meeting,
                },
                mounted : (vueInstance)=>{
                    //Dropzone 셋팅.
                    $('.set-dropzone').addClass('dropzone');

                    ImsService.setDropzone(vueInstance, 'fileSample', uploadAfterAction);
                    ImsService.setDropzone(vueInstance, 'fileSampleConfirm', uploadAfterAction);

                    <?php foreach( $PROJECT_FILE_LIST as $idx => $prjFileField ) { ?>
                    ImsService.setDropzone(vueInstance, '<?=$prjFileField['fieldName']?>', uploadAfterAction);
                    <?php } ?>
                    <?php foreach( $PROJECT_ETC_FILE_LIST as $idx => $prjFileField ) { ?>
                    ImsService.setDropzone(vueInstance, '<?=$prjFileField['fieldName']?>', uploadAfterAction);
                    <?php } ?>
                },
                methods : {
                    openProductReg : (projectSno, sno)=>{
                        openProductReg(projectSno, sno);
                    },
                    save : ( items , project )=>{
                        project = $.refineDateToStr(project);
                        $.postAsync('<?=$imsAjaxUrl?>', {
                            mode:'saveProject',
                            saveCustomer : items,
                            saveProject  : project,
                        }).then((data)=>{
                            if(200 === data.code){
                                let saveSno = data.data.sno;
                                $.msg('저장 되었습니다.', "", "success").then(()=>{
                                    location.href='ims_project_view.php?sno=' + saveSno;
                                });
                            }
                        });
                    },
                    setCustomer : (customerSno)=>{
                        ImsService.getData(DATA_MAP.CUSTOMER, customerSno).then((data)=>{
                            if(200 === data.code){
                                vueApp.items = data.data;
                            }
                        });
                    },
                    saveDesignData : (project)=>{
                        $.imsPost('saveDesignData',{saveData : project}).then(()=>{
                            $.msg('저장 완료','', "success");
                        });
                    },
                    saveQcData : (project)=>{
                        $.imsPost('saveProjectEachData',{
                            sno : sno,
                            customerOrderDt : project.customerOrderDt,
                            customerDeliveryDt : project.customerDeliveryDt,
                            msOrderDt : project.msOrderDt,
                            msDeliveryDt : project.msDeliveryDt,
                            produceCompanySno : project.produceCompanySno,
                            produceType : project.produceType,
                            produceNational : project.produceNational,
                            customerOrderConfirm : project.customerOrderConfirm,
                        }).then((data)=>{
                            if(200 === data.code){
                                $.msg('저장 완료','', "success").then(()=>{
                                    location.reload();
                                });
                            }
                        });
                    },
                    setStatus : (project)=>{
                        if( currentStatus !== project.projectStatus ){
                            $.msgPrompt('변경사유 입력','','변경 사유 입력', (confirmMsg)=>{
                                if( confirmMsg.isConfirmed ){
                                    if( $.isEmpty(confirmMsg.value) ){
                                        $.msg('사유는 필수 입니다.', "", "warning");
                                        return false;
                                    }
                                    $.postAsync('<?=$imsAjaxUrl?>', {
                                        mode : 'setStatus'
                                        , projectSno : project.sno
                                        , reason : confirmMsg.value
                                        , projectStatus : project.projectStatus
                                    }).then((data)=>{
                                        if(200 === data.code){
                                            $.msg('상태가 변경되었습니다.','', "success").then(()=>{
                                                location.href=`ims_project_view.php?sno=${sno}&status=step${project.projectStatus}`;
                                            });
                                        }
                                    });
                                }
                            });
                        }else{
                            $.msg('현재 상태와 동일합니다.','','success');
                        }
                    },
                    setNextStep : (project, nextStep, msg)=>{
                        $.msgPrompt(msg,'','상태변경 메모', (confirmMsg)=>{
                            if( confirmMsg.isConfirmed ){
                                if( $.isEmpty(confirmMsg.value)){
                                    $.msg('상태 변경시 사유(메모) 필수','', "warning");
                                }else{
                                    autoSetStatus(project.sno, nextStep, confirmMsg.value);
                                }
                            }
                        });
                    },
                    checkNextStep60 : (project)=>{
                        if( 'step50' === '<?=$requestParam['status']?>' && 50 == project.projectStatus
                            && 'p' === project.planConfirm
                            && 'p' === project.sampleConfirm
                            && 'p' ===  project.proposalConfirm ){
                            return true;
                        }else{
                            return false;
                        }
                        // && 'p' === project.planConfirm && 'p' ===  project.sampleConfirm && 'p' ===  project.proposalConfirm
                    },
                    checkNextStep80 : (project)=>{
                        if( 'step60' === '<?=$requestParam['status']?>' && 60 == project.projectStatus && 'y' === currentCustomerConfirm    ){
                            return true;
                        }else{
                            return false;
                        }
                    },
                },
            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log(' ims_project_view.php Init OK');
        });
    });


</script>
