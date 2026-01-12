<table class="table table-rows table-default-center mgt5 table-td-height0" id="main-table">
    <colgroup>
        <col style="width:1%" /><!--체크-->
        <col style="width:2%" /><!--번호-->
        <?php foreach($listSetupData['list'] as $each) { ?>
            <col class="w-<?=$each['col']?>p" />
        <?php } ?>
    </colgroup>
    <thead>
    <tr>
        <th>
            <input type='checkbox' value='y' class='js-checkall' data-target-name='sno' @click="toggleAllCheck()" v-model="listAllCheck"  />
        </th>
        <th>번호</th>
        <?php foreach($listSetupData['list'] as $key => $each) { ?>
            <th><?=$each['title']?></th>
        <?php } ?>
    </tr>
    </thead>

    <tbody>
    <template v-for="(project , projectIndex) in projectList">
            <tr :class="'field-parent ' + (project.styleShow?'bg-light-gray2':'')" :data-sno="project.sno">

                <!--고정값-->
                <?php include './admin/ims/list/step_fixed2.php'?>

                <!--한줄값-->
                <?php foreach($listSetupData['list'] as $eachKey => $each) { ?>
                    <?php if(!empty($each['field']) && empty($each['split']) && 'split' !== $each['field'] && !isset($each['split'])  ) { ?>
                        <td :rowspan="project.defaultRowspan" class="relative <?=$each['class']?>">
                            <?php if( 'manager' === $each['field']) { ?>
                                <div>{% project.salesManagerNm %}</div>
                                <div>{% project.designManagerNm %}</div>
                            <?php }else{ ?>
                                <?php if( 10 == $requestParam['status'] ) { ?>
                                    <a :href="`ims_project_view.php?sno=${project.projectSno}&tabMode=todo`" >
                                        <div v-html="project.<?=$each['field']?>" ></div>
                                    </a>
                                <?php }else{ ?>

                                    <?php if( 'negoData' == $each['field'] ) { ?>
                                        {% project.negoText %}
                                    <?php } ?>

                                    <?php if( 'meetingReport' == $each['field'] ) { ?>
                                        <!--미팅보고서-->
                                        <div class="font-11 text-left" >
                                            <ul class="ims-file-list" style="display: block">

                                                <li v-for="(file, fileIdx) in project.file.fileEtc1.files"  style="margin:0 !important;">
                                                    <a :href="`<?=$nasDownloadUrl?>name=${file.fileName}&path=${file.filePath}`" class="text-blue font-10">
                                                        <span v-if="project.file.fileEtc1.files.length === 1">Download</span>
                                                        <span v-if="project.file.fileEtc1.files.length > 1">Download{% fileIdx+1 %}</span>
                                                    </a>
                                                </li>

                                                <li v-if="$.isEmpty(project.file.fileEtc1.files) || null == project.file.fileEtc1.files || 0 >= project.file.fileEtc1.files.length" class="text-muted" style="margin:0 !important;">
                                                    <span class="text-muted">미등록</span>
                                                </li>
                                            </ul>
                                        </div>
                                    <?php }else{ ?>
                                        <!--일반적-->

                                        <?php if( 'number' === $each['format'] ){?>
                                            <div v-html="$.setNumberFormat(Number(project.<?=$each['field']?>))"></div>
                                        <?php }else if( 'isYn' === $each['format'] ){?>
                                            <div v-if="'y' === project.<?=$each['field']?>">예</div>
                                            <div v-if="'y' !== project.<?=$each['field']?>">아니오</div>
                                        <?php }else if( 'ratio' === $each['format'] ){?>
                                            <div v-html="Math.round(Number(project.<?=$each['field']?>)) + '%'"></div>
                                        <?php }else{ ?>
                                            <div v-html="project.<?=$each['field']?>"></div>
                                        <?php } ?>

                                    <?php } ?>

                                <?php } ?>
                            <?php } ?>
                        </td>
                    <?php } ?>
                <?php } ?>

                <!-- ######### 스케쥴(TD 예정) #########  -->
                <td class="bg-light-gray2" v-if="project.defaultRowspan > 1">예정일</td>
                <?php foreach($listSetupData['list'] as $eachKey => $each) { ?>
                    <?php if(!empty($each['field']) && true === $each['split'] && 'split' !== $each['field'] ) { ?>

                        <td rowspan="2" v-if="'해당없음' === project.<?=$each['field']?>AlterText" class="bg-dar-gray">
                            <span class="color-white">해당없음</span>
                        </td>

                        <td class="bg-light-yellow relative font-12" v-if="project.defaultRowspan > 1 && '해당없음' !== project.<?=$each['field']?>AlterText">

                            <div v-if="!$.isEmpty(project.<?=$each['field']?>ExpectedDtShort)" class="cursor-pointer hover-btn"
                                 @click="openProjectUnit(project.sno,'<?=$each['field']?>','<?=$each['title']?>')"
                            >

                                <!--대체-->
                                <span v-if="!$.isEmpty(project.<?=$each['field']?>AlterText)" class="font-11">
                                    {% project.<?=$each['field']?>AlterText %}
                                </span>

                                <span v-if="$.isEmpty(project.<?=$each['field']?>AlterText)"
                                      v-html="project.<?=$each['field']?>ExpectedDtShort"
                                      :class="(true == project.<?=$each['field']?>Delay)?'text-danger':''"
                                ></span>

                            </div>

                            <!--코멘트수 표기-->
                            <div v-if="project.<?=$each['field']?>CommentCnt > 0">
                                <div style="position:absolute; top:0;right:0; font-size: 14px !important; color:#f78800; display: block"  class="font-12 comment-cnt-<?=$each['field']?>ExpectedDt comment-cnt">
                                    <i class="fa fa-circle" aria-hidden="true"></i>
                                </div>
                                <div style="position:absolute; top:4px;right:1px; color:#fff; font-size: 9px; text-align: center; width:10px; display: block"  class="comment-cnt-<?=$each['field']?>ExpectedDt comment-cnt">
                                    {% project.<?=$each['field']?>CommentCnt %}
                                </div>
                            </div>
                        </td>
                    <?php } ?>

                    <?php if('customerWait' === $each['field'] ) { ?>
                        <td class="text-left pdl5 font-11 relative hover-btn cursor-pointer" rowspan="2"
                            @click="openProjectUnit(project.sno,'<?=$each['field']?>Memo','','<?=$each['field']?>','<?=$each['title']?>')">
                            <!--//(sno, div1, div2, type, title)-->
                            {% project.customerWaitMemo %}

                            <div v-if="project.<?=$each['field']?>CommentCnt > 0">
                                <div style="position:absolute; top:0;right:0; font-size: 14px !important; color:#f78800; display: block"  class="font-12 comment-cnt-<?=$each['field']?>ExpectedDt comment-cnt">
                                    <i class="fa fa-circle" aria-hidden="true"></i>
                                </div>
                                <div style="position:absolute; top:4px;right:1px; color:#fff; font-size: 9px; text-align: center; width:10px; display: block"  class="comment-cnt-<?=$each['field']?>ExpectedDt comment-cnt">
                                    {% project.<?=$each['field']?>CommentCnt %}
                                </div>
                            </div>
                        </td>
                    <?php } ?>

                <?php } ?>
            </tr>
            <!-- ######### 스케쥴(TR 완료) #########  -->
            <tr v-if="project.defaultRowspan > 1">
                <td>완료일</td>
                <?php foreach($listSetupData['list'] as $eachKey => $each) { ?>
                    <?php if(!empty($each['field']) && true === $each['split'] && 'split' !== $each['field'] ) { ?>
                        <td v-if="'해당없음' !== project.<?=$each['field']?>AlterText" class="<?=!empty($each['completeBlank'])?'bg-muted':''?>">
                            <div v-if="!$.isEmpty(project.<?=$each['field']?>ExpectedDtShort)" class="font-12 cursor-pointer hover-btn"
                                 @click="openProjectUnit(project.sno,'<?=$each['field']?>ExpectedDt','<?=$each['field']?>CompleteDt','<?=$each['field']?>','<?=$each['title']?>')">
                                <!--대체-->
                                <div v-if="!$.isEmpty(project.<?=$each['field']?>AlterText)" class="font-11">
                                    {% project.<?=$each['field']?>AlterText %}
                                </div>
                                <span v-if="$.isEmpty(project.<?=$each['field']?>AlterText)" v-html="project.<?=$each['field']?>CompleteDtShort"></span>
                            </div>
                        </td>
                    <?php } ?>
                <?php } ?>
            </tr>

            <!--스타일 데이터-->
            <tr v-show="project.styleShow" class="style-title">
                <td class="center bg-light-gray pd0 " style="height:25px!important;" v-for="styleTitle in project.styleList.titles" :colspan="styleTitle.colspan">
                    {% styleTitle.name %}
                </td>
            </tr>
            <tr v-show="project.styleShow" v-for="data in project.styleList.dataList" style="background-color: #eff9ff">
                <td v-for="dataValue in data" :colspan="dataValue.colspan" :class="'text-center ' + dataValue.class">
                    <!--일반값-->
                    <span v-html="dataValue.value" v-if="$.isEmpty(dataValue.type)" :class="'font-11 '"></span>
                    <!--값+LINK-->
                    <span v-html="dataValue.value" v-if="'prdOpen' === dataValue.type" @click="openProductReg2(project.sno, dataValue.link, dataValue.tabMode)" class="hover-btn cursor-pointer font-11"></span>
                    <div v-html="dataValue.styleCode" v-if="'prdOpen' === dataValue.type" class="text-muted font-11"></div>


                    <!--파일-->
                    <div v-if="'file' === dataValue.type" class="font-11 text-center" style="display: block">
                        <ul class="ims-file-list" style="display: block">
                            <li v-for="(file, fileIdx) in dataValue.value" v-if="dataValue.value.length === 1" style="margin:0 !important;">
                                <a :href="`<?=$nasDownloadUrl?>name=${file.fileName}&path=${file.filePath}`" class="text-blue font-10">Download</a>
                                <div class="btn btn-white btn-sm" @click="openFileHistory2({eachSno:'',projectSno:'',customerSno:'',styleSno:dataValue.link}, 'fileWork')">
                                    이력
                                </div>
                            </li>
                            <li v-for="(file, fileIdx) in dataValue.value" v-if="dataValue.value.length > 1" style="margin:0 !important;">
                                <a :href="`<?=$nasDownloadUrl?>name=${file.fileName}&path=${file.filePath}`" class="text-blue font-10">Download{% fileIdx+1 %}</a>
                            </li>
                            <li v-if="$.isEmpty(dataValue.value) || null == dataValue.value || 0 >= dataValue.value.length" class="text-muted" style="margin:0 !important;">
                                <span class="text-muted">미등록</span>
                            </li>
                        </ul>
                    </div>
                    
                    <!--작업지시서-->
                    <div v-if="'work' === dataValue.type" class="font-11 text-center" style="display: block">

                        <div class="btn btn-white btn-sm" v-html="$.getStatusIcon(dataValue.workStatus)+'열기'" @click="openCommonPopup('ework', 1300, 850, {sno:dataValue.eachSno, tabMode:'main'})"></div>

                    </div>
                </td>
            </tr>

    </template>
    </tbody>

    <!--데이터가 없을 때 처리-->
    <tbody v-if="0 >= projectList.length">
    <tr>
        <td colspan="99" class="ta-c"></td>
    </tr>
    </tbody>

</table>