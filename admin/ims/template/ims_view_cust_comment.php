<!-- 프로젝트 기본 정보 -->
<section >

    <div class="col-xs-12" >
        <div>
            <div class="">
                <div class="flo-left"></div>
                <div class="flo-right pdb5"></div>
            </div>

            <div class="mgt10">

                <label class="radio-inline font-14">
                    <input type="radio" name="issueType" value="all" style="margin:5px 0 0 -20px !important;" v-model="issueShowList" />
                    전체
                </label>

                <?php foreach(\Component\Ims\ImsCodeMap::ISSUE_TYPE as $issueKey => $issueTitle){ ?>
                    <label class="radio-inline font-14">
                        <input type="radio" name="issueType" value="<?=$issueKey?>" style="margin:5px 0 0 -18px !important;" v-model="issueShowList" />
                        <?php if( 'issue' === $issueKey ) { ?>
                            <span class="text-danger"><?=$issueTitle?></span>
                            ({% <?=$issueKey?>List.length %})
                        <?php }else if( 'order' === $issueKey ) { ?>
                            <span class="sl-blue"><?=$issueTitle?></span>
                            ({% <?=$issueKey?>List.length %})
                        <?php }else{ ?>
                            <?=$issueTitle?>
                            ({% <?=$issueKey?>List.length %})
                        <?php } ?>
                    </label>

                    <div class="btn btn-sm btn-white mgr15" @click="openCustomerComment(customer.sno, 0, '<?=$issueKey?>')">
                        등록
                    </div>
                <?php } ?>
            </div>
            <div class="mgt10 new-style2 new-style2-border1">
                <table class="table-default-center">
                    <colgroup>
                        <col class="w-8p" />
                        <col class="w-3p" />
                        <col class="w-30p"/>
                        <col />
                        <col class="w-8p" />
                        <col class="w-8p" />
                    </colgroup>
                    <tr>
                        <th class="border-bottom-black" style="height:40px">구분</th>
                        <th class="border-bottom-black">번호</th>
                        <th class="border-bottom-black">제목</th>
                        <th class="border-bottom-black">내용(간소화)</th>
                        <th class="border-bottom-black">등록일</th>
                        <th class="border-bottom-black">등록자</th>
                    </tr>
                    <tr v-if="true<?php foreach(\Component\Ims\ImsCodeMap::ISSUE_TYPE as $issueKey => $issueTitle){ ?> && 0 >= <?=$issueKey?>List.length<?php } ?>">
                        <td colspan="99">데이터가 없습니다.</td>
                    </tr>
                    <?php foreach(\Component\Ims\ImsCodeMap::ISSUE_TYPE as $issueKey => $issueTitle){ ?>
                        <?php if( 'issue' === $issueKey ) { $colorClass='text-danger';  ?>
                        <?php }else if( 'order' === $issueKey ) { $colorClass='sl-blue'; ?>
                        <?php }else{ $colorClass=''; } ?>

                        <tr v-for="(each, eachIndex) in <?=$issueKey?>List" class="<?=$colorClass?>"  v-show="'all' === issueShowList || '<?=$issueKey?>' === issueShowList">
                            <td :rowspan="<?=$issueKey?>List.length" v-if="0 === eachIndex" class="border-bottom-gray">
                                <span><?=$issueTitle?></span>
                            </td>
                            <td :class="<?=$issueKey?>List.length === (eachIndex+1)?'border-bottom-gray':''">
                                {% <?=$issueKey?>List.length-eachIndex %}
                            </td>
                            <td :class=" <?=$issueKey?>List.length === (eachIndex+1)?'border-bottom-gray text-left pdl5':'text-left pdl5'">
                                <span class="font-black hover-btn cursor-pointer" @click="openCustomerComment(customer.sno, each.sno, '<?=$issueKey?>')">
                                    {% each.subject %}
                                    <span v-if="each.cnt_reply > 0" class="relative"><div class="font-12" style="position: absolute; top: -5px; left: 5px; font-size: 14px !important; color: rgb(255, 99, 71);"><i aria-hidden="true" class="fa fa-circle"></i></div> <div class="font-12" style="position: absolute; top: 0px; left: 0px; color: rgb(255, 255, 255); font-size: 8px !important; text-align: center; width: 22px;">
                                        {% each.cnt_reply %}
                                    </div></span>
                                </span>
                            </td>
                            <td :class=" <?=$issueKey?>List.length === (eachIndex+1)?'border-bottom-gray text-left pdl5':'text-left pdl5'">
                                <div class="" >
                                    <div class="hover-btn cursor-pointer font-11" @click="openCustomerComment(customer.sno, each.sno, '<?=$issueKey?>')">
                                        {% each.textContents %}
                                    </div>
                                    <div class="font-11 dp-flex" >
                                        <div class="sl-blue" v-if="!$.isEmpty(each.fileData.files) && each.fileData.files.length > 0">첨부 : </div>
                                        <simple-file-only-not-history-upload :file="each.fileData" :id="'fileDataView'" v-if="!$.isEmpty(each.fileData.files) && each.fileData.files.length > 0"></simple-file-only-not-history-upload>
                                    </div>
                                </div>
                            </td>
                            <td :class=" <?=$issueKey?>List.length === (eachIndex+1)?'border-bottom-gray':''">
                                {% $.formatShortDate(each.regDt) %}
                            </td>
                            <td :class=" <?=$issueKey?>List.length === (eachIndex+1)?'border-bottom-gray':''">
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