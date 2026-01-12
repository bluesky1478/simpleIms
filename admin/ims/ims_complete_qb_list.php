<?php include 'library_all.php'?>
<?php include 'library.php'?>

<section id="imsApp" class="project-view">

    <form id="frm">
        <div class="page-header js-affix">
            <h3> 확정 BT 리스트</h3>
            <div class="btn-group font-20 pdt10">
                <!-- 처리 요청 건 -> 스케쥴입력 : 0건, 퀄리티&BT요청 : 0건, 가견적: 0건, 생산가확정: 0건, 원부자재선행: 0건 -->
            </div>
        </div>
    </form>

    <div class="row" >


        <div class="col-xs-12 pd-custom" style="padding-top:0 !important;">
            <div>
                <div class="table-title ">
                    검색
                </div>
                <!--검색 시작-->
                <div class="search-detail-box form-inline">
                    <table class="table table-cols">
                        <colgroup>
                            <col class="width-md">
                            <col class="width-3xl">
                            <col class="width-md">
                            <col class="width-3xl">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>검색어</th>
                            <td >
                                <div v-for="(keyCondition,multiKeyIndex) in cqbSearchCondition.multiKey" class="mgb5">
                                    검색조건{% multiKeyIndex+1 %} : <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                                    <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="searchCqb()" />
                                    <div class="btn btn-sm btn-red" @click="cqbSearchCondition.multiKey.push($.copyObject(defaultMultiKey2))" v-if="(multiKeyIndex+1) === cqbSearchCondition.multiKey.length ">+추가</div>
                                    <div class="btn btn-sm btn-gray" @click="cqbSearchCondition.multiKey.splice(multiKeyIndex, 1)" v-if="cqbSearchCondition.multiKey.length > 1 ">-제거</div>
                                </div>
                                <div class="notice-info">다중 검색시 AND 검색</div>
                            </td>
                            <th>연도/시즌</th>
                            <td >
                                연도 : <input type="text" name="projectYear" value="<?= gd_isset($search['projectYear']); ?>" class="form-control w80p" placeholder="연도" v-model="cqbSearchCondition.year" style="width:80px" />
                                시즌 :
                                <select class="form-control" name="projectSeason" v-model="cqbSearchCondition.season">
                                    <option value="">선택</option>
                                    <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                                        <option value="<?=$codeKey?>" <?= $codeKey == $search['projectSeason'] ? 'selected':'' ; ?>><?=$codeKey?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td >

                            </td>
                            <?php if( empty($imsProduceCompany) ){ ?>
                                <th>의뢰처</th>
                                <td>
                                    <select2 class="js-example-basic-single" style="width:200px" v-model="cqbSearchCondition.reqFactory" >
                                        <option value="0">전체</option>
                                        <?php foreach ($produceCompanyList as $key => $value ) { ?>
                                            <option value="<?=$key?>"><?=$value?></option>
                                        <?php } ?>
                                    </select2>
                                </td>
                            <?php }else{ ?>
                                <td colspan="99"></td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td colspan="99" class="ta-c" style="border-bottom: none">
                                <input type="submit" value="검색" class="btn btn-lg btn-black" @click="searchCqb(1)">
                                <input type="submit" value="초기화" class="btn btn-lg btn-white" @click="qbConditionReset()">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!--검색 끝-->
            </div>


            <div >
                <div class="">
                    <div class="flo-left mgb5 mgt25">

                        <div class="dp-flex">

                            <span class="font-16 mgr10">
                            총 <span class="bold text-danger">{% $.setNumberFormat(cqbTotal.recode.total) %}</span> 건
                            </span>

                        </div>
                    </div>
                    <div class="flo-right mgb5">
                        <div class="bold font-18 ta-r">확정 BT 리스트</div>
                        <div style="display: flex">
                            <select @change="searchCqb()" class="form-control" v-model="cqbSearchCondition.sort">
                                <option value="C1,desc">납기D/L ▼</option>
                                <option value="C1,asc">납기D/L ▲</option>
                                <option value="B,desc">고객사별 ▼</option>
                                <option value="B,asc">고객사별 ▲</option>
                            </select>

                            <select v-model="cqbSearchCondition.pageNum" @change="searchCqb(1)" class="form-control mgl5">
                                <option value="20">20개 보기</option>
                                <option value="50">50개 보기</option>
                                <option value="100">100개 보기</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="">
                    <table class="table table-rows table-default-center mgt5">
                        <colgroup>
                            <col class="w-12p"/><!--<th>고객사/프로젝트</th>-->
                            <col class="w-12p"/><!--<th>스타일</th>-->
                            <col class="w-9p"/><!--<th>원단명</th>-->
                            <col class="w-12p"/><!--<th>원단정보</th>-->
                            <col class="w-15p"/><!--<th>퀄리티확정내용/비고</th>-->
                            <col class="w-15p"/><!--<th>BT확정내용/비고</th>-->
                            <col class="w-6p"/><!--<th>의뢰처</th>-->
                            <col class="w-21p"/><!--<th>의뢰정보</th>-->
                        </colgroup>
                        <thead>
                        <tr>
                            <th>고객사/프로젝트</th>
                            <th>스타일</th>
                            <th>원단명</th>
                            <th>원단정보</th>
                            <th>퀄리티 확정 내용/비고</th>
                            <th>BT 확정 내용/비고</th>
                            <th>의뢰처</th>
                            <th>의뢰정보</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(fabric , fabricIndex) in cqbList">
                            <!--고객사/프로젝트-->
                            <td class="ta-l" :rowspan="fabric.projectRowspan" v-if="fabric.projectRowspan > 0">

                                <div class="sl-blue hover-btn cursor-pointer font-14 mgb10" @click="openCustomer(fabric.customerSno)" v-if="!isFactory">{% fabric.customerName %}</div>
                                <div class="sl-blue hover-btn cursor-pointer font-14 mgb10" v-if="isFactory">{% fabric.customerName %}</div>

                                <span :class="'label-icon label-icon'+fabric.projectType">{% fabric.projectTypeEn %}</span>

                                <span class="text-danger hover-btn cursor-pointer font-15" v-if="isFactory" @click="openProjectViewFactory(fabric.projectSno)">{% fabric.projectSno %}</span>
                                <span class="text-danger hover-btn cursor-pointer font-15" v-if="!isFactory" @click="openProjectView(fabric.projectSno)">{% fabric.projectSno %}</span>

                                <span class="text-muted">{% fabric.projectStatusKr %}</span>
                                <div v-html="fabric.useInfo" class="mgt15 pdl5 ta-l"></div>
                                <!--<div class="">
                                    <div class="btn btn-sm btn-white hover-btn cursor-pointer mgt5 w80p" @click="openProjectViewAndSetTabMode(fabric.projectSno,'style')">스 타 일</div>
                                    <div class="btn btn-sm btn-white hover-btn cursor-pointer mgt5 w80p" @click="openProjectViewAndSetTabMode(fabric.projectSno,'comment')">코 멘 트</div>
                                </div>-->
                            </td>
                            <!--스타일-->
                            <td class="ta-l" :rowspan="fabric.fabricRowspan" v-if="fabric.fabricRowspan > 0">
                                <div class="font-14 cursor-pointer hover-btn" @click="openProductReg2(fabric.projectSno, fabric.styleSno, 1)" v-if="!isFactory">
                                    <b>{% fabric.styleFullName %}</b>
                                    <div class="font-12">
                                        ({% fabric.styleCode %})
                                    </div>
                                </div>
                                <div class="font-14 cursor-pointer hover-btn"  v-if="isFactory">
                                    <b>{% fabric.styleFullName %}</b>
                                    <div class="font-12">
                                        ({% fabric.styleCode %})
                                    </div>
                                </div>
                            </td>
                            <!--원단명-->
                            <td class="ta-l" :rowspan="fabric.fabricRowspan" v-if="fabric.fabricRowspan > 0">
                                {% fabric.fabricName %}
                            </td>

                            <!--원단정보-->
                            <td class="ta-l pdl5" :rowspan="fabric.fabricRowspan" v-if="fabric.fabricRowspan > 0">
                                <div>
                                    {% fabric.position %} {% fabric.attached %} {% fabric.fabricMix %} {% fabric.color %}
                                </div>
                                <div>
                                    <span v-if="!$.isEmpty(fabric.spec)">규격 : {% fabric.spec %}</span>
                                    <span v-if="!$.isEmpty(fabric.meas)">가요척 : {% fabric.meas %}</span>
                                    <span v-if="!$.isEmpty(fabric.weight)">중량 : {% fabric.weight %}</span>
                                </div>
                                <div>
                                    <span v-if="!$.isEmpty(fabric.fabricWidth)">원단폭 : {% fabric.fabricWidth %}</span>
                                    <span v-if="!$.isEmpty(fabric.afterMake)">후가공 : {% fabric.afterMake %} </span>
                                    <span v-if="!$.isEmpty(fabric.makeNational)">제조국 : <i :class="'flag flag-16 flag-'+ fabric.makeNational" ></i></span>
                                </div>
                            </td>
                            <!--퀄리티 확정 내용-->
                            <td class="ta-l pdl5" :rowspan="fabric.fabricRowspan" v-if="fabric.fabricRowspan > 0">
                                <div v-html="fabric.fabricConfirmInfo"></div>
                                <div v-html="fabric.fabricMemo"></div>
                            </td>
                            <!--BT 확정 내용-->
                            <td class="ta-l pdl5" :rowspan="fabric.fabricRowspan" v-if="fabric.fabricRowspan > 0">
                                <div v-html="fabric.btConfirmInfo"></div>
                                <div v-html="fabric.btMemo"></div>
                                <li class="hover-btn" v-for="(file, fileIndex) in fabric.fileList.fileList.btFile2.files">
                                    <a :href="'<?=$nasDownloadUrl?>name='+file.fileName+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                                </li>
                            </td>

                            <!--의뢰처-->
                            <td class="">
                                {% fabric.reqFactoryNm %}
                            </td>
                            
                            <!--의뢰 정보-->
                            <td class="ta-l pdl5">
                                요청{% fabric.reqCount %}회차
                                <div v-html="fabric.confirmInfo"></div>
                            </td>
                        </tr>
                        <tr v-show=" 0 >= cqbList.length">
                            <td colspan="99" class="ta-c"><span class="text-muted">데이터 없음</span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div id="qb-page" v-html="cqbPage" class="ta-c"></div>

            </div>

        </div>


    </div>

    <div style="margin-bottom:150px"></div>

</section>

<?php include 'script/ims_complete_qb_list_script.php'?>

