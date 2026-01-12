<!--예정 스케쥴-->
<div v-if="true !== fieldData.subRow" :class="'relative cursor-pointer'" @click="openProjectUnit(each.sno,fieldData.name,fieldData.title)">

    <div class="ims-tt ims-tt-left ims-tt-light">
        <!--텍스트표시-->
        <div v-if="!$.isEmpty(each['tx'+$.ucfirst(fieldData.name)])" class="bg-light-gray">
            {% each['tx'+$.ucfirst(fieldData.name)] %}
        </div>

        <!--미정-->
        <div v-if="$.isEmpty(each['ex'+$.ucfirst(fieldData.name)]) && $.isEmpty(each['tx'+$.ucfirst(fieldData.name)])" class="text-muted">
            <div class="font-11" v-if="9 == each['st'+$.ucfirst(fieldData.name)]">
                <span v-html="$.getProjectScheduleIcon(each['st'+$.ucfirst(fieldData.name)])"></span>
                {% $.formatShortDateWithoutWeek(each['cp'+$.ucfirst(fieldData.name)]) %}
            </div>
            <div v-else>
                미정
            </div>
        </div>

        <!--예정일표시-->
        <div >
            <div v-if="!$.isEmpty(each['ex'+$.ucfirst(fieldData.name)]) && $.isEmpty(each['tx'+$.ucfirst(fieldData.name)])" class="font-12">
                <div class="font-11" v-if="9 == each['st'+$.ucfirst(fieldData.name)]">
                    <span v-html="$.getProjectScheduleIcon(each['st'+$.ucfirst(fieldData.name)])"></span>
                    {% $.formatShortDateWithoutWeek(each['cp'+$.ucfirst(fieldData.name)]) %}
                </div>
                <div v-else-if="'y' === each['delay'+$.ucfirst(fieldData.name)]" class="text-danger">
                    {% $.formatShortDateWithoutWeek(each['ex'+$.ucfirst(fieldData.name)]) %}
                </div>
                <div v-else>
                    <div v-if="$.getToday() == each['ex'+$.ucfirst(fieldData.name)]" class="sl-blue font-bold">
                        {% $.formatShortDateWithoutWeek(each['ex'+$.ucfirst(fieldData.name)]) %}
                    </div>
                    <div v-else>
                        {% $.formatShortDateWithoutWeek(each['ex'+$.ucfirst(fieldData.name)]) %}
                    </div>
                </div>
            </div>
        </div>

        <!--코멘트 데이터-->
        <div class="ims-tt-box" v-if="!$.isEmpty(commentMap[each.sno]) && !$.isEmpty(commentMap[each.sno][fieldData.name])">
            <div>
                <ul class="ta-l">
                    <li v-for="(comment, commentIdx) in commentMap[each.sno][fieldData.name]"
                        v-if="6 >= commentIdx"
                        style="border-bottom:dot-dot-dash 1px #000" class="font-12 mgb5 pdb2"
                    >
                        <!--{% commentMap[each.sno][fieldData.name].length - commentIdx %}.-->
                        <div>{% $.formatShortDateWithoutWeek(comment.regDt) %} {% comment.regManagerName %}</div>
                        <div class="pdl2">▶ {% comment.comment %}</div>
                    </li>
                    <li v-if="commentMap[each.sno][fieldData.name].length > 6" class="font-11">
                        코멘트는 최대 6개만 표시 됩니다.
                    </li>
                </ul>
            </div>
        </div>

    </div>

    <div v-if="!$.isEmpty(each.commentInfo)">
        <comment-cnt2 :data="each.commentInfo[fieldData.name]" ></comment-cnt2>
    </div>

</div>
<!--완료 스케쥴-->
<div v-else>

    <div v-if="'subTitle' !== fieldData.name " class="font-11">
        <span v-html="$.getProjectScheduleIcon(each['st'+$.ucfirst(fieldData.name)])"></span>
        {% $.formatShortDateWithoutWeek(each['cp'+$.ucfirst(fieldData.name)]) %}
    </div>

</div>


