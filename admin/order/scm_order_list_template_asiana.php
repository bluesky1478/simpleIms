<style>
    .page-header {
        z-index:1000 !important;
    }
</style>

<div id="imsApp">

    <table class="table table-cols" >
        <colgroup>
            <col style="width:50px"/>
            <col style="width:60px"/>
            <col /><!--주문정보-->
        </colgroup>

        <thead>
        <tr>
            <th>
                <input type="checkbox" id="chk_all" class="js-checkall" data-target-name="orderNo"/>
            </th>
            <th>번호</th>
            <th>주문정보</th>
        </tr>
        </thead>
        <tbody class="order-list">
        <?php
        if (gd_isset($data)) {
            foreach ($data as $val) {
                $rowSpan = ( empty($val['requestToAdmin']) && empty($val['orderFileList'])  )?1:2;
                ?>
                <tr class="center">
                    <td >
                        <input type="checkbox" name="orderNo[]" value="<?= $val['orderNo']; ?>" <?= $val['isRevoke'] ? '' :  'disabled="disabled"'  ?> />
                    </td>
                    <!--번호-->
                    <td class="font-num" >
                        <span class="number"><?= $page->idx--; ?></span>
                    </td>

                    <!--주문정보-->
                    <td class="text-nowrap"  style="padding:2px;text-align: left; " >

                        <div class="text-nowrap order-no">

                            <div>
                                주문자 : <?php $memberMasking = \App::load('Component\\Member\\MemberMasking'); ?>
                                <?=$val['orderName']?>
                                <?=$val['nickNm']?>
                                <span style="color:#777">
                            <?= $memberMasking->masking('order','id',$val['memId']); ?>
                            </span>
                            </div>


                            <strong class="<?=$val['orderAcctStatusColor'];?>">
                                <?php if( in_array(substr($val['orderStatus'],0,1), ['g','d','s']) ) { ?>
                                    출고처리
                                <?php }else{ ?>
                                    <?=$val['orderAcctStatusStr']; ?>
                                <?php } ?>
                            </strong>

                            <a href="#;" onclick="javascript:open_order_link('<?= $val["orderNo"]; ?>', 'newTab', '1')" title="주문번호" class="font-num" data-order-no="<?= $val["orderNo"]; ?>" data-is-provider="true"><?= $val["orderNo"]; ?></a>

                            <span class="font-11">
                            <?= str_replace(' ', '<br>', gd_date_format('y/m/d', $val['regDt'])); ?> 신청
                            <?php if(!empty($val['acctDt']) && '0000-00-00 00:00:00' != $val['acctDt'] ){ ?>
                                / <?= gd_date_format('y/m/d H:i', $val['acctDt']); ?> <?=$val['orderAcctStatusStr']; ?> 함
                            <?php } ?>
                        </span>
                        </div>

                        <?php if( !empty($val['requestToAdmin']) ) { ?>
                            <div class="font-kor text-muted" >전달 메세지 : <?= $val['requestToAdmin']; ?></div>
                        <?php } ?>

                        <!--아시아나 주문 정보 ( ScmOrderListService ) -->
                        <div class="new-style2 dp-flex mgt5" style="flex-wrap: wrap; align-items: flex-start;gap:7px ">
                            <?php foreach($val['asianaOrderMap'] as $classOrder ){ ?>
                            <!--<div class="w-49 sl-test1">-->
                                    <table class="table table-cols table-th-height0 table-td-height0 table-pd-3 mgb2 noto "  style="width:49%!important; table-layout: fixed" >
                                        <!--style="table-layout: fixed"-->
                                        <colgroup>
                                            <col class="w-65p">
                                            <col class="w-35p">
                                        </colgroup>
                                        <tr>
                                            <th >
                                                <div class="font-11 cursor-pointer" style="" @click='showHistory(true,"<?=addslashes($classOrder['provideInfo'])?>","<?=$classOrder['info']['companyId']?>","<?=$classOrder['info']['name']?>")'>
                                                    <?=$classOrder['info']['name']?> <?=$classOrder['info']['companyId']?>
                                                    <?=$classOrder['info']['empTeam']?>
                                                    <?=$classOrder['info']['empPart1']?> <?=$classOrder['info']['empPart2']?>
                                                </div>
                                            </th>
                                            <td rowspan="2" style="word-wrap: break-word; word-break: break-word; white-space: normal;">
                                                <div class="font-11 dp-flex" style="flex-wrap: wrap;">
                                                    <?php foreach($classOrder['orderCnt'] as $eachKey => $eachOrder ){ ?>
                                                        <div class="dp-flex">
                                                            <div style="width:105px" >
                                                                <?=$eachKey?>
                                                            </div>
                                                            <div style="width:15px"  >
                                                                <?=$eachOrder?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #f9f9f9">
                                                <!--이력 : -->
                                                <table style="border:none!important; word-wrap: break-word; word-break: break-word; white-space: normal;" class="font-11 cursor-pointer hover-btn" @click='showHistory(true,"<?=addslashes($classOrder['provideInfo'])?>","<?=$classOrder['info']['companyId']?>","<?=$classOrder['info']['name']?>")'>
                                                    <?php foreach($classOrder['provideYear'] as $provideYear => $provideList ) { ?>
                                                    <tr>
                                                            <td style="border:none!important; color:#777">
                                                                <?=$provideYear?>년 :
                                                                <?php foreach($provideList as $provideName => $provideCnt ) { ?>
                                                                    <?=$provideName?>*<?=$provideCnt?>
                                                                <?php } ?>
                                                            </td>
                                                    </tr>
                                                    <?php } ?>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                            <!--</div>-->
                            <?php } ?>
                        </div>

                        <?php if ( 3 == $val['orderAcctStatus'] && !empty($val['reason']) ) { ?>
                            <div style="max-width:330px;">
                                <small class="text-muted" style="white-space:normal">출고 불가 사유 : <?=$val['reason']?></small>
                            </div>
                        <?php } ?>
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

    <div v-if="modalVisible" class="ims-modal" > <!--@click.stop-->
        <div class="ims-modal-content relative" style="width: 400px; height:600px;" >
            <span class="ims-close-button" @click="showHistory(false,{},'','')">&times;</span>
            <span class="ims-close-button" @click="showHistory(false,{},'','')">&times;</span>
            <h2 class="ims-modal-title">
                {% modalUserInfo %}님 지급이력
            </h2>

            <div class="btn btn-white cursor-pointer hover-btn" style="position:absolute; top:27px; right:20px "
                 @click="downloadHistory(modalUserInfo +'님_지급이력')">다운로드</div>

            <div class="ims-modal-body " style="overflow-y: auto; max-height: 480px !important; /* 가로 스크롤 자동 생성 */">

                <table class="w-100p simple-table" style="border-top:solid 1px #d1d1d1" id="order-history">
                    <tr >
                        <th colspan="3" >
                            {% modalUserInfo %}님 지급이력
                        </th>
                    </tr>
                    <tr>
                        <th>번호</th>
                        <th>신청일</th>
                        <th class="text-left">품목</th>
                        <th>수량</th>
                    </tr>
                    <tr v-for="(modalEach, modalIndex) in modalData">
                        <td>{% modalIndex+1 %}</td>
                        <td>{% $.formatStringDateShort(modalEach.requestDt) %}</td>
                        <td class="text-left">{% modalEach.prdName %}</td>
                        <td>{% modalEach.orderCnt %}</td>
                    </tr>
                </table>
            </div>
            <div class="ims-modal-footer">
                <div class="btn btn-sm btn-white mgt5" @click="showHistory(false,{},'','')">닫기</div>
            </div>
        </div>
    </div>

</div>


<script type="text/javascript">

    let vueApp = null;

    $(()=>{
        vueApp = new Vue({
            el: '#imsApp',
            delimiters: ['{%', '%}'],
            data : {
                modalVisible: false,
                modalData: [],
                modalUserInfo: '',
                uploadProcFl: false,
                isOrder: false,
            },
            methods : {
                showHistory : (bool, history, companyId, name)=>{
                    vueApp.modalVisible = bool;

                    if(bool){
                        history = JSON.parse(history);
                        console.log(history);
                        vueApp.modalData = history;
                        vueApp.modalUserInfo = companyId + ' ' + name;
                    }else{
                        vueApp.modalData = [];
                    }
                },
            },
            mounted : function() {
                //$('#layerDim').show();
                this.$nextTick(function () {
                    $('#imsApp').show();
                    console.log('complete');
                });
            },
        });
    });
</script>
