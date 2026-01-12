<!--<style>
    html{ overflow: hidden }
</style>-->

<div class="row mgt20">
    <div class="row">
        <!-- 주문자 정보 -->
        <div class="col-xs-12">
            <div class="col-xs-12" >
                <div class="table-title ">
                    <div class="flo-left">
                        <span class="text-danger"></span>고객안내 일정 수정 이력
                    </div>
                    <div class="flo-right"></div>
                </div>
                <div>
                    <table class="table table-cols">
                        <colgroup>
                            <col class="width-md"/>
                            <col />
                            <col class="width-md"/>
                            <col />
                        </colgroup>
                        <tr>
                            <th class="text-center">수정일/수정자</th>
                            <th class="text-center">진행계획</th>
                            <th class="text-center">수정사유 구분</th>
                            <th class="text-center">수정사유 상세</th>
                        </tr>
                        <?php foreach( $planHistory as $each ) { ?>
                        <tr>
                            <td  class="text-center">
                                <div><?=$each['regDt']?></div>
                                <div><?=$each['managerNm']?></div>
                            </td>
                            <td  class="text-center">
                                <table class="table table-cols">
                                    <tr>
                                        <th>제목</th>
                                        <?php foreach( $each['afterStepData2'] as $planData ) { ?>
                                            <th class="text-center"><?=$planData['title']?></th>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <th>이전 계획</th>
                                        <?php foreach( $each['beforeStepData2'] as $stepKey => $beforeStep ) { ?>
                                            <?php if( $each['afterStepData2'][$stepKey]['planDt'] != $beforeStep['planDt'] ) { ?>
                                                <td class="text-center"><b class="text-danger"><?=$beforeStep['planDt']?></b></td>
                                            <?php }else{ ?>
                                                <td class="text-center"><?=$beforeStep['planDt']?></td>
                                            <?php } ?>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <th>수정 계획</th>
                                        <?php foreach( $each['afterStepData2']  as $stepKey => $afterStep ) { ?>
                                            <?php if( $each['beforeStepData2'][$stepKey]['planDt'] != $afterStep['planDt'] ) { ?>
                                                <td class="text-center"><b class="text-danger"><?=$afterStep['planDt']?></b></td>
                                            <?php }else{ ?>
                                                <td class="text-center"><?=$afterStep['planDt']?></td>
                                            <?php } ?>
                                        <?php } ?>
                                    </tr>
                                </table>
                            </td>
                            <td  class="text-center">
                                <?= \Component\Work\WorkCodeMap::PLAN_MOD_REASON_TYPE[$each['reasonType']]?>
                            </td>
                            <td  class="text-left">
                                <?=nl2br($each['reasonText'])?>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
            <div class="col-xs-12" style="text-align: center">
                <button type="button" class="btn btn-lg btn-white js-pop-close"  onclick="self.close();" style="width:300px">닫기</button>
            </div>
        </div>
    </div>
</div>

