<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
    .mgb4 { margin-bottom:4px!important; }
</style>

<section id="imsApp">
    <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
        <h3 class="">신규고객 발굴 영업현황</h3>
        <div class="btn-group font-18 bold">
            <div @click="self.close()" class="btn btn-white hover-btn btn-lg mg5" style="line-height:35px;">닫기</div>
        </div>
    </div>
    <div>
        <!--검색 시작-->
        <div class="search-detail-box form-inline">
            <table class="table table-cols table-td-height0">
                <colgroup>
                    <col class="w-10p">
                    <col class="">
                </colgroup>
                <tbody>
                <tr>
                    <th>기간</th>
                    <td>
                        <span class="mini-picker " style="margin-right: -18px;">
                            <date-picker v-model="searchCondition.sTextboxRangeStartSchStatsDt" value-type="format" format="YYYY-MM-DD"  :editable="false" ></date-picker>
                        </span>
                        ~
                        <span class="mini-picker ">
                            <date-picker v-model="searchCondition.sTextboxRangeEndSchStatsDt"  value-type="format" format="YYYY-MM-DD"  :editable="false" ></date-picker>
                        </span>
                        <div class="btn btn-sm btn-white mgb4" @click="ImsService.setSearchDate(searchCondition, 'sTextboxRangeStartSchStatsDt', 'sTextboxRangeEndSchStatsDt', 'today')">오늘</div>
                        <div class="btn btn-sm btn-white mgb4" @click="ImsService.setSearchDate(searchCondition, 'sTextboxRangeStartSchStatsDt', 'sTextboxRangeEndSchStatsDt', 'week')">이번주</div>
                        <div class="btn btn-sm btn-white mgb4" @click="ImsService.setSearchDate(searchCondition, 'sTextboxRangeStartSchStatsDt', 'sTextboxRangeEndSchStatsDt', 'month')">이번달</div>
                        <div class="btn btn-sm btn-white mgb4" @click="ImsService.setSearchDate(searchCondition, 'sTextboxRangeStartSchStatsDt', 'sTextboxRangeEndSchStatsDt', 'year')">이번년도</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="99" class="ta-c" style="border-bottom: none">
                        <input type="submit" value="검색" class="btn btn-lg btn-black" @click="refreshList(1)">
                        <input type="submit" value="초기화" class="btn btn-lg btn-white" @click="conditionReset()">
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <!--검색 끝-->
        <table class="table table-rows table-default-center table-td-height30 mgt5 ">
            <colgroup>
                <col />
                <col class="w-10p" />
                <col class="w-10p" />
                <col class="w-10p" />
                <col class="w-10p" />
                <col class="w-12p" />
                <col class="w-12p" />
                <col class="w-12p" />
                <col class="w-12p" />
            </colgroup>
            <thead>
            <tr>
                <td rowspan="3" class="bg-light-gray" style="border-left: solid 1px #AEAEAE">구분</td>
                <td colspan="4" class="bg-light-gray">영업 활동 현황 (건)</td>
                <td colspan="4" class="bg-light-gray">고객 현황</td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom: solid 1px #AEAEAE">TM</td>
                <td colspan="2" style="border-bottom: solid 1px #AEAEAE">EM</td>
                <td rowspan="2" style="vertical-align: middle;border-left: solid 1px #AEAEAE; border-bottom: solid 1px #AEAEAE">잠재고객</td>
                <td rowspan="2" style="vertical-align: middle;border-bottom: solid 1px #AEAEAE">관심고객</td>
                <td rowspan="2" style="vertical-align: middle;border-bottom: solid 1px #AEAEAE">가망고객</td>
                <td rowspan="2" style="vertical-align: middle;border-right: solid 1px #AEAEAE;border-bottom: solid 1px #AEAEAE">
                    발굴 완료
                </td>
            </tr>
            <tr>
                <td style="border-bottom: solid 1px #AEAEAE">콜수(건)</td>
                <td style="border-bottom: solid 1px #AEAEAE">소요 시간(분)</td>
                <td style="border-bottom: solid 1px #AEAEAE">발송(건)</td>
                <td style="border-bottom: solid 1px #AEAEAE">소요 시간(분)</td>
            </tr>
            </thead>
            <tbody>
            <tr v-if="listData.length == 0">
                <td colspan="9">
                    데이터가 없습니다.
                </td>
            </tr>
            <tr v-else v-for="(val , key) in listData">
                <td style="border-right: solid 1px #AEAEAE">
                    {% $.formatShortDate(val.statsDt) %}
                </td>
                <td>{% $.setNumberFormat(val.cntTm) %}</td>
                <td>{% $.setNumberFormat(val.sumMinTm) %}</td>
                <td>{% $.setNumberFormat(val.cntEm) %}</td>
                <td style="border-right: solid 1px #AEAEAE">{% $.setNumberFormat(val.sumMinEm) %}</td>
                <td>
                    {% $.setNumberFormat(val.cntCustomer1) %}
                    <span @click="openSCList(val.jsonIncCustomer1)" v-if="val.jsonIncCustomer1.length > 0" class="text-danger cursor-pointer hover-btn">(▲{% val.jsonIncCustomer1.length %})</span>
                    <span @click="openSCList(val.jsonDecCustomer1)" v-if="val.jsonDecCustomer1.length > 0" class="text-blue cursor-pointer hover-btn">(▼{% val.jsonDecCustomer1.length %})</span>
                </td>
                <td>
                    {% $.setNumberFormat(val.cntCustomer2) %}
                    <span @click="openSCList(val.jsonIncCustomer2)" v-if="val.jsonIncCustomer2.length > 0" class="text-danger cursor-pointer hover-btn">(▲{% val.jsonIncCustomer2.length %})</span>
                    <span @click="openSCList(val.jsonDecCustomer2)" v-if="val.jsonDecCustomer2.length > 0" class="text-blue cursor-pointer hover-btn">(▼{% val.jsonDecCustomer2.length %})</span>
                </td>
                <td>
                    {% $.setNumberFormat(val.cntCustomer3) %}
                    <span @click="openSCList(val.jsonIncCustomer3)" v-if="val.jsonIncCustomer3.length > 0" class="text-danger cursor-pointer hover-btn">(▲{% val.jsonIncCustomer3.length %})</span>
                    <span @click="openSCList(val.jsonDecCustomer3)" v-if="val.jsonDecCustomer3.length > 0" class="text-blue cursor-pointer hover-btn">(▼{% val.jsonDecCustomer3.length %})</span>
                </td>
                <td>
                    {% $.setNumberFormat(val.cntCustomer4) %}
                    <span @click="openSCList(val.jsonIncCustomer4)" v-if="val.jsonIncCustomer4.length > 0" class="text-danger cursor-pointer hover-btn">(▲{% val.jsonIncCustomer4.length %})</span>
                    <span @click="openSCList(val.jsonDecCustomer4)" v-if="val.jsonDecCustomer4.length > 0" class="text-blue cursor-pointer hover-btn">(▼{% val.jsonDecCustomer4.length %})</span>
                </td>
            </tr>
            </tbody>
        </table>
        <div id="sales_customer_stats-page" v-html="pageHtml" class="ta-c"></div>
    </div>
    <div class="modal fade" id="modalSCList" tabindex="-1" role="dialog"  aria-hidden="true" >
        <div class="modal-dialog" role="document" style="width:98%;">
            <div class="modal-content" style="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                    <span class="modal-title font-18 bold" >
                        고객발굴 리스트
                    </span>
                </div>
                <div class="modal-body">
                    <table class="table table-rows table-default-center table-td-height30 mgt5 ">
                        <colgroup>
                            <col v-if="true != fieldData.skip && fldKey < 13" v-for="(fieldData, fldKey) in aoSCFlds" :class="fieldData.col==0?``:`w-${fieldData.col+2}p`" />
                        </colgroup>
                        <tr>
                            <th v-if="true != fieldData.skip && fldKey < 13" v-for="(fieldData, fldKey) in aoSCFlds" :class="fieldData.titleClass">
                                {% fieldData.title %}
                            </th>
                        </tr>
                        <tr v-for="(val , key) in aoSCList" class="hover-light">
                            <td v-if="true != fieldData.skip && fldKey < 13" v-for="(fieldData, fldKey) in aoSCFlds" :class="fieldData.class">
                                <span v-if="fieldData.type === 'i'">{% $.setNumberFormat(val[fieldData.name]) %}</span>
                                <span v-else-if="fieldData.type === 'date'">{% $.formatShortDate(val[fieldData.name]) %}</span>
                                <span v-else>{% val[fieldData.name]===null||val[fieldData.name]==''?'-':val[fieldData.name] %}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    const mainListPrefix = 'sales_customer_stats';
    const listSearchDefaultData = {
        page : 1,
        pageNum : 10,
        sort : 'D,asc',

        sTextboxRangeStartSchStatsDt : '',
        sTextboxRangeEndSchStatsDt : '',
    };
    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        //기간검색값이 없으면 이번달이 default
        if (vueApp.searchCondition.sTextboxRangeStartSchStatsDt == '' && vueApp.searchCondition.sTextboxRangeEndSchStatsDt == '') {
            ImsService.setSearchDate(vueApp.searchCondition, 'sTextboxRangeStartSchStatsDt', 'sTextboxRangeEndSchStatsDt', 'month');
        }
        params.mode = 'getListSalesCustomerStats';
        return ImsNkService.getList('salesCustomerStats', params);
    };

    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            aoSCFlds : [],
            aoSCList : [],
        });

        ImsBoneService.setMethod(serviceData,{
            openSCList : (aSnos)=>{
                vueApp.aoSCFlds = [];
                vueApp.aoSCList = [];
                ImsNkService.getList('findCustomer', {'getSimple':1, 'SCSnos':aSnos}).then((data)=>{
                    $.imsPostAfter(data, (data)=> {
                        if (data.list.length > 0) {
                            vueApp.aoSCFlds = data.fieldData;
                            vueApp.aoSCList = data.list;
                            $('#modalSCList').modal('show');
                        } else {
                            $.msg('접근오류','','error');
                            return false;
                        }
                    });
                });
            },
        });
        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData);
        listService.init(serviceData);
    });
</script>