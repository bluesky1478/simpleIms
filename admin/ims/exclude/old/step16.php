<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

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
                <?php if(!empty($isDev)) { ?>
                    <!--
                    <input type="button" value="프로젝트 일괄 등록" class="btn btn-red-line"  onclick="$('.excel-upload-goods-info').show('fade')"  />
                    -->
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
        총
        <strong>
            <?= empty($page->recode['total'])? 0 : number_format($page->recode['total']); ?></strong> 건
            ( 단계변경 : 영업, 입력 : 영업 )
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
            <col style="width:1%" />
            <col style="width:2%" />
            <?php foreach($listSetupData as $each) { ?>
                <col class="w-<?=$each[1]?>p" />
            <?php } ?>
        </colgroup>
        <thead>
        <tr>
            <th><input type='checkbox' value='y' class='js-checkall' data-target-name='sno'/></th>
            <th>번호</th>
            <?php foreach($listSetupData as $each) { ?>
                <th><?=$each[0]?></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php
        if (gd_isset($data)) {
            foreach ($data as $key=> $val) {
                ?>
                <tr class="center field-parent" data-sno="<?=$val['sno']?>">

                    <?php include './admin/ims/list/step_fixed.php'?>

                    <!--구분-->
                    <td class="center" style="height:68px">
                        <?=$val['urgency']?>
                    </td>

                    <!-- 미팅일자 -->
                    <td class="center">
                        <?=$val['meetingInfoExpectedDtShort']?>
                        <br><?=$val['meetingInfoMemo']?>
                    </td>
                    
                    <!-- 참석자 -->
                    <td class="center">
                        <?=$val['meetingMemberMemo']?>
                    </td>
                    
                    <!-- 미팅보고서 -->
                    <td class="center">
                        <div class="get-file-area" data-file-type="fileEtc1" data-project-sno="<?=$val['sno']?>">
                            <ul class="ims-file-list" ></ul>
                        </div>
                    </td>
                    
                    <!-- 고객 안내일  -->
                    <td class="center">
                        <?=$val['custMeetingInformExpectedDtShort']?>
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

        //File 가져오기.
        $('.get-file-area').each(function(){
            ImsService.getLatestFileList({
                'fileDiv' : $(this).data('fileType'),
                'projectSno' : $(this).data('projectSno'),
            }).then((data)=>{
                if(200 === data.code){
                    if( null !== data.data.projectSno ){
                        const $el = $(this).find('.ims-file-list').eq(0);
                        let idx = 1;
                        data.data.files.forEach((file)=>{
                            const fileName = encodeURIComponent(file.fileName);
                            $el.append(
                                `<li><a href="<?=$nasDownloadUrl?>name=${fileName}&path=${file.filePath}" class="text-blue">다운로드${idx}</a></li>`
                            );
                            idx++;
                        });
                        $(this).append(`<div class="font-11">${data.data.title}</div>`);
                    }
                }
            });
        });

    });
</script>
