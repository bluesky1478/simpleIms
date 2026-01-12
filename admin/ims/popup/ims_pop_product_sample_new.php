<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

<style>
    input[type=number] { padding:4px 6px; }
    .btn.btn_big { margin-right:20px; height: 38px; line-height: 38px;font-size: 14px;font-weight: bold;padding: 0px 20px; }
    .table-default-center input { margin:0px auto; }
    .sample_review_image_detail li { display: inline-block; width: 50%; vertical-align: middle; }
</style>

<section id="imsApp">
    <div >
        <form id="frm">
            <div class="page-header js-affix mgb10">
                <h3>
                    <span v-if="oUpsertInfo.sno == 0">샘플</span>
                    <span v-else>{% oUpsertInfo.productName %}의 <span class="sl-blue">{% oUpsertInfo.sampleName %}</span></span>
                    {% oUpsertInfo.sno == 0 ? '지시서 등록' : (isModify ? '수정' : '상세') %}
                    </span>
                </h3>
                <!--최상위 버튼-->
                <div class="btn-group">
                    <?php if ($sTabMenu == 'instruct') { ?>
                        <!--<div  class="btn btn_big btn-red hover-btn" style="margin-top:-13px;">샘플 지시서 인쇄</div>-->
                        <input type="button" value="샘플 지시서 인쇄" class="btn btn-lg btn-white"
                               v-if="oUpsertInfo.sno > 0"
                               v-show="!isModify"
                               @click="openUrl(`sampleInstructP_${oUpsertInfo.sno}`,`<?=$sampleInstructUrl?>?sno=${oUpsertInfo.sno}`,1600,950);">
                    <?php } else if ($sTabMenu == 'confirm') { ?>
                        <!--<div  class="btn btn_big btn-red hover-btn" style="margin-top:-13px;"></div>-->
                        <input type="button" value="샘플 확정서 인쇄" class="btn btn-lg btn-red btn-red2"
                               v-if="oUpsertInfo.sno > 0"
                               v-show="!isModify"
                               @click="openUrl(`sampleConfirmP_${oUpsertInfo.sno}`,`<?=$sampleConfirmUrl?>?sno=${oUpsertInfo.sno}`,1600,950);">
                    <?php } ?>

                    <?php if ($sTabMenu == 'instruct') { ?>
                        <button type="button" v-show="!isModify && oUpsertInfo.sno > 0" @click="ImsProductService.fabricDownload(oUpsertInfo.sampleName)"
                                class="btn btn-white btn-icon-excel simple-download mgr3 font-13">&nbsp;&nbsp;원부자재 다운로드</button>
                    <?php } ?>

                    <input type="button" v-show="!isModify" @click="isModify = true" value="수정" class="btn btn-lg btn-red btn-red-line2 mgl10" />
                    <input type="button" v-show="isModify" @click="saveSampleNew()" value="저장" class="btn btn-lg btn-red btn-red2 mgl10" />
                    <input type="button" v-show="oUpsertInfo.sno > 0  && isModify" @click="isModify = false" value="수정취소" class="btn btn-lg btn-white mgl10" />
                    <input type="button" @click="self.close()" value="닫기" class="btn btn-white" />
                </div>
            </div>
        </form>

        <!--샘플 탭 / 컨텐츠 -->
        <div >
            <div class="table-title gd-help-manual">
                <!--샘플 탭메뉴(페이지 이동)-->
                <div v-show="oUpsertInfo.sno > 0">
                    <ul class="nav nav-tabs mgb20" role="tablist">
                        <li class="<?=$sTabMenu == 'instruct'?'active':''?>">
                            <a href="/ims/popup/ims_pop_product_sample_new.php?sno=<?=$iSno?>&tabmenu=instruct" class="font-13">샘플지시서</a>
                        </li>
                        <li class="<?=$sTabMenu == 'review'?'active':''?>">
                            <a href="/ims/popup/ims_pop_product_sample_new.php?sno=<?=$iSno?>&tabmenu=review" class="font-13">샘플리뷰서</a>
                        </li>
                        <li class="<?=$sTabMenu == 'confirm'?'active':''?>">
                            <a v-if="bFlagEnableConfirm" href="/ims/popup/ims_pop_product_sample_new.php?sno=<?=$iSno?>&tabmenu=confirm" class="font-13">샘플확정서</a>
                            <a v-else @click="$.msg('샘플리뷰서를 먼저 작성하세요.','샘플리뷰서에서 피팅체크를 설정하셔야 합니다.','warning');" href="#" class="font-13">샘플확정서</a>
                        </li>
                    </ul>
                </div>

                <div class="flo-left area-title">
                    <span class="godo"># 기본정보</span>
                    <span class="sl-green" v-if="'y' === oUpsertInfo.sampleConfirm">( 고객 확정 샘플 )</span>
                </div>
                <div class="flo-right pdt5 pdl5 mgb5">

                </div>
            </div>

            <!--기본정보-->
            <div class="">
                <?php if ($sTabMenu == 'confirm') { ?>
                    <table class="table table-cols table-default-center table-pd-5 table-th-height30 table-td-height30">
                        <colgroup>
                            <col class="w-10p" />
                            <col class="w-23p" />
                            <col class="w-10p" />
                            <col class="w-23p" />
                            <col class="w-10p" />
                            <col class="" />
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>고객사</th>
                            <td>{% oUpsertInfo.customerName %}</td>
                            <th>스타일</th>
                            <td>{% oUpsertInfo.productName %}</td>
                            <th>샘플 확정일</th>
                            <td>{% $.formatShortDate(oUpsertInfo.sampleConfirmDt) %}</td>
                        </tr>
                        <tr>
                            <th>성별</th>
                            <td>{% oUpsertInfo.prdGenderHan %}</td>
                            <th>제조국</th>
                            <td>{% oUpsertInfo.produceNational %}</td>
                            <th>예상 납기일</th>
                            <td>{% $.formatShortDate(oUpsertInfo.sampleDeliveryDt) %}</td>
                        </tr>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <table class="table ims-sample-table table-cols table-pd-5 table-th-height30 table-td-height30">
                        <colgroup>
                            <col v-if="oUpsertInfo.sno > 0" class="width-xl">
                            <col class="width-md">
                            <col class="width-xl">
                            <col class="width-md">
                            <col class="width-xl">
                            <col class="width-md">
                            <col class="width-xl">
                        </colgroup>
                        <tbody>
                        <tr>
                            <td v-if="oUpsertInfo.sno > 0" rowspan="6" >
                                <div v-if="!$.isEmpty(oUpsertInfo.fileList['sampleFile7']) && oUpsertInfo.fileList['sampleFile7'].files.length > 0" class="dp-flex dp-flex-center">
                                    <img :src="'<?=$nasUrl?>'+oUpsertInfo.fileList['sampleFile7'].files[0].filePath" class="w-100p h100p" style="height:100%"  />
                                </div>
                                <div v-else class="dp-flex dp-flex-center">
                                    <img src="<?=\Component\Ims\ImsCodeMap::DEFAULT_THUMBNAIL_IMG?>" class="w-100p" />
                                </div>
                            </td>
                            <th>스타일기획</th>
                            <td>
                                {% oUpsertInfo.planConcept %}
                                <span v-if="isModify && bFlagChangePlan === true">
                                    <span class="btn btn-blue-line" @click="schListModalServiceNk.popup({title:'스타일기획 검색',width:1150}, 'stylePlan', oUpsertInfo, oMatchFldNmSamplePlan, {'sRadioSchStyleSno':oUpsertInfo.styleSno}, 'getCustomerFit')">선택</span>
                                </span>

                                <div v-if="false"><!--샘플정보 불러오기. 주석처리-->
                                    <div v-if="isModify">
                                        <input type="text" class="form-control w120p inline-block" placeholder="샘플번호(숫자만)" v-model="loadSampleNo" >
                                        <div class="btn btn-gray" @click="loadSampleNk()">샘플 불러오기</div>
                                    </div>
                                    <div v-else >
                                        {% Number(oUpsertInfo.sno) + 1000 %}
                                    </div>
                                </div>
                            </td>
                            <th>제작 차수</th>
                            <td>
                                <div v-if="isModify">
                                    <select v-model="oUpsertInfo.sampleTerm" class="form-control" style="width: 80px;">
                                        <?php for ($i = 1; $i <= 10; $i++) { ?>
                                            <option value="<?=$i?>"><?=$i?>차</option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div v-else >
                                    <div v-if="!$.isEmpty(oUpsertInfo.sampleTerm)">{% oUpsertInfo.sampleTerm %} 차</div>
                                    <div v-else class="text-muted">미입력</div>
                                </div>
                            </td>
                            <th>샘플 구분</th>
                            <td>
                                <div v-if="isModify">
                                    <select v-model="oUpsertInfo.sampleType" class="form-control">
                                        <?php foreach(\Component\Ims\NkCodeMap::SAMPLE_TYPE as $key => $val) { ?>
                                            <option value="<?= $key ?>"><?= $val ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div v-else >{% oUpsertInfo.sampleTypeHan %}</div>
                            </td>
                        </tr>
                        <tr>
                            <th>샘플명</th>
                            <td>
                                <?php $model="oUpsertInfo.sampleName"; $placeholder='샘플명'; ?>
                                <?php include './admin/ims/template/basic_view/_text.php'?>
                            </td>
                            <th>수량 / 사이즈</th>
                            <td>
                                <span v-if="isModify">
                                    <input type="text" v-model="oUpsertInfo.sampleCount" placeholder="수량" style="width: 50px; display: inline;" class="form-control" />
                                </span>
                                <span v-else >
                                    <span v-if="!$.isEmpty(oUpsertInfo.sampleCount)">{% oUpsertInfo.sampleCount %}</span>
                                    <span v-else class="text-muted">미입력</span>
                                </span>
                                /
                                <span v-if="isModify">
                                    <input type="text" v-model="oUpsertInfo.fitSize" placeholder="사이즈" style="width: 70px; display: inline;" class="form-control" />
                                </span>
                                <span v-else >
                                    <span v-if="!$.isEmpty(oUpsertInfo.fitSize)">{% oUpsertInfo.fitSize %} 차</span>
                                    <span v-else class="text-muted">미입력</span>
                                </span>
                            </td>
                            <th>담당자</th>
                            <td>
                                <span v-if="isModify">
                                    <select v-model="oUpsertInfo.sampleManagerSno" class="form-control">
                                        <option value="0">미선택</option>
                                        <?php foreach ($designManagerList as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select>
                                </span>
                                <span v-else >{% oUpsertInfo.sampleManagerNm %}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>패턴실/연락처</th>
                            <td>
                                <span v-if="isModify">
                                    <select v-model="oUpsertInfo.patternFactorySno" class="form-control" style="display: inline;">
                                        <option value="0">선택</option>
                                        <?php foreach ($patternFactoryMap as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select>
                                </span>
                                <span v-else>{% oUpsertInfo.patternFactoryName %}</span>
                                / {% factoryTel[oUpsertInfo.patternFactorySno] != undefined ? factoryTel[oUpsertInfo.patternFactorySno] : '미정' %}
                            </td>
                            <th>패턴 의뢰일</th>
                            <td>
                                <?php $model="oUpsertInfo.patternRequestDt"; $placeholder='패턴 의뢰일'; ?>
                                <?php include './admin/ims/template/basic_view/_picker2.php'?>
                            </td>
                            <th>패턴 납기일</th>
                            <td>
                                <?php $model="oUpsertInfo.patternDeliveryDt"; $placeholder='패턴 납기일'; ?>
                                <?php include './admin/ims/template/basic_view/_picker2.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>샘플실/연락처</th>
                            <td>
                                <span v-if="isModify">
                                    <select v-model="oUpsertInfo.sampleFactorySno" class="form-control" style="display: inline;">
                                        <option value="0">선택</option>
                                        <?php foreach ($sampleFactoryMap as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select>
                                </span>
                                <span v-else>{% oUpsertInfo.factoryName %}</span>
                                / {% factoryTel[oUpsertInfo.sampleFactorySno] != undefined ? factoryTel[oUpsertInfo.sampleFactorySno] : '미정' %}
                            </td>
                            <th>샘플 의뢰일</th>
                            <td>
                                <?php $model="oUpsertInfo.sampleRequestDt"; $placeholder='샘플 의뢰일'; ?>
                                <?php include './admin/ims/template/basic_view/_picker2.php'?>
                            </td>
                            <th>샘플 납기일</th>
                            <td>
                                <?php $model="oUpsertInfo.sampleDeliveryDt"; $placeholder='샘플 납기일'; ?>
                                <?php include './admin/ims/template/basic_view/_picker2.php'?>
                            </td>
                        </tr>
                        <tr>
                            <th>사이즈스펙</th>
                            <td>
                                {% oUpsertInfo.fitName %}
                                <?php if ($sTabMenu == 'instruct') { ?>
                                    <span class="btn btn-blue-line" v-show="isModify" @click="openCommonPopup('upsert_fit_spec', 640, 910, {'prdStyleGet':'<?=$sSendPrdStyle?>','prdSeasonGet':'<?=$sSendPrdSeason?>'});">보기</span>
                                <?php } ?>
                            </td>
                            <th>샘플 투입일</th>
                            <td>
                                <?php $model="oUpsertInfo.sampleFactoryBeginDt"; $placeholder='샘플 투입일'; ?>
                                <?php include './admin/ims/template/basic_view/_picker2.php'?>
                            </td>
                            <th>샘플 마감일</th>
                            <td>
                                <?php $model="oUpsertInfo.sampleFactoryEndDt"; $placeholder='샘플 마감일'; ?>
                                <?php include './admin/ims/template/basic_view/_picker2.php'?>
                            </td>
                        </tr>

                        <tr >
                            <th>썸네일</th>
                            <td>
                                <span v-if="oUpsertInfo.sno == 0">샘플등록 후 첨부가능</span>
                                <span v-else-if="isModify">샘플상세보기 모드에서 첨부가능</span>
                                <simple-file-not-history-upload :file="oUpsertInfo.fileList.sampleFile7" id="sampleFile7" :params="oUpsertInfo" :accept="false" v-show="!isModify">
                                </simple-file-not-history-upload>
                            </td>
                            <th>실물 사진</th>
                            <td>
                                <span v-if="oUpsertInfo.sno == 0">샘플등록 후 첨부가능</span>
                                <span v-else-if="isModify">샘플상세보기 모드에서 첨부가능</span>
                                <simple-file-not-history-upload :file="oUpsertInfo.fileList.sampleFile2" id="sampleFile2" :params="oUpsertInfo" :accept="false" v-show="!isModify">
                                </simple-file-not-history-upload>
                            </td>
                            <th>메모</th>
                            <td colspan="5">
                                <div v-show="isModify">
                                    <textarea class="form-control" rows="2" v-model="oUpsertInfo.sampleMemo" placeholder="메모"></textarea>
                                </div>
                                <div v-show="!isModify" >
                                    <div v-if="!$.isEmpty(oUpsertInfo.sampleMemo)" v-html="oUpsertInfo.sampleMemo.replaceAll('\n','<br/>')"></div>
                                    <div v-else class="text-muted">미입력</div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                <?php } ?>
            </div>

            <?php include './admin/ims/popup/ims_pop_product_sample_'.$sTabMenu.'.php'?>

        </div>

        <div class="ta-c mg20">
            <div v-show="!isModify" @click="isModify=true;" class="btn btn-lg btn-red btn-red-line2">수정</div>
            <div v-show="isModify" @click="saveSampleNew()" class="btn btn-lg btn-red">저장</div>
            <div v-show="oUpsertInfo.sno > 0 && isModify" @click="isModify=false" class="btn btn-lg btn-white">수정취소</div>
            <div @click="self.close();" class="btn btn-lg btn-white">닫기</div>
        </div>

    </div>

    <!-- 우측 하단 플로팅 메뉴 -->
    <div class="ims-fab2" style="bottom:75px;" v-if="oUpsertInfo.sno > 0">
        <button type="button" class="ims-fab2-btn bg-red" aria-label="수정" v-show="!isModify" @click="isModify=true;">
            수정
        </button>
        <button type="button" class="ims-fab2-btn bg-red" aria-label="수정" v-show="isModify" @click="saveSampleNew();">
            저장
        </button>
        <button type="button" class="ims-fab2-btn bg-white font-black" aria-label="취소" v-show="isModify && oUpsertInfo.sno > 0" @click="isModify = false;">
            취소
        </button>
    </div>

</section>

<?php include './admin/ims/popup/script/ims_pop_product_sample_'.$sTabMenu.'_script.php'?>
