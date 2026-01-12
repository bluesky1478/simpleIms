<div class="font-18 bold noto mgt20">
    입출고 이력
    <div class="dp-flex dp-flex-gap10"></div>
</div>

<table class="table table-cols mgb0">
    <colgroup>
        <col class="w-10p"/>
        <col class="w-30p">
        <col class="w-10p"/>
        <col class="w-50p"/>
    </colgroup>
    <tbody>
    <tr>
        <th rowspan="2">검색어</th>
        <td class="contents" rowspan="2">
            <span v-for="(keyCondition,multiKeyIndex) in ioSearchCondition.multiKey" class="mgb5 dp-flex">
                <?= gd_select_box('key', 'key', $search['ioSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword" @keyup.enter="getStockInOutList()" />
                <div class="btn btn-sm btn-red" @click="ioSearchCondition.multiKey.push($.copyObject(defaultMultiKey1))" v-if="(multiKeyIndex+1) === ioSearchCondition.multiKey.length ">+추가</div>
                <div class="btn btn-sm btn-gray" @click="ioSearchCondition.multiKey.splice(multiKeyIndex, 1)" v-if="ioSearchCondition.multiKey.length > 1 ">-제거</div>
            </span>
            <div class="mgb5 dp-flex">
                다중 검색 :
                <select class="form-control" v-model="ioSearchCondition.multiCondition">
                    <option value="AND">AND (그리고)</option>
                    <option value="OR">OR (또는)</option>
                </select>
            </div>
        </td>
        <th>구분</th>
        <td class="contents">
            <label class="radio-inline">
                <input type="radio" name="inOutType" value="1" v-model="ioSearchCondition.inOutType"/>입고
            </label>
            <label class="radio-inline">
                <input type="radio" name="inOutType" value="2" v-model="ioSearchCondition.inOutType"/>출고
            </label>
        </td>
    </tr>
    <tr>
        <th>날짜</th>
        <td class="contents">
            <div class="dp-flex">

                <div class="mini-picker mgl5">
                    <date-picker v-model="ioSearchCondition.startDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="시작일" style="font-weight: normal"></date-picker>
                </div>

                ~

                <div class="mini-picker mgl15">
                    <date-picker v-model="ioSearchCondition.endDt" value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="종료일" style="font-weight: normal;"></date-picker>
                </div>

                <div class="form-inline" >
                    <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(ioSearchCondition, 'startDt', 'endDt', 'today')">오늘</div>
                    <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(ioSearchCondition, 'startDt', 'endDt', 'week')">이번주</div>
                    <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(ioSearchCondition, 'startDt', 'endDt', 'month')">이번달</div>
                    <div class="btn btn-sm btn-white" @click="ImsService.setSearchDate(ioSearchCondition, 'startDt', 'endDt', 'year')">이번년</div>
                </div>

            </div>
        </td>
    </tr>
    
    </tbody>
</table>

<div class="dp-flex dp-flex-center mgt10 mgb10 ">
    <div class="btn btn-lg btn-black w-100px" @click="getStockInOutList()">검색</div>
    <div class="btn btn-lg btn-white w-100px" @click="ioConditionReset()">초기화</div>
</div>
<div class="mgt5">

    <?php $defaultText = '-' ?>
    <div class="font-13 dp-flex dp-flex-between mgb5">
        <div>
            검색 : <span class="text-danger">{% $.setNumberFormat(stockInOutData.page.recode.amount) %}</span>
            <!--<span class="font-11">(수량 : $.setNumberFormat()장 )</span>-->
        </div>

        <div>
            <div class="btn btn-white btn-icon-excel btn-excel" @click="listDownload()">다운로드</div>
        </div>
    </div>

    <table class="table table-rows table-default-center table-th-height0 table-td-height0 table-pd-2">
        <colgroup>
            <col class="w-3p">
            <col :class="`w-${fieldData.col}p`" v-for="fieldData in stockInOutData.fieldData" v-if="true != fieldData.skip && true !== fieldData.subRow" />
        </colgroup>
        <thead>
        <tr>
            <th>번호</th>
            <th v-for="fieldData in stockInOutData.fieldData" v-if="true != fieldData.subRow">
                <div :class="'font-11'">
                    {% fieldData.title %}
                </div>
            </th>
        </tr>
        </thead>
        <tbody class="hover-light" v-if="0 >= stockInOutData.list.length">
        <tr>
            <td colspan="99">
                데이터가 없습니다.
            </td>
        </tr>
        </tbody>
        <tbody v-for="(each, idx) in stockInOutData.list" class="hover-light">
        <tr>
            <td class="">{% $.setNumberFormat(stockInOutData.page.recode.total - idx) %}</td>
            <td v-for="fieldData in stockInOutData.fieldData" v-if="true !== fieldData.subRow" :class="fieldData.class">
                <?php include './admin/ims/nlist/list_template.php'?>
                <div v-if="'c' === fieldData.type">
                    <div v-if="'receiverName' === fieldData.name">
                        {% each['customerName'] %} {% each['memo'] %}
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div>
    <div id="inout-page" v-html="stockInOutData.pageEx" class="ta-c"></div>
</div>
