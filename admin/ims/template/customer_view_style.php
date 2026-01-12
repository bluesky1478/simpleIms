<div class="">
    <div class="col-xs-12 pd15">

        <div class="table-title gd-help-manual">
            <div class="font-18">
                고객사 스타일 정보
                <span class="font-12 mgl5">( 발주한 스타일만 표시됩니다. )</span>
                <div>
                    <div class="dp-flex dp-flex-gap5 font-14" style="font-weight: normal !important;">
                        연도 : <input type="text" name="prdYear" class="form-control" placeholder="연도" v-model="customerPrdSearchCondition.prdYear" @keyup.enter="refreshCustomerProductList()" style="width:80px; height:28px" />
                        &nbsp;&nbsp;시즌 :
                        <select class="form-control" name="projectSeason" v-model="customerPrdSearchCondition.prdSeason" @change="refreshCustomerProductList()">
                            <option value="">선택</option>
                            <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                                <option value="<?=$codeKey?>" <?= $codeKey == $search['projectSeason'] ? 'selected':'' ; ?>><?=$codeKey?></option>
                            <?php } ?>
                        </select>
                        <input type="text" name="keyword" class="form-control w-20p" v-model="customerPrdSearchCondition.prdName"  @keyup.enter="refreshCustomerProductList()" placeholder="스타일명" style="height:28px" />
                        <div class="btn btn-gray" @click="refreshCustomerProductList()">검색</div>
                    </div>
                </div>
            </div>

            <div class="checkbox ">
                <div v-show="false">
                    <label class="checkbox-inline mgr10">
                        <input type="checkbox" name="projectType[]" value="all" class="js-not-checkall" data-target-name="projectType[]" :checked="0 >= customerPrdSearchCondition.projectTypeChk.length?'checked':''" @click="customerPrdSearchCondition.projectTypeChk=[]; refreshCustomerProductList()"> 전체
                    </label>
                    <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_TYPE as $k => $v){ ?>
                        <label class="mgr10">
                            <input class="checkbox-inline chk-progress" type="checkbox" name="projectType[]" value="<?=$k?>"  v-model="customerPrdSearchCondition.projectTypeChk" @click="refreshCustomerProductList()"> <?=$v?>
                        </label>
                    <?php } ?>
                </div>
            </div>

        </div>
        <div class="_table-responsive mgt5">

            <div class="mgt20 ta-l" id="customer-style-preloader">
                <div class="sl-pre-loader ta-c border-right-gray pd20" style="position: relative !important;top:0;left:0">
                    <div class="font-18">고객사의 스타일을 불러오는 중입니다...</div>
                    <!--<div class="mgt10 throbber-loader"> </div>-->
                </div>
            </div>

            <table class="table table-pd-5 table-default-left table-th-height30 table-td-height0 table-rows" v-if="!$.isEmpty(customerPrdField)">
                <colgroup>
                    <!--<col class="w-1p" />
                    <col class="w-3p" />-->
                    <col :class="`w-${fieldData.col}p`" v-for="fieldData in customerPrdField" v-if="true != fieldData.skip && true !== fieldData.subRow" />
                </colgroup>
                <thead>
                <tr>
                    <!--
                    <th ><input type='checkbox' value='y' class='js-checkall' data-target-name='sno' /></th>-->
                    <th v-for="fieldData in customerPrdField">
                        <div :class="">
                            {% fieldData.title %}
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody >
                <tr v-if="0 >= customerPrdList.length">
                    <td colspan="99" class="ta-c">데이터가 없습니다.</td>
                </tr>
                <tr v-for="each in customerPrdList">
                    <!--
                    <td class="center">
                        <div class="display-block">
                            <input type="checkbox" name="prdSno" :value="each.sno">
                        </div>
                    </td>-->
                    <td v-for="fieldData in customerPrdField" :class="fieldData.class + ''" >

                        <?php $defaultText='-'?>
                        <?php include './admin/ims/nlist/list_template.php'?>

                        <div v-if="'c' == fieldData.type">
                            <!--마스터 코드-->
                            <div v-if="'masterCode' == fieldData.name">
                                {% each.prdSeason %} {% each.prdStyle %} {% each.addStyleCode %}
                            </div>

                            <div v-if="'styleFullName' == fieldData.name" class="sl-blue cursor-pointer hover-btn"
                                 @click="openProductReg2(each.projectSno, each.sno, -1)">
                                {% each.styleFullName %}
                                <span class="text-muted">{% each.styleCode %}</span>
                            </div>

                            <!--작업지시서-->
                            <div v-if="'work' == fieldData.name">
                                <!--1. 전산 작지 -->
                                <!--{% each.eworkMainFl %} => 메인 도안이 있으면 전산 작지 열기-->

                                <!--전산작지-->
                                <div class="btn btn-white btn-sm font-11" @click="openUrl(`eworkP_${each.sno}`,`<?=$eworkUrl?>?sno=${each.sno}`,1600,950);" v-if="1 == each.eworkMainFl">
                                    전산
                                </div>
                                <!--개별파일 작지-->
                                <div v-else-if="Array.isArray(each.fileWork)">
                                    <simple-file-list2 :files="each.fileWork"></simple-file-list2>
                                    <mini-file-history :file_div="'fileWork'" :params="each"></mini-file-history>
                                </div>
                                <!--프로젝트 작지-->
                                <div v-else-if="each.fileWork > 0">
                                    <div class="btn btn-white font-11" style="height:25px!important;padding:4px 6px 2px 4px!important;" @click="openFileHistory2({projectSno:each.projectSno}, 'fileWork')">이력</div>
                                </div>
                                <div v-else>
                                    -
                                </div>

                            </div>

                            <div v-if="'order' == fieldData.name">
                                작업중
                            </div>

                            <div v-if="'fabric' == fieldData.name">
                                작업중
                            </div>

                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <!--
        <div class="table-action" style="display: none;">
            <div class="pull-left form-inline"><span class="action-title">선택한 상품을</span></div>
            <div class="pull-right form-inline"></div>
        </div>
        -->
    </div>
</div>