<!-- 프로젝트 기본 정보 -->
<section >

    <div class="col-xs-12" >
        <div>
            <div class="table-title gd-help-manual">
                <div class="flo-left">

                    <div class="dp-flex dp-flex-gap10">
                        <!--openCustomerComment(customerSno, sno, issueType)-->
                        <button type="button" class="btn btn-lg btn-white" @click="openCustomerComment(items.sno, 0, 'req')" >고객 요청 정보 등록</button>
                        <button type="button" class="btn btn-lg btn-white" @click="openCustomerComment(items.sno, 0, 'meeting')" >협상/미팅 정보 등록</button>
                        <button type="button" class="btn btn-lg btn-white" @click="openCustomerComment(items.sno, 0, 'delivery')" >납품완료 정보 등록</button>
                        <button type="button" class="btn btn-lg btn-white" @click="openCustomerComment(items.sno, 0, 'issue')" >이슈 등록</button>
                        <button type="button" class="btn btn-lg btn-white" @click="openCustomerComment(items.sno, 0, 'work')" >작지 수정</button>
                        <button type="button" class="btn btn-lg btn-white" @click="openCustomerComment(items.sno, 0, 'order')" >발주 특이사항</button>
                        <button type="button" class="btn btn-lg btn-white" @click="openCustomerComment(items.sno, 0, 'fabric')" >원단 보유 현황</button>
                    </div>

                </div>
                <div class="flo-right pdb5">
                </div>
            </div>

            <div class="mgt20 clear-both" style="border-bottom: solid 1px #9a9a9a">&nbsp;</div>

            <div class="mgt10">

                <div class="col-xs-12">

                    <div class="row">

                        <?php foreach(\Component\Ims\ImsCodeMap::ISSUE_TYPE as $issueKey => $issueTitle){ ?>
                            <div class="col-xs-4 new-style mgt15 mgb15">
                                <div class="table-title gd-help-manual">
                                    <div class="flo-left area-title font-20 <?='issue'===$issueKey?'text-danger':''?>">
                                        <i class="fa fa-chevron-right fa-title-icon" aria-hidden="true" ></i>
                                        <?=$issueTitle?>
                                        <button type="button" class="btn btn-sm btn-white" @click="openCustomerComment(items.sno, 0, '<?=$issueKey?>')" >등록</button>
                                    </div>
                                    <div class="flo-right"></div>
                                </div>
                                <div class="clear-both mgt20">
                                    <ul class="pdl15" v-if="<?=$issueKey?>List.length > 0">
                                        <li class="font-14 NanumSquare mgt5 mgb5 pdb5" v-for="(each, eachIndex) in <?=$issueKey?>List" style="border-bottom: solid 1px #f1f1f1">
                                            <div @click="openCustomerComment(items.sno, each.sno, '<?=$issueKey?>')" class="cursor-pointer hover-btn">
                                                {% <?=$issueKey?>List.length-eachIndex %}. {% each.subject %}
                                            </div>
                                            <div class="font-11 dp-flex pdl15 mgt5" >
                                                <div class="sl-blue" v-if="!$.isEmpty(each.fileData.files) && each.fileData.files.length > 0">첨부 : </div>
                                                <simple-file-only-not-history-upload :file="each.fileData" :id="'fileDataView'" v-if="!$.isEmpty(each.fileData.files) && each.fileData.files.length > 0"></simple-file-only-not-history-upload>
                                                <span class="font-11 text-muted">({% $.formatShortDateWithoutWeek(each.regDt) %} {% each.regManagerNm %} 등록)</span>
                                            </div>
                                        </li>
                                    </ul>
                                    <ul class="pdl15" v-if="0 >= <?=$issueKey?>List.length"><li class="font-14 NanumSquare">· 등록 데이터 없음</li></ul>
                                </div>
                            </div>
                        <?php } ?>


                        <!--//100개의 리스트가 나올 수 있다.-->

                    </div>
                </div>

            </div>

        </div>
    </div>
    <!-- 기본 정보 -->

</section>