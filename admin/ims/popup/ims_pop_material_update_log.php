<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>
<?php include './admin/ims/library_nk.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
            <h3 class="">수정이력 리스트</h3>
            <div class="btn-group font-18 bold">
            </div>
        </div>
    </form>

    <div>
        <table v-if="listData.length === 0" class="table table-rows table-default-center table-td-height30 mgt5 ">
            <colgroup><col /></colgroup>
            <tr>
                <td>
                    데이터가 없습니다.
                </td>
            </tr>
        </table>
        <table v-else v-for="(val , key) in listData" class="table table-rows table-default-center table-td-height30 mgt5 ">
            <colgroup>
                <col class="w-12p" />
                <col class="w-10p" />
                <col class="w-15p" />
                <col />
                <col />
            </colgroup>
            <tr>
                <th >수정일시</th>
                <th >수정인</th>
                <th >수정항목</th>
                <th >이전값</th>
                <th >수정값</th>
            </tr>
            <tr v-for="(val2 , key2) in val.updateDesc">
                <td v-if="key2 === 0" :rowspan="val.updateDesc.length">{% val.RegDt %}</td>
                <td v-if="key2 === 0" :rowspan="val.updateDesc.length">{% val.managerNm %}</td>
                <td >{% val2.fld_name %}</td>
                <td >{% val2.before %}</td>
                <td >{% val2.after %}</td>
            </tr>
        </table>
    </div>
    <div class="dp-flex" style="justify-content: center">
        <div class="btn btn-white hover-btn btn-lg mg5" @click="self.close()">닫기</div>
    </div>
</section>

<script type="text/javascript">
    const mainListPrefix = 'material_update_log';
    const listSearchDefaultData = {
        page : 1,
        pageNum : 15000,
        materialSno : <?=$iSno?>,
        sort : 'D,desc'
    };
    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.mode = 'getListMaterialUpdateLog';
        return ImsNkService.getList('materialUpdateLog', params);
    };

    $(()=>{
        const serviceData = {};
        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData);
        listService.init(serviceData);
    });
</script>