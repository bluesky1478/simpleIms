<div class="page-header js-affix">
    <h3><?= end($naviMenu->location); ?></h3>
    <div class="dp-flex">
        <div class="notice-info">&nbsp;</div><div>배송지 정보는 수정시 신규 주문건에만 적용됩니다. 이전 주문 건 변경 필요시 이노버에 문의 주세요.</div>
    </div>

    <div class="pull-right pdr0">
        <button type="button" class="btn btn-red btn-sm js-add-address" data-sno="" data-scmno="<?=$scmNo?>" >배송지 추가</button>
    </div>
</div>

<table class="table table-rows table-pd-3 table-th-height0 table-td-height0 table-center" style="table-layout:fixed; padding:0!important;">
    <colgroup>
        <col style="width:50px" />
        <col style="width:15%" />
        <col style="width:120px" />
        <col />
        <col style="width:13%" />
        <col style="width:13%" />
        <col style="width:80px" />
        <col style="width:80px" />
    </colgroup>
    <thead>
    <tr>
        <th>번호</th>
        <th>배송지점명</th>
        <th>우편번호</th>
        <th>주소</th>
        <th>대표 수령자 연락처</th>
        <th>대표 수령자 이름</th>
        <th>수정</th>
        <th>삭제</th>
    </tr>
    <?php foreach($list as $key => $each) { ?>
    <tr>
        <td class="ta-c"><?=$key+1?></td>
        <td style="padding-left:10px !important;"><?=$each['subject']?></td>
        <td class="ta-c"><?=$each['receiverZonecode']?></td>
        <td style="padding-left:10px !important;">
            <?=$each['receiverAddress']?>
            <?='-' === $each['receiverAddressSub']?'':$each['receiverAddressSub']?>
        </td>
        <td class="ta-c"><?=$each['receiverCellPhone']?></td>
        <td class="ta-c"><?=$each['receiverName']?></td>
        <td class="ta-c">
            <a href="#" class="btn btn-dark-gray btn-sm js-add-address" data-sno="<?= $each['sno'] ?>" data-scmno="<?= $each['scmNo'] ?>">수정</a>
        </td>
        <td class="ta-c">
            <a href="#" class="btn btn-dark-gray btn-sm js-remove-address" data-sno="<?= $each['sno'] ?>" data-scmno="<?= $each['scmNo'] ?>">삭제</a>
        </td>
    </tr>
    <?php } ?>
    </thead>
    <tbody>
    </tbody>
</table>

<script type="text/javascript">
    $(document).ready(function () {
        //배송지 주소 추가/수정
        $('.js-add-address').click(function(){

            console.log('Click Layer');

            var scmNo = $(this).data('scmno');
            var sno = $(this).data('sno');
            var title = '배송지 주소 추가';
            if( !$.isEmpty(sno) ){
                title = '배송지 주소 수정';
            }

            var childNm = 'add_address';

            const reqUrl = '../../share/layer_add_address.php';

            var addParam = {
                mode: 'simple',
                layerTitle: title,
                layerFormID: childNm + "Layer",
                parentFormID: childNm + "Row",
                dataFormID: childNm + "Id",
                dataInputNm: childNm,
                scmNo: scmNo,
                sno: sno,
                layerSubject: title,
                reqUrl: reqUrl,
            };
            layer_add_info(childNm, addParam);
        });

        $('.js-remove-address').click(function(){

            if( confirm('해당 배송지 주소를 정말 삭제하시겠습니까?') ) {
                var scmNo = $(this).data('scmno');
                var sno = $(this).data('sno');
                var param = {
                    mode : 'delete_address'
                    , scmNo : scmNo
                    , sno : sno
                };

                $.post('scm_custom_ps.php', param, function (data) {
                    alert("삭제 완료 되었습니다.");
                    location.reload();
                });
            }
        });

    });

</script>
