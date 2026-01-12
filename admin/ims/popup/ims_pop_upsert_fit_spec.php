<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
        <h3 class="">사이즈스펙 리스트</h3>
        <div class="btn-group font-18 bold">
        </div>
    </div>
    <!-- 상세 정보 -->
    <div v-show="fitDetail.sno != 0">
        <table class="table table-cols table-pd-5 table-th-height30 table-td-height30" style="border-top:none !important;">
            <colgroup>
                <col class="w-18p">
                <col class="w-22p">
                <col class="w-18p">
                <col class="w-22p">
                <col class="w-22p">
            </colgroup>
            <tbody>
            <tr>
                <th>시즌</th>
                <td colspan="4">{% fitDetail.fitSeasonHan %}</td>
            </tr>
            <tr>
                <th>스타일</th>
                <td colspan="4">{% fitDetail.fitStyleHan %}</td>
            </tr>
            <tr>
                <th>핏</th>
                <td colspan="4">{% fitDetail.fitName %}</td>
            </tr>
            <tr>
                <th>구분</th>
                <td colspan="4">{% fitDetail.fitSizeName %}</td>
            </tr>
            <tr>
                <th>기준 사이즈</th>
                <td colspan="4">{% fitDetail.fitSize %}</td>
            </tr>
            <tr>
                <th :rowspan="fitDetail.jsonOptions.length+1">측정항목</th>
                <th class="ta-c">부위</th>
                <th class="ta-c">편차</th>
                <th class="ta-c">스펙</th>
                <th class="ta-c">단위</th>
            </tr>
            <tr v-for="(val, key) in fitDetail.jsonOptions">
                <td>{% val.optionName %}</td>
                <td class="ta-c">{% val.optionRange %}</td>
                <td class="ta-c">{% val.optionValue %}</td>
                <td class="ta-c">{% val.optionUnit %}</td>
            </tr>
            </tbody>
        </table>
        <div class="dp-flex" style="justify-content: center">
            <div class="btn btn-white hover-btn btn-lg mg5" @click="fitDetail.sno = 0;">상세접기</div>
        </div>
    </div>

    <table class="table table-rows table-default-center table-td-height30 mgt5 " v-if="!$.isEmpty(searchData)" :id="'list-main-table'">
        <colgroup>
            <col class="w-20px" />
            <col class="w-15p" />
            <col class="w-15p" />
            <col class="w-18p" />
            <col class="w-18p" />
            <col class="w-13p" />
            <col class="w-13p" />
        </colgroup>
        <tr>
            <th >번호</th>
            <th >선택</th>
            <th v-for="fieldData in searchData.fieldData"
                v-if="true != fieldData.skip" :class="fieldData.titleClass">
                {% fieldData.title %}
            </th>
        </tr>
        <tr v-if="0 >= listData.length">
            <td :colspan="searchData.fieldData.length+2">
                데이터가 없습니다.
            </td>
        </tr>
        <tr v-for="(each , key) in listData" :class="each.sno == fitDetail.sno ? 'focused' : ''">
            <td >{% (listData.length-key) %}</td>
            <td >
                <span class='btn btn-sm btn-white hover-btn cursor-pointer' @click="viewSizeSpecDetail(each);">상세</span>
                <span class='btn btn-sm btn-red btn-red-line2 hover-btn cursor-pointer' @click="chooseFitSpec(each.sno, each.fitName, each.fitSize, each.jsonOptions);">선택</span>
            </td>
            <td v-for="fieldData in searchData.fieldData"
                v-if="true != fieldData.skip" :class="fieldData.class">
                <div v-if="fieldData.type === 'c'">
                    <!--명칭-->
                    <div v-if="fieldData.name === 'fitSpecName'">
                        {% each[fieldData.name] %}
                    </div>
                </div>
                <div v-else>
                    <?php include './admin/ims/nlist/list_template.php'?>
                </div>
            </td>
        </tr>
    </table>
    <div class="dp-flex" style="justify-content: center">
        <div class="btn btn-white hover-btn btn-lg mg5" @click="self.close()">닫기</div>
    </div>
</section>

<script type="text/javascript">
    const mainListPrefix = 'fit_spec_detail';
    const listSearchDefaultData = {
        page : 1,
        pageNum : 99999,
        sort : 'D,desc',
        sRadioSchFitStyle : '<?=$sPrdStyleGet?>',
        sRadioSchFitSeason : '<?=$sPrdSeasonGet?>',
    };
    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.mode = 'getListFitSpec';
        return ImsNkService.getList('fitSpec', params);
    };

    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            fitDetail : {
                'sno':0,
                'fitName':'',
                'fitStyle':'',
                'fitSeason':'',
                'fitSize':'',
                'jsonOptions':[],
            },
        });

        ImsBoneService.setMethod(serviceData,{
            chooseFitSpec : (sSno, sFitName, sFitSize, aoOption)=>{
                parent.opener.copyFitSpec(sSno, sFitName, sFitSize, aoOption);
                self.close();
            },
            viewSizeSpecDetail : (oObj)=>{
                vueApp.fitDetail = $.copyObject(oObj);
            },
        });
        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData);
        listService.init(serviceData);
    });
</script>