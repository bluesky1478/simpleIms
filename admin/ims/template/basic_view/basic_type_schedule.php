
<div class="row">
    <div class="col-xs-12" style="padding:15px;">
        <div class="table-title gd-help-manual">
            <div class="flo-left area-title">
                <div class="dp-flex pdb5">

                    <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
                    발주 준비 상태

                    <!--견적서-->
                    <button class="badge-button gray-button mgl20" v-if="customerEstimateList.length > 0" @click="window.open(`<?=$customerEstimateUrl?>?key=${customerEstimateList[0].key}`);" class="">
                        견적서
                    </button>
                    <button class="badge-button gray-button mgl20 disabled" v-if="0 >= customerEstimateList.length">
                        견적서
                        <span class="ims-tooltip">발송한 견적서가 없습니다.</span>
                    </button>

                    <button class="badge-button gray-button mgl10" @click="window.open(`<?=$assortUrl?>?key=<?=$projectKey?>`);">
                        아소트 고객 화면
                    </button>

                    <button class="badge-button gray-button mgl10" @click="window.open(`<?=$guideUrl?>?key=<?=$projectKey?>`);">
                        사양서 고객 화면
                    </button>

                </div>

            </div>
            <div class="flo-right"></div>
        </div>

        <div class="">
            <table class="mgb0 table table-cols w100 table-default-center table-pd-5 " style="table-layout: fixed; ">
                <tr>
                    <th >생산가 상태</th>
                    <th>판매가 상태</th>
                    <th>아소트 상태</th>
                    <th>작업지시서 상태</th>
                    <th colspan="2">사양서 상태</th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <td><!--생산가-->
                        <div v-html="$.getAcceptName(project.prdCostApproval)" class="font-16"></div>
                    </td>
                    <td><!--판매가-->
                        <div v-html="$.getAcceptName(project.prdPriceApproval)" class="font-16"></div>
                    </td>
                    <td><!--아소트상태-->
                        <div :class="$.getAssortAcceptNameColor(project.assortApproval)['color'] + ' font-16'">
                            {% $.getAssortAcceptNameColor(project.assortApproval)['name'] %}
                        </div>

                        <div class="mgt3" v-if="'n'===project.assortApproval">
                            <div class=" btn btn-sm btn-black-line" @click="visibleAssortSendUrl=true">
                                입력URL전달
                            </div>
                        </div>

                        <div class="mgt3 btn btn-sm btn-black-line"
                             v-if="'r'===project.assortApproval"
                             @click="visibleAssortSendUrl=true">
                            입력URL 재전달
                        </div>

                        <div class="mgt3 btn btn-sm btn-blue-line mgt3 cursor-pointer hover-btn"
                             v-if="'f'===project.assortApproval"
                             @click="setAssortStatus('p')">
                            아소트 확정 하기
                        </div>

                        <div class=" btn btn-sm btn-black-line mgt3"
                             v-if="'f'===project.assortApproval"
                             @click="setAssortStatus('r')">
                            상태변경하기(고객입력단계)
                        </div>

                        <!--아소트 입력URL 전송 레이어팝업-->
                        <ims-modal :visible.sync="visibleAssortSendUrl" title="아소트 입력 URL 전송정보">
                            <div class="dp-flex justify-content-start">
                                <label class="w-80px">담당자:</label>
                                <input type="text" class="form-control w-85p" v-model="project.assortReceiver">
                            </div>
                            <div class="dp-flex justify-content-start mgt5">
                                <label class="w-80px">Email:</label>
                                <input type="text" class="form-control w-85p" v-model="project.assortEmail">
                            </div>

                            <template #footer>
                                <div class="btn btn-blue mgt5" @click="sendAssortUrl(project.assortReceiver, project.assortEmail)">전송</div>
                                <div class="btn btn-white mgt5" @click="visibleAssortSendUrl=false">취소</div>
                            </template>
                        </ims-modal>

                    </td>
                    <td><!--작지상태-->
                        <div v-if="'0' == project.workStatus" class="font-16">
                            준비
                        </div>

                        <div v-if="'1' == project.workStatus" class="text-orange font-16">
                            진행
                        </div>
                        
                        <div v-if="'2' == project.workStatus" class="sl-green font-16">
                            완료
                        </div>
                    </td>
                    <td class="" colspan="2" ><!--사양서상태-->
                        <div class="dp-flex" style="justify-content: center">

                            <div v-html="$.getAcceptName(project.customerOrderConfirm)" class="font-16 mgr10"></div>

                            <div class="mgt3" >
                                <div class=" btn btn-sm btn-black-line"
                                     v-if="'n'===project.customerOrderConfirm"
                                     @click="visibleOrderSendUrl=true">
                                    사양서 전달
                                </div>
                            </div>

                            <div class="mgt3 btn btn-sm btn-black-line"
                                 v-if="'r'===project.customerOrderConfirm"
                                 @click="visibleOrderSendUrl=true">
                                사양서 재전달
                            </div>

                            <!-- 추가 기능 상태 변경 -> 요청 단계로 -->
                            <div class=" btn btn-sm btn-black-line mgt3"
                                 v-if="'p'===project.customerOrderConfirm"
                                 @click="setOrderStatus('r')">
                                상태변경하기(고객확인단계)
                            </div>
                        </div>

                        <!--사양서URL 전송 레이어팝업-->
                        <ims-modal :visible.sync="visibleOrderSendUrl" title="사양서 입력 URL 전송정보">
                            <div class="dp-flex justify-content-start">
                                <label class="w-80px">담당자:</label>
                                <input type="text" class="form-control w-85p" v-model="project.customerOrderReceiver">
                            </div>
                            <div class="dp-flex justify-content-start mgt5">
                                <label class="w-80px">Email:</label>
                                <input type="text" class="form-control w-85p" v-model="project.customerOrderEmail">
                            </div>
                            <template #footer>
                                <div class="btn btn-blue mgt5" @click="sendOrderUrl(project.customerOrderReceiver, project.customerOrderEmail)">전송</div>
                                <div class="btn btn-white mgt5" @click="visibleOrderSendUrl=false">취소</div>
                            </template>
                        </ims-modal>

                    </td>
                    <td class="">&nbsp;</td>
                    <td class="">&nbsp;</td>
                </tr>
            </table>

            <table class="table table-cols w100 table-default-center " style="table-layout: fixed; border-top:none !important;">
                <colgroup>
                    <col class="w-9p">
                    <col class="w-8p">
                    <col class="w-9p">
                    <col class="w-9p">
                    <col class="w-8p">
                    <col class="w-8p">
                    <col class="w-8p">
                </colgroup>
                <tr>
                    <th style="background-color: #eff5ff!important;">아소트 입력 발송일</th>
                    <th style="background-color: #eff5ff!important;">아소트 수신인</th>
                    <th style="background-color: #eff5ff!important;">고객 아소트 입력일</th>
                    <th style="background-color: #eff5ff!important;">이노버 아소트 확정일</th>
                    <th style="background-color: #ffefdb!important;">사양서 발송일</th>
                    <th style="background-color: #ffefdb!important;">사양서 수신인</th>
                    <th style="background-color: #ffefdb!important;">사양서 확정일</th>
                </tr>
                <tr>
                    <td><!--아소트발송-->
                        {% $.formatShortDateWithoutWeek(project.assortSendDt) %}
                    </td>
                    <td><!--아소트수신-->
                        {% project.assortReceiver %}
                        <span class="font-11">{% project.assortEmail %}</span>
                    </td>
                    <td><!--아소트입력-->
                        {% $.formatShortDateWithoutWeek(project.assortCustomerDt) %}
                    </td>
                    <td><!--아소트확정-->
                        {% $.formatShortDateWithoutWeek(project.assortManagerDt) %}
                        <span class="font-11">{% project.assortApprovalManager %}</span>
                    </td>
                    <td><!--사양서발송일-->
                        {% $.formatShortDateWithoutWeek(project.customerOrderSendDt) %}
                    </td>
                    <td><!--사양서수신자-->
                        {% project.customerOrderReceiver %}
                        <span class="font-11">{% project.customerOrderEmail %}</span>
                    </td>
                    <td><!--사양서확정일-->
                        <div v-show="!$.isEmpty(project.customerOrderConfirmDt)">
                            {% $.formatShortDateWithoutWeek(project.customerOrderConfirmDt) %}
                            <span class="font-11">{% project.customerOrderReceiver %}</span>
                        </div>
                    </td>
                </tr>

            </table>

            <div class="">'프로젝트 단위 상태'입니다.  스타일별 상태가 모두 완료되어야 프로젝트 단위 상태가 변경 됩니다.</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12" style="padding:15px;">

        <div class="table-title gd-help-manual">
            <div class="flo-left area-title">
                <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
                업무 스케쥴
            </div>
            <div class="flo-right">
                <div class="btn btn-white" @click="setModifyMode()" v-show="!isModify">수정</div>
                <div class="btn btn-red btn-red2" @click="saveProject()" v-show="isModify">저장</div>
                <div class="btn btn-white" @click="cancelProjectSave()" v-show="isModify">수정취소</div>
            </div>
        </div>

        <?php foreach($fieldList as $step => $fieldData) { ?>
            <div v-if="[<?=$fieldData['viewCondition']?>].indexOf(Number(project.projectStatus)) !== -1">
                <table class="table table-cols w100 table-default-center table-pd-0 table-td-height30 table-th-height30">
                    <colgroup>
                        <col class="width-xs"/>
                        <?php foreach($fieldData['list'] as $each) { ?>
                            <?php if( true === $each['split'] && !empty($each['col'])) { ?>
                                <col class="width-md"/>
                            <?php } ?>
                        <?php } ?>
                    </colgroup>
                    <tr>
                        <th>구분</th>
                        <?php foreach($fieldData['list'] as $each) { ?>
                            <?php if(true === $each['split']){ ?>
                                <th class="center">
                                    <?=$each['title']?>
                                </th>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                    <tr>
                        <th>예정일</td>
                        <?php foreach($fieldData['list'] as $each) { ?>
                            <?php if(true === $each['split']){ ?>
                                <td class="center bg-light-yellow">
                                    <div v-show="!isModify">
                                        <div v-if="!$.isEmpty(project.<?=$each['field']?>AlterText)" >
                                            {% project.<?=$each['field']?>AlterText %}
                                        </div>
                                        <div v-if="$.isEmpty(project.<?=$each['field']?>AlterText)" v-html="project.<?=$each['field']?>ExpectedDtShort"></div>
                                    </div>
                                    <div v-show="isModify">
                                        <date-picker v-model="project.<?=$each['field']?>ExpectedDt" value-type="format" format="YYYY-MM-DD" :editable="false" placeholder="<?=strip_tags($each['title'])?> 예정" class="" style="margin-top:0 !important;font-size:11px !important;"></date-picker>
                                    </div>
                                </td>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                    <tr>
                        <th>완료일</td>
                        <?php foreach($fieldData['list'] as $each) { ?>
                            <?php if(true === $each['split']){ ?>
                                <td class="<?=!empty($each['completeBlank'])?'bg-muted':''?>">
                                    <?php if( empty($each['completeBlank'])) { ?>
                                        <div v-show="!isModify">
                                            <div v-if="!$.isEmpty(project.<?=$each['field']?>AlterText)" >
                                                {% project.<?=$each['field']?>AlterText %}
                                            </div>
                                            <div v-if="$.isEmpty(project.<?=$each['field']?>AlterText)" v-html="$.formatShortDate(project.<?=$each['field']?>CompleteDt)"></div>
                                        </div>

                                        <div v-show="isModify">
                                            <?php if('planConfirm' === $each['approval'] || 'proposalConfirm' === $each['approval']) { ?>
                                                결재완료시 완료처리 됨
                                            <?php }else{ ?>
                                                <date-picker v-model="project.<?=$each['field']?>CompleteDt" value-type="format" format="YYYY-MM-DD" :editable="false" placeholder="<?=strip_tags($each['title'])?>" class="" style="margin-top:0 !important;font-size:11px !important;"></date-picker>
                                            <?php } ?>

                                            <div class=" mgt10">
                                                <span class="" style="display: inline-block !important;">
                                                    <input type="text" class="form-control" v-model="project.<?=$each['field']?>AlterText" placeholder="대체텍스트" style="display: inline-block !important;" maxlength="10">
                                                </span>
                                                <div class="mgt2 mgb3"><span @click="project.<?=$each['field']?>AlterText='해당없음'" class="text-blue line cursor-pointer hover-btn">해당없음</span></div>
                                            </div>

                                        </div>

                                    <?php } ?>
                                </td>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                    <tr>
                        <th>결재</th>
                        <?php foreach($fieldData['list'] as $each) { ?>
                            <?php if(true === $each['split']){ ?>
                                <?php if(!empty($each['completeBlank']) || empty($each['approval'])) { ?>
                                    <td class="bg-muted"></td>
                                <?php }else { ?>
                                    <td class="pd5">
                                        <?php if(!empty($each['fileDiv'])){ ?>
                                            <div v-show="'n' === project.<?=$each['approval']?>">
                                                <div class="btn btn-sm btn-red btn-red-line2 disabled" disabled>
                                                    결재요청<!--승인요청(파일을 올렸을 때)-->
                                                </div>
                                            </div>
                                            <div v-show="'r' === project.<?=$each['approval']?> && ( 0 >= projectApprovalInfo.<?=$each['field']?>.sno || $.isEmpty(projectApprovalInfo.<?=$each['field']?>.sno) ) ">
                                                <div class="btn btn-sm btn-red btn-red-line2" @click="openApprovalWrite(items.sno, project.sno, '<?=$each['field']?>')" >
                                                    결재요청<!--승인요청(파일을 올렸을 때)-->
                                                </div>
                                            </div>
                                        <?php }else{ ?>

                                            <?php if(\SiteLabUtil\SlCommonUtil::isDevId()) { ?>
                                            <!-- project.<?=$each['approval']?> : {% project.<?=$each['approval']?> %} -->
                                            <?php } ?>


                                            <div v-show="( 'r' === project.<?=$each['approval']?> || 'n' === project.<?=$each['approval']?> ) && ( 0 >= projectApprovalInfo.<?=$each['field']?>.sno || $.isEmpty(projectApprovalInfo.<?=$each['field']?>.sno) ) ">
                                                <div class="btn btn-sm btn-red btn-red-line2" @click="openApprovalWrite(items.sno, project.sno, '<?=$each['field']?>')" >
                                                    결재요청<!--승인요청(파일을 올렸을 때)-->
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <!--상태/버튼-->
                                        <div v-if="projectApprovalInfo.<?=$each['field']?>.sno > 0" class="text-left dp-flex">
                                            <!--상태-->
                                            <div class="mgt10 pdl5 text-green" v-show="'p' === project.<?=$each['approval']?>">
                                                결재완료
                                            </div>

                                            <div class="pd5 text-danger" v-show="'f' === project.<?=$each['approval']?>">
                                                반려
                                                <div class="btn btn-sm btn-red btn-red-line2" @click="openApprovalWrite(items.sno, project.sno, '<?=$each['field']?>')">
                                                    재결재요청
                                                </div>
                                            </div>

                                            <div class="pd5" v-show="'r' === project.<?=$each['approval']?>">
                                                결재 진행 중
                                            </div>

                                            <div class="btn btn-white btn-sm" @click="openApprovalHistory({projectSno:project.sno}, '<?=$each['field']?>')">결재이력</div>
                                        </div>

                                        <!--결재라인-->
                                        <div v-if="projectApprovalInfo.<?=$each['field']?>.sno > 0" class="text-left">
                                            <div class="font-11 pdt5 pdl5">
                                                기안:{% projectApprovalInfo.<?=$each['field']?>.regManagerNm %}
                                            </div>
                                            <div class="font-11 pd5 dp-flex_" style="justify-content: center; align-items: center;  ">
                                                <div @click="openApprovalView(projectApprovalInfo.<?=$each['field']?>.sno)" class="cursor-pointer hover-btn">

                                                    <div v-for="(target, targetIndex) in projectApprovalInfo.<?=$each['field']?>.targetManagerList" class="mgr5">
                                                        <i class="fa fa-chevron-right" aria-hidden="true" v-if="targetIndex >= 0"></i>
                                                        {% target.name %}

                                                        <span class="text-muted" v-show="'proc' === target.status">({% target.statusKr %}{% $.formatShortDateWithoutWeek(target.completeDt) %})</span>
                                                        <span class="text-danger" v-show="'reject' === target.status">({% target.statusKr %}{% $.formatShortDateWithoutWeek(target.completeDt)%})</span>
                                                        <span class="text-green" v-show="'accept' === target.status">({% target.statusKr %}{% $.formatShortDateWithoutWeek(target.completeDt)%})</span>

                                                        <span class="text-green" v-show="'complete' === target.status">({% target.statusKr %}{% $.formatShortDateWithoutWeek(target.completeDt)%})</span>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="0 >= projectApprovalInfo.<?=$each['field']?>.sno">
                                            <div class="btn btn-sm btn-red btn-red-line2" v-show="'r' === project.<?=$each['approval']?>" @click="openApprovalWrite(items.sno, project.sno, '<?=$each['field']?>')">
                                                결재요청 <!--승인요청(파일을 올렸을 때)-->
                                            </div>
                                        </div>
                                    </td>

                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                    <?php if( 'true' === $fieldData['fileCondition'] ) { ?>
                    <tr>
                        <th >파일</th>
                        <?php foreach($fieldData['list'] as $each) { ?>
                            <?php if(true === $each['split']){ ?>
                                <?php if(empty($each['fileDiv'])) { ?>
                                    <td class="bg-muted"></td>
                                <?php }else { ?>
                                    <td class="font-11 ">

                                        <?php if('작지/사양서' === $each['title']) { $upTitle = '사양서';  ?>
                                        <?php }else{ $upTitle=$each['title']; ?>
                                        <?php } ?>

                                        <div class="text-muted text-center font-11" v-show="'n' === project.<?=$each['approval']?>">(<?=$upTitle?> 파일 업로드 필요)</div>

                                        <div class="text-left">
                                            <simple-file-only-not-history-upload :file="fileList.<?=$each['fileDiv']?>" :project="project" v-show="!isModify" class="font-11"></simple-file-only-not-history-upload>
                                        </div>

                                        <div class="w-100p text-left" >
                                            <file-upload :file="fileList.<?=$each['fileDiv']?>" :id="'<?=$each['fileDiv']?>'" :project="project" :accept="'p'===project.<?=$each['approval']?>" v-show="isModify"></file-upload>
                                        </div>
                                    </td>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>
    </div>
</div>