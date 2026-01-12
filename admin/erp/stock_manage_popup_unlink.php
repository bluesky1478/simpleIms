<div class="font-18 bold noto mgt20">
    미연결: <span class="bold text-danger">{% unlinkList.length %}</span>개
    <div class="dp-flex dp-flex-gap10"></div>
</div>

<table class="table table-cols mgb0">
    <colgroup>
        <col class="width-md"/>
        <col>
        <col class="width-md"/>
        <col/>
    </colgroup>
    <tbody>
    <tr>
        <th>검색어</th>
        <td class="contents">
            <div v-for="(keyCondition,multiKeyIndex) in searchCondition.multiKey" class="mgb5  dp-flex">
                <?= gd_select_box('key', 'key', $search['combineSearch'], null, null, gd_isset($search['key']), 'v-model="keyCondition.key"', 'form-control'); ?>
                <input type="text" name="keyword" class="form-control" v-model="keyCondition.keyword"  @keyup.enter="getUnLinkData()" />
                <div class="btn btn-sm btn-red" @click="searchCondition.multiKey.push($.copyObject(defaultMultiKey1))" v-if="(multiKeyIndex+1) === searchCondition.multiKey.length ">+추가</div>
                <div class="btn btn-sm btn-gray" @click="searchCondition.multiKey.splice(multiKeyIndex, 1)" v-if="searchCondition.multiKey.length > 1 ">-제거</div>
            </div>
            <div class="mgb5 dp-flex">
                다중 검색 :
                <select class="form-control" v-model="searchCondition.multiCondition">
                    <option value="AND">AND (그리고)</option>
                    <option value="OR">OR (또는)</option>
                </select>
            </div>
        </td>
        <th>속성</th>
        <td class="contents">
            <?php $ATTR_MAP = [1=>'분류',2=>'시즌',3=>'타입',4=>'색상',5=>'년도'] ?>
            <div class="dp-flex dp-flex-gap10 mgt10" v-for="(attr,attrIdx) in searchCondition.attr">
                <?php foreach($thirdPartyCategory as $key => $categoryList){ ?>
                    <?=$ATTR_MAP[$key]?>:
                    <select class="form-control" v-model="attr[<?=$key?>]">
                        <option value="">선택</option>
                        <?php foreach($categoryList as $category){ ?>
                            <option><?=$category?></option>
                        <?php } ?>
                    </select>
                <?php } ?>
                <i class="fa fa-plus cursor-pointer hover-btn" aria-hidden="true" @click="addElement(searchCondition.attr, searchCondition.attr[0], 'after')"></i>
                <i class="fa fa-minus cursor-pointer hover-btn" aria-hidden="true" v-show="attrIdx > 0" @click="deleteElement(searchCondition.attr, attrIdx)"></i>
            </div>
        </td>
    </tr>
    </tbody>
</table>

<div class="dp-flex dp-flex-center mgt10 mgb10 ">
    <div class="btn btn-lg btn-black w-100px" @click="getUnLinkData()">검색</div>
    <div class="btn btn-lg btn-white w-100px" @click="unlinkConditionReset()">초기화</div>
</div>
<div class="mgt5">
    <table class="table table-rows table-default-center table-th-height0 table-td-height0 table-pd-2 mgt10">
        <colgroup>
            <col class='w-5p' />
            <col class='w-15p' />
            <col class='w-10p' />
            <col class='w-10p' />
            <col class='w-10p' />
            <col class='w-10p' />
            <col class='w-10p' />
            <col class='w-10p' />
            <col class='w-10p' />
            <col class='w-10p' />
        </colgroup>
        <thead>
        <tr>
            <th>선택</th>
            <th>상품명</th>
            <th>옵션명</th>
            <th>코드</th>
            <th>현재수량</th>
            <th>분류</th>
            <th>시즌</th>
            <th>타입</th>
            <th>색상</th>
            <th>년도</th>
        </tr>
        </thead>
        <tbody class="hover-light" v-if="0 >= unlinkList.length">
        <tr>
            <td colspan="99">
                데이터가 없습니다.
            </td>
        </tr>
        </tbody>
        <tbody v-for="(unlink, idx) in unlinkList" class="hover-light">
        <tr>
            <td>
                <input type="checkbox" :value="unlink.thirdPartyProductCode" v-model="linkCode">
            </td>
            <td class="pdl5 ta-l">{% unlink.productName %}</td>
            <td>{% unlink.optionName %}</td>
            <td class="pdl5 ta-l">{% unlink.thirdPartyProductCode %}</td>
            <td>{% unlink.stockCnt %}</td>
            <td>{% unlink.attr1 %}</td>
            <td>{% unlink.attr2 %}</td>
            <td>{% unlink.attr3 %}</td>
            <td>{% unlink.attr4 %}</td>
            <td>{% unlink.attr5 %}</td>
        </tr>
        </tbody>
    </table>
</div>