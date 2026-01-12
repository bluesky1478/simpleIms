<?php include './admin/ims/library_all.php'?>
<style>
    /*주문리스트 관리자 추가 상품 정보 */
    .layer-order-add-info {padding:15px}
    .layer-order-add-info .order-add-info-title {font-size:14px;text-align: left;font-weight: bold;}
    .layer-order-add-info th{ background-color:#F6F6F6!important; text-align: center; color:#5c5c5c }
    .layer-order-add-info td{ text-align: left }
    .sales-table td,th{
        font-size:11px;
    }
</style>
<?php
$openType = 'newTab';
?>

<div class="page-header js-affix affix-top" style="width: auto; left: auto; padding-bottom:0 !important;">
    <section id="affix-show-type1">
        <h3><?= end($naviMenu->location); ?></h3>

        <?php if(!empty($stepManagerInfo)) { ?>
            <div class="relative">
                <div class="list-photo" style="background-image:url('../..<?=$stepManagerInfo['dispImage']?>');"></div>
                <div class="list-photo-title">담당 : <?=$stepManagerInfo['managerNm']?> <?=$stepManagerInfo['positionName']?>
                    <div class="font-14">(
                        <?=$stepManagerInfo['cellPhone'] ?>
                        <?=empty($stepManagerInfo['email'])?'':"<a href='mailto:{$stepManagerInfo['email']}' class='sl-blue'>{$stepManagerInfo['email']}</a>"?>
                        )</div>
                </div>
            </div>
        <?php } ?>

        <div class="btn-group">
            <?php if(!empty($isSales) || !empty($isAuth) ) { ?>
                <input type="button" value="<?=$regBtnName?>" class="btn btn-red btn-reg hover-btn" />
            <?php } ?>
            <?php if(!empty($isDev)) { ?>
                <!--
                <input type="button" value="프로젝트 일괄 등록" class="btn btn-red-line"  onclick="$('.excel-upload-goods-info').show('fade')"  />
                -->
            <?php } ?>
        </div>
    </section>
    <section id="affix-show-type2" style="margin:0 !important; display: none ">
        <h3><?= end($naviMenu->location); ?></h3>

        <?php if(!empty($stepManagerInfo)) { ?>
            <div class="relative">
                <div class="list-photo" style="background-image:url('../..<?=$stepManagerInfo['dispImage']?>');"></div>
                <div class="list-photo-title">담당 : <?=$stepManagerInfo['managerNm']?> <?=$stepManagerInfo['positionName']?>
                    <div class="font-14">(
                        <?=$stepManagerInfo['cellPhone'] ?>
                        <?=empty($stepManagerInfo['email'])?'':"<a href='mailto:{$stepManagerInfo['email']}' class='sl-blue'>{$stepManagerInfo['email']}</a>"?>
                        )</div>
                </div>
            </div>
        <?php } ?>

        <div class="btn-group">
            <?php if(!empty($isSales) || !empty($isAuth) ) { ?>
                <input type="button" value="<?=$regBtnName?>" class="btn btn-red btn-reg hover-btn" />
            <?php } ?>
        </div>
    </section>
    <table class="table table-rows" id="affix-show-type3" style="margin:0 !important; display: none ">
        <colgroup>
            <!--공통 열 colgroup-->
            <?php include './admin/ims/list/_fixed_colgroup.php'?>
            <?php foreach ($stepItem as $stepKey => $stepValue) { ?>
                <col style="width:<?=$stepValue['col']?>%" />
            <?php } ?>
            <col style="width:3%" />
        </colgroup>
        <thead>
        <tr>
            <th>
                <div style="width:14px; height:14px"></div>
            </th>
            <th>번호</th>
            <?php include './admin/ims/list/_fixed_title.php'?>
            <?php foreach ($stepItem as $stepKey => $stepValue) { ?>
                <th style='<?=$stepValue['titleStyle']?>' ><?=$stepValue['title']?></th>
            <?php } ?>
            <th>등록/수정일</th>
        </tr>
        </thead>
    </table>

</div>

<?php include './admin/ims/list/_common_search.php'?>

<form id="frmList" action="" method="get" target="ifrmProcess">

    <div class="table-header form-inline">
        <div class="pull-left">
        <span class="font-15">
        검색
        <strong>
            <?= empty($page->recode['total'])? 0 : number_format($page->recode['total']); ?></strong> 건
        </span>
        </div>
        <div class="pull-right">
            <div>
                <!--<button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀 다운로드</button>-->
                <?= gd_select_box('sort', 'sort', $search['sortList'], null, $search['sort']); ?>
                <?= gd_select_box_by_page_view_count(Request::get()->get('pageNum', 30)); ?>
            </div>
        </div>
    </div>

    <table class="table table-rows">
        <colgroup>
            <col style="width:1%" /><!--1-->
            <col style="width:2%" /><!--2-->

            <col class="w-4p" /><!--3-->
            <col class="w-4p" /><!--4-->
            <col class="w-4p" /><!--5-->
            <col class="w-14p" /><!--6--><!--고객사/프로젝트-->
            <col class="w-4p" /><!--7-->
            <col class="w-4p" /><!--8-->
            <col class="w-4p" /><!--9-->
            <col class="w-13p" /><!--10--><!--스타일-->
            <col class="w-4p" /><!--11-->
            <col class="w-4p" /><!--12-->
            <col class="w-4p" /><!--13-->
            <col class="w-4p" /><!--14-->
            <col class="w-4p" /><!--15-->
            <col class="w-4p" /><!--16-->
            <col class="w-4p" /><!--17-->
            <col class="w-4p" /><!--18-->
            <col class="w-4p" /><!--19-->
        </colgroup>
        <thead>
        <tr>
            <th><input type='checkbox' value='y' class='js-checkall' data-target-name='sno'/></th>
            <th>번호</th>
            <th>등록일</th>
            <th>시즌</th>
            <th>프로젝트 타입</th>
            <th>고객사</th>
            <th>고객희망<br>납기일</th>
            <th>매출규모</th>
            <th>계약 형태</th>
            <th>스타일</th>
            <th>구분</th>
            <th>담당자</th>
            <th>스케줄</th>
            <th>Q/B확정일</th>
            <th>가발주</th>
            <th>고객사발주일</th>
            <th>사양서발송일</th>
            <th>발주서완료일</th>
            <th>고객발주확정</th>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php
        if (gd_isset($data)) {
            foreach ($data as $val) {
                ?>
                <tr class="center field-parent " data-sno="<?=$val['sno']?>">
                    <?php include './admin/ims/list/step_fixed.php'?>
                    <!-- 구분 -->
                    <td class="center" rowspan="<?=$defaultRowspan?>">
                        <span class="text-muted"><span class="text-muted">미입력</span></span>
                    </td>
                    <!-- 담당자 -->
                    <td class="center" rowspan="<?=$defaultRowspan?>">
                        <div><?=$val['salesManagerNm']?></div>
                        <div><?=$val['designManagerNm']?></div>
                    </td>
                    <td class="bg-light-gray center">예정일</td>
                    <td class="center bg-light-yellow"><span class="text-muted">미입력</span></td><!--QB확정일-->
                    <td class="center bg-light-yellow"><span class="text-muted">미입력</span></td><!--가발주-->
                    <td class="center bg-light-yellow"><span class="text-muted">미입력</span></td><!--고객사발주일-->
                    <td class="center bg-light-yellow"><span class="text-muted">미입력</span></td><!--사양서발주일-->
                    <td class="center bg-light-yellow"><span class="text-muted">미입력</span></td><!--발주서완료일-->
                    <td class="center bg-light-yellow"><span class="text-muted">미입력</span></td><!--고객발주확정-->
                </tr>
                <tr class="">
                    <td class="bg-light-gray center">완료일</td>
                    <td class="center"><span class="text-muted">미입력</span></td><!--QB확정일-->
                    <td class="center"><span class="text-muted">미입력</span></td><!--가발주-->
                    <td class="center"><span class="text-muted">미입력</span></td><!--고객사발주일-->
                    <td class="center"><span class="text-muted">미입력</span></td><!--사양서발주일-->
                    <td class="center"><span class="text-muted">미입력</span></td><!--발주서완료일-->
                    <td class="center"><span class="text-muted">미입력</span></td><!--고객발주확정-->
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

        <div class="pull-left"></div>
        <div class="pull-right">
        </div>
    </div>

    <div class="center"><?= $page->getPage(); ?></div>

    <script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>

</form>

<?php include './admin/ims/list/_common_script.php'?>

<script>
    $(()=>{
        $('.simple-download').click(function(){
            let sno = $(this).data('sno');
            let type = $(this).data('type');
            location.href = "<?=$requestUrl?>&sno="+sno+"&type="+type;
        });

        const setAffix = function(){
            if ($(document).scrollTop() > 400) {
                $('#affix-show-type2').show();
                $('#affix-show-type1').hide();
            }else{
                $('#affix-show-type1').show();
                $('#affix-show-type2').hide();
            }
        }

        $(window).resize(function (e) {
            setAffix();
        });
        $(window).scroll(setAffix);

    });
</script>