<!--집계-->
<div class="dp-flex dp-flex-gap10 font-16">
    <div class="total" v-if="!$.isEmpty(anotherList.tab2.page)">
        검색 <span class="text-danger">{% $.setNumberFormat(anotherList.tab2.page.recode.amount) %}</span> 건
    </div>
</div>

<div class="">

    <table class="table table-rows table-fixed table-default-center table-td-height30">
        <colgroup>
            <col class="w-2p" />
            <col class="w-3p" />
            <col :class="`w-${field.col}p`" v-for="field in anotherList.tab2.field" v-if="true != field.skip && true != field.subRow" />
        </colgroup>
        <tr>
            <th rowspan="2"><input type='checkbox' value='y' class='js-checkall' data-target-name='sno' /></th>
            <th rowspan="2" >번호</th>
            <th v-for="fieldData in anotherList.tab2.field"
                v-if="true != fieldData.skip && true != fieldData.subRow"
                class="pd5" v-html="fieldData.title">
            </th>
        </tr>
        <tbody v-for="(each , index) in anotherList.tab2.list" class="hover-light">
        <tr>
            <td >
                <input type="checkbox" name="sno[]" :value="each.sno" class="list-check" >
            </td>
            <td >
                <div>{% (anotherList.tab2.list.length-index) %}</div>
            </td>
            <td v-for="fieldData in anotherList.tab2.field"
                :rowspan="true == fieldData.rowspan || !$.isEmpty(each['tx'+$.ucfirst(fieldData.name)]) ?2:1"
                :class="fieldData.class"
                :style="!$.isEmpty(each['tx'+$.ucfirst(fieldData.name)])?'background-color:#f0f0f0':''"
                v-if="true != fieldData.subRow">

                <?php include './admin/ims/nlist/list_template.php'?>

                <section v-if="'c' === fieldData.type">
                    <!--프로젝트 번호-->
                    <div v-if="'sno' === fieldData.name">
                        <a :href="'./ims25_view.php?sno='+each.sno" class="text-danger">{% each.sno %}</a>
                        <img src="/admin/gd_share/img/icon_grid_open.png" alt="프로젝트 새창 열기"
                             class="hand mgl5" border="0" @click="window.open(`./ims25_view.php?sno=${each.sno}`)">
                    </div>

                </section>


            </td>
        </tr>
        <tbody v-if="0 >= anotherList.tab2.list.length">
        <tr>
            <td colspan="99">
                데이터가 없습니다.
            </td>
        </tr>
        </tbody>
    </table>

</div>
