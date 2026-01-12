<div class="page-header js-affix">
    <h3><?= end($naviMenu->location); ?></h3>
</div>

<?php if( 8 == $scmNo) { ?>
<div class="table-title excel-upload-goods-info ">
    회원 유형 일괄 수정
</div>
<div class="excel-upload-goods-info ">
    <form id="frmModifyGoodsInfo" name="frmModifyGoodsInfo" action="./scm_member_batch_ps.php" method="post" enctype="multipart/form-data" target="ifrmProcess">
        <table class="table table-cols">
            <colgroup>
                <col class="width20p"/>
                <col class="width-xl"/>
            </colgroup>
            <tbody>
            <tr>
                <th>회원 정보 업로드</th>
                <td>
                    <div class="form-inline">
                        <input type="hidden" name="mode" value="scm_modify_batch_member"/>
                        <input type="file" name="excel" value="" class="form-control width50p" />
                        <input type="submit"  class="btn btn-white btn-sm excel-submit" value="엑셀업로드 하기">
                    </div>
                    <div>
                        <span class="notice-info">엑셀 파일은 반드시 &quot;Excel 97-2003 통합문서&quot;만 가능하며, csv 파일은 업로드가 되지 않습니다.</span>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
<?php } ?>

<form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
    <input type="hidden" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
    <input type="hidden" name="searchFl" value="y"/>
    <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 10); ?>"/>
    <div class="table-title gd-help-manual">
         <?=$companyNm?> 회원 검색
    </div>
    <?php include('member_detail_search.php'); ?>
</form>
<form id="frmList" action="" method="get" target="ifrmProcess">
    <div class="table-header form-inline">
        <div class="pull-left">
            <div class="dp-flex">
                <div class="dp-flex">
                    검색
                    <strong><?= number_format($page->recode['total']); ?></strong>
                    명
                </div>
                <div class="dp-flex">
                    / 약관동의
                    <?php if(32 == $scmNo) { ?>
                        <?=number_format($hdAcctY)?>/<?=number_format($hdTotal)?> (<?=$hdAcctPercent?>%)
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="pull-right">
            <div>

                <?php if (8 == $scmNo || 21 == $scmNo) {?>
                    <div class="btn btn-red btn-red-line2 update-limit">
                        구매 제한수량 수정
                    </div>
                <?php }?>

                <select name="sort" class="form-control">
                    <option value="entryDt desc" <?= gd_isset($selected['sort']['entryDt desc']); ?>>회원가입일&darr;</option>
                    <option value="entryDt asc" <?= gd_isset($selected['sort']['entryDt asc']); ?>>회원가입일&uarr;</option>
                    <option value="lastLoginDt desc" <?= gd_isset($selected['sort']['lastLoginDt desc']); ?>>
                        최종로그인&darr;
                    </option>
                    <option value="lastLoginDt asc" <?= gd_isset($selected['sort']['lastLoginDt asc']); ?>>
                        최종로그인&uarr;
                    </option>
                    <option value="sleepWakeDt desc" <?= gd_isset($selected['sort']['sleepWakeDt desc']); ?>>
                        휴면해제일&darr;
                    </option>
                    <option value="sleepWakeDt asc" <?= gd_isset($selected['sort']['sleepWakeDt asc']); ?>>
                        휴면해제일&uarr;
                    </option>
                    <option value="loginCnt desc" <?= gd_isset($selected['sort']['loginCnt desc']); ?>>
                        방문횟수&darr;
                    </option>
                    <option value="loginCnt asc" <?= gd_isset($selected['sort']['loginCnt asc']); ?>>방문횟수&uarr;</option>
                    <option value="memNm desc" <?= gd_isset($selected['sort']['memNm desc']); ?>>이름&darr;</option>
                    <option value="memNm asc" <?= gd_isset($selected['sort']['memNm asc']); ?>>이름&uarr;</option>
                    <option value="memId desc" <?= gd_isset($selected['sort']['memId desc']); ?>>아이디&darr;</option>
                    <option value="memId asc" <?= gd_isset($selected['sort']['memId asc']); ?>>아이디&uarr;</option>
                    <option value="mileage desc" <?= gd_isset($selected['sort']['mileage desc']); ?>>마일리지&darr;</option>
                    <option value="mileage asc" <?= gd_isset($selected['sort']['mileage asc']); ?>>마일리지&uarr;</option>
                    <option value="deposit desc" <?= gd_isset($selected['sort']['deposit desc']); ?>>예치금&darr;</option>
                    <option value="deposit asc" <?= gd_isset($selected['sort']['deposit asc']); ?>>예치금&uarr;</option>
                    <option value="saleAmt desc" <?= gd_isset($selected['sort']['saleAmt desc']); ?>>주문금액&darr;</option>
                    <option value="saleAmt asc" <?= gd_isset($selected['sort']['saleAmt asc']); ?>>주문금액&uarr;</option>
                </select>&nbsp;
                <?= gd_select_box_by_page_view_count(Request::get()->get('pageNum', 10)); ?>
                <!--<button type="button" class="btn btn-sm btn-default" id="btnGrid">GRID</button>-->
            </div>
        </div>
    </div>

    <table class="table table-rows" style="table-layout:fixed">
        <colgroup>
            <col style="width:50px" />
            <?php if ($gGlobal['isUse']) { ?>
            <col/>
            <?php } ?>
            <col  style="width:50px"/>
            <col/>
            <?php if( 'y' === $scmConfig['memberAcceptFl'] ) { ?>
            <col style="width:430px"/>
            <?php } ?>
            <col/>
            <col/>
            <col/>
            <col style="width:50px" />
            <col style="width:80px" />
            <?php if( 8 == $scmNo) { ?>
                <col/>
            <?php } ?>
            <?php if( 6 == $scmNo) { ?>
            <col/>
            <?php } ?>
        </colgroup>
        <thead>
        <tr>
            <th>
                <input type="checkbox" id="chk_all" class="js-checkall" data-target-name="chk"/>
            </th>
            <th>번호</th>
            <?php if ($gGlobal['isUse']) { ?>
                <th>상점 구분</th>
            <?php } ?>
            <th>아이디/닉네임</th>
            <?php if( 'y' === $scmConfig['memberAcceptFl'] ) { ?>
            <th>정보수정</th>
            <?php } ?>
            <th>이름</th>
            <th>회원가입일</th>
            <th>최종로그인</th>
            <th>가입승인</th>
            <?php if( 32 == $scmNo) { ?>
                <th>약관동의</th>
            <?php }else{ ?>
                <th>유/무료 구분</th>
            <?php } ?>
            <?php if( 8 == $scmNo) { ?>
            <th>회원유형/구매제한</th>
            <?php } ?>
            <?php if( 6 == $scmNo) { ?>
            <th>매장유형</th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php
        if (gd_isset($data)) {
            $memberMasking = \App::load('Component\\Member\\MemberMasking');
            foreach ($data as $val) {
                $lastLoginDt = (substr($val['lastLoginDt'], 2, 8) != date('y-m-d')) ? substr($val['lastLoginDt'], 2, 8) : '<span class="">' . substr($val['lastLoginDt'], 11) . '</span>';
                $txtAppFl = ($val['appFl'] == 'y' ? '승인' : '미승인');
                ?>
                <tr class="center" data-member-no="<?= $val['memNo']; ?>">
                    <td>
                        <input type="checkbox" name="chk[]" value="<?= $val['memNo']; ?>"
                               data-memid="<?= $val['memId'] ?>"
                               data-memnm="<?= $val['memNm'] ?>"
                               data-deposit="<?= $val['deposit'] ?>"
                               data-mileage="<?= $val['mileage'] ?>"
                               data-couponcount="<?= $val['couponCount'] ?>"
                               data-appfl="<?= ($val['appFl'] == 'y' ? 'y' : 'n') ?>"
                               data-maillingfl="<?= ($val['maillingFl'] == 'y' ? 'y' : 'n') ?>"
                               data-smsfl="<?= ($val['smsFl'] == 'y' ? 'y' : 'n') ?>"
                               data-cellphone="<?= $val['cellPhone'] ?>"
                               data-email="<?= $val['email'] ?>"/>
                    </td>
                    <td class="font-num">
                        <span class="number "><?= $page->idx--; ?></span>
                    </td>
                    <?php if ($gGlobal['isUse']) { ?>
                        <td class="">
                            <span class="flag flag-16 flag-<?= gd_isset($gGlobal['mallList'][$val['mallSno']]['domainFl'], 'kr'); ?>"></span><?= gd_isset($gGlobal['mallList'][$val['mallSno']]['mallName'], '기준몰'); ?>
                        </td>
                    <?php } ?>
                    <td style="text-align: left;padding-left:15px">
                        <div class="font-eng "  style="float:left"><?= $memberMasking->masking('member','id',$val['memId']); ?>
                            <?= gd_get_third_party_icon_web_path($val['snsTypeFl']); ?>
                            <div class="notice-ref notice-sm"><?= $val['nickNm']; ?></div>
                        </div>
                    </td>
                    <?php if( 'y' === $scmConfig['memberAcceptFl'] ) { ?>
                    <td>
                        <div style="float:left;margin-left:10px">
                            <input type="text" class="form-control change-nick" style="width:150px" value="<?= $val['nickNm']; ?>">
                        </div>
                        <div style="float:left;margin-left:10px">
                            <input type="button" value="닉네임 변경" class="btn btn-white btn-sm btn-nick-change" data-sno="<?= $val['memNo']; ?>" >
                        </div>
                        <div style="float:left;margin-left:10px">
                            <input type="button" value="비밀번호 초기화" class="btn btn-white btn-sm btn-pw-reset" data-sno="<?= $val['memNo']; ?>" >
                        </div>
                        <?php if( 6 == $scmNo) { ?>
                        <div style="clear: both;  text-align: left; margin-left:10px;padding-top:10px;padding-bottom:5px" >
                            <?php foreach( $hankookTypeMap as $hankookTypeKey => $hankookType) { ?>
                                <input type="checkbox" class="chk-hankook-type" name="hankookType<?=$val['memNo']?>[]" value="<?=$hankookTypeKey?>" <?=($hankookTypeKey & $val['hankookType']) ? 'checked':'' ?>> <?=$hankookType?>
                            <?php } ?>
                            <input type="button" value="매장유형 변경" class="btn btn-white btn-sm btn-hankook-change" data-sno="<?= $val['memNo']; ?>"  style="display: inline-block;margin-left:5px">
                        </div>
                        <?php } ?>
                    </td>
                    <?php } ?>
                    <td>
                        <span class="font-15">
                            <?= $memberMasking->masking('member','name',$val['memNm']); ?>
                        </span>

                        <?php if ( !empty($val['buyLimitCount']) ) { ?>
                        <div>
                            구매제한 수량 : <?= $val['buyLimitCount']; ?>
                            <input type="text" class="form-control buy-limit-count" value="<?=$val['buyLimitCount']?>" data-sno="<?= $val['memNo']; ?>">
                        </div>
                        <?php } ?>
                    </td>
                    <td>
                        <span class="font-date "><?= substr($val['entryDt'], 2, 8); ?></span>
                    </td>
                    <td>
                        <span class="font-date "><?= $lastLoginDt; ?></span>
                    </td>
                    <td class="">
                        <span class="">
                            <?php if( '승인' == $txtAppFl ) { ?>
                                <b style="color:#009e25">승인</b>
                            <?php }else{ ?>
                                <span style="color:red">미승인</span>
                            <?php }?>
                        </span>
                    </td>

                    <?php if( 32 == $scmNo) { ?>
                        <td class="">
                            <?='y' === $val['adultFl'] ? "<span class='text-green'>예</span>":"<span class='text-muted'>아니오</span>"?>
                        </td>
                    <?php }else{ ?>
                        <td class="">
                            <?=('y' == $val['freeFl'])? '무료회원' : '유료회원' ;?>
                        </td>
                    <?php } ?>

                    <?php if( 8 == $scmNo) { ?>
                    <td class="">
                        <div class="display-none">
                            <?= \SlComponent\Util\SlCodeMap::MEMBER_TYPE[ $val['memberType'] ] ; ?>
                            <?= empty( $val['memberType']) ? '' : ' - '.  $val['buyLimitCount']   .  '개 제한'  ?>
                        </div>
                        <div>
                            <select class="form-control sel-member-type" style="float:left" >
                                <option>미지정</option>
                                <option value="1" <?=$val['memberType']==1?'selected':''?> >정규직원</option>
                                <option value="2" <?=$val['memberType']==2?'selected':''?> >파트너사</option>
                            </select>
                            <div style="float:left;margin-left:10px">
                                <input type="text" class="form-control buy-limit-count" style="width:60px;text-align:right" value="<?= $val['buyLimitCount']; ?>">
                            </div>
                            <div style="float:left; margin-left:2px">
                                개 제한
                            </div>
                            <div style="float:left;margin-left:10px">
                                <input type="button" value="유형/구매제한 변경" class="btn btn-white btn-sm btn-tke-change" data-sno="<?= $val['memNo']; ?>" >
                            </div>
                        </div>
                    </td>
                    <?php } ?>
                    <?php if( 6 == $scmNo) { ?>
                    <td class="">
                        <?php foreach(  $hankookTypeMap as $hankookKey => $hankookValue  )  { ?>
                            <div><?= $hankookKey & $val['hankookType'] ? $hankookValue : '' ?></div>
                        <?php } ?>
                    </td>
                    <?php } ?>
                </tr>
                <?php
            }
        } elseif ($isSkip) {
            echo '<tr><td class="center" colspan="16">검색기능을 이용해주세요.</td></tr>';
        } else {
            echo '<tr><td class="center" colspan="16">검색된 정보가 없습니다.</td></tr>';
        }
        ?>
        </tbody>
    </table>

    <div class="table-action clearfix">
        <div class="pull-left">
            <?php if( 'y' === $scmConfig['memberAcceptFl'] ) { ?>
            <span class="action-title">선택한 회원을</span>
            <button type="button" class="btn btn-white" id="btnApply">가입승인 처리</button>
            <button type="button" class="btn btn-white" id="btnReject">미승인 처리</button>
            <span style="margin-left:10px;margin-right:10px">/</span>
            <button type="button" class="btn btn-white" id="btnNotFreeMember">유료회원 처리</button>
            <button type="button" class="btn btn-white" id="btnFreeMember">무료회원 처리</button>
            <?php } ?>
        </div>
        <div class="pull-right">
            <button type="button" class="btn btn-white btn-icon-excel" onclick="location.href='<?=$requestUrl?>'">엑셀다운로드</button>
        </div>

    </div>

    <div class="center"><?= $page->getPage(); ?></div>
</form>


<script type="text/javascript">
    var $formList = $('#frmList');
    var msg = {
        DENY_MAIL: "수신거부한 회원 입니다. 메일 발송을 하시려면 확인을 눌러주세요.",
        DENY_SMS: "수신거부한 회원 입니다. SMS 발송을 하시려면 확인을 눌러주세요.",
        CHECK: "회원을 선택해 주세요.",
        APPLY: "명의 회원이 가입 승인처리되었습니다",
        APPLY_MISS: "가입을 승인할 회원이 없습니다.",
        DELETE: "회원정보 삭제가 완료되었습니다."
    };

    $(document).ready(function () {

        $('.update-limit').click(()=>{
            $('.buy-limit-count').each(function(){
                const params = {
                    'mode' : 'updateMemberBuyLimit',
                    'sno'  : $(this).data('sno'),
                    'buyLimitCount' : $(this).val(),
                }
                
                console.log('업데이트 수량',params);
                
                $.postAsync('../../ajax/custom_api_ps.php', params).then((data)=>{
                    if( 200 == data.code ){
                    }
                });
            });
            alert('처리되었습니다.');
        });

        $('.btn-hankook-change').on('click',function(){
            var memNo = $(this).data('sno');
            var hankookType = 0;
            $(this).parent().find('.chk-hankook-type').each(function(){
                if( true == $(this).is(':checked') ){
                    hankookType += Number($(this).val());
                }
            });

            var param = {
                mode : 'hankookChange'
                , memNo : memNo
                , hankookType : hankookType
            } ;
            post_with_reload('scm_member_batch_ps.php', param);
        });

        $('.btn-nick-change').on('click', function(){
            var chNick = $(this).closest('td').find('.change-nick').val();
            var memNo = $(this).data('sno');
            var param = {
               mode : 'nickChange'
               , nick : chNick
               , memNo : memNo
            } ;
            post_with_reload('scm_member_batch_ps.php', param);
        });

        $('.btn-pw-reset').on('click', function(){
            var memNo = $(this).data('sno');
            dialog_confirm('선택한 사용자의 암호를 초기화 하시겠습니까?', function (result) {
                if (result) {
                    var param = {
                        mode : 'pw_reset'
                        , scmNo : <?=$scmNo?>
                        , memNo : memNo
                    } ;
                    post_with_reload('scm_member_batch_ps.php', param);
                } else {
                    layer_close();
                }
            });
        });

        $('.btn-tke-change').on('click', function(){
            var buyLimitCount = $(this).closest('td').find('.buy-limit-count').val();
            var memberType = $(this).closest('td').find('.sel-member-type').val();
            var memNo = $(this).data('sno');
            var param = {
               mode : 'tke_change'
               , memberType : memberType
               , buyLimitCount : buyLimitCount
               , memNo : memNo
            } ;
            post_with_reload('scm_member_batch_ps.php', param);
        });

        $('.btnModify', $formList).on('click', function (e) {
            location.href = './member_modify.php?memNo=' + member.get_member_attribute(e)
        });

        //회원 승인 처리
        $('#btnApply', $formList).on('click', apply_member);
        $('#btnReject', $formList).on('click', reject_member);

        //회원 유무료 구분
        $('#btnFreeMember', $formList).on('click', apply_free_member);
        $('#btnNotFreeMember', $formList).on('click', reject_free_member);

        $('#btnDelete', $formList).on('click', check_delete);

        $('.btnSendMail', $formList).on('click', function (e) {
            if (member.get_member_attribute(e, 'data-email').length < 1) {
                alert('이메일 정보가 없는 회원입니다.');
                return;
            }
            if (member.get_member_attribute(e, 'data-maillingFl') === 'y') {
                member_mail(member.get_member_attribute(e));
            } else {
                dialog_confirm(msg.DENY_MAIL, function (result) {
                    if (result) {
                        member_mail(member.get_member_attribute(e));
                    } else {
                        layer_close();
                    }
                });
            }
        });

        $('#btnJoin').on('click', function (e) {
            location.href = '../member/member_register.php';
        });

        // 정렬&출력수
        $('select[name=\'sort\']').change({targetForm: '#frmSearchBase'}, member.page_sort);
    });

    function check_delete() {
        var isValid = false;
        var hasDeposit = false; // 예치금 보유 여부
        var hasMileage = false; // 마일리지, 쿠폰 보유 여부
        var $checkList = $formList.find(':checkbox[name="chk[]"]:checked');
        var length = $checkList.length;
        var lastIdx = length - 1;

        if (length == 0) {
            alert("회원을 선택해 주세요.");
            return false;
        }
        $checkList.each(function (idx, item) {
            var $item = $(item);
            if ($item.data('deposit') > 0) {
                hasDeposit = true;
                return false;
            }
            if (hasMileage === false && ($item.data('mileage') > 0 || $item.data('couponcount') > 0)) {
                hasMileage = true;
            }
        });

        if (hasDeposit == true) {
            alert('예치금을 보유중인 회원이 포함되어있습니다. 예치금을 보유중인 회원은 탈퇴처리할 수 없습니다.');
            isValid = false;
            return false;
        }
        if (hasMileage == true) {
            dialog_confirm('사용가능한 쿠폰/마일리지를 보유중인 회원이 포함되어있습니다. 탈퇴처리 시 보유중인 회원혜택은 모두 삭제되고 즉시 탈퇴처리되며, 탈퇴완료 시 취소하실 수 없습니다.\n선택한 회원을 탈퇴처리하시겠습니까?', function (result) {
                if (result) {
                    delete_member();
                }
            });
            return false;
        }
        delete_member();
    }

    function delete_member() {
        if (!member.alert_check($formList, "회원을 선택해 주세요.")) {
            return false;
        }
        dialog_confirm('선택한 회원을 탈퇴처리하시겠습니까? 해당 회원은 즉시 탈퇴처리되며, 탈퇴완료 시 취소할 수 없습니다.', function (result) {
            if (result) {
                var data = $formList.serializeArray();
                data.push({name: "mode", value: "delete"});
                post_with_reload('scm_member_ps.php', data);
            } else {
                layer_close();
            }
        });
    }

    function apply_member(e) {
        e.preventDefault();
        if (member.alert_check($formList, "회원을 선택해 주세요.")) {
            dialog_confirm('선택한' + $(':checkbox:checked', $formList).not('.js-checkall').length + '명의 가입을 승인하시겠습니까?<br/>(자동발송 설정에 따라 회원에게 SMS/메일이 발송됩니다.)', function (result) {
                if (result) {
                    var data = $formList.serializeArray();
                    data.push({name: "mode", value: "approval_join"});
                    post_with_reload('scm_member_batch_ps.php', data);
                }
            });
        }
    }
    function reject_member(e) {
        e.preventDefault();
        if (member.alert_check($formList, "회원을 선택해 주세요.")) {
            dialog_confirm('선택한' + $(':checkbox:checked', $formList).not('.js-checkall').length + '명의 승인상태를 미승인으로 변경하시겠습니까?', function (result) {
                if (result) {
                    var data = $formList.serializeArray();
                    data.push({name: "mode", value: "disapproval_join"});
                    post_with_reload('scm_member_batch_ps.php', data);
                }
            });
        }
    }

    function apply_free_member(e) {
        e.preventDefault();
        if (member.alert_check($formList, "회원을 선택해 주세요.")) {
            dialog_confirm('선택한' + $(':checkbox:checked', $formList).not('.js-checkall').length + '명을 무료회원으로 전환 하시겠습니까?', function (result) {
                if (result) {
                    var data = $formList.serializeArray();
                    data.push({name: "mode", value: "member_free"});
                    post_with_reload('scm_member_batch_ps.php', data);
                }
            });
        }
    }
    function reject_free_member(e) {
        e.preventDefault();
        if (member.alert_check($formList, "회원을 선택해 주세요.")) {
            dialog_confirm('선택한' + $(':checkbox:checked', $formList).not('.js-checkall').length + '명을 유료회원으로 전환 하시겠습니까?', function (result) {
                if (result) {
                    var data = $formList.serializeArray();
                    data.push({name: "mode", value: "member_not_free"});
                    post_with_reload('scm_member_batch_ps.php', data);
                }
            });
        }
    }

    <?php if (\Framework\Utility\GodoUtils::isGodoIp() === true) { ?>
    function sleep() {
        if (member.alert_check($formList)) {
            if (confirm('선택한 회원을 전환하시겠습니까?')) {
                var data = $formList.serializeArray();
                data.push({name: "mode", value: "sleep_member"});
                post_with_reload('../member/member_sleep_ps.php', data);
            }
        }
    }

    function sleepMail() {
        if (member.alert_check($formList)) {
            if (confirm('선택한 회원에게 메일을 발송 하시겠습니까?')) {
                var data = $formList.serializeArray();
                data.push({name: "mode", value: "sleep_send_mail"});
                post_with_reload('../member/member_sleep_ps.php', data);
            }
        }
    }

    <?php    }    ?>
</script>
