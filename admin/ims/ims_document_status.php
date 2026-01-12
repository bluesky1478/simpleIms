<?php include './admin/ims/library_all.php'?>
<?php
$openType = 'newTab';
?>

    <div class="page-header js-affix">
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

    </div>

<form id="frmList" action="" method="get" target="ifrmProcess">

    <?php if( 'step10' !== $requestParam['status'] ) { ?>
        <ul class="nav nav-tabs mgb0" role="tablist" style="border-bottom:none!important;">
            <li role="presentation">
                <a href="../ims/ims_project_list.php?view=project&<?=$queryString ? 'searchFl=y&' . $queryString : ''?>">프로젝트별</a>
            </li>
            <li role="presentation" <?=$requestParam['view'] == 'style' ? 'class="active"' : ''?>>
                <a href="../ims/ims_project_list.php?view=style&<?=$queryString ? 'searchFl=y&' . $queryString : ''?>">스타일별</a>
            </li>
            <?php if( empty($requestParam['status']) ) { ?>
            <li role="presentation" <?=empty($requestParam['view']) ? 'class="active"' : ''?>>
                <a href="../ims/ims_document_status.php">자료현황</a>
            </li>
            <?php } ?>
        </ul>
    <?php } ?>


    <div class="table-header form-inline">
        <div class="pull-left">
            <span class="font-15"></span>
        </div>
        <div class="pull-right"></div>
    </div>

    <style>
        .table-default-center th{ font-size:15px !important; }
        .table-default-center td{ font-size:14px !important; }
    </style>

    <h3>프로젝트 자료 등록 현황(기성복,추가생산제외)</h3>

    <table class="font-16 table-cols table-default-center">
        <tr>
            <th>구분 </th>
            <th>고객사 </th>
            <th>프로젝트</th>
            <th>스타일수 </th>
            <th>견적서 </th>
            <th>영업확정서 </th>
            <th>작업지시서 </th>
            <th>확정생산가 </th>
            <th>인&nbsp;라&nbsp;인 </th>
        </tr>
        <?php foreach($list as $each) { ?>
        <tr>
            <td class=""><?=empty($each['season']) ? '<span class="text-muted">구분없음</span>':$each['season']?></td>
            <td><?=$each['customerCount']?></td>
            <td><?=$each['projectCount']?></td>
            <td><?=$each['styleCount']?></td>
            <td class="ta-r">
                <?=round($each['estimate'] / $each['projectCount'] * 100) ?>%
                <br><small class="text-muted">(<?=$each['estimate']?>/<?=$each['projectCount']?>)</small>
            </td>
            <td class="ta-r">
                <?=round($each['sales'] / $each['projectCount'] * 100) ?>%
                <br><small class="text-muted">(<?=$each['sales']?>/<?=$each['projectCount']?>)</small>
            </td>
            <td class="ta-r">
                <?=round($each['workCnt'] / $each['projectCount'] * 100) ?>%
                <br><small class="text-muted">(<?=$each['workCnt']?>/<?=$each['projectCount']?>)</small>
            </td>
            <td class="ta-r">
                <?=round($each['costCount'] / $each['projectCount'] * 100) ?>%
                <br><small class="text-muted">(<?=$each['costCount']?>/<?=$each['projectCount']?>)</small>
            </td>
            <td class="ta-r">
                <?=round($each['inlineCount'] / $each['styleCount'] * 100) ?>%
                <br><small class="text-muted">(<?=$each['inlineCount']?>/<?=$each['styleCount']?>)</small>
            </td>
        </tr>
        <?php } ?>
    </table>


</form>
