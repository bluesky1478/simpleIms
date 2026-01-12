<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<!--스위트 얼럿-->
<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@7.1.0/dist/promise.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css" id="theme-styles">

<?php
$openType = 'newTab';
?>

<div class="page-header js-affix">
    <h3><?= end($naviMenu->location); ?></h3>
    <div class="btn-group">
        <input type="button" value="재입고 알림 상품 관리" class="btn btn-red-box js-batch-restock"/>
    </div>
</div>

<form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
    <input type="hidden" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
    <input type="hidden" name="searchFl" value="y"/>
    <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 30); ?>"/>
    <div class="table-title gd-help-manual">
        품절상품 요청 리스트(요청기준)
    </div>
    <?php include('soldout_req_search.php'); ?>
</form>

<form id="frmList" action="" method="get" target="ifrmProcess">
    <div class="table-header form-inline">
        <div class="pull-left">
            검색
            <strong><?= empty($page->recode['total'])? 0 : $page->recode['total']; ?></strong>
            건
        </div>
        <div class="pull-right">
            <div>
                <?= gd_select_box('sort', 'sort', $search['sortList'], null, $search['sort']); ?>
                <?= gd_select_box_by_page_view_count(Request::get()->get('pageNum', 30)); ?>
            </div>
        </div>
    </div>

    <table class="table table-rows">
        <colgroup>
            <col style="width:55px" /><!--체크-->
            <col style="width:55px" /><!--번호-->
            <col style="width:110px" /><!--공급사-->
            <col style="width:80px" /><!--신청자-->
            <col style="width:110px" /><!--연락처-->
            <col style="width:140px" /><!--회원정보-->
            <col style="width:300px" /><!--상품명-->
            <col  />
            <col style="width:150px" />
            <col style="width:100px" />
            <col style="width:80px" />
            <col style="width:100px" />
        </colgroup>

        <thead>
        <tr>
            <th>
                <input type="checkbox" value="y" class="js-checkall" data-target-name="reqSno">
            </th>
            <?php foreach ($listTitles as $val) { ?>
            <th><?=$val?></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php
        if (gd_isset($data)) {
            foreach ($data as $val) {
        ?>
                <tr class="center">
                    <td class="center rowspan-class">
                        <?php if( empty($val['sendType']) ) { ?>
                            <input type="checkbox" name="reqSno[]" class="check-req-sno" value="<?=$val['reqSno']?>">
                        <?php }else{ ?>
                            <input type="checkbox" class="disabled" disabled="disabled">
                        <?php } ?>

                    </td>
                    <td class="font-num"><!--번호-->
                        <?=number_format($page->idx--); ?>
                    </td>
                    <td class="center text-nowrap" ><!--공급사-->
                        <?=$val['companyNm']; ?>
                    </td>
                    <td class="center text-nowrap" ><!--신청자-->
                        <?=$val['reqName']; ?>
                    </td>
                    <td class="center text-nowrap" ><!--연락처-->
                        <?=$val['cellPhone']; ?>
                    </td>
                    <td class="center text-nowrap" ><!--회원정보-->
                        <?= $val['memNm'] ?>
                        <p class="mgb0">
                            <?php if (!$val['memNo']) { ?>
                                <?php if (!$val['memNoCheck']) { ?>
                                    <span class="font-kor">(비회원)</span>
                                <?php } else { ?>
                                    <span class="font-kor">(탈퇴회원)</span>
                                <?php } ?>
                            <?php } else { ?>
                                <?php if (!$isProvider) { ?>
                                    <button type="button" class="btn btn-link font-eng js-layer-crm" data-member-no="<?= $val['memNo'] ?>">(<?= $val['memId'] ?>)
                                <?php } else { ?>
                                    (<?= $val['memId'] ?>)
                                <?php } ?>
                                </button>
                            <?php } ?>
                        </p>
                        <div class="text-muted"><?=$val['nickNm']; ?></div>
                        <div class="text-muted"><?=$val['masterCellPhone']; ?></div>
                    </td>
                    <td class="center text-nowrap" ><!--상품명-->
                        <?=$val['goodsNm']; ?>
                        <br><small class="text-muted"><?=$val['goodsNo']?></small>
                    </td>
                    <td class="left text-nowrap" ><!--옵션/수량-->
                        <?=$val['optionListStr']; ?>
                    </td>
                    <td class="center text-nowrap" ><!--선택지점-->
                        <?=$val['deliveryName']; ?>
                    </td>
                    <td class="center text-nowrap" ><!--요청일-->
                        <?=gd_date_format('Y-m-d',$val['regDt']); ?>
                        <br><small class="text-muted"><?=gd_date_format('h:i:s',$val['regDt']); ?></small>
                    </td>
                    <td class="center text-nowrap" ><!--입고알림 여부/타입-->
                        <?=$soldoutReqSendType[$val['sendType']]?>
                    </td>
                    <td class="center text-nowrap" ><!--알림시간-->
                        <?=gd_date_format('Y-m-d',$val['sendDt']); ?>
                        <br><small class="text-muted"><?=gd_date_format('h:i:s',$val['sendDt']); ?></small>
                    </td>
                </tr>
                <?php
            }
        } else {
            echo '<tr><td class="center" colspan="16">검색된 정보가 없습니다.</td></tr>';
        }
        ?>
        </tbody>
    </table>

    <div class="table-action clearfix">

        <div class="pull-left">
            <button type="button" class="btn btn-red" id="btn-req-del">선택한 요청 삭제</button>
            <button type="button" class="btn btn-red" id="btn-req-response">선택한 요청 알림보내기</button>
            <?php if( !empty($categoryList) ) { ?>
            <span id="sendCategory" class="dn">
                주문하기 연결 카테고리 :
                <select id="selected-send-category">
                    <option value="">없음</option>
                    <?php foreach( $categoryList as $scmCateKey => $scmCate ) { ?>
                        <option value="<?=$scmCateKey?>"><?=$scmCate?></option>
                    <?php } ?>
                </select>
            </span>
            <?php } ?>
            <div>* 공급사 1개를 선택하면 알림톡 발송시 '주문하기' 버튼에 연결할 카테고리를 설정할 수 있습니다. </div>
            <div>* 카테고리를 선택하면 주문하기 버튼을 클릭할 때 해당 카테고리로 자동으로 이동 됩니다. </div>
        </div>
        <div class="pull-right">
            <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀다운로드</button>
        </div>
    </div>

    <div class="center"><?= $page->getPage(); ?></div>

    <script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>

</form>

<script>
    $(function(){
        $('#btn-req-del').click(function(){
            var chkCnt = $('input[name*="reqSno"]:checked').length;
            if (chkCnt == 0) {
                alert('선택된 요청이 없습니다.');
                return;
            }

            $.msgConfirm(`선택된 ${chkCnt}명의 입고 요청을 삭제하시겠습니까?`,'삭제후 복원이 불가합니다.').then(function(result){
                if( result.isConfirmed ){
                    let reqSnoList = [];
                    $('input[name*="reqSno"]:checked').each(function(){
                        reqSnoList.push($(this).val());
                    });
                    let params = {
                        'mode' : 'delSoldOutRequest',
                        'reqSnoList': reqSnoList,
                    }
                    $.postAsync('../ajax/custom_api_ps.php', params).then((data)=>{
                        if( 200 == data.code ){
                            $.msg('삭제 되었습니다.', "", "success").then(()=>{
                                location.reload();
                            });
                        }
                    });
                }
            });
        });


        $('#btn-req-response').click(function(){
            var chkCnt = $('input[name*="reqSno"]:checked').length;
            if (chkCnt == 0) {
                alert('선택된 요청이 없습니다.');
                return;
            }

            $.msgConfirm(`선택된 ${chkCnt}명에게 입고 알림톡을 전송하시겠습니까?`,'').then(function(result){
                if( result.isConfirmed ){

                    let reqSnoList = [];
                    let category = $('#selected-send-category').val();

                    $('input[name*="reqSno"]:checked').each(function(){
                        reqSnoList.push($(this).val());
                    });

                    let params = {
                        'mode' : 'sendSoldOutRequestEach',
                        'reqSnoList': reqSnoList,
                        'category': category,
                    }

                    $.postAsync('../ajax/custom_api_ps.php', params).then((data)=>{
                        if( 200 == data.code ){
                            $.msg('전송이 완료되었습니다.', "", "success").then(()=>{
                                location.reload();
                            });
                        }
                    });
                }
            });
        });

        // 재입고 알림 상품 관리
        $('.js-batch-restock').on('click', function (e) {
            var addParam = {
                "layerFormID": 'restockBatchLayer',
                "parentFormID": 'restockScmLayer',
                "dataFormID": 'restock_info_scm',
                "layerTitle": '재입고 알림 상품 관리'
            };
            layer_add_info('goods_restock_batch', addParam);
        });

        //Sort value link other form
        $('select[name=\'sort\']').change(function(){
            $('#frmSearchBase').find('input[name=\'sort\']').val( $(this).val() );
        });
        //simple excel download
        $('.simple-download').click(function(){
            location.href = "<?=$requestUrl?>";
        });
        //console.log( '<?=$requestUrl?>' );
    });

    /**
     * 카테고리 연결하기 Ajax layer
     */
    function layer_register(typeStr, mode, isDisabled) {
        var addParam = {
            "mode": mode,
        };

        if (typeStr == 'scm') {
            $('input:radio[name=scmFl]:input[value=y]').prop("checked", true);
        }

        if (!_.isUndefined(isDisabled) && isDisabled == true) {
            addParam.disabled = 'disabled';
        }
        layer_add_info(typeStr,addParam);
    }

    function afterProcScmLayerClose(){
        var defaultOption = '<option value="">없음</option>';
        $('#selected-send-category').html(defaultOption);

        if( 1 == $('.selected-scm-no').length ){
            var params = {
                'mode' : 'getScmCategory',
                'scmNo' : $('.selected-scm-no').eq(0).val() ,
            }
            $.postAsync('../ajax/custom_api_ps.php', params).then((data)=>{
                if( 200 == data.code ){
                    data.data.forEach(function(each){
                        var optionHtml = `<option value="${each.cateCd}">${each.cateNm}</option>`;
                        $('#selected-send-category').append(optionHtml);
                    });
                }
            });
        }
    }

</script>
