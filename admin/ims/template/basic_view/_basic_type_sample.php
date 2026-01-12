<?php use \SiteLabUtil\SlCommonUtil; ?>

<?php foreach($viewRowList as $viewRow){ ?>
    <div class="row">
    <?php foreach($viewRow as $divList){ ?>
        <?php foreach($divList as $divData){ ?>
            <div class="col-xs-6">
                <div class="table-title gd-help-manual">
                    <div class="flo-left area-title">
                        <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
                        <?=$divData['title']?>
                    </div>
                    <div class="flo-right">
                    </div>
                </div>
                <table class="table table-cols  xsmall-picker">
                    <colgroup v-if="!isModify">
                        <col class="width-sm">
                        <col class="width-md">
                        <col class="width-sm">
                        <col class="width-md">
                    </colgroup>
                    <colgroup v-if="isModify">
                        <col class="w90p">
                        <col class="width-md">
                        <col class="w90p">
                        <col class="width-md">
                    </colgroup>
                    <tbody>
                    <?php foreach($divData['field'] as $field ) { ?>
                        <tr >
                            <?php foreach($field as $fieldData) { ?>
                                <th >
                                    <?=$addInfo[$fieldData['key']]?>
                                </th>
                                <td colspan="<?=count($field)>1?'1':'3'?>">
                                    <?php if( 'special' === $fieldData['type'] ) {?>
                                        <!--혹시 특수 케이스 있으면 여기에 작업-->
                                    <?php }else{ ?>
                                        <?=SlCommonUtil::setTemplateValue($htmlTemplate[$fieldData['type']],$fieldData['params'])?>
                                    <?php } ?>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    <?php } ?>
    </div>
<?php } ?>
