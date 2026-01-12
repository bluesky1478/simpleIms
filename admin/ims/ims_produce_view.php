<?php include 'library_all.php'?>
<?php include 'library.php'?>

<style>
    .ims-produce-schedule-table .mx-input-wrapper{ width:130px !important;}
</style>

<div class="" id="project-comment"  style="display: none">
    <textarea class="form-control" placeholder="코멘트 내용" id="editor"></textarea>
</div>

<section id="imsApp" >

    <form id="frm">
        <div class="page-header js-affix">
            <h3><span class="text-blue">{% items.customerName %} {% project.projectYear %} {% project.projectSeason %}  </span> 프로젝트 생산관리</h3>

            <div class="btn-group">
                <?php if(!$imsProduceCompany) { ?>
                    <input type="button" value="미팅정보" class="btn btn-white" @click="openMeeting(project.sno)" >
                    <input type="button" value="기획정보" class="btn btn-white" @click="openProjectView(project.sno)" >
                <?php } ?>
                <input type="button" value="목록" class="btn btn-white" @click="window.history.back()" >
                <input type="button" value="저장" class="btn btn-red btn-register" @click="save(produce, items, project)">
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-xs-12">
            <div class="col-xs-12" >
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <span style="font-size:15px;font-weight: bold">프로젝트번호 : </span>
                        <span style="font-size:15px;font-weight: bold" class="text-danger">{% project.projectNo %} (신규)</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="font-16"><b class="text-danger">이노버 요청 납기일 : {% project.msDeliveryDtShort %} ( <span v-html="project.msDeliveryRemainDt"></span> )</b></span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="font-16"><b>이노버발주일: {% project.msOrderDt %}</b></span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        생산관리등록일 : <span>{% produce.regDt %}</span>
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
                    <div class="flo-right">
                        <div class="btn btn-sm btn-white" @click="copyProject(project.sno)">프로젝트 복사</div>
                    </div>
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
                                        <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="allCheck" value="y" class="js-checkall" data-target-name="prdSno">
                                            </th>
                                            <th>번호</th>
                                            <th>이미지</th>
                                            <th>스타일명</th>
                                            <th>스타일코드</th>
                                            <th>제작수량</th>
                                            <th>생산가</th>
                                        </tr></thead>
                                        <tbody>
                                        <tr class="text-center" v-for="(prd, prdIndex) in productList">
                                            <td class="center">
                                                <div class="display-block">
                                                    <input type="checkbox" name="prdSno" :value="prd.sno" class="prd-sno">
                                                </div>
                                            </td>
                                            <td>{% prdIndex+1 %}</td>
                                            <td>
                                                <?php if(!$imsProduceCompany) { ?>
                                                <span class="hover-btn cursor-pointer"  @click="openProductReg(project.sno, prd.sno)">
                                                    <img src="/data/commonimg/ico_noimg_75.gif" v-show="$.isEmpty(prd.fileThumbnail)" class="middle" width="40">
                                                    <img :src="prd.fileThumbnail" v-show="!$.isEmpty(prd.fileThumbnail)" class="middle" width="40">
                                                </span>
                                                <?php }else{ ?>
                                                <span class="hover-btn cursor-pointer"  >
                                                    <img src="/data/commonimg/ico_noimg_75.gif" v-show="$.isEmpty(prd.fileThumbnail)" class="middle" width="40">
                                                    <img :src="prd.fileThumbnail" v-show="!$.isEmpty(prd.fileThumbnail)" class="middle" width="40">
                                                </span>
                                                <?php } ?>
                                            </td>
                                            <td><!--스타일명-->
                                                <?php if(!$imsProduceCompany) { ?>
                                                    <span class="font-16 text-blue hover-btn cursor-pointer" @click="openProductReg(project.sno, prd.sno)">
                                                        {% prd.productName %}
                                                    </span>
                                                <?php }else{ ?>
                                                    <span class="font-16">{% prd.productName %}</span>
                                                <?php } ?>
                                            </td>
                                            <td class="font-16"><!--스타일코드-->
                                                {% prd.styleCode.toUpperCase() %}
                                            </td>
                                            <td><!--제작수량-->
                                                {% $.setNumberFormat(prd.prdExQty) %}
                                            </td>
                                            <td><!--생산가-->

                                                <div v-if="1 == prd.prdCostStatus" class="text-muted">
                                                    (가) {% $.setNumberFormat(prd.prdCost) %}원
                                                </div>
                                                <div v-if="2 == prd.prdCostStatus">
                                                    (확) {% $.setNumberFormat(prd.prdCost) %}원
                                                </div>
                                                <div class="text-muted" v-if="0 != prd.prdCostStatus " >({% $.setNumberFormat(Number(prd.prdCost) * Number(prd.prdExQty) )%}원)</div>

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
                                        <button type="button" class="btn btn-sm btn-black mgr5" onclick="javascript:order_view_status_popup('exchange', '2307141214590015', '');">상품교환</button>
                                        <button type="button" class="btn btn-sm btn-black mgr5" onclick="javascript:order_view_status_popup('back', '2307141214590015', '');">상품반품</button>
                                    </div>
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
            <!-- 프로젝트 기본 정보 -->
            <div class="col-xs-12" >
                <div class="table-title gd-help-manual">
                    <div class="flo-left">기본정보</div>
                    <div class="flo-right"></div>
                </div>
                <div>
                    <table class="table table-cols">
                        <colgroup>
                            <col class="width-md" />
                            <col class="" />
                            <col class="width-md" />
                            <col class="" />
                            <col class="width-md" />
                            <col class="" />
                            <col class="width-md" />
                            <col class="" />
                        </colgroup>
                        <tbody>
                        <?php if(!$imsProduceCompany) { ?>
                            <tr>
                                <th>생산 업체</th>
                                <td colspan="99">
                                    <select2 class="js-example-basic-single" style="width:300px" v-model="produce.produceCompanySno" >
                                        <option value="0">미정</option>
                                        <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select2>
                                    <div class="btn btn-red btn-red-line2" @click="save(produce, items, project)">저장</div>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <th>스케쥴확정상태</th>
                            <td colspan="3">
                                <span class="font-16">{% produce.produceStatusKr %} 단계</span>
                                <div class="btn btn-white" @click="openStatusHistory(project.sno, 'produce')">상태변경이력</div>
                                <?php if($imsProduceCompany) { ?>
                                <div class="btn btn-red" @click="setProduceChangeStep('스케쥴 확정을 요청하시겠습니까?', 20, produce.sno)" v-show="10 == produce.produceStatus">스케쥴확정요청</div>
                                <?php } ?>

                                <?php if(!$imsProduceCompany) { ?>
                                <div class="btn claim-proc-status-4 hover-btn" style="color:#fff" @click="setProduceChangeStep('스케쥴 확정하시겠습니까?', 30, produce.sno)" v-show="20 == produce.produceStatus">스케쥴확정 </div>
                                <div class="btn btn-red" @click="setProduceChangeStep('스케쥴 반려(재요청) 하시겠습니까?', 10, produce.sno)" v-show="20 == produce.produceStatus">스케쥴반려</div>
                                <?php } ?>
                            </td>
                            <?php if(!$imsProduceCompany) { ?>
                            <th>스케쥴 확정 메모</th>
                            <td colspan="3">
                                <textarea class="form-control w100" v-model="produce.confirmMemo"></textarea>
                            </td>
                            <?php }else{ ?>
                                <th></th>
                                <td colspan="3"></td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <th>이노버 발주일</th>
                            <td>
                                <span class="font-16">{% project.msOrderDt %}</span>
                            </td>
                            <th>이노버 납기일</th>
                            <td>
                                <span class="font-16">{% project.msDeliveryDt %}</span>
                            </td>

                            <?php if($imsProduceCompany) { ?>
                            <th></th>
                            <td>

                            </td>
                            <th></th>
                            <td>

                            </td>
                            <?php }else{ ?>
                                <th>고객 발주일</th>
                                <td>
                                    <span class="font-16">{% project.customerOrderDt %}</span>
                                </td>
                                <th>고객 납기일</th>
                                <td>
                                <span class="font-16 text-danger">
                                    <b>{% project.customerDeliveryDtShort %}</b>
                                    ( <span v-html="project.customerDeliveryRemainDt"></span> )
                                </span>
                                </td>
                            <?php }?>
                        </tr>
                        <tr>
                            <th ><i class="fa fa-gift fa-lg" aria-hidden="true" ></i> 분류패킹 여부</th>
                            <td colspan="3">
                                <label class="radio-inline">
                                    <input type="radio" name="packingYn"  value="n"  v-model="project.packingYn" <?=empty($imsProduceCompany)?'':'disabled'?>  />미진행
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="packingYn"  value="y"  v-model="project.packingYn" <?=empty($imsProduceCompany)?'':'disabled'?> />진행
                                </label>
                            </td>
                            <th>
                                <i class="fa fa-gift fa-lg" aria-hidden="true" ></i> 분류패킹파일
                            </th>
                            <td colspan="99">
                                <div v-show="'y' === project.packingYn">
                                    <?php if(empty($imsProduceCompany)){ ?>
                                        <simple-file-upload :file="fileList.filePacking" :id="'filePacking'" :project="project" ></simple-file-upload>
                                    <?php }else{ ?>
                                        <simple-file-only :file="fileList.filePacking" :id="'filePacking'" :project="project" ></simple-file-only>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th ><i class="fa fa-university fa-lg" aria-hidden="true" ></i> 3PL 사용여부</th>
                            <td colspan="3">
                                <label class="radio-inline">
                                    <input type="radio" name="use3pl"  value="n"  v-model="items.use3pl" <?=empty($imsProduceCompany)?'':'disabled'?> />미사용
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="use3pl"  value="y"  v-model="items.use3pl" <?=empty($imsProduceCompany)?'':'disabled'?> />사용
                                </label>
                            </td>
                            <th ><i class="fa fa-university fa-lg" aria-hidden="true" ></i> 3PL 바코드파일</th>
                            <td colspan="3">
                                <div v-show="'y' === items.use3pl">
                                    <?php if(empty($imsProduceCompany)){ ?>
                                        <simple-file-upload :file="fileList.fileBarcode" :id="'fileBarcode'" :project="project" ></simple-file-upload>
                                    <?php }else{ ?>
                                        <simple-file-only :file="fileList.fileBarcode" :id="'fileBarcode'" :project="project" ></simple-file-only>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th ><i class="fa fa-internet-explorer fa-lg" aria-hidden="true"></i> 폐쇄몰 사용여부</th>
                            <td colspan="3">
                                <label class="radio-inline">
                                    <input type="radio" name="useMall"  value="n"  v-model="items.useMall" <?=empty($imsProduceCompany)?'':'disabled'?> />미사용
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="useMall"  value="y"  v-model="items.useMall" <?=empty($imsProduceCompany)?'':'disabled'?> />사용
                                </label>
                            </td>
                            <th ><i class="fa fa-internet-explorer fa-lg font-12" aria-hidden="true"></i> 폐쇄몰 출고가능일</th>
                            <td colspan="3">
                                <div v-show="'y' === items.useMall">
                                    <date-picker v-model="produce.privateMallDeliveryDt" value-type="format" format="YYYY-MM-DD" :lang="lang"  :editable="false" placeholder="출고가능일" style="width:130px !important;"></date-picker>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>납품방법</th>
                            <td colspan="3">
                                <textarea class="form-control" v-model="produce.deliveryMethod" rows="5" <?=empty($imsProduceCompany)?'':'disabled'?>></textarea>
                            </td>
                            <th>운송</th>
                            <td colspan="3">
                                <label class="radio-inline">
                                    <input type="radio" name="globalDeliveryDiv"  value="n"  v-model="produce.globalDeliveryDiv" <?=empty($imsProduceCompany)?'':'disabled'?> />미정
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="globalDeliveryDiv"  value="ship"  v-model="produce.globalDeliveryDiv" <?=empty($imsProduceCompany)?'':'disabled'?> /><i class="fa fa-ship" aria-hidden="true"></i> 선적
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="globalDeliveryDiv"  value="air"  v-model="produce.globalDeliveryDiv" <?=empty($imsProduceCompany)?'':'disabled'?> /><i class="fa fa-plane" aria-hidden="true"></i> 항공
                                </label>

                                <div class="mgt15 form-inline" v-show="'air' === produce.globalDeliveryDiv">

                                    항공 비용 부담 :

                                    <select class="form-control" v-model="produce.planPayDiv" >
                                        <option value="이노버">이노버</option>
                                        <option value="생산처">생산처</option>
                                        <option value="고객">고객</option>
                                    </select>

                                    <br>

                                    <div class="mgt10">항공 배송 메모</div>
                                    <textarea class="form-control w50" v-model="produce.planPayMemo" rows="3"></textarea>

                                </div>

                            </td>
                        </tr>
                        <tr>
                            <th>작업지시서</th>
                            <td colspan="99">
                                <simple-file-only :file="fileList.fileWork" :id="'fileWork'" :project="project" ></simple-file-only>
                            </td>
                        </tr>
                        <tr>
                            <th>케어라벨&마크</th>
                            <td colspan="99">
                                <simple-file-only :file="fileList.fileCareMark" :id="'fileCareMark'" :project="project" ></simple-file-only>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 ">
            <div class="col-xs-12 ">

                <div class="table-title gd-help-manual">
                    <div class="flo-left">생산관리 단계 비고</div>
                    <div class="flo-right"></div>
                </div>
                <div class="clear-both">
                    <table class="table table-cols">
                        <colgroup>
                            <col class="width-md">
                            <col class="width-3xl">
                            <col class="width-md">
                            <col class="width-3xl">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>
                                비고<span style="font-weight: normal">({% commentList.length %})</span>
                                <br><div class="btn btn-white btn-sm mgt5" @click="commentShowCnt=commentInitShowCnt" v-show="commentShowCnt > commentInitShowCnt ">최소화▲</div>
                                <br><div class="btn btn-white btn-sm mgt5" @click="commentShowCnt+=4" v-show="commentList.length > commentShowCnt" >더보기▼</div>
                            </th>
                            <td colspan="99" >
                                <div class="form-inline">

                                    <div v-show="'y' === swWriteComment">
                                        <span id="project-comment-area" class=""></span>
                                        <div class="btn btn-red hover-btn" @click="saveComment(project); swWriteComment='n'" style="padding: 10px 10px; width:100%">코멘트 쓰기 완료</div>
                                    </div>

                                    <div v-show="'n' === swWriteComment">
                                        <div class="btn btn-red btn-red-line2 hover-btn" @click="swWriteComment='y'" style="padding: 10px 10px; width:100%">코멘트 쓰기</div>
                                    </div>

                                    <table class="table table-rows mgt10 ims-comment-table">
                                        <colgroup>
                                            <col style="width:90px">
                                            <col class="width-xs">
                                            <col class="width-xs">
                                            <col class="width-xs">
                                            <col>
                                        </colgroup>
                                        <tbody>
                                        <tr>
                                            <th class="ta-c">번호</th>
                                            <th class="ta-c">등록일자</th>
                                            <th class="ta-c">등록자</th>
                                            <th class="ta-c">단계</th>
                                            <th class="ta-c">내용</th>
                                        </tr>
                                        <tr v-for="(comment, commentIndex) in commentList" v-show="commentShowCnt >= commentIndex">
                                            <td class="ta-c">
                                                <span class="font-16">{% commentList.length - commentIndex %}</span>
                                                <br>
                                                <div v-show="<?=$managerInfo['sno']?> == comment.regManagerSno " class="btn btn-sm btn-white btn-memo-delete" @click="modifyComment(commentList, comment, commentIndex)">수정</div>
                                                <div v-show="<?=$managerInfo['sno']?> == comment.regManagerSno " class="btn btn-sm btn-white btn-memo-delete" @click="deleteComment(commentList, comment, commentIndex)">삭제</div>
                                            </td>
                                            <td class="ta-c">{% comment.regDt %}</td>
                                            <td class="ta-c">{% comment.regManagerName %}</td>
                                            <td class="ta-c">{% comment.commentDivKr %}</td>
                                            <td>
                                                <div v-html="comment.commentBr"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="99" class="text-center">
                                                <div class="btn btn-white btn-sm mgt5" @click="commentShowCnt+=4" v-show="commentList.length > commentShowCnt" >더보기▼</div>
                                                <div class="btn btn-white btn-sm mgt5" @click="commentShowCnt=commentInitShowCnt" v-show="commentShowCnt > commentInitShowCnt ">최소화▲</div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div class="col-xs-12">
            <!-- 스케쥴관리 상세 -->
            <div class="col-xs-12" >
                <div class="table-title gd-help-manual">
                    <div class="flo-left">스케쥴관리(요약)</div>
                    <div class="flo-right">
                        <div class="btn btn-red btn-red-line2" @click="save(produce, items, project)">저장</div>
                    </div>
                </div>
                <div>
                    <table class="table table-cols table-pd-0 table-default-center ims-produce-schedule-table">
                        <tr>
                            <th style="background: #3f3f3f; color:#fff">항목</th>
                            <?php foreach( $PRODUCE_STEP_MAP as $stepKey => $stepTitle ) { ?>
                            <th><?=trim($stepTitle)?></th>
                            <?php } ?>
                        </tr>
                        <tr>
                            <th style="background: #3f3f3f; color:#fff">예정</th>
                            <?php foreach( $PRODUCE_STEP_MAP as $stepKey => $stepTitle ) { ?>
                            <td class="text-center " style="padding:3px!important;">
                                <date-picker v-model="produce.prdStep<?=$stepKey?>.expectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang"  :editable="false" placeholder="예정일자" style="width:130px !important;"></date-picker>
                                <div class="text-center w100" style="margin-top:2px">
                                    <input type="text" v-model="produce.prdStep<?=$stepKey?>.memo" class="" style="width:130px;border:solid 1px #d1d1d1; height:26px; border-radius: 5px; padding:5px" placeholder="일정 대체 내용">
                                </div>
                            </td>
                            <?php } ?>
                        </tr>

                        <?php if( 'step10' !== $requestParam['status'] && 'step20' !== $requestParam['status'] ) { ?>
                        <tr>
                            <th style="background: #3f3f3f; color:#fff">완료</th>
                            <?php foreach( $PRODUCE_STEP_MAP as $stepKey => $stepTitle ) { ?>
                                <td >
                                    <date-picker v-model="produce.prdStep<?=$stepKey?>.completeDt" value-type="format" format="YYYY-MM-DD" :lang="lang"  :editable="false" placeholder="완료일자" style="width:130px !important;"></date-picker>
                                </td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <th style="background: #3f3f3f; color:#fff">승인</th>
                            <?php foreach( $PRODUCE_STEP_MAP as $stepKey => $stepTitle ) { ?>
                                <td >
                                    <span v-html="produce.prdStep<?=$stepKey?>.confirmYnKr"></span>
                                </td>
                            <?php } ?>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>

            <!-- 스케쥴관리 상세 -->
            <div class="col-xs-12" >
                <div class="table-title gd-help-manual">
                    <div class="flo-left">
                        스케쥴관리(상세)
                        <span class="notice-info">파일이 등록되면 자동으로 승인 요청상태가 됩니다.</span>
                        <span class="notice-info">반려된 항목은 반드시 파일 다시올려 재승인 받아야합니다.</span>
                    </div>
                    <div class="flo-right">
                        <div class="btn btn-red btn-red-line2" @click="save(produce, items, project)">저장</div>
                    </div>
                </div>
                <div >
                    <table class="table table-cols">
                        <colgroup>
                            <col class="width-md">
                            <col class="width-3xl">
                            <col class="width-md">
                            <col class="width-3xl">
                        </colgroup>
                        <tbody>
                        <?php $idx=0 ?>
                        <?php foreach( $PRODUCE_STEP_MAP as $stepKey => $stepTitle ) { ?>
                            <?php if( 0 === $idx%2) { ?>
                            <tr>
                            <?php } ?>
                                <th ><?=$stepTitle?></th>
                                <td >
                                    <div>
                                        <span>예정일 :</span>
                                        <date-picker v-model="produce.prdStep<?=$stepKey?>.expectedDt" value-type="format" format="YYYY-MM-DD" :lang="lang"  :editable="false" placeholder="예정일자"></date-picker>

                                        <?php if( 'step10' !== $requestParam['status'] ) { ?>
                                        <span class="">완료일 :</span>
                                        <date-picker v-model="produce.prdStep<?=$stepKey?>.completeDt" value-type="format" format="YYYY-MM-DD" :lang="lang"  :editable="false" placeholder="완료일자"></date-picker>
                                        <?php } ?>
                                    </div>

                                    <div class="mgt10">
                                        <div class="ims-file-list-title">
                                            <!-- <i class="fa fa-trash-o hover-btn" aria-hidden="true"></i> -->
                                            <span><b>{% fileList.prdStep<?=$stepKey?>.title %}</b></span>
                                            <span>{% fileList.prdStep<?=$stepKey?>.memo %}</span>
                                        </div>

                                        <ul class="ims-file-list" >
                                            <li class="hover-btn" v-for="(file, fileIndex) in fileList.prdStep<?=$stepKey?>.files">
                                                <a :href="'<?=$nasDownloadUrl?>name='+file.fileName+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                                            </li>
                                        </ul>
                                        <form id="prdStep<?=$stepKey?>" class="set-dropzone mgt5" @submit.prevent="uploadFiles" v-show="'y' !== produce.prdStep<?=$stepKey?>.confirmYn">
                                            <div class="fallback">
                                                <input name="upfile" type="file" multiple @change="fileList.prdStep<?=$stepKey?>.files = $event.target.files" />
                                            </div>
                                        </form>
                                        <div class="btn btn-white" style="padding-top:10px;height:42px!important;" @click="openFileHistory(project.sno, 'prdStep<?=$stepKey?>')">업로드 이력</div>

                                        <?php if( strpos($stepTitle,'ⓒ')!==false ) { ?>
                                            <div class="mgt5 font-16">
                                                상태 : <span v-html="produce.prdStep<?=$stepKey?>.confirmYnKr"></span>
                                            </div>
                                        <?php } ?>
                                        <div class="mgt5">
                                            <?php if( strpos($stepTitle,'ⓒ')!==false && !$imsProduceCompany ) { ?>
                                                <div class="btn btn-accept hover-btn" @click="setPrdStepConfirmY('prdStep<?=$stepKey?>')" v-show="'y' !== produce.prdStep<?=$stepKey?>.confirmYn"  style="font-size:13px; font-weight: bold">승인</div><!--style="height:30px;padding:3px"-->
                                                <div class="btn btn-red hover-btn"  @click="setPrdStepConfirmN('prdStep<?=$stepKey?>')" v-show="'n' !== produce.prdStep<?=$stepKey?>.confirmYn">재요청(반려)</div>
                                                <div class="btn btn-white" @click="openStatusHistory(project.sno, 'prdStep<?=$stepKey?>')" >승인/반려 이력</div>
                                            <?php } ?>
                                        </div>

                                        <!--
                                        <div class="mgt10">
                                            <b>단계별 메모</b>
                                            <input type="text" v-model="produce.prdStep<?=$stepKey?>.memo" class="form-control w100">
                                        </div>
                                        -->

                                    </div>
                                </td>

                                <?php if( count($PRODUCE_STEP_MAP)-1 === $idx && !$imsProduceCompany ) { ?>
                                <th>생산완료 처리</th>
                                <td>
                                    <div class="btn btn-lg btn-gray" @click="setProduceChangeStep('현재 프로젝트를 생산완료 처리 하시겠습니까?', 99, produce.sno)" v-show="30 == produce.produceStatus" >생산완료 처리</div>
                                    <div class="notice-info">납기가 모두 완료된 후 생산완료 상태로 변경합니다.</div>
                                </td>
                                <?php } ?>


                            <?php if( 1 === $idx%2) { ?>
                            </tr>
                            <?php } ?>
                            <?php $idx++ ?>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-xs-12" ></div>

        </div>

    </div>

</section>

<script type="text/javascript">
    const sno = '<?=$requestParam['sno']?>';
    let currentStatus = null;
    $(appId).hide();

    const setPrdStepAccept = async (dropzoneId, confirmStatus, memo)=>{
        //vueApp.produce[dropzoneId].confirmYn = confirmStatus;
        return $.imsPost('setPrdStepConfirm', {
            projectSno: sno,
            prdStep: dropzoneId,
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

        let promptValue = '';
        if( 'filePacking' !== dropzoneId && 'fileBarcode' !== dropzoneId ){
            promptValue = window.prompt("메모입력 : ");
        }

        $.imsPost('saveProjectFiles',{
            saveData : {
                projectSno : sno,
                fileDiv : dropzoneId,
                fileList : saveFileList,
                memo : promptValue,
            }
        }).then((data)=>{
            vueApp.fileList[dropzoneId] = data.data[dropzoneId];
            setPrdStepAccept(dropzoneId, 'r', '파일등록으로 자동 승인요청').then((data)=>{
                if( !$.isEmpty(data.data) && 'false' !== data.data && false !== data.data  ){
                    vueApp.produce[dropzoneId]['confirmYnKr'] = data.data[dropzoneId]['confirmYnKr'];
                    vueApp.produce[dropzoneId]['confirmYn'] = data.data[dropzoneId]['confirmYn'];
                }
                $.msg('저장 되었습니다.', "", "success");
            });
        });
    }

    $(()=>{
        //Load Data.
        ImsService.getData(DATA_MAP.PRODUCE,sno).then((data)=>{
            console.log('초기 데이터 : ',data.data);
            const initParams = {
                data : {
                    swTotalMemo : 'y',
                    swWriteComment : 'y',
                    commentInitShowCnt : 4,
                    commentShowCnt : 4,
                    items : data.data.customer,
                    project : data.data.project,
                    productList : data.data.productList,
                    fileList: data.data.fileList,
                    preparedList: data.data.preparedList,
                    produce : data.data.produce,
                    commentList : data.data.commentList,
                },
                mounted : (vueInstance)=>{

                    $('#layerDim').show();
                    $('#app-hide').show();

                    //Dropzone 셋팅.
                    $('.set-dropzone').addClass('dropzone');
                    for(let fileDiv in vueInstance.fileList){
                        if(fileDiv.indexOf('prdStep') !== -1){
                            ImsService.setDropzone(vueInstance, fileDiv, uploadAfterAction);
                        }
                    }

                    ImsService.setDropzone(vueInstance, 'filePacking', uploadAfterAction);
                    ImsService.setDropzone(vueInstance, 'fileBarcode', uploadAfterAction);

                    $('#project-comment').appendTo('#project-comment-area');
                    $('#project-comment').show();

                    setTimeout(()=>{
                        vueInstance.swWriteComment = 'n';
                        $('#app-hide').hide();
                        $('#layerDim').hide();
                    },500);

                },
                methods : {
                    modifyComment : (commentList, comment, commentIndex)=>{
                        openCallView(`call_view.php?sno=${comment.projectSno}&commentSno=${comment.sno}`);
                    },
                    deleteComment : (commentList, comment, commentIndex)=>{
                        $.msgConfirm('코멘트를 삭제 하시겠습니까?','').then(function(result){
                            if( result.isConfirmed ){
                                $.imsPost('deleteComment',{
                                    'sno' : comment.sno,
                                }).then((data)=>{
                                    commentList.splice(commentIndex, 1);
                                });
                            }
                        });
                    },
                    saveComment : (project)=>{
                        oEditors.getById["editor"].exec("UPDATE_CONTENTS_FIELD", []);
                        if( '&nbsp;' !== $('#editor').val().replace(/<\/?p[^>]*>/gi, "") ){
                            $.imsPost('saveComment',{
                                'projectSno' : project.sno,
                                'comment' : $('#editor').val(),
                            }).then((data)=>{
                                vueApp.commentList = data.data;
                                $('#editor').val('');
                                oEditors.getById["editor"].exec("LOAD_CONTENTS_FIELD", []);
                            });
                        }
                    },
                    openCallView : (project, div)=>{
                        const sno = project.sno;
                        const url = `call_view.php?sno=${sno}&div=${div}`;
                        openCallView(url);
                    },
                    setPrdStepConfirmY : (dropzoneId)=>{

                        $.msgConfirm('승인 하시겠습니까?','승인 후 파일 업로드 불가.').then(function(result){
                            if( result.isConfirmed ){
                                //승인
                                $.msgPrompt('승인 메모 입력','','메모는 필수 사항은 아닙니다.', (confirmMsg)=>{
                                    if( confirmMsg.isConfirmed ){
                                        setPrdStepAccept(dropzoneId, 'y', confirmMsg.value).then((data)=>{
                                            vueApp.produce[dropzoneId]['confirmYnKr'] = data.data[dropzoneId]['confirmYnKr'];
                                            vueApp.produce[dropzoneId]['confirmYn'] = data.data[dropzoneId]['confirmYn'];
                                            $.msg('승인 되었습니다.', "", "success");
                                        });
                                    }
                                });
                            }
                        });
                    },
                    setPrdStepConfirmN : (dropzoneId)=>{
                        //반려
                        $.msgPrompt('반려사유 입력','','반려 사유 입력', (confirmMsg)=>{
                            if( confirmMsg.isConfirmed ){
                                if( $.isEmpty(confirmMsg.value) ){
                                    $.msg('반려 사유는 필수 입니다.', "", "warning");
                                    return false;
                                }
                                setPrdStepAccept(dropzoneId, 'n', confirmMsg.value).then(()=>{
                                    console.log(dropzoneId);
                                    //vueApp.produce[dropzoneId]['confirmYnKr'] = data.data[dropzoneId]['confirmYnKr'];
                                    //vueApp.produce[dropzoneId]['confirmYn'] = data.data[dropzoneId]['confirmYn'];
                                    $.msg('반려 처리 되었습니다.', "", "success").then(()=>{
                                        location.reload();
                                    });
                                });
                            }
                        });
                    },
                    openProductReg : (projectSno, sno)=>{
                        openProductReg(projectSno, sno);
                    },
                    save : ( produce, items, project )=>{
                        validProduceSchedule(produce).then((isContinue)=>{
                            if( isContinue ){
                                $.imsPost('saveProduce', {
                                    saveData : produce,
                                }).then((data)=>{
                                    project = $.refineDateToStr(project);
                                    $.msg('저장 되었습니다.', "", "success").then(()=>{
                                        $.imsPost('saveProject', {
                                            saveCustomer : items,
                                            saveProject  : project,
                                        });
                                    });
                                });
                            }
                        });
                    },
                    setCustomer : (customerSno)=>{
                        ImsService.getData(DATA_MAP.CUSTOMER, customerSno).then((data)=>{
                            vueApp.items = data.data;
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
                                        $.msg('상태가 변경되었습니다.','', "success");
                                    });
                                }
                            });
                        }else{
                            $.msg('현재 상태와 동일합니다.','','success');
                        }
                    },copyProject : (projectSno)=>{
                        //console.log(projectSno);
                        const prdSnoList = [];
                        $('input[name*="prdSno"]:checked').each(function(){
                            prdSnoList.push( $(this).val() );
                        });
                        //console.log(prdSnoList);

                        let subMsg = '';
                        if( prdSnoList.length > 0 ){
                            subMsg = prdSnoList.length + '의 스타일을 복사한 프로젝트로 복사 또는 이동합니다.';
                        }else{
                            alert('프로젝트 복사시 이동하거나 복사하실 스타일을 선택해주세요!');
                            return false;
                        }

                        $.msgConfirm('프로젝트를 복사 하시겠습니까?',subMsg).then(function(result){
                            if( result.isConfirmed ){
                                $.msgConfirm('스타일을 이동할지 복사할지 선택해주세요.<br>스타일 복사=>예 , 이동=>아니오',subMsg).then(function(result2){
                                    let prdCopy = 'n';
                                    if( result2.isConfirmed ){
                                        prdCopy = 'y';
                                    };
                                    //승인
                                    $.imsPost('copyProject',{
                                        'projectSno':projectSno,
                                        'prdSnoList':prdSnoList,
                                        'prdCopy':prdCopy,
                                    }).then((data)=>{
                                        const newSno = data.data;
                                        //console.log('ims_produce_view.php?sno='+ newSno +'&status=<?=$requestParam['status']?>');
                                        $.msg('프로젝트 복사가 완료 되었습니다.','', "success").then(()=>{
                                            location.href = 'ims_produce_view.php?sno='+ newSno +'&status=<?=$requestParam['status']?>';
                                        });
                                        //location.reload(); ims_produce_view.php?sno=46
                                    });

                                });
                            }
                        });

                    }
                }
            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        });
    });

</script>


<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/smart/js/service/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/smart/js/editorLoad.js?ss=<?= date('YmdHis') ?>" charset="utf-8"></script>