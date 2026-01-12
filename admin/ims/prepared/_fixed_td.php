<td >
    <input type="checkbox" name="sno[<?=$val['sno']; ?>]" value="<?=$val['sno']; ?>" />
</td>

<td class="font-num">
    <span class="number"><?= $page->idx--; ?></span>
</td>

<td class="font-14" >
    <b><?= $val['projectYear']; ?> <?= $val['projectSeason']; ?></b>
    <div class="mgt10">
        <?=$val['seasonIcon']?>
    </div>
</td>

<td class="font-num field-customer">

    <span class="label-icon label-icon<?=$val['projectType']?>"><?=$val['projectTypeEn']?></span>

    <span class="ims-customer-name">
        <?=$val['customerName']; ?>
        <?= $val['projectYear']; ?>
        <?= $val['projectSeason']; ?>
        <?=$val['use3plKr']; ?>
    </span>

    <?php if( 'work' === $requestParam['preparedType'] ) { ?>
        <div class="number text-danger project-no">
            <a href="<?=$targetPage?>?sno=<?=$val['sno']?>&preparedType=<?=$requestParam['preparedType']?>" class="ims-project-no text-danger">
                <?= $val['projectNo']; ?>
            </a>
            <?php if($isDev) { ?>
                <div class="btn btn-sm btn-white btn-delete" data-sno="<?=$val['sno']?>">삭제</div>
            <?php } ?>
        </div>
    <?php }else{ ?>
        <div class="number text-danger project-no">
            <a href="#" class="ims-project-no text-danger" onclick="openProduceRequest(<?= $val['sno']; ?>, '<?= $val['preparedType']; ?>', <?= $val['preparedSno']; ?>)">
                <?= $val['projectNo']; ?>
            </a>
            <span class="font-11 text-muted">(<?= $val['projectTypeKr']; ?>)</span>
            <?php if($isDev) { ?>
                <div class="btn btn-sm btn-white btn-delete display-none" data-sno="<?=$val['sno']?>">삭제</div>
            <?php } ?>
        </div>
    <?php } ?>
</td>