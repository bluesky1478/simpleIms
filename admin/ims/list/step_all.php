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
    <div class="page-header js-affix affix-top" style="width: auto; left: auto; padding-bottom:0 !important; ">
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
            <table class="table table-rows" style="margin-bottom:0 !important; "></table>
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
                <button type="button" class="btn btn-white btn-icon-excel simple-download" >엑셀 다운로드</button>
                <?= gd_select_box('sort', 'sort', $search['sortList'], null, $search['sort']); ?>
                <?= gd_select_box_by_page_view_count(Request::get()->get('pageNum', 30)); ?>
            </div>
        </div>
    </div>

    <table class="table table-rows" id="main-table">
        <colgroup>
            <col style="width:1%" />
            <col style="width:2%" />
            <col class="w-13p" /><!--고객사/프로젝트-->
            <col class="w-3p" /><!--담당자-->
            <col class="w-5p" /><!--타입-->
            <col class="w-4p" /><!--시즌연도-->
            <col class="w-13p" /><!--품목-->
            <col class="w-4p" /><!--수량-->
            <col class="w-4p" /><!--생산가-->
            <col class="w-4p" /><!--판매가-->
            <col class="w-4p" /><!--마진-->
            <col class="w-4p" /><!--고객발주-->
            <col class="w-4p" /><!--희망납기-->
            <col class="w-3p" /><!--폐쇄몰-->
            <col class="w-3p" /><!--3PL-->
            <col class="w-4p" /><!--등록일-->
        </colgroup>
        <thead>
        <tr>
            <th><input type='checkbox' value='y' class='js-checkall' data-target-name='sno'/></th>
            <th>번호</th>
            <th>고객사/프로젝트</th>
            <th>담당자</th>
            <th>타입</th>
            <th>시즌/연도</th>
            <th>품목</th>
            <th>수량</th>
            <th>생산가</th>
            <th>판매가</th>
            <th>마진</th>
            <th>고객발주</th>
            <th>고객납기</th>
            <th>3PL</th>
            <th>폐쇄몰</th>
            <th>등록일</th>
        </tr>
        </thead>

        <?php
        if (gd_isset($data)) {
            foreach ($data as $key=> $val) {
                ?>
                <tbody class="order-list row-master ">
                <tr class="center field-parent" data-sno="<?=$val['sno']?>">
                    <td rowspan="<?=$defaultRowspan?>" class="rspan">
                        <input type="checkbox" name="sno[<?=$val['sno']; ?>]" value="<?=$val['sno']; ?>" />
                    </td>
                    <td class="font-num rspan" rowspan="<?=$defaultRowspan?>">
                        <span class="number"><?= $page->idx--; ?></span>
                    </td>
                    <!--고객사-->
                    <td class="text-left pdl10 rspan"   rowspan="<?=$defaultRowspan?>">
                        <div class="pdl10">
                            <span class="<?php if(!$imsProduceCompany){ ?> _hover-btn   _btn-pop-customer-info<?php } ?>" data-sno="<?=$val['customerSno']?>">
                                <?=$val['customerName']; ?>
                                <span class="text-muted">
                                (
                                    <a href="ims_project_list.php?view=<?=$requestParam['view']; ?>&key[]=b.customerName&keyword[]=<?=$val['customerName']; ?>&status=step<?=$val['projectStatus']?>" class="text-muted"><?=$val['projectStatusKr']; ?></a>
                                    <?php if( 1 == $val['productionStatus'] ) {?> <a href="/ims/imsProductionList.php?initStatus=0&key=prj.projectNo&keyword=<?=$val['projectNo']?>" target="_blank" class="text-muted">/ 생산중</a> <?php } ?>
                                    <?php if( 2 == $val['productionStatus'] ) {?> <a href="/ims/imsProductionList.php?initStatus=0&key=prj.projectNo&keyword=<?=$val['projectNo']?>" target="_blank" class="text-muted">/ 생산완료</a> <?php } ?>
                                )
                                </span>
                            </span>

                            <span class="text-muted mgl5"></span>
                            <div class="number text-danger">
                                <?php if($imsProduceCompany){ ?>
                                    <?= $val['projectNo']; ?>
                                    <br>
                                    <div class="btn btn-sm btn-white hover-btn cursor-pointer mgt5 " onclick="openProjectViewAndSetTabMode(<?= $val['sno']; ?>,'comment')">프로젝트 코멘트</div>
                                    <div class="btn btn-sm btn-white hover-btn cursor-pointer mgt5 " onclick="openProjectViewAndSetTabMode(<?= $val['sno']; ?>,'basic')">구버전파일</div>
                                <?php }else{ ?>
                                    <a href="<?=$targetPage?>?sno=<?=$val['sno']?>&status=<?=$requestParam['status']?>&tabMode=style" class="text-danger">
                                        <?= $val['projectNo']; ?>
                                    </a>

                                    <span class="btn btn-white btn-sm" onclick="openSimpleProject({'projectSno':<?= $val['sno']; ?>})">일정수정</span>

                                    <?php if( in_array($managerId, \Component\Ims\ImsCodeMap::IMS_ADMIN) ) { ?>
                                        <div class="btn btn-sm btn-red btn-red-line2 btn-delete" data-sno="<?=$val['sno']?>">삭제</div>
                                    <?php } ?>

                                    <span class="flex-column mgt10 mgl5 display-none">
                                        <div class="btn btn-white btn-sm" onclick="openProjectViewAndSetTabMode(<?=$val['sno']?>,'style')">스타일</div>
                                        <div class="btn btn-white btn-sm" onclick="openProjectViewAndSetTabMode(<?=$val['sno']?>,'comment')">코멘트</div>
                                        <div class="btn btn-white btn-sm" onclick="openTodoRequestWrite(<?=$val['customerSno']?>,<?=$val['sno']?>)">요청</div>
                                        <?php if( in_array($managerId, \Component\Ims\ImsCodeMap::IMS_ADMIN) ) { ?>
                                            <!--
                                            <div class="btn btn-sm btn-red btn-red-line2 btn-delete" data-sno="<?=$val['sno']?>">삭제</div>
                                            -->
                                        <?php } ?>
                                    </span>
                                <?php } ?>
                            </div>
                            <span class="text-muted"></span>
                        </div>
                    </td>

                    <!-- 담당자 -->
                    <td class="center rspan" rowspan="<?=$defaultRowspan?>">
                        <div><?=$val['salesManagerNm']?></div>
                        <div><?=$val['designManagerNm']?></div>
                    </td>

                    <!--타입-->
                    <td class="center" rowspan="<?=$defaultRowspan?>">
                        <div><?=$val['projectTypeKr']?></div>
                    </td>

                    <!--시즌/년도-->
                    <td class="text-left" rowspan="<?=$defaultRowspan?>" style="padding-left:10px !important;">
                        <div>
                            <?=$val['projectYear']?>
                            <?=$val['projectSeason']?>
                        </div>
                    </td>

                    <!--스타일-->
                    <td class="text-left pdl10 relative" style="padding-left:10px !important;" rowspan="<?=$defaultRowspan?>">
                        <?php if( empty($val['styleWithCount']) ) { ?>
                            <span class="text-muted">스타일미등록</span>
                        <?php }else{ ?>
                            <span class="<?=$val['styleWithCount']?>">
                                <?=str_replace('\\','',nl2br($val['styleWithCount']))?>

                                <div class="btn btn-sm btn-white hover-btn cursor-pointer mgl10 btn-style-on" data-style-count="<?=$val['styleCount']?>" style="position: absolute; top:10px; right:2px">
                                     <i class="fa fa-chevron-down" aria-hidden="true" style="color:#9E9E9E"></i> 상세
                                </div>

                                <div class="btn btn-sm btn-white hover-btn cursor-pointer mgl10 btn-style-off" data-style-count="<?=$val['styleCount']?>" style="display: none; position: absolute; top:10px; right:2px">
                                     <i class="fa fa-chevron-up" aria-hidden="true" style="color:#9E9E9E"></i> 닫기
                                </div>

                                <!--<i class="fa fa-plus" aria-hidden="true"></i><span class=""> 상세</span>-->
                                <i class="display-none fa fa-caret-square-o-up" aria-hidden="true"></i>
                            </span>
                        <?php } ?>
                    </td>

                    <!--수량-->
                    <td class="center" rowspan="<?=$defaultRowspan?>">
                        <div>
                            <?=number_format($val['prdExQty'])?>
                        </div>
                    </td>

                    <!--생산가-->
                    <td class="center" rowspan="<?=$defaultRowspan?>">
                        <div>
                            <?=$val['prdCost']?>
                        </div>
                    </td>

                    <!--판매가-->
                    <td class="center" rowspan="<?=$defaultRowspan?>">
                        <div>
                            <?=$val['salePrice']?>
                        </div>
                    </td>

                    <!--마진-->
                    <td class="center" rowspan="<?=$defaultRowspan?>">
                        <?php if( !empty($val['prdCost']) && !empty($val['salePrice']) ) { ?>
                            <div>
                                <?=round($val['msMargin'])?>%
                            </div>
                        <?php }else{ ?>
                            <div>
                                0%
                            </div>
                        <?php } ?>
                    </td>
                    <!--고객발주-->
                    <td class="center" rowspan="<?=$defaultRowspan?>">
                        <span class="text-muted">미입력</span>
                    </td>
                    
                    <!--고객 희망 납기-->
                    <td class="center" rowspan="<?=$defaultRowspan?>">
                        <?=$val['customerDeliveryDtShort']?>
                    </td>
                    <td class="center">
                        <?php if('y' === $val['use3pl']) { ?>
                            <span class="hover-btn cursor-pointer">사용</span>
                        <?php }else{ ?>
                            <span class="text-muted">미사용</span>
                        <?php } ?>
                    </td>

                    <td class="center">
                        <?php if('y' === $val['useMall']) { ?>
                            <span class="hover-btn cursor-pointer">사용</span>
                        <?php }else{ ?>
                            <span class="text-muted">미사용</span>
                        <?php } ?>
                    </td>
                    <!--둥록일-->
                    <td class="" rowspan="<?=$defaultRowspan?>" >
                        <div><?=gd_date_format('y/m/d',$val['regDtOrg']) ?></div>
                        <div class="text-muted"><?=gd_date_format('H:i:s',$val['regDtOrg']) ?></div>
                    </td>

                </tr>

                <tr style="display: none; " class="style-title">
                    <td class="center bg-light-gray pd0 " style="height:25px!important;" >타입</td>
                    <td class="center bg-light-gray pd0 " style="height:25px!important;" >시즌/연도</td>
                    <td class="center bg-light-gray pd0 " style="height:25px!important;" >품목</td>
                    <td class="center bg-light-gray pd0 " style="height:25px!important;" >수량</td>
                    <td class="center bg-light-gray pd0 " style="height:25px!important;" >생산가</td>
                    <td class="center bg-light-gray pd0 " style="height:25px!important;" >판매가</td>
                    <td class="center bg-light-gray pd0 " style="height:25px!important;" >마진</td>
                </tr>
                <?php foreach( $val['productList'] as $prd ) { ?>
                <tr class="center style-body bg-light-yellow" style="display: none;">
                    <td class="center "><?=$val['projectTypeKr']?></td>
                    <td class="center "><?=$prd['prdYear']?> <?=$prd['prdSeason']?></td>
                    <td class="text-left"><?=$prd['productName']?></td>
                    <td class="center "><?=number_format($prd['prdExQty'])?></td>
                    <td class="center "><?=number_format($prd['prdCost'])?></td>
                    <td class="center "><?=number_format($prd['salePrice'])?></td>
                    <td class="center ">

                        <?php if( !empty($prd['prdCost']) && !empty($prd['salePrice']) ) { ?>
                            <div>
                                <?=round(100-($prd['prdCost']/$prd['salePrice']*100))?>%
                            </div>
                        <?php }else{ ?>
                            <div>
                                0%
                            </div>
                        <?php } ?>
                        
                    </td>
                </tr>
                <?php } ?>
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
            location.href = "<?=$requestUrl?>&sno="+sno+"&view=style&type="+type;
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