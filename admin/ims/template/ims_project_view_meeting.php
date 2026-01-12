<!-- 프로젝트 기본 정보 -->
<section >

    <div class="col-xs-12" >
        <div>
            <div class="">
                <div class="flo-left"></div>
                <div class="flo-right pdb5"></div>
            </div>

            <div class="mgt10">

                <label class="radio-inline font-16">
                    <input type="radio" name="issueType" value="all" style="margin:5px 0 0 -20px !important;" v-model="issueShowList" />
                    전체
                </label>
                
                <?php foreach(\Component\Ims\ImsCodeMap::ISSUE_TYPE as $issueKey => $issueTitle){ ?>
                    <label class="radio-inline font-16">
                        <input type="radio" name="issueType" value="<?=$issueKey?>" style="margin:5px 0 0 -20px !important;" v-model="issueShowList" />
                        <?php if( 'issue' === $issueKey ) { ?>
                            <span class="text-danger"><?=$issueTitle?></span>
                            ({% <?=$issueKey?>List.length %})
                        <?php }else{ ?>
                            <?=$issueTitle?>
                            ({% <?=$issueKey?>List.length %})
                        <?php } ?>

                    </label>

                    <div class="btn btn-sm btn-white mgr15" @click="openCustomerComment(items.sno, 0, '<?=$issueKey?>')">
                        등록
                    </div>
                <?php } ?>
            </div>
            <div class="mgt10 new-style2">
                <table class="table-default-center">
                    <colgroup>
                        <col class="w-8p" />
                        <col class="w-8p" />
                        <col class="w-30p"/>
                        <col />
                        <col class="w-8p" />
                    </colgroup>
                    <tr>
                        <th>구분</th>
                        <th>등록일</th>
                        <th>제목</th>
                        <th>내용(간소화)</th>
                        <th>등록자</th>
                    </tr>
                    <?php foreach(\Component\Ims\ImsCodeMap::ISSUE_TYPE as $issueKey => $issueTitle){ ?>
                        <tr v-for="(each, eachIndex) in <?=$issueKey?>List" class=""  v-show="'all' === issueShowList || '<?=$issueKey?>' === issueShowList">
                            <td :rowspan="<?=$issueKey?>List.length" v-if="0 === eachIndex">
                                <?php if( 'issue' === $issueKey ) { ?>
                                    <span class="text-danger"><?=$issueTitle?></span>
                                <?php }else{ ?>
                                    <?=$issueTitle?>
                                <?php } ?>
                            </td>
                            <td>{% $.formatShortDateWithoutWeek(each.regDt) %}</td>
                            <td class="text-left pdl5">
                                <span class="hover-btn cursor-pointer" @click="openCustomerComment(items.sno, each.sno, '<?=$issueKey?>')">
                                    {% each.subject %}
                                </span>
                            </td>
                            <td class="text-left pdl5">

                                <div class="hover-btn cursor-pointer" @click="openCustomerComment(items.sno, each.sno, '<?=$issueKey?>')">
                                    {% each.textContents %}
                                </div>
                                <div class="font-11 dp-flex" >
                                    <div class="sl-blue" v-if="!$.isEmpty(each.fileData.files) && each.fileData.files.length > 0">첨부 : </div>
                                    <simple-file-only-not-history-upload :file="each.fileData" :id="'fileDataView'" v-if="!$.isEmpty(each.fileData.files) && each.fileData.files.length > 0"></simple-file-only-not-history-upload>
                                </div>
                            </td>
                            <td>
                                {% each.regManagerNm %}
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

        </div>
    </div>
    <!-- 기본 정보 -->

</section>