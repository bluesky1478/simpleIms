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
            <h3 class="">품목 구분 관리</h3>
            <div class="btn-group font-18 bold">
            </div>
        </div>
    </form>

    <div class="">
        <!-- 기본 정보 -->
        <div>
            <table class="table table-cols table-pd-5" style="border-top:none !important;">
                <colgroup>
                    <col style="width:16%;">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>자재타입</th>
                    <td>
                        <select2 v-model="typeDetail.materialTypeByDetail" style="width:100%;">
                            <?php foreach( \Component\Ims\NkCodeMap::MATERIAL_TYPE as $k => $v){ ?>
                                <option value="<?=$k?>"><?=$v?></option>
                            <?php } ?>
                        </select2>
                    </td>
                </tr>
                <tr>
                    <th>분류명</th>
                    <td>
                        <?php $model='typeDetail.materialTypeText'; $placeholder='분류명' ?>
                        <?php include './admin/ims/template/basic_view/_text.php'?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="dp-flex" style="justify-content: center">
            <div class="btn btn-accept hover-btn btn-lg mg5" @click="save()">{% btnText %}</div>
            <div v-show="btnText=='수정'" class="btn btn-accept hover-btn btn-lg mg5" @click="returnRegist()">등록으로 전환</div>
            <div class="btn btn-white hover-btn btn-lg mg5" @click="self.close()">닫기</div>
        </div>
    </div>

    <div>
        <table class="table table-rows table-default-center table-td-height30 mgt5 " v-if="!$.isEmpty(searchData)" :id="'list-main-table'">
            <colgroup>
                <col class="w-10p" />
                <col class="w-20p" />
                <col />
                <col class="w-10p" />
            </colgroup>
            <tr>
                <th >번호</th>
                <th >자재타입</th>
                <th >분류명</th>
                <th >관리</th>
            </tr>
            <tr  v-if="0 >= listData.length">
                <td colspan="4">
                    데이터가 없습니다.
                </td>
            </tr>
            <tr v-show="typeDetail.materialTypeByDetail==each.materialTypeByDetail" v-for="(each , index) in listData">
                <td >{% (index+1) %}</td>
                <td >{% each.materialTypeHan %}</td>
                <td >{% each.materialTypeText %}</td>
                <td ><span class='btn btn-sm btn-white hover-btn cursor-pointer' @click="modMType(index)">수정</span></td>
            </tr>
        </table>
    </div>
</section>

<script type="text/javascript">
    const mainListPrefix = 'material_type_detail';
    const listSearchDefaultData = {
        page : 1,
        pageNum : 999,
        sort : 'D,asc'
    };
    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.mode = 'getListMaterialTypeDetail';
        return ImsNkService.getList('materialTypeDetail', params);
    };

    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            isModify : true,
            typeDetail : { //현재 존재하는 원단리스트 obj에 담기
                'sno':0,
                'materialTypeByDetail':1,
                'materialTypeText':''
            },
            btnText : '등록',
        });

        ImsBoneService.setMethod(serviceData,{
            returnRegist : ()=>{
                vueApp.typeDetail.sno = 0;
                vueApp.typeDetail.materialTypeByDetail = 1;
                vueApp.typeDetail.materialTypeText = '';
                vueApp.btnText = '등록';
            },
            modMType : (index) => {
                vueApp.typeDetail.sno = vueApp.listData[index].sno;
                vueApp.typeDetail.materialTypeByDetail = vueApp.listData[index].materialTypeByDetail;
                vueApp.typeDetail.materialTypeText = vueApp.listData[index].materialTypeText;
                vueApp.btnText = '수정';
            },
            save : ()=>{
                if (vueApp.typeDetail.materialTypeText === null || vueApp.typeDetail.materialTypeText === '') {
                    $.msg('분류명을 입력하세요','','error');
                    return false;
                }

                $.imsPost('setMaterialTypeDetail', {'data':vueApp.typeDetail}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        vueApp.returnRegist();

                        listService.refreshList(vueApp.searchCondition.page);
                    });
                });
            }
        });
        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData);
        listService.init(serviceData);
    });
</script>