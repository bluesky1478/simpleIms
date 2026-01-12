<div class="page-header js-affix">
    <h3><?= end($naviMenu->location); ?></h3>
    <div class="btn-group">
        <input type="button" value="일괄 등록" class="btn btn-red-line"  onclick="$('.excel-upload-goods-info').show('fade')"  />
        <input type="submit" value="회원 등록" class="btn btn-red-line" id="btnJoin"/>
        <input type="button" value="로그인 시도 초기화" class="btn btn-red" id="btnReset"/>
    </div>
</div>

<div class="table-title excel-upload-goods-info display-none">
    회원 정보 일괄등록
</div>
<div class="excel-upload-goods-info display-none">

        <table class="table table-cols">
            <colgroup>
                <col class="width20p"/>
                <col class="width-xl"/>
            </colgroup>
            <form id="frmModifyGoodsInfo" name="frmModifyGoodsInfo" action="./member_custom_ps.php" method="post" enctype="multipart/form-data" target="ifrmProcess">
                <tbody>
                <tr>
                    <th>고객사 선택</th>
                    <td>
                        <?=gd_select_box('targetScm', 'targetScm', $scmList, null, null, '==고객사 선택(필수)==', 'form-control js-status-change width-lg'); ?>
                    </td>
                </tr>
                <tr>
                    <th>회원 정보 업로드</th>
                    <td>
                        <div class="form-inline">
                            <input type="hidden" name="mode" value="add_member"/>
                            <input type="file" name="excel" value="" class="form-control width50p" />
                            <input type="submit"  class="btn btn-white btn-sm excel-submit" value="엑셀업로드 하기">
                        </div>
                        <div class="notice-info">엑셀 파일은 반드시 &quot;Excel 97-2003 통합문서&quot;만 가능하며, csv 파일은 업로드가 되지 않습니다.</div>
                        <div class="notice-info">
                            아이디,이름,닉네임,핸드폰,우편번호,주소,주소상세,이메일,그룹번호,직원타입,구매수량제한  ( MemberService . addMember )
                        </div>
                    </td>
                </tr>
                </tbody>
            </form>
            <form id="frmModifyGoodsInfo2" name="frmModifyGoodsInfo2" action="./member_custom_ps.php" method="post" enctype="multipart/form-data" target="ifrmProcess">
                <tbody>
                <tr>
                    <th>TKE 회원 정보 업로드</th>
                    <td>
                        <div class="form-inline">
                            <input type="hidden" name="mode" value="add_member_tke"/>
                            <input type="file" name="excel" value="" class="form-control width50p" />
                            <input type="submit"  class="btn btn-white btn-sm excel-submit" value="엑셀업로드 하기">
                        </div>
                    </td>
                </tr>
                </tbody>
            </form>
        </table>
</div>

<form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
    <input type="hidden" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
    <input type="hidden" name="searchFl" value="y"/>
    <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 10); ?>"/>
    <div class="table-title gd-help-manual">
        회원 검색
    </div>
    <?php include('member_detail_search.php'); ?>
</form>
<form id="frmList" action="" method="get" target="ifrmProcess">
    <div class="table-headmember_list.phper form-inline">
        <div class="pull-left">
            검색
            <strong><?= $page->recode['total']; ?></strong>
            명
        </div>
        <div class="pull-right">
            <div>
                
                <div class="btn btn-white btn-white update-limit">
                    구매 제한수량 수정
                </div>
                
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

    <table class="table table-rows">
        <colgroup>
            <col class="width-xs"/>
            <?php if ($gGlobal['isUse']) { ?>
                <col/>
            <?php } ?>
            <col/>
            <col class="width-xs"/>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
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
            <th>신청 고객사</th>
            <th>연결 고객사</th>
            <th>아이디/닉네임</th>
            <th>이름</th>
            <th>등급</th>
            <th>마일리지</th>
            <th>예치금</th>
            <th>상품주문건수</th>
            <th>주문금액</th>
            <th>회원가입일</th>
            <th>최종로그인</th>
            <th>가입승인</th>
            <th>유/무료</th>
            <th>회원유형</th>
            <th>매장유형</th>
            <th>정보수정</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (gd_isset($data)) {
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
                        <span class="number js-layer-crm hand"><?= $page->idx--; ?></span>
                    </td>
                    <?php if ($gGlobal['isUse']) { ?>
                        <td class="">
                            <span class="flag flag-16 flag-<?= gd_isset($gGlobal['mallList'][$val['mallSno']]['domainFl'], 'kr'); ?>"></span><?= gd_isset($gGlobal['mallList'][$val['mallSno']]['mallName'], '기준몰'); ?>
                        </td>
                    <?php } ?>
                    <td>
                        <?= $val['ex2']; ?>
                        <div class="text-muted font-11"><?= $val['memNo']; ?></div>
                    </td>
                    <td>
                        <?= $val['ex1']; ?>
                    </td>
                    <td>
                        <span class="font-eng js-layer-crm hand"><?= $val['memId']; ?>
                            <?= gd_get_third_party_icon_web_path($val['snsTypeFl']); ?>
                            <?php if ($val['nickNm']) { ?>
                                <div class="notice-ref notice-sm"><?= $val['nickNm']; ?></div><?php } ?>
                        </span>
                    </td>
                    <td>
                        <span class="js-layer-crm hand"><?= $val['memNm']; ?></span>
                    </td>
                    <td>
                        <span class="js-layer-crm hand"><?= gd_isset($groups[$val['groupSno']]); ?></span>
                    </td>
                    <td>
                        <span class="font-num js-layer-crm hand"><?= gd_money_format($val['mileage']) . gd_display_mileage_unit() ?></span>
                    </td>
                    <td>
                        <span class="font-num js-layer-crm hand"><?= gd_money_format($val['deposit']) . gd_display_deposit('unit') ?></span>
                    </td>
                    <td>
                        <span class="font-num js-layer-crm hand"><?= $val['saleCnt']; ?>건</span>
                    </td>
                    <td>
                        <span class="font-num js-layer-crm hand"><?= gd_currency_display($val['saleAmt']); ?></span>
                    </td>
                    <td>
                        <span class="font-date js-layer-crm hand"><?= substr($val['entryDt'], 2, 8); ?></span>
                    </td>
                    <td>
                        <span class="font-date js-layer-crm hand"><?= $lastLoginDt; ?></span>
                    </td>
                    <td>
                        <!-- 휴면해제일 제외<span class="font-date js-layer-crm hand"><?= substr($val['sleepWakeDt'], 2, 8); ?></span>-->
                        <span class="js-layer-crm hand"><?= $txtAppFl; ?></span>
                    </td>
                    <td>
                        <?=('y' == $val['freeFl'])? '<b>무료회원</b>' : '<small style="color:#919191">유료회원</small>' ;?>
                    </td>
                    <td class="">
                        <div>
                            <?= \SlComponent\Util\SlCodeMap::MEMBER_TYPE[ $val['memberType'] ] ; ?>
                            <?= empty( $val['memberType']) ? '' : ' - '.  $val['buyLimitCount']   .  '개 제한'  ?>
                        </div>

                        <?php if( $val['memberType'] ){ ?>
                            <input type="text" class="form-control buy-limit-count" value="<?=$val['buyLimitCount']?>" data-sno="<?= $val['memNo']; ?>">
                        <?php } ?>
                    </td>
                    <td>
                        <?php foreach(  \SlComponent\Util\SlCodeMap::HANKOOK_TYPE as $hankookKey => $hankookValue  )  { ?>
                            <div><?= $hankookKey & $val['hankookType'] ? $hankookValue : '' ?></div>
                        <?php } ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-white btn-sm btnModify">수정
                        </button>
                    </td>
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
            <button type="button" class="btn btn-white" id="btnApply">선택 가입승인</button>
            <button type="button" class="btn btn-white" id="btnDelete">선택 탈퇴처리</button>

            <div class="inline" style="padding-left:20px">
                <div  class="inline"><strong>고객사일괄연결 : </strong></div>
                <div  class="inline">
                    <?= gd_select_box('batchScmName', 'batchScmName', $scmList, null, null, '= 고객사선택 = ', 'color:#000','inline') ?>
                </div>
                <div  class="inline"><button type="button" class="btn btn-white" id="btnBatchScm">고객사일괄연결</button></div>
            </div>

            <div class="inline" style="padding-left:20px">
                <div  class="inline"><strong>선택된 회원을 : </strong></div>
                <div  class="inline">
                    <button type="button" class="btn btn-white" id="btnNotFreeMember">유료회원 처리</button>
                    <button type="button" class="btn btn-white" id="btnFreeMember">무료회원 처리</button>
                </div>
            </div>

        </div>
        <div class="pull-right">

            <?php if ($isGodoIp) { ?>
                <button type="button" class="btn btn-white" id="btnSleepMail" onclick="sleepMail()">휴면회원 메일발송</button>
                <button type="button" class="btn btn-white" id="btnSleep" onclick="sleep()">휴면회원 전환</button>
            <?php } ?>
            <button type="button" class="btn btn-white btn-icon-excel js-excel-download" data-target-form="frmSearchBase" data-search-count="<?= $page->recode['total'] ?>" data-total-count="<?= $page->recode['amount'] ?>"
                    data-target-list-form="frmList" data-target-list-sno="chk">엑셀다운로드
            </button>

            <button type="button" class="btn btn-white btn-icon-excel" onclick="location.href='<?=$requestUrl?>'">엑셀다운로드(Simple)</button>

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
        $('.btnModify', $formList).on('click', function (e) {
            location.href = './member_modify.php?memNo=' + member.get_member_attribute(e)
        });

        $('#btnApply', $formList).on('click', apply_member);
        $('#btnDelete', $formList).on('click', check_delete);
        $('#btnBatchScm', $formList).on('click', batch_scm);

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

        $('#btnReset').on('click', function (e) {
            let params = {
                'mode' : 'resetLoginTry',
            }
            $.postAsync('../ajax/custom_api_ps.php', params).then((data)=>{
                if( 200 == data.code ){
                    alert(data.message);
                }
            });
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
                post_with_reload('../member/member_ps.php', data);
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
                    post_with_reload('../member/member_batch_ps.php', data);
                }
            });

        }
    }

    function batch_scm(e) {
        e.preventDefault();
        if (member.alert_check($formList, "회원을 선택해 주세요.")) {
            dialog_confirm('선택한' + $(':checkbox:checked', $formList).not('.js-checkall').length + '명을 선택한 고객사로 연결합니다.)', function (result) {
                if (result) {
                    var data = $formList.serializeArray();
                    data.push({name: "mode", value: "batch_scm"});
                    post_with_reload('../member/member_scm_batch_ps.php', data);
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


<script type="text/javascript">
    $(function(){

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

        //회원 유무료 구분
        $('#btnFreeMember', $formList).on('click', apply_free_member);
        $('#btnNotFreeMember', $formList).on('click', reject_free_member);

        //제한수량 일괄수정
        $('.update-limit').click(()=>{
            $('.buy-limit-count').each(function(){
                const params = {
                    'mode' : 'updateMemberBuyLimit',
                    'sno'  : $(this).data('sno'),
                    'buyLimitCount' : $(this).val(),
                }
                $.postAsync('../ajax/custom_api_ps.php', params).then((data)=>{
                    if( 200 == data.code ){
                    }
                });
            });
            alert('처리되었습니다.');
        });

    });
</script>
