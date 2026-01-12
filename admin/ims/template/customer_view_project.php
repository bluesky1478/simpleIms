<div class="col-xs-12">
    <div>
        <div>
            <div class="table-responsive mgt5">

                <div class="table-title gd-help-manual mgr20">
                    <div class="flo-left">프로젝트 리스트</div>
                    <div class="flo-right pdb5">
                    </div>
                </div>

                <div class="">
                    <div class="flo-left">
                        <label class="radio-inline mgl30">
                            <input type="radio" name="isRtw" value="all" v-model="isRtw"/>전체
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="isRtw" value="y" v-model="isRtw"/>기성금액 제외
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="isRtw" value="n" v-model="isRtw"/>기성금액만
                        </label>
                    </div>
                    <div class="flo-right">

                        <select class="form-control mgl5" v-model="projectListSearchCondition.sort" @change="ProjectService.getList()">
                            <option value="P8,asc">고객납기일 ▲</option>
                            <option value="P8,desc">고객납기일 ▼</option>

                            <option value="P9,asc">발주일 ▲</option>
                            <option value="P9,desc">발주일 ▼</option>

                            <option value="P2,asc">연도/등록일 ▲</option>
                            <option value="P2,desc">연도/등록일 ▼</option>
                        </select>

                    </div>
                </div>

                <div class="checkbox clear-both mgt15 pdt10">
                    <div >
                        <label class="checkbox-inline " style="width:115px">
                            <input type="checkbox" name="orderProgressChk[]" value="all" class="js-not-checkall" data-target-name="orderProgressChk[]"
                                   :checked="0 >= projectListSearchCondition.orderProgressChk.length?'checked':''" @click="projectListSearchCondition.orderProgressChk=[];"> 전체
                        </label>
                        <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_STATUS_ALL_MAP as $k => $v){ ?>
                            <label class="" style="width:115px">
                                <input class="checkbox-inline chk-progress" type="checkbox" name="orderProgressChk[]" value="<?=$k?>"  v-model="projectListSearchCondition.orderProgressChk"  >
                                <?=$v?>
                            </label>
                        <?php } ?>
                    </div>
                </div>

                <div class="ta-c">
                    <div class="btn btn-gray" @click="ProjectService.getList()">검색</div>
                </div>


                <table class="table table-rows mgt5">
                    <colgroup>
                        <col style="width: 3%;"><!-- 전체 선택 체크박스 -->
                        <col style="width: 3%;"><!-- 번호 -->
                        <col style="width: 3%;"><!-- 프로젝트번호 -->
                        <col style="width: 3%;"><!-- 프로젝트타입 -->
                        <col style="width: 3%;"><!-- 연도/시즌 -->
                        <col style="width: 15%;"><!-- 스타일 -->
                        <col style="width: 4%;"><!-- 수량 -->
                        <col style="width: 6%;"><!-- 매입 -->
                        <col style="width: 6%;"><!-- 매출 -->
                        <col style="width: 4%;"><!-- 마진 -->
                        <col style="width: 8%;"><!-- 영업담당 -->
                        <col style="width: 8%;"><!-- 디자인담당 -->
                        <col style="width: 8%;"><!-- 발주일 -->
                        <col style="width: 8%;"><!-- 고객납기일 -->
                        <!--<col style="width: 8%;"> 매출규모 -->
                        <col style="width: 3%;"><!-- 등록/수정일 -->
                    </colgroup>
                    <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="allCheck" value="y" data-target-name="prdSno" class="js-checkall">
                        </th>
                        <th>번호</th>
                        <th>PNO</th>
                        <th>프로젝트 타입</th>
                        <th>연도/시즌</th>
                        <th>스타일</th>
                        <th>제작수량</th>
                        <th>매입</th>
                        <th>매출</th>
                        <th>마진</th>
                        <th>상태</th>
                        <th>영업담당</th>
                        <th>디자인담당</th>
                        <th>발주일</th>
                        <th>고객납기일</th>
                        <!--<th>매출규모</th>-->
                        <th>등록일</th>
                    </tr>
                    </thead>
                    <tbody class="text-center"> <!--(('y' === isRtw && 4 != project.projectType) || 'y' !== isRtw)-->
                    <tr v-for="(project, index) in projectList"
                        v-if="project.projectStatus != 11 &&  ('all' === isRtw || ( 'y' === isRtw && 4 != project.projectType ) || ( 'n' === isRtw && 4 == project.projectType ))"
                        class="hover-light">
                        <td class="center">
                            <div class="display-block"><input type="checkbox" name="prdSno" value="373"></div>
                        </td>
                        <td>{% projectList.length - index %}</td>
                        <td class="text-danger">
                            <span class="text-danger hover-btn cursor-pointer" @click="openProjectView(project.projectSno)">{% project.projectSno %}</span>
                        </td>
                        <td>{% project.projectTypeKr %}</td>
                        <td class="pdl5 ta-l">
                            {% project.projectYear %}
                            {% project.projectSeason %}
                        </td>
                        <td class="ta-l pdl5">{% project.styleName %}</td>
                        <td class="ta-r">{% $.setNumberFormat(project.prdExQty) %}</td>
                        <td class="text-blue ta-r">{% $.setNumberFormat(project.projectCost) %}</td>
                        <td class="text-danger ta-r">{% $.setNumberFormat(project.projectPrice) %}</td>
                        <td class="">{% $.getMargin(project.projectCost, project.projectPrice) %}%</td>
                        <td>{% project.projectStatusKr %}</td>
                        <td>{% project.salesManagerNm %}</td>
                        <td>{% project.designManagerNm %}</td>
                        <td>{% $.formatShortDate(project.cpProductionOrder) %}</td>
                        <td>{% $.formatShortDate(project.customerDeliveryDt) %}</td>
                        <!--<td>{% $.setNumberFormat(project.saleAmount) %}</td>-->
                        <td>
                            <div>{% $.formatShortDate(project.regDt) %}</div>
                            <!--<div class="text-muted">{% $.formatShortDate(project.modDt) %}</div>-->
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="table-action" style="display: none;">
                <div class="pull-left form-inline"><span class="action-title">선택한 상품을</span></div>
                <div class="pull-right form-inline"></div>
            </div>
        </div>
    </div>
</div>