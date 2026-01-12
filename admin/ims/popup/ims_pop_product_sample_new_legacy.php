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
    <?php if ($sTabMenu == 'instruct') include './admin/ims/library_nk_sch_modal.php'?>
    <div class="modal" style="overflow-y:scroll; display:block!important;">
        <div class="">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" style="opacity: 1" @click="self.close();"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                    <span class="modal-title font-18 bold">
                        <span v-if="sampleView.sno == 0">샘플</span>
                        <span v-else>{% sampleView.productName %}의 <span class="sl-blue">{% sampleView.sampleName %}</span></span>
                        {% sampleView.sno == 0 ? '등록' : (isModify ? '수정' : '상세') %}
                    </span>
                    <div class="flo-right pdt5 pdl5 mgb5">
                        <?php if ($sTabMenu == 'instruct') { ?>
                        <div v-if="sampleView.sno > 0" @click="openUrl(`sampleInstructP_${sampleView.sno}`,`<?=$sampleInstructUrl?>?sno=${sampleView.sno}`,1600,950);" v-show="!isModify" class="btn btn_big btn-red hover-btn" style="margin-top:-13px;">샘플 지시서 인쇄</div>
                        <?php } else if ($sTabMenu == 'confirm') { ?>
                        <div v-if="sampleView.sno > 0" @click="openUrl(`sampleConfirmP_${sampleView.sno}`,`<?=$sampleConfirmUrl?>?sno=${sampleView.sno}`,1600,950);" v-show="!isModify" class="btn btn_big btn-red hover-btn" style="margin-top:-13px;">샘플 확정서 인쇄</div>
                        <?php } ?>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="table-title gd-help-manual">
                        <div v-show="sampleView.sno > 0">
                            <ul class="nav nav-tabs mgb20" role="tablist">
                                <li class="<?=$sTabMenu == 'instruct'?'active':''?>">
                                    <a href="/ims/popup/ims_pop_product_sample_new.php?sno=<?=$iSno?>&tabmenu=instruct" >샘플지시서</a>
                                </li>
                                <li class="<?=$sTabMenu == 'review'?'active':''?>">
                                    <a href="/ims/popup/ims_pop_product_sample_new.php?sno=<?=$iSno?>&tabmenu=review" >샘플리뷰서</a>
                                </li>
                                <li class="<?=$sTabMenu == 'confirm'?'active':''?>">
                                    <a v-if="bFlagEnableConfirm" href="/ims/popup/ims_pop_product_sample_new.php?sno=<?=$iSno?>&tabmenu=confirm" >샘플확정서</a>
                                    <a v-else @click="$.msg('샘플리뷰서를 먼저 작성하세요.','','warning');" href="#">샘플확정서</a>
                                </li>
                            </ul>
                        </div>

                        <div class="flo-left pdt5 pdl5">
                            # 기본정보
                            <span class="sl-green" v-if="'y' === sampleView.sampleConfirm">( 고객 확정 샘플 )</span>
                        </div>
                        <div class="flo-right pdt5 pdl5 mgb5">
                            <?php if ($sTabMenu == 'instruct') { ?>
                            <button type="button" v-show="!isModify && sampleView.sno > 0" @click="ImsProductService.fabricDownload(sampleView.sampleName)" class="btn btn-white btn-icon-excel simple-download mgr3">원부자재 다운로드</button>
                            <?php } ?>
                            <div class="btn btn-white" @click="isModify = true" v-show="!isModify">수정</div>
                            <div class="btn btn-white" @click="isModify = false" v-show=" sampleView.sno > 0  && isModify">수정취소</div>
                            <div class="btn btn-red" @click="saveSampleNew()" v-show="isModify">저장</div>
                        </div>
                    </div>
                    <div class="">
                        <?php if ($sTabMenu == 'confirm') { ?>
                            <table class="table table-cols table-default-center table-pd-5">
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
                                    <td>{% sampleView.customerName %}</td>
                                    <th>스타일</th>
                                    <td>{% sampleView.productName %}</td>
                                    <th>샘플 확정일</th>
                                    <td>{% $.formatShortDate(sampleView.sampleConfirmDt) %}</td>
                                </tr>
                                <tr>
                                    <th>성별</th>
                                    <td>{% sampleView.prdGenderHan %}</td>
                                    <th>제조국</th>
                                    <td>{% sampleView.produceNational %}</td>
                                    <th>예상 납기일</th>
                                    <td>{% $.formatShortDate(sampleView.sampleDeliveryDt) %}</td>
                                </tr>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <table class="table table-cols table-pd-5">
                                <colgroup>
                                    <col v-if="sampleView.sno > 0" class="width-20px">
                                    <col v-if="sampleView.sno > 0" class="width-xl">
                                    <col class="width-md">
                                    <col class="width-xl">
                                    <col class="width-md">
                                    <col class="width-xl">
                                    <col class="width-md">
                                    <col class="width-xl">
                                </colgroup>
                                <tbody>
                                <tr>
                                    <th v-if="sampleView.sno > 0" :rowspan="isModify == true ? 6 : 8">샘플<br/>썸네일</th>
                                    <td v-if="sampleView.sno > 0" :rowspan="isModify == true ? 6 : 8"><img v-if="checkImageExtension(val.fileName) && key == 0" :src="'<?=$nasUrl?>'+val.filePath" v-for="(val, key) in sampleView.fileList['sampleFile7'].files" style="width:100%;" /></td>
                                    <th>스타일기획</th>
                                    <td>
                                        {% sampleView.planConcept %}
                                        <span v-if="isModify && bFlagChangePlan === true">
                                        <span class="btn btn-blue-line" @click="schListModalServiceNk.popup({title:'스타일기획 검색',width:1150}, 'stylePlan', sampleView, oMatchFldNmSamplePlan, {'sRadioSchStyleSno':sampleView.styleSno}, 'getCustomerFit')">선택</span>
                                    </span>

                                        <div v-if="false"><!--샘플정보 불러오기. 주석처리-->
                                            <div v-if="isModify">
                                                <input type="text" class="form-control w120p inline-block" placeholder="샘플번호(숫자만)" v-model="loadSampleNo" >
                                                <div class="btn btn-gray" @click="loadSampleNk()">샘플 불러오기</div>
                                            </div>
                                            <div v-else >
                                                {% Number(sampleView.sno) + 1000 %}
                                            </div>
                                        </div>
                                    </td>
                                    <th>제작 차수</th>
                                    <td>
                                        <div v-if="isModify">
                                            <select v-model="sampleView.sampleTerm" class="form-control" style="width: 80px;">
                                                <?php for ($i = 1; $i <= 10; $i++) { ?>
                                                    <option value="<?=$i?>"><?=$i?>차</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div v-else >
                                            <div v-if="!$.isEmpty(sampleView.sampleTerm)">{% sampleView.sampleTerm %} 차</div>
                                            <div v-else class="text-muted">미입력</div>
                                        </div>
                                    </td>
                                    <th>샘플 구분</th>
                                    <td>
                                        <div v-if="isModify">
                                            <select v-model="sampleView.sampleType" class="form-control">
                                                <?php foreach(\Component\Ims\NkCodeMap::SAMPLE_TYPE as $key => $val) { ?>
                                                    <option value="<?= $key ?>"><?= $val ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div v-else >{% sampleView.sampleTypeHan %}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>샘플명</th>
                                    <td>
                                        <?php $model="sampleView.sampleName"; $placeholder='샘플명'; ?>
                                        <?php include './admin/ims/template/basic_view/_text.php'?>
                                    </td>
                                    <th>수량 / 사이즈</th>
                                    <td>
                                    <span v-if="isModify">
                                        <input type="text" v-model="sampleView.sampleCount" placeholder="수량" style="width: 50px; display: inline;" class="form-control" />
                                    </span>
                                        <span v-else >
                                        <span v-if="!$.isEmpty(sampleView.sampleCount)">{% sampleView.sampleCount %}</span>
                                        <span v-else class="text-muted">미입력</span>
                                    </span>
                                        /
                                        <span v-if="isModify">
                                        <input type="text" v-model="sampleView.fitSize" placeholder="사이즈" style="width: 70px; display: inline;" class="form-control" />
                                    </span>
                                        <span v-else >
                                        <span v-if="!$.isEmpty(sampleView.fitSize)">{% sampleView.fitSize %} 차</span>
                                        <span v-else class="text-muted">미입력</span>
                                    </span>
                                    </td>
                                    <th>담당자</th>
                                    <td>
                                    <span v-if="isModify">
                                        <select v-model="sampleView.sampleManagerSno" class="form-control">
                                            <option value="0">미선택</option>
                                            <?php foreach ($designManagerList as $key => $value ) { ?>
                                                <option value="<?=$key?>"><?=$value?></option>
                                            <?php } ?>
                                        </select>
                                    </span>
                                        <span v-else >{% sampleView.sampleManagerNm %}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>패턴실/연락처</th>
                                    <td>
                                    <span v-if="isModify">
                                        <select v-model="sampleView.patternFactorySno" class="form-control" style="display: inline;">
                                            <option value="0">선택</option>
                                            <?php foreach ($patternFactoryMap as $key => $value ) { ?>
                                                <option value="<?=$key?>"><?=$value?></option>
                                            <?php } ?>
                                        </select>
                                    </span>
                                        <span v-else>{% sampleView.patternFactoryName %}</span>
                                        / {% factoryTel[sampleView.patternFactorySno] != undefined ? factoryTel[sampleView.patternFactorySno] : '미정' %}
                                    </td>
                                    <th>패턴 의뢰일</th>
                                    <td>
                                        <?php $model="sampleView.patternRequestDt"; $placeholder='패턴 의뢰일'; ?>
                                        <?php include './admin/ims/template/basic_view/_picker2.php'?>
                                    </td>
                                    <th>패턴 납기일</th>
                                    <td>
                                        <?php $model="sampleView.patternDeliveryDt"; $placeholder='패턴 납기일'; ?>
                                        <?php include './admin/ims/template/basic_view/_picker2.php'?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>샘플실/연락처</th>
                                    <td>
                                    <span v-if="isModify">
                                        <select v-model="sampleView.sampleFactorySno" class="form-control" style="display: inline;">
                                            <option value="0">선택</option>
                                            <?php foreach ($sampleFactoryMap as $key => $value ) { ?>
                                                <option value="<?=$key?>"><?=$value?></option>
                                            <?php } ?>
                                        </select>
                                    </span>
                                        <span v-else>{% sampleView.factoryName %}</span>
                                        / {% factoryTel[sampleView.sampleFactorySno] != undefined ? factoryTel[sampleView.sampleFactorySno] : '미정' %}
                                    </td>
                                    <th>샘플 의뢰일</th>
                                    <td>
                                        <?php $model="sampleView.sampleRequestDt"; $placeholder='샘플 의뢰일'; ?>
                                        <?php include './admin/ims/template/basic_view/_picker2.php'?>
                                    </td>
                                    <th>샘플 납기일</th>
                                    <td>
                                        <?php $model="sampleView.sampleDeliveryDt"; $placeholder='샘플 납기일'; ?>
                                        <?php include './admin/ims/template/basic_view/_picker2.php'?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>사이즈스펙</th>
                                    <td>
                                        {% sampleView.fitName %}
                                        <?php if ($sTabMenu == 'instruct') { ?>
                                            <span class="btn btn-blue-line" v-show="isModify" @click="openCommonPopup('upsert_fit_spec', 640, 910, {'prdStyleGet':'<?=$sSendPrdStyle?>','prdSeasonGet':'<?=$sSendPrdSeason?>'});">보기</span>
                                        <?php } ?>
                                    </td>
                                    <th>샘플 투입일</th>
                                    <td>
                                        <?php $model="sampleView.sampleFactoryBeginDt"; $placeholder='샘플 투입일'; ?>
                                        <?php include './admin/ims/template/basic_view/_picker2.php'?>
                                    </td>
                                    <th>샘플 마감일</th>
                                    <td>
                                        <?php $model="sampleView.sampleFactoryEndDt"; $placeholder='샘플 마감일'; ?>
                                        <?php include './admin/ims/template/basic_view/_picker2.php'?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>메모</th>
                                    <td colspan="5">
                                        <div v-show="isModify">
                                            <textarea class="form-control" rows="2" v-model="sampleView.sampleMemo" placeholder="메모"></textarea>
                                        </div>
                                        <div v-show="!isModify" >
                                            <div v-if="!$.isEmpty(sampleView.sampleMemo)" v-html="sampleView.sampleMemo.replaceAll('\n','<br/>')"></div>
                                            <div v-else class="text-muted">미입력</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-show="!isModify">
                                    <th>썸네일 파일</th>
                                    <td>
                                        <simple-file-not-history-upload :file="sampleView.fileList.sampleFile7" id="sampleFile7" :params="sampleView" :accept="false"></simple-file-not-history-upload>
                                    </td>
                                    <th>샘플 도안 파일</th>
                                    <td>
                                        <simple-file-not-history-upload :file="sampleView.fileList.sampleFile8" id="sampleFile8" :params="sampleView" :accept="false"></simple-file-not-history-upload>
                                        <div>
                                            <div>
                                                <draggable v-model="sampleView.fileList.sampleFile8.files" @end="$.imsPost('modifySimpleDbCol', {'table_number':1, 'colNm':'fileList', 'where':{'sno':sampleView.fileList.sampleFile8.sno}, 'data':sampleView.fileList.sampleFile8.files});">
                                                    <div v-for="(file,fileIndex) in sampleView.fileList.sampleFile8.files" :key="fileIndex" class="cursor-pointer">
                                                        {% fileIndex+1 %} : {% file.fileName %}
                                                    </div>
                                                </draggable>
                                            </div>
                                        </div>
                                    </td>
                                    <th>마크 도안 파일</th>
                                    <td>
                                        <simple-file-not-history-upload :file="sampleView.fileList.sampleFile9" id="sampleFile9" :params="sampleView" :accept="false"></simple-file-not-history-upload>
                                    </td>
                                </tr>
                                <tr v-show="!isModify">
                                    <th>실물 사진</th>
                                    <td>
                                        <simple-file-not-history-upload :file="sampleView.fileList.sampleFile2" id="sampleFile2" :params="sampleView" :accept="false"></simple-file-not-history-upload>
                                    </td>
                                    <th>패턴 파일</th>
                                    <td>
                                        <simple-file-not-history-upload :file="sampleView.fileList.sampleFile3" id="sampleFile3" :params="sampleView" :accept="false"></simple-file-not-history-upload>
                                    </td>
                                    <th>마카 파일</th>
                                    <td>
                                        <simple-file-not-history-upload :file="sampleView.fileList.sampleFile10" id="sampleFile10" :params="sampleView" :accept="false"></simple-file-not-history-upload>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        <?php } ?>
                    </div>
                    <?php include './admin/ims/popup/ims_pop_product_sample_'.$sTabMenu.'.php'?>
                </div>
                <div class="modal-footer">
                    <div class="btn btn-white" @click="isModify=true;" v-show="!isModify">수정</div>
                    <div class="btn btn-white" @click="isModify=false" v-show=" sampleView.sno > 0  && isModify ">수정취소</div>
                    <div class="btn btn-red" @click="saveSampleNew()" v-show="isModify ">저장</div>
                    <div class="btn btn-gray" @click="self.close();">닫기</div>
                </div>
            </div>
        </div>
    </div>
    <div class="floating-area " style="z-index: 99999 !important; width:auto;">
        <div class="btn btn-white" @click="isModify=true;" v-show="!isModify">수정</div>
        <div class="btn btn-white" @click="isModify=false" v-show=" sampleView.sno > 0  && isModify ">수정취소</div>
        <div class="btn btn-red" @click="saveSampleNew()" v-show="isModify ">저장</div>
    </div>
</section>

<?php include './admin/ims/popup/script/ims_pop_product_sample_'.$sTabMenu.'_script.php'?>
