                    <td rowspan="<?=$defaultRowspan?>" class="">
                        <input type="checkbox" name="sno[<?=$val['sno']; ?>]" value="<?=$val['sno']; ?>" />
                    </td>
                    <td class="font-num" rowspan="<?=$defaultRowspan?>">
                        <span class="number"><?= $page->idx--; ?></span>
                        <?php if(\SiteLabUtil\SlCommonUtil::isDevId()) { ?>
                            <div class="text-muted"><?=$val['sno']?></div>
                        <?php } ?>
                    </td>

                    <!--등록일-->
                    <td class="center" rowspan="<?=$defaultRowspan?>">
                        <div><?=gd_date_format('y/m/d',$val['regDtOrg']) ?></div>
                        <div class="text-muted"><?=gd_date_format('H:i:s',$val['regDtOrg']) ?></div>
                    </td>
                    
                    <!--시즌-->
                    <td class="center" rowspan="<?=$defaultRowspan?>">
                        <?=$val['projectYear']; ?>
                        <?=$val['projectSeason']; ?>
                    </td>

                    <!--프로젝트 타입-->
                    <td class="center" rowspan="<?=$defaultRowspan?>">
                        <?=$val['projectTypeKr']; ?>
                    </td>

                    <!--고객사-->
                    <td class="text-left pdl10" rowspan="<?=$defaultRowspan?>">
                        
                        <div class="pdl10">

                            <span class="<?php if(!$imsProduceCompany){ ?> tn-pop-customer-info<?php } ?>" data-sno="<?=$val['customerSno']?>">
                                <?=$val['customerName']; ?>
                                <!--
                                <?=$val['projectYear']; ?>
                                <?=$val['projectSeason']; ?>
                                <?=$val['use3plAndMall']; ?>
                                -->
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

                    <!--희망 납기일-->
                    <td class="center" rowspan="<?=$defaultRowspan?>">
                        <?php if( $val['projectStatus'] >= 98 ) { ?>
                            <?=$val['projectStatusKr']?>
                        <?php }else{ ?>
                            <?php if( '-' === $val['customerDeliveryDt'] ) { ?>
                                <div class="text-muted">고객납기 미입력</div>
                            <?php }else{ ?>
                                <div class=""><?=$val['customerDeliveryDtShort']?></div>
                                <div class=""><?=$val['customerDeliveryRemainDt']?></div>

                                <?php if( '완료' != $val['customerDeliveryDtShort'] ) { ?>
                                    <div class="mgt5 <?='y'==$val['customerDeliveryDtConfirmed']?'text-green':'text-danger'?> ">
                                        <?='y'==$val['customerDeliveryDtConfirmed']?'변경가능':'변경불가'?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </td>

                    <!--매출규모-->
                    <td class="center text-nowrap" rowspan="<?=$defaultRowspan?>">
                        <?=$val['customerSize']; ?>
                    </td>
                    
                    <!--계약형태-->
                    <td class="center text-nowrap" rowspan="<?=$defaultRowspan?>">
                        <?=gd_isset($val['bidType'],'<span class="text-muted">미정</span>')?>
                    </td>

                    <!--스타일-->
                    <td class="text-left pdl10" style="padding-left:10px !important;" rowspan="<?=$defaultRowspan?>">
                        <?php if( empty($val['styleWithCount']) ) { ?>
                            <span class="text-muted">스타일미등록</span>
                        <?php }else{ ?>

                        <?php } ?>
                        <span class="<?=$val['styleWithCount']?>">
                            <?=str_replace('\\','',nl2br($val['styleWithCount']))?>
                        </span>
                    </td>
