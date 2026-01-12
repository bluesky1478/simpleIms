

<div class="page-header js-affix">
    <h3><?= end($naviMenu->location); ?></h3>
</div>

<div class="table-title excel-upload-goods-info ">
</div>

<div id="imsApp">
    <div class="excel-upload-goods-info ">

            <table class="table table-cols" style="margin-bottom:0 !important;">
                <colgroup>
                    <col class="width-md">
                    <col class="width-3xl">
                    <col class="width-md">
                    <col class="width-3xl">
                </colgroup>
                <tbody>
                <tr>
                    <th>임직원 업데이트</th>
                    <td colspan="3">
                        <div class="form-inline" v-show="!uploadProcFl" style="display:none">
                            <form @submit.prevent="uploadFile">
                                <input :type="'file'" ref="fileOrder" style="width:1px!important;" />
                                <input type="button" class="btn btn-black" value="업로드" @click="uploadFile('fileOrder')"  />
                            </form>
                        </div>

                        <div class="spinner-loader vue-loader" v-show="uploadProcFl" style="display:block"> </div>

                        <div>
                            <span class="notice-info">
                                ※ 임직원 업데이트는 마스터ID만 가능합니다.
                            </span>
                        </div>
                        <div>
                            <span class="notice-info">
                                ※ 업로드한 데이터의 사원번호 기준으로 데이터가 수정 또는 추가 됩니다.
                            </span>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

    </div>

    <form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
        <input type="hidden" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
        <input type="hidden" name="searchFl" value="y"/>
        <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 100); ?>"/>

        <div class="search-detail-box form-inline">
            <input type="hidden" name="detailSearch" value="<?= gd_isset($search['detailSearch']); ?>"/>
            <table class="table table-cols" style="border-top:none !important;">
                <colgroup>
                    <col class="width-md">
                    <col class="width-3xl">
                    <col class="width-md">
                    <col class="width-3xl">
                </colgroup>
                <tbody>
                <tr>
                    <th>검색어</th>
                    <td >
                        <?= gd_select_box('key', 'key', $combineSearch, null, gd_isset($search['key']), null, null, 'form-control'); ?>
                        <input type="text" name="keyword" value="<?= gd_isset($search['keyword']); ?>" class="form-control"/>
                        <input type="hidden" name="scmFl" value="y" />
                        <input type="hidden" name="scmNo[]" value="<?= $scmNo ?>" />
                        <input type="hidden" name="scmNoNm[]" value="<?= $companyNm ?>"/>

                        <input type="submit" value="검색" class="btn btn-black js-search-button"/>

                    </td>
                    <th>퇴사여부</th>
                    <td >
                        <label class="radio-inline">
                            <input type="radio" name="retiredFl" value="all" <?= gd_isset($checked['retiredFl']['all']); ?>/>
                            전체
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="retiredFl" value="n" <?= gd_isset($checked['retiredFl']['n']); ?>/>
                            정상
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="retiredFl" value="y" <?= gd_isset($checked['retiredFl']['y']); ?>/>
                            퇴사
                        </label>

                        <input type="submit" value="검색" class="mgl10 btn btn-black js-search-button"/>

                    </td>
                </tr>
                </tbody>
            </table>
        </div>


    </form>
    <form id="frmList" action="" method="get" target="ifrmProcess">
        <div class="table-header form-inline" style="border-top:none !important;">
            <div class="pull-left">
                검색
                <strong><?= number_format($page->recode['total']); ?></strong>
                명
            </div>
            <div class="pull-right">
                <div>
                    <?= gd_select_box_by_page_view_count(Request::get()->get('pageNum', 100)); ?>
                </div>
            </div>
        </div>

        <table class="table table-rows table-fixed" style="table-layout:fixed;">
            <colgroup>
                <col style="width:60px">
                <col style="width:6%">
                <col style="width:6%">
                <col style="width:6%">
                <col style="width:9%">
                <col style="width:9%">
                <col style="width:9%">
                <col style="">
            </colgroup>
            <thead>
            <tr>
                <th>번호</th>
                <th>사번</th>
                <th>이름</th>
                <th>직급</th>
                <th>팀명</th>
                <th>파트명</th>
                <th>소부문명</th>
                <th>최근 지급내역</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (gd_isset($data)) {
                foreach ($data as $val) { ?>
                    <tr class="center">
                        <td class="font-num">
                            <span class="number "><?= number_format($page->idx--); ?></span>
                        </td>
                        <td class="font-num">
                            <?=$val['companyId']?>
                        </td>
                        <td class="font-num">
                            <?=$val['empName']?>
                            <?php if('y' === $val['retiredFl']){?>
                                <div class="text-danger">퇴직</div>
                            <?php }?>
                        </td>
                        <td class="font-num">
                            <?=$val['empRank']?>
                        </td>
                        <td class="font-num">
                            <?=$val['empTeam']?>
                        </td>
                        <td class="font-num">
                            <?=$val['empPart1']?>
                        </td>
                        <td class="font-num">
                            <?=$val['empPart2']?>
                        </td>
                        <td class="font-num">
                            <ul class="provide-contents cursor-pointer hover-btn" @click='showHistory(true,"<?=addslashes($val['provideInfo'])?>","<?=$val['companyId']?>","<?=$val['name']?>")'>
                                <?php foreach( $val['orderAgg'] as $key => $each ){ ?>
                                    <li style="display: flex; flex-wrap: wrap;">
                                        <?=$key?>: <?=implode(', ',$each)?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo '<tr><td class="center" colspan="8">검색된 정보가 없습니다.</td></tr>';
            }
            ?>
            </tbody>
        </table>

        <div class="center"><?= $page->getPage(); ?></div>
    </form>

    <div v-if="modalVisible" class="ims-modal" > <!--@click.stop-->
        <div class="ims-modal-content relative" style="width: 400px; height:600px;" >
            <span class="ims-close-button" @click="showHistory(false,{},'','')">&times;</span>
            <span class="ims-close-button" @click="showHistory(false,{},'','')">&times;</span>
            <h2 class="ims-modal-title">
                {% modalUserInfo %}님 지급이력
            </h2>
            <div class="ims-modal-body " style="overflow-y: auto; max-height: 480px !important; /* 가로 스크롤 자동 생성 */">

                <table class="w-100p simple-table" style="border-top:solid 1px #d1d1d1">
                    <tr>
                        <th>번호</th>
                        <th>신청일</th>
                        <th class="text-left">품목</th>
                        <th>수량</th>
                    </tr>
                    <tr v-for="(modalEach, modalIndex) in modalData">
                        <td>{% modalIndex+1 %}</td>
                        <td>{% $.formatStringDate(modalEach.requestDt) %}</td>
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
                uploadProcFl: true,
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
                },uploadFile : (fieldName)=>{
                    vueApp.uploadProcFl = true;
                    const fileInput = vueApp.$refs[fieldName];
                    if (fileInput.files.length > 0) {
                        const formData = new FormData();
                        formData.append('mode', 'asiana_emp_upload');
                        formData.append('excel', fileInput.files[0]);
                        const params = {
                            mode : 'uploadOrderFile',
                            file : formData,
                        };
                        $.ajax({
                            url: 'asiana_order_upload.php',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(result){
                                //console.log(result);
                                vueApp.uploadProcFl = false;
                                location.reload();
                            }
                        });
                    }
                },
            },
            mounted : function() {
                //$('#layerDim').show();
                this.$nextTick(function () {
                    $('#imsApp').show();

                    setTimeout(()=>{
                        vueApp.uploadProcFl = false;
                    },700);

                    console.log('complete');
                });
            },
        });
    });

</script>
