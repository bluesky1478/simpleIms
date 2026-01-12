<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix">
            <h3>파일 등록 히스토리</h3>
            <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
        </div>
    </form>

    <table class="table table-rows ch-table">
        <colgroup>
            <col style="width:5%" />
            <col  />
            <col style="width:30%" />
            <col style="width:60px" />
            <col style="width:12%" />
        </colgroup>
        <tr>
            <th>Rev</th>
            <th>파일</th>
            <th>메모</th>
            <th>등록자</th>
            <th>등록일</th>
        </tr>
        <tr v-for="(item, itemIndex) in items">
            <td>
                {% item.rev %}
                <?php if( in_array($managerId, \Component\Ims\ImsCodeMap::IMS_ADMIN) ) { ?>
                    <div class="btn btn-red btn-sm btn-red-line2 cursor-pointer hover-btn" @click="ImsService.deleteData('imsFile',item.sno, ()=>{parent.opener.location.reload(); location.reload();})" >삭제</div>
                <?php } ?>
            </td>
            <td class="text-left">
                <div class="text-muted">
                    <!--{% item.customerName %}-->
                    {% item.projectSno %}
                    {% item.productName %}({% item.styleCode %})의 파일
                </div>
                <div>
                    <ul class="ims-file-list" >
                        <li class="hover-btn" v-for="(file, fileIndex) in item.fileList" style="display: block">
                            <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                        </li>
                    </ul>
                </div>
            </td>
            <td class="ta-l pdl5">{% item.memo %}</td>
            <td>
                {% item.managerNm %}
            </td>
            <td>
                {% item.regDt %}
            </td>
        </tr>
    </table>
</section>

<script type="text/javascript">

    $(appId).hide();

    $(()=>{
        //Load Data.
        const customerSno = '<?=$requestParam['customerSno']?>';
        const projectSno = '<?=$requestParam['projectSno']?>';
        const styleSno = '<?=$requestParam['styleSno']?>';
        const eachSno = '<?=$requestParam['eachSno']?>';

        ImsService.getDataParams(DATA_MAP.FILE_HISTORY, {
            customerSno : customerSno,
            projectSno : projectSno,
            styleSno : styleSno,
            eachSno : eachSno,
            fileDiv : '<?=$requestParam['fileDiv']?>',
        } ).then((data)=>{
            console.log(data);
            const initParams = {
                data : {
                    items : data.data,
                },
            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        });
    });
</script>
