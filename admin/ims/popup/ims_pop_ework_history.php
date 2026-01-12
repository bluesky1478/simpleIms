<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix">
            <h3 v-if="'material' === historyDiv">원부자재 이력</h3>
            <h3 v-if="'spec' === historyDiv">스펙 이력</h3>
            <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
        </div>
    </form>

    <table class="table table-rows ch-table table-td-height30">
        <tr v-if="0 >= items.length">
            <td colspan="99">데이터가 없습니다.</td>
        </tr>
        <tr v-for="(item, itemIndex) in items">
            <td class="pd0 ta-l">
                <div class="font-11 mgt5">▶ {% item.regManagerNm %} {% $.formatShortDateWithoutWeek(item.regDt) %} {% $.formatShortTime(item.regDt) %} {% item.comment %} 하여 이전 정보 저장</div>

                <table class="table table-pd-2 font-11" v-if="'material' === item.updateType">
                    <tr>
                        <th>원단번호</th>
                        <th>구분</th>
                        <th>부위</th>
                        <th>부착위치</th>
                        <th>자재명</th>
                        <th>혼용율</th>
                        <th>컬러</th>
                        <th>규격</th>
                        <th>단위</th>
                        <th>중량</th>
                        <th>후가공</th>
                        <th>가요척</th>
                        <th>단가</th>
                        <th>제조국</th>
                        <th>업체</th>
                        <th>비고</th>
                    </tr>
                    <tr v-for="content in item.contents">
                        <td v-if="contentKey != 'materialSno'" v-for="(contentItem, contentKey) in content">
                            {% contentItem %}
                        </td>
                    </tr>
                </table>

                <div v-if="'spec' === item.updateType">
                    <table class="table table-pd-2 font-11" >
                        <colgroup>
                            <col class="w-13p">
                            <col class="w-4p">
                            <col class="w-13p">
                            <col class="w-5p">
                            <col class="w-5p">
                            <col class="w-16p">
                            <col class="w-45p">
                        </colgroup>
                        <tr>
                            <th>타이틀</th>
                            <th>공유</th>
                            <th>편차</th>
                            <th>기준값</th>
                            <th>단위</th>
                            <th>메모</th>
                            <th>보정값</th>
                        </tr>
                        <tr v-for="content in item.contents.specData">
                            <td v-for="(contentItem, contentKey) in content">
                                {% contentItem %}
                            </td>
                        </tr>
                    </table>
                </div>

            </td>
        </tr>
    </table>
</section>

<script type="text/javascript">

    $(appId).hide();

    $(()=>{
        //Load Data.
        const styleSno = '<?=$requestParam['styleSno']?>';
        const historyDiv = '<?=$requestParam['historyDiv']?>';
        ImsService.getDataParams(DATA_MAP.EWORK_HISTORY, {
            styleSno : styleSno,
            historyDiv : historyDiv,
        }).then((data)=>{
            const initParams = {
                data : {
                    historyDiv : historyDiv,
                    items : data.data,
                },
            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        });
    });
</script>
