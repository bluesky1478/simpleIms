<style>
    .form-ul li{
        margin-bottom:15px
    }
</style>

<div class="page-header js-affix">
    <h3><?= end($naviMenu->location); ?></h3>
    <div class="btn-group"></div>
</div>

<section class="excel-upload-section ">
    <div class="table-title">
        양식 다운로드
    </div>
    <div class="search-detail-box form-inline">
        <table class="table table-cols">
            <colgroup>
                <col class="width-md">
                <col class="width-3xl">
                <col class="width-md">
                <col class="width-3xl">
            </colgroup>
                <th>업로드 양식 설정</th>
                <td colspan="3">
                    <form id="frm1" action="./util_packing.php" method="post" target="ifrmProcess" ></form>
                    <input type="hidden" name="mode" value="downloadUploadForm">

                    <ul class="form-ul">
                        <li>
                            상&nbsp;&nbsp;&nbsp;품
                            <textarea name="prdList" class="form-control" style="width:300px;" rows="5" placeholder="ex)&#13;&#10;하계 티셔츠&#13;&#10;하계 바지" id="prdList"></textarea>
                            <div class="text-muted notice-info" style="margin-left:40px">개행(enter)으로 구분</div>
                        </li>
                        <li style="margin-top:5px;">
                            옵&nbsp;&nbsp;&nbsp;션
                            <input name="optionList" type="text" class="form-control" style="width:300px" value="85,90,95,100,105,110,115,120" id="optionList">
                            <div class="text-muted notice-info" style="margin-left:40px">컴마(,)로 구분</div>
                        </li>
                        <li style="margin-top:5px;">
                            배송지
                            <input name="deliveryCount" type="text" class="form-control" style="width:50px" value="15" id="deliveryCount">
                            <div class="text-muted notice-info" style="margin-left:40px">숫자만 입력, 배송지점 수</div>
                        </li>
                        <li>
                            받&nbsp;&nbsp;&nbsp;기
                            <input type="button" value="패킹 리스트 업로드 양식 다운로드" class="btn btn-white btn-icon-excel btn-download-upload-form mgt10" >
                        </li>
                    </ul>
                    </form>
                </td>
            </tr>
            <tr>
                <th>패킹 리스트</th>
                <td colspan="3">
                    <form id="frmExcel1" name="frmExcel1" action="./erp_ps.php" method="post" enctype="multipart/form-data" target="ifrmProcess">
                        <div class="form-inline">
                            <input type="hidden" name="runMethod" value="iframe"/>
                            <input type="hidden" name="mode" value="downloadPackingList"/>
                            <input type="file" name="excel" value="" class="form-control width50p" />
                            <input type="button"  class="btn btn-white btn-sm excel-submit1" value="업로드">
                        </div>
                    </form>

                    <div class="text-muted notice-info">양식을 업로드하면 패킹리스트로 변환되어 다운로드 됩니다.</div>
                </td>
            </tr>
        </table>
    </div>
</section>

<script type="text/javascript">
    $(()=>{

        $('.excel-submit1').click(()=>{
            $('#frmExcel1').submit();
        });

        $('.btn-download-upload-form').click(()=>{
            const prdList = $('#prdList').val();
            const optionList = $('#optionList').val();
            if( $.isEmpty(prdList) ){
                alert('상품을 입력하세요.');
                return false;
            }
            if( $.isEmpty(optionList) ){
                alert('옵션을 입력하세요.');
                return false;
            }
            let myObject = {
                mode : 'downloadUploadForm',
                prdList : prdList,
                optionList : optionList,
                deliveryCount : $('#deliveryCount').val(),
            };
            const params = new URLSearchParams();
            for (let key in myObject) {
                if (myObject.hasOwnProperty(key)) {
                    params.append(key, myObject[key]);
                }
            }

            const queryString = params.toString();
            location.href="util_packing.php?"+queryString;

        });

        /*$('.btn-download-upload-form').click(()=>{
            let params = {
                mode : 'downloadUploadForm',
                prdList : $('#prdList').val(),
                optionList : $('#optionList').val(),
                deliveryCount : $('#deliveryCount').val(),
            };
            $.post('util_packing.php', params, function (data) {
                console.log(data);
                //alert('ok');
            });
        })*/

        //onclick="location.href='util_packing.php?mode=downloadUploadForm'"/

    });
</script>