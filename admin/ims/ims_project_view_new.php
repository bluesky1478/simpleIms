<?php include 'library_all.php'?>
<?php include 'library.php'?>

<style>
    .mx-input{ font-size: 13px}
</style>

<section id="imsApp" class="project-view">

    <form id="frm">
        <div class="page-header js-affix">
            <h3>
                <span class="text-blue">{% items.customerName %} {% project.projectYear %} {% project.projectSeason %}</span> 프로젝트 상세정보
                <span class="text-danger" style="font-weight:normal" v-show="!$.isEmpty(project.projectNo)">({% project.projectStatusKr %}-{% project.projectNo %})</span>
            </h3>

            <div class="btn-group">
                <input type="button" value="To-DoList 요청" class="btn btn-red btn-red-line2 btn-white" @click="openTodoRequestWrite(items.sno,project.sno)" >
                <input type="button" value="변경이력" class="btn btn-white" @click="openUpdateHistory(project.sno, 'project')" >
                <?php if( !empty($requestParam['popup']) ) { ?>
                    <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
                <?php }else{ ?>
                    <input type="button" value="목록" class="btn btn-white" @click="window.history.back()" >
                <?php } ?>
                <!--BT 확정 내용/비고
                <input type="button" value="저장" class="btn btn-red btn-register" @click="save(items, project)">
                -->

                <?php if($isDev) { ?>
                    <div class="btn btn-white" @click="copyProject(project.sno)"><i class="fa fa-files-o" aria-hidden="true"></i> 프로젝트 복사</div>
                <?php } ?>

            </div>
        </div>
    </form>

    <div class="row ">
        <div class="col-xs-12" >
            <table class="table table-cols ">
                <colgroup>
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                </colgroup>
                <tr>
                    <th>고객명</th>
                    <td>{% items.customerName %}</td>
                    <th>입찰형태</th>
                    <td>단독입찰</td>
                    <th>프로젝트NO</th>
                    <td class="text-danger">{% project.projectNo %}</td>
                    <th>진행상태</th>
                    <td class="">{% project.projectStatusKr %}</td>
                    <th>프로젝트 등록일</th>
                    <td class="">{% project.regDt %}</td>
                </tr>
            </table>
        </div>
    </div>


    <section v-show="'basic' === tabMode">

        <div class="table-title gd-help-manual">
            <div class="flo-left">
                <!--생산 기본 정보-->
            </div>
            <div class="flo-right">
                <div class="btn btn-red btn-red-line2" @click="saveDesignData(project)">저장</div>
            </div>
        </div>


        <!--공개입찰일정-->
        <div v-show="'public' == isSingleBid">
            <table class="table table-cols table-center xsmall-picker">
                <colgroup>
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                </colgroup>
                <tbody>
                <tr >
                    <th >구분</th>
                    <th >입찰 설명회</th>
                    <th >기획서</th>
                    <th >제안서</th>
                    <th >
                        가견적
                        <div class="btn btn-white btn-sm">요청</div>
                    </th>
                    <th >제안서 확정</th>
                    <th >샘플</th>
                    <th >확정생산가</th>
                    <th >QB 확정</th>
                    <th >발주 D/L</th>
                    <th >고객 납기</th>
                </tr>
                <tr>
                    <th>예정일</th>
                    <td><!--입찰 설명회-->
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td><!--기획서-->
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td><!--제안서-->
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td><!--가견적-->
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td><!--제안서확정-->
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td><!-- 샘플 -->
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td><!--확정생산가-->
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td><!--QB확정-->
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td><!--발주DL-->
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td><!--발주DL-->
                        계약일로부터 106일
                    </td>
                </tr>
                <tr>
                    <th>완료일</th>
                    <td>
                        진행중
                    </td>
                    <td>
                        진행중
                    </td>
                    <td>
                        진행중
                    </td>
                    <td>
                        진행중
                    </td>
                    <td>
                        진행중
                    </td>
                    <td>
                        진행중
                    </td>
                    <td>
                        진행중
                    </td>
                    <td>
                        진행중
                    </td>
                    <td>
                        진행중
                    </td>
                    <td>
                        진행중
                    </td>
                </tr>
                </tbody>
            </table>
        </div>


        <!--단독입찰일정-->
        <div v-show="'single' == isSingleBid">
            <table class="table table-cols table-center xsmall-picker">
                <colgroup>
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                    <col class="width-md">
                </colgroup>
                <tbody>
                <tr >
                    <th >구분</th>
                    <th >기획서(디자인)</th>
                    <th >제안서</th>
                    <th >
                        가견적
                        <div class="btn btn-white btn-sm">요청</div>
                    </th>
                    <th >제안서 확정 예정</th>
                    <th >
                        QB 확정 D/L
                        <div class="btn btn-white btn-sm">요청?</div>
                    </th>
                    <th >발주 D/L</th>
                    <th >고객 안내일</th>
                </tr>
                <tr>
                    <th>예정일</th>
                    <td><!--기획서-->
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td><!--제안서-->
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td><!--가견적-->
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td><!--고객 제안서 확정 예정-->
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td><!--QB확정DL-->
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td><!--발주DL -->
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td><!--고객안내일-->
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                </tr>
                <tr>
                    <th>완료일</th>
                    <td>
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td>
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td>
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td>
                        <date-picker value="2024-06-01" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="" style="font-weight: normal"></date-picker>
                    </td>
                    <td>
                        진행중
                    </td>
                    <td>
                        진행중
                    </td>
                    <td>
                        미안내
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="row" >
            <div class="col-xs-12 text-right"  style="padding-right:25px; padding-bottom:0">
                <span class="hover-btn cursor-pointer" @click="showStyle=false" v-show="showStyle">
                    <i class="fa fa-chevron-up " aria-hidden="true" style="color:#7E7E7E"></i> 상품 숨기기
                </span>
                <span class="hover-btn cursor-pointer" @click="showStyle=true" v-show="!showStyle">
                    <i class="fa fa-chevron-up " aria-hidden="true" style="color:#7E7E7E"></i> 상품 보기
                </span>
            </div>
            <div>
                <?php include 'template/ims_project_view_new_style.php'?>
            </div>
        </div>

        <div class="row" >
            <div class="col-xs-6 ">
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
                            <th>
                                미팅 준비 보고서
                            </th>
                            <td class="bg-light-yellow">
                                <div>
                                    24/06/12 등록 완료 <span class="hover-btn cursor-pointer">[ 보기 ]</span>
                                </div>
                            </td>
                            <th>
                                입찰형태
                            </th>
                            <td class="bg-light-yellow">
                                <label class="radio-inline">
                                    <input type="radio" name="isSingleBid" value="single" checked v-model="isSingleBid" />단독입찰
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="isSingleBid" value="public" v-model="isSingleBid"  />공개입찰
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                제안형태
                            </th>
                            <td >
                                신규 제작
                            </td>
                            <th>
                                제안서 제출
                            </th>
                            <td >
                                2024.05.26 / <span class="text-green">변경 가능</span> (A타입)
                            </td>
                        </tr>
                        <tr>
                            <th>
                                샘플 제작
                            </th>
                            <td >
                                스타일별 1벌
                            </td>
                            <th>
                                샘플 제출일
                            </th>
                            <td >
                                2024.05.26 / <span class="text-danger">변경 불가</span>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                고객 의사 결정
                            </th>
                            <td>
                                샘플 제출 후 14일
                            </td>
                            <th>
                                現 유니폼
                            </th>
                            <td>
                                확보가능 06/12(수)
                            </td>
                        </tr>
                        <tr>
                            <th>
                                생산기간
                            </th>
                            <td >
                                사양서 확정 후 120일
                            </td>
                            <th>
                                납기일
                            </th>
                            <td>
                                2024. 10. 15 예상
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-xs-6 ">
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
                            <th>
                                업종
                            </th>
                            <td>
                                제조업
                            </td>
                            <th>
                                색상
                            </th>
                            <td >
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo1" value="a" />상
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo1" value="a1"  />중
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo1" value="a2" />하
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo1" value="u" checked/>미확인
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                유사 샘플 제공
                            </th>
                            <td>
                                고객요청
                            </td>
                            <th>
                                품질
                            </th>
                            <td >
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo2" value="a" />상
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo2" value="a1"  />중
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo2" value="a2" />하
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo2" value="u" checked/>미확인
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                폐쇄몰 관심도
                            </th>
                            <td>
                                관심없음
                            </td>
                            <th>
                                단가
                            </th>
                            <td >
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo3" value="a" />상
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo3" value="a1"  />중
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo3" value="a2" />하
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo3" value="u" checked/>미확인
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                재고관리 관심도
                            </th>
                            <td>
                                관심없음 자체 관리
                            </td>
                            <th>
                                납기
                            </th>
                            <td >
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo4" value="a" />상
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo4" value="a1" checked />중
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo4" value="a2" />하
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo4" value="u" checked/>미확인
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                현장 조사
                            </th>
                            <td >
                                가능
                            </td>
                            <th>
                                노조 유무
                            </th>
                            <td >
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo5" value="a2" />상
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo5" value="a" />중
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo5" value="a"  />하
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="showMemo5" value="u" checked/>미확인
                                </label>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <!--영업팀-->
            <div class="col-xs-6" >
                <div class="mgt10">
                    <table class="table table-cols table-center">
                        <colgroup>
                            <col class="width-md">
                            <col class="w-30p">
                            <col class="width-md">
                            <col class="width-md">
                            <col class="w-30p">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th colspan="99" class="relative text-left">
                                <div class="dp-flex-between">
                                    <span class="font-14 pdl15">영업팀</span>
                                    <div>
                                        <label class="radio-inline">
                                            <input type="radio" name="showMemo" value="r" checked />요청
                                        </label>
                                        <label class="radio-inline pdl5">
                                            <input type="radio" name="showMemo" value="c" />완료
                                        </label>
                                    </div>
                                    <div class="btn btn-white btn-sm">+more</div>
                                </div>
                                <!--<div class="btn btn-white btn-sm" style="position:absolute;top:5px; right:5px">+more</div>-->
                            </th>
                        </tr>
                        <tr>
                            <th>요청부서</th>
                            <th style="text-align:left !important;">요청사항</th>
                            <th>D/L</th>
                            <th>진행</th>
                            <th>처리사항</th>
                        </tr>
                        <tr>
                            <td>디자인실</td>
                            <td class="pdl10" style="text-align:left!important;">
                                고객 CI 확인 필요
                            </td>
                            <td>5/28</td>
                            <td>미확인</td>
                            <td class="pdl10" style="text-align:left!important;">
                            </td>
                        </tr>
                        <tr>
                            <td>생산팀</td>
                            <td class="pdl10" style="text-align:left!important;">
                                생산 수량 확인 필요
                            </td>
                            <td>6/4</td>
                            <td>진행</td>
                            <td class="pdl10" style="text-align:left!important;">
                            </td>
                        </tr>
                        <tr>
                            <td>생산팀</td>
                            <td class="pdl10" style="text-align:left!important;">
                                고객사 샘플 회수 요청
                            </td>
                            <td>6/3</td>
                            <td>완료</td>
                            <td class="pdl10" style="text-align:left!important;">
                                조치 완료
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!--디자인실-->
            <div class="col-xs-6" >
                <div class="mgt10">
                    <table class="table table-cols table-center">
                        <colgroup>
                            <col class="width-md">
                            <col class="w-30p">
                            <col class="width-md">
                            <col class="width-md">
                            <col class="w-30p">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th colspan="99" class="relative text-left">
                                <div class="dp-flex-between">
                                    <span class="font-14 pdl15">디자인실</span>
                                    <div>
                                        <label class="radio-inline">
                                            <input type="radio" name="req2" value="r" checked />요청
                                        </label>
                                        <label class="radio-inline pdl5">
                                            <input type="radio" name="req2" value="p" />진행
                                        </label>
                                        <label class="radio-inline pdl5">
                                            <input type="radio" name="req2" value="c" />완료
                                        </label>
                                    </div>
                                    <div class="btn btn-white btn-sm">+more</div>
                                </div>
                                <!--<div class="btn btn-white btn-sm" style="position:absolute;top:5px; right:5px">+more</div>-->
                            </th>
                        </tr>
                        <tr>
                            <th>요청부서</th>
                            <th style="text-align:left !important;">요청사항</th>
                            <th>D/L</th>
                            <th>진행</th>
                            <th>처리사항</th>
                        </tr>
                        <tr>
                            <td>영업팀</td>
                            <td class="pdl10" style="text-align:left!important;">
                                TKE 동점퍼 개선 자료 요청
                            </td>
                            <td>6/4</td>
                            <td>미확인</td>
                            <td class="pdl10" style="text-align:left!important;">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">

                            </td>
                            <td></td>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">

                            </td>
                            <td></td>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!--생산관리-->
            <div class="col-xs-6" >
                <div class="mgt10">
                    <table class="table table-cols table-center">
                        <colgroup>
                            <col class="width-md">
                            <col class="w-30p">
                            <col class="width-md">
                            <col class="width-md">
                            <col class="w-30p">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th colspan="99" class="relative text-left">
                                <div class="dp-flex-between">
                                    <span class="font-14 pdl15">생산관리</span>
                                    <div>
                                        <label class="radio-inline">
                                            <input type="radio" name="req3" value="r" checked />요청
                                        </label>
                                        <label class="radio-inline pdl5">
                                            <input type="radio" name="req3" value="c" />완료
                                        </label>
                                    </div>
                                    <div class="btn btn-white btn-sm">+more</div>
                                </div>
                                <!--<div class="btn btn-white btn-sm" style="position:absolute;top:5px; right:5px">+more</div>-->
                            </th>
                        </tr>
                        <tr>
                            <th>요청부서</th>
                            <th style="text-align:left !important;">요청사항</th>
                            <th>D/L</th>
                            <th>진행</th>
                            <th>처리사항</th>
                        </tr>
                        <tr>
                            <td>영업팀</td>
                            <td class="pdl10" style="text-align:left!important;">
                                가견적 요청
                            </td>
                            <td>6/12</td>
                            <td>완료</td>
                            <td class="pdl10" style="text-align:left!important;">
                                TR 변경시 단가 5% 절감 가능
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">

                            </td>
                            <td></td>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">

                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">

                            </td>
                            <td></td>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!--폐쇄몰-->
            <div class="col-xs-6" >
                <div class="mgt10">
                    <table class="table table-cols table-center">
                        <colgroup>
                            <col class="width-md">
                            <col class="w-30p">
                            <col class="width-md">
                            <col class="width-md">
                            <col class="w-30p">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th colspan="99" class="relative text-left">
                                <div class="dp-flex-between">
                                    <span class="font-14 pdl15">폐쇄몰</span>
                                    <div>
                                        <label class="radio-inline">
                                            <input type="radio" name="req4" value="r" checked />요청
                                        </label>
                                        <label class="radio-inline pdl5">
                                            <input type="radio" name="req4" value="c" />완료
                                        </label>
                                    </div>
                                    <div class="btn btn-white btn-sm">+more</div>
                                </div>
                                <!--<div class="btn btn-white btn-sm" style="position:absolute;top:5px; right:5px">+more</div>-->
                            </th>
                        </tr>
                        <tr>
                            <th>요청부서</th>
                            <th style="text-align:left !important;">요청사항</th>
                            <th>D/L</th>
                            <th>진행</th>
                            <th>처리사항</th>
                        </tr>
                        <tr>
                            <td>영업팀</td>
                            <td class="pdl10" style="text-align:left!important;">
                                폐쇄몰 관련 OPEN일정 확인
                            </td>
                            <td>6/14</td>
                            <td>미확인</td>
                            <td class="pdl10" style="text-align:left!important;">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">

                            </td>
                            <td></td>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">

                            </td>
                            <td></td>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!--개발팀 -->
            <div class="col-xs-6" >
                <div class="mgt10">
                    <table class="table table-cols table-center">
                        <colgroup>
                            <col class="width-md">
                            <col class="w-30p">
                            <col class="width-md">
                            <col class="width-md">
                            <col class="w-30p">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th colspan="99" class="relative text-left">
                                <div class="dp-flex-between">
                                    <span class="font-14 pdl15">개발팀</span>
                                    <div>
                                        <label class="radio-inline">
                                            <input type="radio" name="req13" value="r" checked />요청
                                        </label>
                                        <label class="radio-inline pdl5">
                                            <input type="radio" name="req13" value="c" />완료
                                        </label>
                                    </div>
                                    <div class="btn btn-white btn-sm">+more</div>
                                </div>
                                <!--<div class="btn btn-white btn-sm" style="position:absolute;top:5px; right:5px">+more</div>-->
                            </th>
                        </tr>
                        <tr>
                            <th>요청부서</th>
                            <th style="text-align:left !important;">요청사항</th>
                            <th>D/L</th>
                            <th>진행</th>
                            <th>처리사항</th>
                        </tr>
                        <tr>
                            <td>영업팀</td>
                            <td class="pdl10" style="text-align:left!important;">
                                폐쇄몰 커스텀 요청
                            </td>
                            <td>6/12</td>
                            <td>미확인</td>
                            <td class="pdl10" style="text-align:left!important;">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">

                            </td>
                            <td></td>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">

                            </td>
                            <td></td>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!--회계-->
            <div class="col-xs-6" >
                <div class="mgt10">
                    <table class="table table-cols table-center">
                        <colgroup>
                            <col class="width-md">
                            <col class="w-30p">
                            <col class="width-md">
                            <col class="width-md">
                            <col class="w-30p">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th colspan="99" class="relative text-left">
                                <div class="dp-flex-between">
                                    <span class="font-14 pdl15">회계</span>
                                    <div>
                                        <label class="radio-inline">
                                            <input type="radio" name="req41" value="r" checked />요청
                                        </label>
                                        <label class="radio-inline pdl5">
                                            <input type="radio" name="req41" value="c" />완료
                                        </label>
                                    </div>
                                    <div class="btn btn-white btn-sm">+more</div>
                                </div>
                                <!--<div class="btn btn-white btn-sm" style="position:absolute;top:5px; right:5px">+more</div>-->
                            </th>
                        </tr>
                        <tr>
                            <th>요청부서</th>
                            <th style="text-align:left !important;">요청사항</th>
                            <th>D/L</th>
                            <th>진행</th>
                            <th>처리사항</th>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">

                            </td>
                            <td></td>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">

                            </td>
                            <td></td>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">

                            </td>
                            <td></td>
                            <td></td>
                            <td class="pdl10" style="text-align:left!important;">

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row ">
            <div class="col-xs-12">
                <div class="table-title gd-help-manual">
                    <div class="flo-left">미팅 내용</div>
                    <div class="flo-right">

                    </div>
                </div>

                <div style="padding:10px; border:solid 1px #d1d1d1d1">

                    <p>1.&nbsp; &nbsp; &nbsp;브랜드디자인실 의견</p><p><br></p><p>1)&nbsp; &nbsp; &nbsp;인테리어 신규 사업 관련 유니폼 디자인 희망</p><p>2)&nbsp; &nbsp; &nbsp;브랜드디자인실에서 협업 담당자 인터뷰 결과 고객 접점에서 근무를 하기 때문에 전문가 답고 세련된 디자인 희망하여 유니폼 디지안 희망</p><p>3)&nbsp; &nbsp; &nbsp;유니폼 통한 신규 브랜드 마케팅 효과 기대</p><p><br></p><p>2.&nbsp; &nbsp; &nbsp;안전관리팀 의견</p><p><br></p><p>1)&nbsp; &nbsp; &nbsp;퍼시스 소속 직원이 아니기 때문에 신규디자인 유니폼 좋지 않다고 판단</p><p>2)&nbsp; &nbsp; &nbsp;가격이 비싸면 작업업체에서 부담으로 느낄 수 있음</p><p>3)&nbsp; &nbsp; &nbsp;인테레어 공사 업무 특성상 일용직 근무자들고 많기에 비싼 유니폼을 전부 공급하기 어려우며, 퍼시스 일만 하지 않기에 추후 퍼시스 유니폼이 좋아 다른 현장 작업에서 착용하여 사고 발생시 브랜 이미지 타격 있을 수 있음</p><p>4)&nbsp; &nbsp; &nbsp;현재 만원이하 저가형 기성 조끼 착용중</p><p><br></p><p>3.&nbsp; &nbsp; &nbsp;종합 의견</p><p>1)&nbsp; &nbsp; &nbsp;단가 부분 해결 방안에서는 한국타이어 같은 경우 본사에서 일부 부담하는 방안 안내하였고 퍼시스 내부에서도 마케팅 비용 투입 가능한지 검토 해보기로 함</p><p>2)&nbsp; &nbsp; &nbsp;기성복 관련해서도 이노버 폐쇄몰 통해서 공급 가능한 부분 안내하였고 이부분도 내부 검토</p><p><br></p><p>4.&nbsp; &nbsp; &nbsp;협의 내용</p><p>1)&nbsp; &nbsp; &nbsp;조끼 1,500벌 제작 대략적인 단가 제안시 마케팅 비용 관련 내부 검토해본다고 함</p>

                </div>

            </div>
        </div>

        <div class="row mgt20">
            <div class="col-xs-6" >
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-lg">
                        <col class="width-xl">
                        <col class="width-lg">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th colspan="99" class="text-center">
                            단독입찰
                        </th>
                    </tr>
                    <tr>
                        <th>
                            기타파일
                        </th>
                        <td colspan="99">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            기타파일2
                        </th>
                        <td colspan="99">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            기타파일3
                        </th>
                        <td colspan="99">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-xs-6" >
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-lg">
                        <col class="width-xl">
                        <col class="width-lg">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th colspan="99" class="text-center">
                            공개 입찰
                        </th>
                    </tr>
                    <tr>
                        <th>
                            제안 공고(가이드라인)
                        </th>
                        <td colspan="99">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            시방서
                        </th>
                        <td colspan="99">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            기타파일
                        </th>
                        <td colspan="99">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="row" v-show="false & !isFactory" >
            <!--기본정보-->
            <div class="col-xs-6" >
                <div class="table-title gd-help-manual">
                    <div class="flo-left">고객/프로젝트 기본 정보</div>
                    <div class="flo-right">
                        <span class="radio-inline" style="font-weight: normal;font-size:12px">납기일정의 </span>
                        <label class="radio-inline" style="font-weight: normal;font-size:12px">
                            <input type="radio" name="syncProduct"  value="y" v-model="project.syncProduct"/> 스타일연동
                        </label>
                        <label class="radio-inline" style="font-weight: normal;font-size:12px">
                            <input type="radio" name="syncProduct"  value="n" v-model="project.syncProduct"/> 별도관리
                        </label>
                        <div class="btn btn-red btn-red-line2" @click="saveDesignData(project)">저장</div>

                    </div>
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
                            <th>
                                고객사
                            </th>
                            <td>
                                <span class="font-16" >{% items.customerName %}</span>
                                <span class="text-danger">{% !$.isEmpty(items.use3plAndMall) ? `(${items.use3plAndMall})`:'' %}</span>
                                <div class="btn btn-sm btn-white" @click="openCustomer(items.sno)">상세</div>
                            </td>
                            <th>연도/시즌</th>
                            <td>
                                <select v-model="project.projectYear" class="form-control form-inline inline-block font-18" style="height: 35px; width:70px;">
                                    <?php foreach($yearList as $yearEach) {?>
                                        <option><?=$yearEach?></option>
                                    <?php }?>
                                </select>
                                <select v-model="project.projectSeason" class="form-control form-inline inline-block font-18" style="height: 35px; width:70px;">
                                    <option >ALL</option>
                                    <?php foreach($seasonList as $seasonEn => $seasonKr) {?>
                                        <option><?=$seasonEn?></option>
                                    <?php }?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                프로젝트 별칭
                            </th>
                            <td>
                                <input type="text" class="form-control" v-model="project.projectName" placeholder="프로젝트 별칭" style="width:100%;height:30px;">
                            </td>
                            <th>프로젝트 상태</th>
                            <td>
                                <select2 v-model="project.projectStatus" style="width:150px;" >
                                    <?php foreach ($projectListMap as $key => $value ) { ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php } ?>
                                </select2>
                                <div class="btn btn-red" @click="setStatus(project)">변경</div>
                            </td>
                        </tr>
                        <tr>
                            <th>프로젝트 타입</th>
                            <td colspan="3">
                                <?php foreach ( $projectTypeMap as $key => $value ) { ?>
                                    <label class="radio-inline">
                                        <input type="radio" name="projectType" value="<?=$key?>"  v-model="project.projectType" /><?=$value?>
                                    </label>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <th >영업 담당자</th>
                            <td>
                                <select2 class="js-example-basic-single" v-model="items.salesManagerSno"  style="width:100%" >
                                    <?php foreach ($managerList as $key => $value ) { ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php } ?>
                                </select2>
                            </td>
                            <th >디자인 담당자</th>
                            <td>
                                <select2 class="js-example-basic-single" v-model="project.designManagerSno"  style="width:100%" >
                                    <?php foreach ($designManagerList as $key => $value ) { ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php } ?>
                                </select2>
                            </td>
                        </tr>
                        <tr>
                            <th >고객 제안 마감일</th>
                            <td >
                                <date-picker v-model="project.recommendDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"></date-picker>
                            </td>
                            <th >이노버 발주</th>
                            <td>
                                <date-picker v-model="project.msOrderDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="이노버 발주"></date-picker>
                            </td>
                        </tr>
                        <tr>
                            <th >
                                <span class="sl-blue font-14">
                                    이노버 납기
                                </span>
                                <div class="mgt5" v-if="'y' === project.syncProduct">
                                    <div class="block-blue">연동</div>
                                </div>
                            </th>
                            <td>
                                <date-picker v-model="project.msDeliveryDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="이노버 납기"></date-picker>
                            </td>
                            <th >
                                <div class="text-danger font-14">고객 납기</div>
                                <div class="mgt5" v-if="'y' === project.syncProduct">
                                    <div class="block-blue">연동</div>
                                </div>
                            </th>
                            <td>
                                <date-picker v-model="project.customerDeliveryDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="고객 납기"></date-picker>
                                <div class="mgt5">
                                    <div>
                                        변경여부 :
                                        <label class="radio-inline">
                                            <input type="radio" name="deliveryConfirm"  value="y" v-model="project.customerDeliveryDtConfirmed"/>변경가능
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="deliveryConfirm"  value="n" v-model="project.customerDeliveryDtConfirmed"/>변경불가
                                        </label>
                                    </div>

                                    <div class="mgt5">
                                        납기확정 :
                                        <label class="radio-inline">
                                            <input type="radio" name="customerDeliveryDtStatus2"  value="n" v-model="project.customerDeliveryDtStatus2"/>미확정
                                        </label>
                                        <label class="radio-inline " style="margin-left:27px">
                                            <input type="radio" name="customerDeliveryDtStatus2"  value="y" v-model="project.customerDeliveryDtStatus2"/>확정
                                        </label>
                                    </div>

                                    <div class="mgt5 dp-flex" >
                                        확보상태 :
                                        <select class="form-control mgl5" v-model="project.customerDeliveryDtStatus">
                                            <?php foreach($customerDeliveryStatus as $cdsKey => $cdsValue) { ?>
                                                <option value="<?=$cdsKey?>"><?=$cdsValue?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th >
                                고객 발주일
                            </th>
                            <td>
                                <date-picker v-model="project.customerOrderDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="고객 발주"></date-picker>
                            </td>
                            <th ><span class="sl-purple font-14">발주D/L</span></th>
                            <td>
                                <date-picker v-model="project.customerOrderDeadLine" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"></date-picker>
                            </td>
                        </tr>
                        <tr>
                            <th rowspan="2">샘플비용 협의사항</th>
                            <td rowspan="2">
                                <div class="mgb10">
                                    <label class="radio-inline">
                                        <input type="radio" name="sampleCost"  value="0"  v-model="project.sampleCost"  />미확정
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="sampleCost"  value="1"  v-model="project.sampleCost" />무상
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="sampleCost"  value="2"  v-model="project.sampleCost" />유상
                                    </label>
                                </div>
                                <div class="mgt5">
                                    <textarea class="form-control w100 h100" placeholder="샘플비용 협의 메모" v-model="project.sampleMemo" rows="4"></textarea>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>배송비용 협의사항</th>
                            <td>
                                <textarea class="form-control w100 h100" v-model="project.deliveryCostMemo" placeholder="배송비용 협의사항"></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>

                <div class="table-title gd-help-manual">
                    <div class="flo-left"></div>
                    <div class="flo-right">
                        <div class="btn btn-red btn-red-line2" @click="saveDesignData(project)">저장</div>
                    </div>
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
                        <tr >
                            <th >메인 생산처</th>
                            <td>
                                <select2 class="js-example-basic-single" style="width:100%" v-model="project.produceCompanySno" >
                                    <option value="0">미정</option>
                                    <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php } ?>
                                </select2>
                            </td>
                            <th >
                                생산처 형태/국가
                            </th>
                            <td>
                                <div class="form-inline">
                                    <select class="form-control " v-model="project.produceType">
                                        <?php foreach ($prdType as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select>
                                    <select class="form-control" v-model="project.produceNational" placeholder="선택">
                                        <option value="">미정</option>
                                        <?php foreach ($prdNational as $key => $value ) { ?>
                                            <option value="<?=$value?>"><?=$value?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th ><i class="fa fa-university fa-lg" aria-hidden="true" ></i> 3PL 사용여부</th>
                            <td>
                                <label class="radio-inline">
                                    <input type="radio" name="use3pl"  value="n"  v-model="items.use3pl" <?=empty($imsProduceCompany)?'':'disabled'?> />미사용
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="use3pl"  value="y"  v-model="items.use3pl" <?=empty($imsProduceCompany)?'':'disabled'?> />사용
                                </label>
                            </td>
                            <th ><i class="fa fa-internet-explorer fa-lg" aria-hidden="true"></i> 폐쇄몰 사용여부</th>
                            <td >
                                <label class="radio-inline">
                                    <input type="radio" name="useMall"  value="n"  v-model="items.useMall" <?=empty($imsProduceCompany)?'':'disabled'?> />미사용
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="useMall"  value="y"  v-model="items.useMall" <?=empty($imsProduceCompany)?'':'disabled'?> />사용
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                3PL 바코드 파일
                            </th>
                            <td colspan="3">
                                <simple-file-upload :file="fileList.fileBarcode" :id="'fileBarcode'" :project="project" ></simple-file-upload>
                            </td>
                        </tr>
                        <tr>
                            <th >분류패킹 여부</th>
                            <td colspan="3">

                                <div >
                                    <label class="radio-inline">
                                        <input type="radio" name="packingYn"  value="n"  v-model="project.packingYn"  />미진행
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="packingYn"  value="y"  v-model="project.packingYn" />진행
                                    </label>
                                </div>

                                <div v-show="'y' === project.packingYn" >
                                    <simple-file-upload :file="fileList.filePacking" :id="'filePacking'" :project="project" ></simple-file-upload>
                                    <span class="notice-info">분류패킹 파일</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                납품 계획/방법 메모
                            </th>
                            <td colspan="3">
                                <textarea class="form-control w50 inline-block flo-left" rows="5" v-model="project.deliveryMethod" placeholder="납품 계획/방법 메모"></textarea>
                                <div class="flo-right">
                                    <simple-file-upload :file="fileList.fileDeliveryPlan" :id="'fileDeliveryPlan'" :project="project" ></simple-file-upload>
                                    <div class="notice-info">납품 계획 파일</div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>

            </div>

            <!--입찰/제안 정보-->
            <div class="col-xs-6" >
                <div class="table-title gd-help-manual">
                    <div class="flo-left">
                        입찰/제안 정보
                    </div>
                    <div class="flo-right">
                        <div class="btn btn-red btn-red-line2" @click="saveDesignData(project)">저장</div>
                    </div>
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
                                <th>입찰정보</th>
                                <td colspan="3">
                                    <input type="text" class="form-control" placeholder="입찰정보" v-model="project.bid" >
                                </td>
                            </tr>
                            <tr>
                                <th>제안형태</th>
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

            <!--퀄리티 정보-->
            <div class="col-xs-6" >
                <div class="table-title gd-help-manual">
                    <div class="flo-left">
                        퀄리티 정보 <span class="notice-info">(상태는 스타일별 개별관리)</span>
                    </div>
                    <div class="flo-right">
                        <div class="btn btn-red btn-red-line2" @click="saveDesignData(project)">저장</div>
                    </div>
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
                            <th>퀄리티메모/정보</th>
                            <td colspan="2" >
                                <textarea class="form-control w100 h100" rows="3" v-model="project.fabricStatusMemo" placeholder="퀄리티 수배상태 메모"></textarea>
                            </td>
                            <td >
                                <div>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" value="1" v-model="project.fabricNational" disabled>
                                        <span class="flag flag-16 flag-kr">
                                    </label>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" value="2" v-model="project.fabricNational" disabled>
                                        <span class="flag flag-16 flag-cn">
                                    </label>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" value="4" v-model="project.fabricNational" disabled>
                                        <span class="flag flag-16 flag-market">
                                    </label>
                                </div>

                                <select class="form-control font-17 mgt10" style="height: 50px;width:100%" v-model="project.fabricStatus" disabled>
                                    <option value="0">미확보</option>
                                    <option value="1">확보중</option>
                                    <option value="2">확보완료</option>
                                </select>
                            </td>
                        </tr>
                        <!--<tr>
                            <th>퀄리티확보(과거참조)</th>
                            <td colspan="99">
                                <div class="btn btn-white">MIG</div>
                            </td>
                        </tr>-->
                        </tbody>
                    </table>
                </div>
            </div>

            <!--디자인실정보-->
            <div class="col-xs-6" >
                <div class="table-title gd-help-manual">
                    <div class="flo-left">
                        디자인실 정보
                        <span class="notice-info">각 단계별 파일 업로드 일자를 완료일로 합니다. (업로드할 파일이 없는 경우 간략한 메모를한 텍스트 파일 업로드 바랍니다.)</span>
                    </div>
                    <div class="flo-right">
                        <div class="btn btn-red btn-red-line2" @click="saveDesignData(project)">저장</div>
                    </div>
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
                            <th >
                                <span class="font-16">기획</span>
                                <br><date-picker v-model="project.planDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="기획 예정일" style="width:140px;font-weight: normal"></date-picker>
                                <div class="mgt10">예정일: {% $.formatShortDate(project.planDt) %}</div>
                                <div>완료일: {% $.formatShortDate(project.planEndDt) %}</div>
                            </th>
                            <td colspan="3" class="relative">

                                <div class="mgl15 font-16 pd5" style="background-color:#eff7ff; position: absolute; top:10px; right:10px">
                                    기획 상태: <b :class="setAcceptClass(project['planConfirm'])" v-html="project['planConfirmKr']"></b>
                                </div>

                                <file-upload :file="fileList.filePlan" :id="'filePlan'" :project="project" :accept="'p'===project.planConfirm"></file-upload>

                                <div>
                                    <section>
                                        <div class="mgt5 " v-if="$.isEmpty(projectApprovalInfo.plan.sno) || 0 >= projectApprovalInfo.plan.sno">
                                            <div class="btn btn-accept hover-btn" @click="openApprovalWrite(items.sno, project.sno, 'plan')">
                                                결재요청
                                            </div>
                                        </div>
                                        <div class="mgt5 " v-if="'f'===project.planConfirm && 'reject' === projectApprovalInfo.plan.approvalStatus">
                                            <div class="btn btn-accept hover-btn" @click="openApprovalWrite(items.sno, project.sno, 'plan')">
                                                재결재요청
                                            </div>
                                        </div>

                                        <div v-if="projectApprovalInfo.plan.sno > 0">
                                            <div class="mgt10 pd0 bold font-14">
                                                승인정보
                                            </div>
                                            <div class="mgt5 font-12 "  >
                                                <span @click="openApprovalView(projectApprovalInfo.plan.sno)" class="cursor-pointer hover-btn">
                                                    기안:{% projectApprovalInfo.plan.regManagerNm %}
                                                    <span v-for="target in projectApprovalInfo.plan.targetManagerList">
                                                        <i class="fa fa-chevron-right" aria-hidden="true" ></i> {% target.name %}( {% target.statusKr %} {% $.formatShortDate(target.completeDt) %} )
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <th >
                                <span class="font-16">제안</span>
                                <br><date-picker v-model="project.proposalDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="제안 예정일" style="width:140px;font-weight: normal"></date-picker>
                                <div class="mgt10">예정일: {% $.formatShortDate(project.proposalDt) %}</div>
                                <div>완료일: {% $.formatShortDate(project.proposalEndDt) %}</div>
                            </th>
                            <td colspan="3" class="relative">

                                <div class="mgl15 font-16 pd5" style="background-color:#eff7ff; position: absolute; top:10px; right:10px">
                                    제안 상태: <b :class="setAcceptClass(project['proposalConfirm'])" v-html="project['proposalConfirmKr']"></b>
                                </div>

                                <file-upload :file="fileList.fileProposal" :id="'fileProposal'" :project="project" :accept="'p'===project.proposalConfirm"></file-upload>

                                <section>
                                    <div class="mgt5 " v-if="$.isEmpty(projectApprovalInfo.proposal.sno) || 0 >= projectApprovalInfo.proposal.sno">
                                        <div class="btn btn-accept hover-btn" @click="openApprovalWrite(items.sno, project.sno, 'proposal')">
                                            결재요청
                                        </div>
                                    </div>

                                    <div v-if="projectApprovalInfo.proposal.sno > 0">
                                        <div class="mgt10 pd0 bold font-14">
                                            승인정보
                                        </div>
                                        <div class="mgt5 font-12">
                                            <span @click="openApprovalView(projectApprovalInfo.proposal.sno)" class="cursor-pointer hover-btn">
                                                기안:{% projectApprovalInfo.proposal.regManagerNm %}
                                                <span v-for="target in projectApprovalInfo.proposal.targetManagerList">
                                                    <i class="fa fa-chevron-right" aria-hidden="true" ></i> {% target.name %}( {% target.statusKr %} {% $.formatShortDate(target.completeDt) %} )
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </section>

                                <div class="mgt10">
                                    <textarea class="form-control" placeholder="포트폴리오 고객 확정 정보" rows="5" v-model="project.proposalMemo"></textarea>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th >
                                <span class="font-16">샘플</span>
                                <br><date-picker v-model="project.sampleStartDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="예정일" style="width:140px;font-weight: normal"></date-picker>
                                <div class="mgt10">예정일: {% $.formatShortDate(project.sampleStartDt) %}</div>
                                <div>완료일: {% $.formatShortDate(project.sampleEndDt) %}</div>
                            </th>
                            <td colspan="3">
                                <div >
                                    <i class="fa fa-lg fa-chevron-circle-right" aria-hidden="true"></i>
                                    샘플은 유관부서에서 오프라인 확인 후 특정 승인자가 수기 승인 처리
                                    <br>(샘플 진행을 하지 않는 경우 강제 승인요청 하세요.)
                                </div>
                                <div class="mgt10 display-none">
                                    <b>샘플 작업자 :</b>
                                    <select2 class="js-example-basic-single" style="width:150px;" v-model="project.sampleManagerSno">
                                        <?php foreach ($designManagerList as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select2>

                                    &nbsp;&nbsp;&nbsp;<b>샘플실 :</b>
                                    <select2 class="js-example-basic-single" style="width:150px" v-model="project.sampleFactorySno">
                                        <?php foreach ($sampleFactoryMap as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select2>
                                </div>

                                <ims-accept :title="'샘플 승인상태'" :field="'sampleConfirm'" :project="project" :type="2" class="mgt5"></ims-accept>
                            </td>
                        </tr>
                        <tr>
                            <th >
                                <span class="font-16">사양서</span>
                                <!--
                                <div class="mgt10">등록일: {% project.proposalDt %}</div>
                                <div class="">확정일: {% project.proposalDt %}</div>
                                -->
                            </th>
                            <td colspan="3">
                                <file-upload :file="fileList.fileConfirm" :id="'fileConfirm'" :project="project"></file-upload>

                                <div class="mgt10">
                                    <label class="radio-inline">
                                        <input type="radio" name="customerOrderConfirmHeader"  value="n"  v-model="project.customerOrderConfirm"  />미확정
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="customerOrderConfirmHeader"  value="y"  v-model="project.customerOrderConfirm" />확정
                                    </label>
                                    <div v-show="'y' === project.customerOrderConfirm" class="mgt10">
                                        확정일자 :
                                        <date-picker v-model="project.customerOrderConfirmDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false" placeholder="확정일자" style="width:160px!important;"></date-picker>
                                        <span class="notice-info">확정날짜 공백시 오늘 날짜로 자동입력</span>
                                    </div>
                                </div>

                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        <!--파일 관리-->
        <div class="row display-none">
            <div class="col-xs-12" v-show="!isFactory">
                <div class="table-title gd-help-manual">
                    <div class="flo-left">프로젝트 파일</div>
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
                            <th>
                                견적서
                            </th>
                            <td colspan="3">
                                <simple-file-upload :file="fileList.fileEtc2" :id="'fileEtc2'" :project="project" ></simple-file-upload>
                            </td>
                            <th>영업 확정서</th>
                            <td colspan="3">
                                <simple-file-upload :file="fileList.fileEtc4" :id="'fileEtc4'" :project="project" ></simple-file-upload>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                입찰 추가 정보
                            </th>
                            <td colspan="3">
                                <simple-file-upload :file="fileList.fileMeeting" :id="'fileMeeting'" :project="project" ></simple-file-upload>
                            </td>
                            <th>미팅 추가 정보</th>
                            <td colspan="3">
                                <simple-file-upload :file="fileList.fileEtc1" :id="'fileEtc1'" :project="project" ></simple-file-upload>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                납품 보고서
                            </th>
                            <td colspan="3">
                                <simple-file-upload :file="fileList.fileDeliveryReport" :id="'fileDeliveryReport'" :project="project" ></simple-file-upload>
                            </td>
                            <th>
                                기타파일
                            </th>
                            <td colspan="3">
                                <simple-file-upload :file="fileList.fileEtc7" :id="'fileEtc7'" :project="project" ></simple-file-upload>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!--TODO : 추후 신/구 구분하여 표기-->
            <div class="col-xs-12" >
                <div class="table-title gd-help-manual">
                    <div class="flo-left">IMS 프로젝트 파일</div>
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
                            <th>
                                샘플 의뢰서
                            </th>
                            <td >
                                <simple-file-upload :file="fileList.fileSample" :id="'fileSample'" :project="project" :accept="true" ></simple-file-upload>
                                <simple-file-upload :file="fileList.sampleFile1" :id="'sampleFile1'" :project="project" :accept="true" ></simple-file-upload>
                            </td>
                            <th>샘플 패턴</th>
                            <td>
                                <simple-file-upload :file="fileList.filePattern" :id="'filePattern'" :project="project" :accept="true"></simple-file-upload>
                            </td>
                            <th>
                                샘플 웨어링
                            </th>
                            <td >
                                <simple-file-upload :file="fileList.fileEtc5" :id="'fileEtc5'" :project="project" :accept="true" ></simple-file-upload>
                            </td>
                            <th>샘플 기타파일</th>
                            <td>
                                <simple-file-upload :file="fileList.fileSampleEtc" :id="'fileSampleEtc'" :project="project" :accept="true"></simple-file-upload>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                샘플 실물사진
                            </th>
                            <td colspan="3">
                                <simple-file-upload :file="fileList.fileSampleConfirm" :id="'fileSampleConfirm'" :project="project" :accept="true" ></simple-file-upload>
                            </td>
                            <th>
                                작업지시서
                            </th>
                            <td colspan="99">
                                <simple-file-upload :file="fileList.fileWork" :id="'fileWork'" :project="project" :accept="true" ></simple-file-upload>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                케어라벨&마크
                            </th>
                            <td colspan="3">
                                <simple-file-upload :file="fileList.fileCareMark" :id="'fileCareMark'" :project="project" :accept="true" ></simple-file-upload>
                            </td>
                            <th>원부자재내역</th>
                            <td colspan="99">
                                <simple-file-upload :file="fileList.fileEtc6" :id="'fileEtc6'" :project="project" :accept="true"></simple-file-upload>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                분류패킹 파일
                            </th>
                            <td colspan="99">
                                <simple-file-only-not-history-upload :file="fileList.filePacking" :project="project" v-show="!isModify"></simple-file-only-not-history-upload>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row display-none">
            <div class="col-xs-12" >
                <div class="table-title gd-help-manual">
                    <div class="flo-left">구 생산 파일</div>
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
                            <th>
                                세탁 및 이화학검사
                            </th>
                            <td colspan="3">
                                <!--<simple-file-upload :file="fileList.prdStep10" :id="'prdStep10'" :project="project" :accept="true"></simple-file-upload>-->
                                <simple-file-upload :file="fileList.prdStep10" :id="'prdStep10'" :project="project" :accept="true"></simple-file-upload>
                            </td>
                            <th>원부자재 확정</th>
                            <td colspan="3">
                                <simple-file-upload :file="fileList.prdStep20" :id="'prdStep20'" :project="project" :accept="true"></simple-file-upload>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                원부자재 선적
                            </th>
                            <td colspan="3">
                                <simple-file-upload :file="fileList.prdStep30" :id="'prdStep30'" :project="project" :accept="true"></simple-file-upload>
                            </td>
                            <th>QC</th>
                            <td colspan="3">
                                <simple-file-upload :file="fileList.prdStep40" :id="'prdStep40'" :project="project" :accept="true"></simple-file-upload>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                인라인
                            </th>
                            <td colspan="3">
                                <simple-file-upload :file="fileList.prdStep50" :id="'prdStep50'" :project="project" :accept="true"></simple-file-upload>
                            </td>
                            <th>선적</th>
                            <td colspan="3">
                                <simple-file-upload :file="fileList.prdStep60" :id="'prdStep60'" :project="project" :accept="true"></simple-file-upload>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                도착
                            </th>
                            <td colspan="3">
                                <simple-file-upload :file="fileList.prdStep70" :id="'prdStep70'" :project="project" :accept="true"></simple-file-upload>
                            </td>
                            <th>입고제품검수</th>
                            <td colspan="3">
                                <simple-file-upload :file="fileList.prdStep80" :id="'prdStep80'" :project="project" :accept="true"></simple-file-upload>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>

    <div class="row" v-show="'meeting' === tabMode">
        <?php include 'template/ims_project_view_meeting.php'?>
    </div>

    <div class="row" v-show="'produce' === tabMode">
        <?php include 'template/ims_project_view_produce.php'?>
    </div>

    <div class="row" v-show="'comment' === tabMode">
        <?php include 'template/ims_project_view_comment.php'?>
    </div>

    <div class="row" v-show="'preOrder' === tabMode">
        <?php include 'template/ims_project_view_preorder.php'?>
    </div>

    <div style="margin-bottom:150px"></div>

</section>

<?php include 'script/ims_project_view_script.php'?>

