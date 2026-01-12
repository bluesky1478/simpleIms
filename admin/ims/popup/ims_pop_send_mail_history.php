<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_nk.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
    .sl-blue{ color:#2b50f0!important; }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix">
            <h3><?=$requestParam['historyDiv']?> 메일 발송이력</h3>
            <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
        </div>
    </form>

    <table class="table table-rows ch-table table-td-height30">
        <colgroup>
            <col style="width:15%" />
            <col  />
            <col style="width:10%" />
            <col style="width:15%" />
            <col style="width:10%" />
        </colgroup>
        <tr>
            <th>발송일</th>
            <th>메일제목</th>
            <th>수신자명</th>
            <th>수신자이메일</th>
            <th>발송직원</th>
        </tr>
        <tr v-if="0 >= items.length">
            <td colspan="5">데이터가 없습니다.</td>
        </tr>
        <tr v-for="(item, itemIndex) in items">
            <td class="font-11">{% item.regDt %}</td>
            <td class="ta-l sl-blue cursor-pointer hover-btn" style="padding-left:10px;" @click="sChooseTitle=item.subject; sChooseContents=item.contents; $('#modalSendMailDetail').modal('show');">{% item.subject %}</td>
            <td>{% item.receiverName %}</td>
            <td>{% item.receiverMail %}</td>
            <td>{% item.managerNm %}</td>
        </tr>
    </table>
    <div class="modal fade" id="modalSendMailDetail" tabindex="-1" role="dialog"  aria-hidden="true" >
        <div class="modal-dialog" role="document" style="width:calc(100vw - 80px);">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                    <span class="modal-title font-18 bold" >
                    {% sChooseTitle %}
                </span>
                </div>
                <div class="modal-body" v-html="sChooseContents"></div>
                <div class="modal-footer ">
                    <div class="btn btn-gray" data-dismiss="modal">닫기</div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    $(()=>{
        ImsNkService.getList('sendHistory', { sno:'<?=$requestParam['sno']?>', historyDiv:'<?=$requestParam['historyDiv']?>' }).then((data)=>{
            $.imsPostAfter(data, (data)=> {
                if (data.code === 200) {
                    const initParams = {
                        data: {
                            items: data.data,
                            sChooseTitle : '',
                            sChooseContents : '',
                        },
                    }
                    vueApp = ImsService.initVueApp(appId, initParams);
                } else {
                    $.msg(data.msg,'','warning').then(()=>{
                        self.close();
                    });

                }
            });
        });
    });
</script>
