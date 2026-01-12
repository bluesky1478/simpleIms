<td <?=gd_isset($checked['showMemo']['y'])?'rowspan="2"':'' ?>>
    <input type="checkbox" name="sno[<?=$val['sno']; ?>]" value="<?=$val['sno']; ?>" />
</td>

<td class="font-num" <?=gd_isset($checked['showMemo']['y'])?'rowspan="2"':'' ?>>
    <span class="number"><?= $page->idx--; ?></span>
    <?php if($isDev) { ?>
        <div class="text-muted"><?=$val['sno']?></div>
    <?php } ?>
</td>

<td class="font-14" <?=gd_isset($checked['showMemo']['y'])?'rowspan="2"':'' ?>>
    <b><?= $val['projectYear']; ?> <?= $val['projectSeason']; ?></b>
    <div class="mgt10">
        <?=$val['seasonIcon']?>
    </div>
</td>

<td class="font-num field-customer" <?=gd_isset($checked['showMemo']['y'])?'rowspan="2"':'' ?>>

    <div class="ims-project-title"><?= $val['projectName']; ?></div>

    <span class="label-icon label-icon<?=$val['projectType']?>"><?=$val['projectTypeEn']?></span>

    <span class="ims-customer-name <?php if(!$imsProduceCompany){ ?> hover-btn btn-pop-customer-info<?php } ?>" data-sno="<?=$val['customerSno']?>">
        <?=$val['customerName']; ?>
        <?=$val['projectYear']; ?>
        <?=$val['projectSeason']; ?>
        <?=$val['use3plAndMall']; ?>
    </span>

    <div class="number text-danger project-no">
        <?php if($imsProduceCompany){ ?>
            <?= $val['projectNo']; ?>

            <br>
            <div class="btn btn-sm btn-white hover-btn cursor-pointer mgt5 " onclick="openProjectViewAndSetTabMode(<?= $val['sno']; ?>,'comment')">프로젝트 코멘트</div>
            <div class="btn btn-sm btn-white hover-btn cursor-pointer mgt5 " onclick="openProjectViewAndSetTabMode(<?= $val['sno']; ?>,'basic')">구버전파일</div>

        <?php }else{ ?>
            <a href="<?=$targetPage?>?sno=<?=$val['sno']?>&status=<?=$requestParam['status']?>" class="ims-project-no text-danger">
                <?= $val['projectNo']; ?>
            </a>

            <span class="flex-column mgt10 mgl5">
                <!--
                <div class="btn btn-white btn-sm" onclick="openProjectViewAndSetTabMode(<?=$val['sno']?>,'basic')">기본보기</div>
                -->
                <div class="btn btn-white btn-sm" onclick="openProjectViewAndSetTabMode(<?=$val['sno']?>,'style')">스타일</div>
                <div class="btn btn-white btn-sm" onclick="openProjectViewAndSetTabMode(<?=$val['sno']?>,'comment')">코멘트</div>
                <div class="btn btn-white btn-sm" onclick="openTodoRequestWrite(<?=$val['customerSno']?>,<?=$val['sno']?>)">요청</div>
                <?php if( in_array($managerId, \Component\Ims\ImsCodeMap::IMS_ADMIN) ) { ?>
                    <div class="btn btn-sm btn-red btn-red-line2 btn-delete" data-sno="<?=$val['sno']?>">삭제</div>
                <?php } ?>
            </span>

            <!--
            <span class="font-11 text-muted">
                (<a href="ims_project_list.php?view=<?=$requestParam['view']; ?>&key=b.customerName&keyword=<?=$val['customerName']; ?>&status=step<?=$val['projectStatus']?>" class="text-muted"><?= $val['projectStatusKr']; ?>상태</a>)
            </span>
            -->
        <?php } ?>
    </div>

    <?php if(!$imsProduceCompany){ ?>
        <div class="flex-column mgt10 display-none">
            <div class="btn btn-white btn-sm" onclick="openProjectViewAndSetTabMode(<?=$val['sno']?>,'basic')">기본보기</div>
            <div class="btn btn-white btn-sm" onclick="openProjectViewAndSetTabMode(<?=$val['sno']?>,'style')">스타일보기</div>
            <div class="btn btn-white btn-sm" onclick="openProjectViewAndSetTabMode(<?=$val['sno']?>,'comment')">코멘트보기</div>
            <?php if( in_array($managerId, \Component\Ims\ImsCodeMap::IMS_ADMIN) ) { ?>
            <div class="btn btn-sm btn-red btn-red-line2 btn-delete" data-sno="<?=$val['sno']?>">삭제</div>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="mgt10">
        <span class="font-12" style="color:#0c4da2"><?=$val['useInfo']?></span>
    </div>

    <div class="mgt10 font-13">
        <ul>
            <li><span class="text-muted1">프로젝트상태 : </span><b>
                    <a href="ims_project_list.php?view=<?=$requestParam['view']; ?>&key=b.customerName&keyword=<?=$val['customerName']; ?>&status=step<?=$val['projectStatus']?>" class=""><?= $val['projectStatusKr']; ?> 단계</a></b></li>
            <li class="mgt2"><span class="text-muted1">생산진행상태 : </span><b>
                <?=$val['productionStatusIcon']?>
                <?php if( 0 == $val['productionStatus'] ) {?> <span class="text-muted">생산미진행</span> <?php } ?>
                <?php if( 1 == $val['productionStatus'] ) {?> <a href="/ims/imsProductionList.php?initStatus=0&key=prj.projectNo&keyword=<?=$val['projectNo']?>" target="_blank">생산진행중</a> <?php } ?>
                <?php if( 2 == $val['productionStatus'] ) {?> <a href="/ims/imsProductionList.php?initStatus=0&key=prj.projectNo&keyword=<?=$val['projectNo']?>" target="_blank">생산완료</a> <?php } ?>
                </b>
            </li>
        </ul>
    </div>

    <div class="mgt10"></div>

</td>

<td class="ta-c font-num font-13 field-customer" <?=gd_isset($checked['showMemo']['y'])?'rowspan="2"':'' ?>>

    <?php if( $val['projectStatus'] >= 98 ) { ?>
        <?=$val['projectStatusKr']?>
    <?php }else{ ?>
        <?php if( '-' === $val['customerDeliveryDt'] ) { ?>
            <div class="text-muted">고객납기 미입력</div>
        <?php }else{ ?>
            <div class="font-14"><?=$val['customerDeliveryDtShort']?></div>
            <div class="font-13"><?=$val['customerDeliveryRemainDt']?></div>

            <?php if( '완료' != $val['customerDeliveryDtShort'] ) { ?>
                <div class="mgt5">
                    <?='n'==$val['customerDeliveryDtStatus2']?'<span class="text-muted">미확정</span>':'<span class="text-danger">확정</span>'?>
                </div>
                <div >
                    <?=$customerDeliveryStatus[$val['customerDeliveryDtStatus']]?>
                </div>
                <div class="mgt5 font-12">
                    <?='y'==$val['customerDeliveryDtConfirmed']?'변경가능':'변경불가'?>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } ?>

</td>

<?php if( 'style' !==  $requestParam['view'] ) { ?>
<td class="center text-nowrap">
    <?=$val['customerSize']; ?>
</td>
<td class="center text-nowrap">
    <?=$val['salesManagerNm']; ?>
    <br><?=$val['designManagerNm']; ?>
</td>
<?php } ?>