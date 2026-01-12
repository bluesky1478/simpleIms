<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

<section id="imsApp">

    <form id="frm">
        <div class="page-header js-affix">
            <h3>
                <span class="text-blue">{% items.customerName %} {% project.projectYear %} {% project.projectSeason %}</span><?=$title?>
                <span class="text-danger" style="font-weight:normal" v-show="!$.isEmpty(project.projectNo)">({% project.projectStatusKr %}-{% project.projectNo %})</span>
            </h3>

            <input type="button" value="변경이력" class="btn btn-white" @click="openUpdateHistory(project.sno, 'project')" style="margin-right:150px">

            <?php if(empty($requestParam['popup'])) { ?>
                <input type="button" value="목록" class="btn btn-white" @click="window.history.back()" >
            <?php }else{ ?>
                <input type="button" value="닫기" class="btn btn-white" @click="self.close()"  >
            <?php } ?>
            <input type="button" value="<?=$saveBtnTitle?>" class="btn btn-red btn-register" @click="save(items, project, meeting)" style="margin-right:75px">
        </div>
    </form>

    <div class="col-xs-12 pd0" v-show="!$.isEmpty(project.sno)">
        <div class="panel panel-default">
            <div class="panel-heading">
                <span style="font-size:15px;font-weight: bold">프로젝트번호 : </span>
                <span style="font-size:15px;font-weight: bold" class="text-danger">{% project.projectNo %} ({% project.projectTypeKr %})</span>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                등록일시 : <span>{% project.regDt %}</span>

                <div class="pull-right">
                    <div class="form-inline">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" v-show="!$.isEmpty(project.sno)">
        <div class="col-xs-12" >

            <div class="table-title gd-help-manual">
                <div class="flo-left">스타일 정보</div>
                <div class="flo-right"></div>
            </div>
            <div id="tabOrderStatus ">
                <div class="tab-content">
                    <div class="table-action" style="margin-bottom: 0px !important; border-top:solid 1px #888888">
                        <div class="pull-left form-inline" style="height: 26px;">
                            <button type="button" class="btn btn-sm btn-red" @click="openProductReg(project.sno, '')">스타일 추가</button>
                        </div>
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
                                        <col style="width:6%"  /><!--가견적-->
                                        <col style="width:6%"  /><!--BT퀄리티-->
                                        <col style="width:6%"  /><!--생산견적-->
                                        <col style="width:6%"  /><!--제작수량-->
                                        <col style="width:6%"  /><!--현재단가-->
                                        <col style="width:6%"  /><!--타겟단가-->
                                        <col style="width:6%"  /><!--타겟생산가-->
                                        <col style="width:6%"  /><!--타겟마진-->
                                        <col style="width:6%"  /><!--생산가-->
                                        <col style="width:4%"  /><!--마진-->
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
                                        <th>가견적</th>
                                        <th>BT&퀄리티</th>
                                        <th>생산견적확정</th>
                                        <th>제작수량</th>
                                        <th>현재 단가</th>
                                        <th>타겟 단가</th>
                                        <th>타겟 생산가</th>
                                        <th>타겟 마진</th>
                                        <th>생산가</th>
                                        <th>마진</th>
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
                                            <span class="hover-btn cursor-pointer"  @click="openProductReg(project.sno, prd.sno)">
                                                <img src="/data/commonimg/ico_noimg_75.gif" v-show="$.isEmpty(prd.fileThumbnail)" class="middle" width="40">
                                                <img :src="prd.fileThumbnail" v-show="!$.isEmpty(prd.fileThumbnail)" class="middle" width="40">
                                            </span>
                                        </td>
                                        <td><!--스타일명-->
                                            <span class="font-16 text-blue hover-btn cursor-pointer" @click="openProductReg(project.sno, prd.sno)" >{% prd.productName %}</span>
                                        </td>
                                        <td style="padding-left:10px; text-align: left"><!--스타일코드-->
                                            <span class="font-16">{% prd.styleCode %}</span>
                                        </td>
                                        <td>
                                            미확정
                                        </td>
                                        <td>
                                            {% prd.fabricConfirmCount %}/{% prd.fabricCount %}
                                            <br><small class="text-muted">확정/원단</small>
                                        </td>
                                        <td>
                                            미확정
                                        </td>
                                        <td class="font-16"><!--제작수량-->
                                            {% $.setNumberFormat(prd.prdExQty) %}장
                                        </td>
                                        <td><!--현재단가-->
                                            {% $.setNumberFormat(prd.currentPrice) %}원
                                            <br><span class="text-muted">({% $.setNumberFormat(Number(prd.currentPrice) * Number(prd.prdExQty)) %}원)</span>
                                        </td>
                                        <td><!--타겟단가-->
                                            {% $.setNumberFormat(prd.targetPrice) %}원
                                            <br><span class="text-muted">({% $.setNumberFormat(Number(prd.targetPrice) * Number(prd.prdExQty)) %}원)</span>
                                        </td>
                                        <td><!--타겟생산가-->
                                            {% $.setNumberFormat(prd.targetPrdCost) %}원
                                            <br><span class="text-muted">({% $.setNumberFormat(Number(prd.targetPrdCost) * Number(prd.prdExQty)) %}원)</span>
                                        </td>
                                        <td><!--타겟마진-->
                                            {% $.setNumberFormat(prd.marginPercent)%}%
                                            <br><span class="text-muted display-none">({% $.setNumberFormat(prd.margin) %}원)</span>
                                        </td>
                                        <td >
                                            {% $.setNumberFormat(prd.prdCost) %}원
                                            <br><span class="text-muted">({% $.setNumberFormat(Number(prd.prdCost) * Number(prd.prdExQty) )%}원)</span>
                                        </td>
                                        <td >
                                            {% $.setNumberFormat(prd.msMargin) %}%
                                            <br><span class="text-muted display-none">({% $.setNumberFormat((prd.targetPrice-prd.targetPrdCost)*Number(prd.prdExQty))%}원)</span>
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

    <div class="row ">

        <!-- 프로젝트 기본 정보 -->
        <?php if(empty($requestParam['popup'])) { ?>
        <div class="col-xs-12" v-show="!$.isEmpty(project.sno)">
            <div class="table-title gd-help-manual">
                <div class="flo-left">프로젝트 상태</div>
                <div class="flo-right"></div>
            </div>
            <div>
                <table class="table table-cols">
                    <tbody>
                    <tr>
                        <th style="width:150px">프로젝트 상태</th>
                        <td colspan="3">
                            <span class="font-16">{% project.projectStatusKr %}</span>
                            <div class="btn btn-gray mgl15" @click="setStatus(project,20)"><b>다음단계(디자인 기획)로 상태 변경</b></div>
                            <div class="btn btn-white" @click="openStatusHistory(project.sno,'')">상태 변경 이력</div>
                        </td>
                    </tr>
                    <tr>
                        <th>프로젝트 별칭</th>
                        <td colspan="99" >
                            <div class="form-inline">
                                <input type="text" class="form-control" v-model="project.projectName" placeholder="프로젝트 별칭" style="width:300px;height:30px;">
                                <div class="btn btn-red btn-red-line2" @click="save(items, project, meeting)">저장</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            프로젝트 통합 비고<span style="font-weight: normal">({% commentList.length %})</span>
                            <br><div class="btn btn-white btn-sm mgt5" @click="commentShowCnt=commentInitShowCnt" v-show="commentShowCnt > commentInitShowCnt ">최소화▲</div>
                            <br><div class="btn btn-white btn-sm mgt5" @click="commentShowCnt+=4" v-show="commentList.length > commentShowCnt" >더보기▼</div>
                        </th>
                        <td colspan="99" >
                            <div class="form-inline">

                                <textarea class="form-control w50" rows="3" placeholder="코멘트 내용" id="project-comment"></textarea>
                                <div class="btn btn-red btn-red-line2 hover-btn" @click="saveComment(project)" style="padding: 20px 10px">코멘트 등록</div>

                                <table class="table table-rows mgt10">
                                    <colgroup>
                                        <col style="width:60px">
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
                                        <td class="ta-c">{% commentList.length - commentIndex %}
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
        <?php } ?>

        <div class="col-xs-12">
            <div class="table-title gd-help-manual">
                <div class="flo-left text-danger">미팅준비 정보</div>
                <div class="flo-right"></div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th class="require" >고객사 선택</th>
                        <td >
                            <select2 class="js-example-basic-single" v-model="project.customerSno" @change="setCustomer(project.customerSno)" style="width:100%" >
                                <option value="-1">신규등록</option>
                                <?php foreach ($customerListMap as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select2>
                        </td>
                        <th >영업담당자</th>
                        <td>
                            {% items.salesManagerNm %}
                        </td>
                        <th >디자인 담당자</th>
                        <td >
                            <select2 class="js-example-basic-single" v-model="project.designManagerSno"  style="width:100%" >
                                <?php foreach ($designManagerList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select2>
                        </td>
                        <th></th>
                        <td></td>
                    </tr>
                    <tr>
                        <th>미팅일자</th>
                        <td>
                            <date-picker v-model="meeting.meetingDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"></date-picker>
                        </td>
                        <th >시간</th>
                        <td>
                            <input type="text" class="form-control" placeholder="시간" v-model="meeting.meetingTime" >
                        </td>
                        <th >진행 사항</th>
                        <td colspan="3">
                            <input type="text" class="form-control" placeholder="진행 사항" v-model="meeting.location" >
                        </td>
                    </tr>
                    <tr>
                        <th>제안방향</th>
                        <td colspan="3">
                            <textarea class="form-control" v-model="meeting.readyItem" placeholder="제안방향" rows="5"></textarea>
                        </td>
                        <th >고객요청사항<br>(준비물품)</th>
                        <td colspan="3">
                            <textarea class="form-control" v-model="meeting.readyContents" placeholder="고객요청사항(준비물품)"  rows="5"></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 프로젝트 기본 정보 -->
        <div class="col-xs-6" >
            <div class="table-title gd-help-manual">
                <div class="flo-left">프로젝트 기본 정보</div>
                <div class="flo-right"></div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th >업무 시작일</th>
                        <td colspan="3">
                            <date-picker v-model="project.salesStartDt" value-type="format" format="YYYY-MM-DD" :lang="lang"  :editable="false"></date-picker>
                        </td>
                    </tr>
                    <tr>
                        <th class="require">발주일자</th>
                        <td>
                            <date-picker v-model="project.customerOrderDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"></date-picker>
                        </td>
                        <th class="require">납기일자</th>
                        <td>
                            <date-picker v-model="project.customerDeliveryDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"></date-picker>
                        </td>
                    </tr>
                    <tr>
                        <th >입찰</th>
                        <td>
                            <input type="text" class="form-control" placeholder="입찰" v-model="project.bid" >
                        </td>
                        <th >고객 제안마감일</th>
                        <td>
                            <date-picker v-model="project.recommendDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"></date-picker>
                        </td>
                    </tr>
                    <tr>
                        <th >발주D/L</th>
                        <td>
                            <date-picker v-model="project.customerOrderDeadLine" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"></date-picker>
                        </td>
                        <th v-show="$.isEmpty(project.sno)">초기상태</th>
                        <td v-show="$.isEmpty(project.sno)">
                            <select2 v-model="project.projectStatus" style="width:200px;" >
                                <?php foreach ($projectListMap as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select2>
                        </td>
                    </tr>
                    <tr>
                        <th >
                            프로젝트 타입
                        </th>
                        <td colspan="3">
                            <?php foreach ( $projectTypeMap as $key => $value ) { ?>
                                <label class="radio-inline">
                                    <input type="radio" name="projectType" value="<?=$key?>"  v-model="project.projectType" /><?=$value?>
                                </label>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="require">제안형태</th>
                        <td colspan="3">
                            <label class="checkbox-inline">
                                <input type="checkbox" value="1" v-model="project.recommend">
                                기획서<span class="ims-recommend ims-recommend1">기</span>
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" value="2" v-model="project.recommend">
                                제안서<span class="ims-recommend ims-recommend2">제</span>
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" value="4" v-model="project.recommend">
                                샘플<span class="ims-recommend ims-recommend4">샘</span>
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" value="8" v-model="project.recommend">
                                견적<span class="ims-recommend ims-recommend8">견</span>
                            </label>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 디자인실/QC 정보 -->
        <div class="col-xs-6" >
            <div class="table-title gd-help-manual">
                <div class="flo-left">디자인실 정보</div>
                <div class="flo-right"></div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <?php foreach($designField as $idx => $field) { ?>
                        <?php if(0 === ($idx % 2)) { ?>
                            <tr>
                        <?php } ?>
                        <?php if(0 === ($idx % 2)) { ?>
                            <th><?=$field['title']?>   </th>
                            <td colspan="3">

                                <date-picker v-model="project.<?=$field['name']?>" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="<?=$field['title']?> " ></date-picker>
                            </td>
                        <?php } ?>
                        <?php if( ($idx % 2) >  0 ) { ?>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                    <!--
                    <tr>
                        <th >QC마감일</th>
                        <td colspan="3">
                            <date-picker v-model="project.prdEndDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"></date-picker>
                        </td>
                    </tr>
                    -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-xs-12"></div>

    </div>

    <div class="row">
        <?php include './admin/ims/template/_template_customer.php'?>
    </div>

    <div class="row" v-show="!$.isEmpty(project.sno)">
        <!-- 고객 협의 사항 -->
        <div class="col-xs-6" >
            <div class="table-title gd-help-manual">
                <div class="flo-left">고객 협의 사항 (미팅보고)</div>
                <div class="flo-right"></div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <?php foreach($addedInfo as $fieldName => $title) { ?>
                        <?php $idx = preg_replace("/[^0-9]*/s", "", $fieldName);  ?>
                        <?php if( ($idx% 2) > 0 ) { ?>
                            <tr>
                        <?php } ?>
                        <th ><?=$title?></th>
                        <td <?=18==$idx?'colspan=3':''?>>
                            <input type="text" class="form-control" placeholder="<?=$title?>" v-model="project.addedInfo.<?=$fieldName?>" >
                        </td>
                        <?php if( 0 === $idx % 2 ) { ?>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 고객 요청사항 -->
        <div class="col-xs-6" >
            <div class="table-title gd-help-manual">
                <div class="flo-left">고객사 요청사항</div>
                <div class="flo-right"></div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <td colspan="4" class="pd0">
                            <textarea class="form-control" rows="23" v-model="project.projectMemo"></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row" >
        <div class="col-xs-12">
            <!-- 프로젝트 기본 정보 -->
            <div class="table-title gd-help-manual" >
                <div class="flo-left " >
                    미팅/입찰 추가 정보 파일
                </div>
                <div class="flo-right"></div>
            </div>

            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-3xl">
                        <col class="width-md">
                        <col class="width-3xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th >입찰 추가 정보 업로드</th>
                        <td >
                            <file-upload :file="fileList.fileMeeting" :id="'fileMeeting'" :project="project" ></file-upload>
                        </td>
                        <th >미팅 추가 정보 업로드</th>
                        <td >
                            <file-upload :file="fileList.fileEtc1" :id="'fileEtc1'" :project="project" ></file-upload>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</section>

<?php include './admin/ims/reg/_common_script.php'?>

