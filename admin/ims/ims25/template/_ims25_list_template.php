<!-- TYPE INDEX
i
d1 : 날짜1 (00/00/00(월))
d2 : 날짜2 (00/00/00)
d2s :날짜2작게 (00/00/00)
d3 : 날짜3 (00/00/00) 00남음
sl : 일반문자 (왼쪽정렬)
html : HTML문자
c : 커스텀
schedule : 스케쥴일자(담당자 컨텍, 사전미팅, 샘플 확보, 현장 리서치, 샘플 제작, 샘플 현장 테스트, 제안미팅)(예정일, 완료일)
fldText : 고정값(name == 고정text)
-->

<!-- i 일반 숫자 + 단위 -->
<div v-if="'i' === fieldData.type">
    {% $.setNumberFormat(each[fieldData.name]) %} {% fieldData.unit %}
    <br/><span v-if="'totalTargetCost' === fieldData.name && !$.isEmpty(each[fieldData.name]) && !$.isEmpty(each.totalTargetPrice) && each[fieldData.name] !== '0' && each.totalTargetPrice !== '0'">({% Math.floor(each[fieldData.name]/each.totalTargetPrice*100) %}%)</span>
</div>
<!-- d1 날짜1 (00/00/00(월)) -->
<div v-else-if="'d1' === fieldData.type">
    <div >
        <div v-if="$.isEmpty(each[fieldData.name])" class="text-muted">미정</div>
        <div v-if="!$.isEmpty(each[fieldData.name])">
            {% $.formatShortDate(each[fieldData.name]) %}
        </div>
    </div>
</div>

<!--d2-->
<!-- d2 날짜2 (00/00/00) -> 등록일 and 예정일(미팅(입찰)예정일) -->
<div v-else-if="'d2' === fieldData.type">
    <div v-if="$.isEmpty(each[fieldData.name])" class="text-muted">미정</div>
    <div v-if="!$.isEmpty(each[fieldData.name])">
        {% $.formatShortDateWithoutWeek(each[fieldData.name]) %}
    </div>
</div>

<!-- d2s -->
<!-- d2s 날짜2 (00/00/00) 작게-->
<div v-else-if="'d2s' === fieldData.type">
    <div v-if="$.isEmpty(each[fieldData.name])" class="text-muted">미정</div>
    <div v-if="!$.isEmpty(each[fieldData.name])" class="font-12">
        {% $.formatShortDateWithoutWeek(each[fieldData.name]) %}
    </div>
</div>

<!--d3-->
<!-- d3 날짜3 (00/00/00) 00남음 -->
<div v-else-if="'d3' === fieldData.type">
    <div >
        <div v-if="$.isEmpty(each[fieldData.name])" class="text-muted">미정</div>
        <div v-if="!$.isEmpty(each[fieldData.name])">
            <span class="font-13">{% $.formatShortDateWithoutWeek(each[fieldData.name]) %}</span>
            <div class="font-11 mgt5" v-html="$.remainDate(each[fieldData.name],true)"></div>
        </div>
    </div>
</div>

<!--sl-->
<!-- sl 일반문자 (왼쪽정렬) -->
<div v-else-if="'sl' === fieldData.type" class="text-left pdl1">
    <div v-if="!$.isEmpty(each[fieldData.name])">{% each[fieldData.name] %}</div>
    <div v-if="$.isEmpty(each[fieldData.name])" class="text-muted font-11"><?=empty($defaultText)?'확인중':$defaultText?></div>
</div>

<!--html-->
<!-- html HTML문자 -->
<div v-else-if="'html' === fieldData.type" class="text-left pdl1">
    <div v-if="!$.isEmpty(each[fieldData.name])" v-html="each[fieldData.name]"></div>
    <div v-if="$.isEmpty(each[fieldData.name])" class="text-muted font-11"><?=empty($defaultText)?'확인중':$defaultText?></div>
</div>

<!--예정일 2 -->
<div v-else-if="'expected2' === fieldData.type" >
    <div :class="'relative cursor-pointer hover-btn'" @click="openProjectUnit(each.sno,fieldData.name,fieldData.title)">

        <!--텍스트표시-->
        <div v-if="!$.isEmpty(each['tx'+$.ucfirst(fieldData.name)])" class="bg-light-gray">
            {% each['tx'+$.ucfirst(fieldData.name)] %}
        </div>
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

        <comment-cnt2 :data="each[fieldData.name+'CommentCnt']"></comment-cnt2>
    </div>
</div>

<!--고정값-->
<div v-else-if="'fldText' === fieldData.type">
    {% fieldData.name %}
</div>

<!--매니저명-->
<div v-else-if="'manager' === fieldData.type" v-html="$.isset(managerMap[each[fieldData.name]],'<span class=\'text-muted font-11\'>미정</span>')"></div>

<!--일반 문자-->
<div v-else>
    <div v-if="'c' !== fieldData.type && 'schedule' !== fieldData.type">
        <div v-if="!$.isEmpty(each[fieldData.name])">{% each[fieldData.name] %}{% fieldData.valueSuffix %}</div>
        <div v-if="$.isEmpty(each[fieldData.name]) || each[fieldData.name] === ''" class="text-muted font-11">
            <?=empty($defaultText)?'확인중':$defaultText?>
        </div>
    </div>
</div>
