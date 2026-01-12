<form id="frmSearchBase" method="get" class="content-form js-search-form js-form-enter-submit">
    <input type="hidden" id="list-sort" name="sort" value="<?= gd_isset($search['sort']) ?>"/>
    <input type="hidden" name="searchFl" value="y"/>
    <input type="hidden" name="pageNum" value="<?= Request::get()->get('pageNum', 30); ?>"/>
    <input type="hidden" name="status" value="<?=$requestParam['status'] ?>"/>
    <input type="hidden" name="view" value="<?=$requestParam['view']?>"/>
    <div class="table-title gd-help-manual">
        프로젝트 검색
    </div>
    <!--검색 시작-->

    <div class="search-detail-box form-inline">
        <table class="table table-cols">
            <colgroup>
                <col class="width-md">
                <col class="width-3xl">
                <col class="width-md">
                <col class="width-3xl">
            </colgroup>
            <tbody>
            <tr>
                <th>검색어</th>
                <td  id="keyword-search-area">
                    <?php if( empty ( $search['key'] ) ) { ?>
                        <div class="form-inline mgt5" >
                            <?=gd_select_box('', 'key[]', $search['combineSearch'], null, null, null, null, 'multi-search form-control'); ?>
                            <input type="text" name="keyword[]" value="" class="form-control"/>
                            <button type="button" class="btn btn-sm btn-red js-add-keyword">+추가</button>
                        </div>
                    <?php }else{ ?>
                        <?php foreach( $search['key']  as $searchIdx => $searchKey ) { ?>
                            <div class="form-inline mgt5" >
                                <?=gd_select_box('', 'key[]', $search['combineSearch'], null, $search['key'][$searchIdx], null); ?>
                                <input type="text" name="keyword[]" value="<?=$search['keyword'][$searchIdx]; ?>" class="form-control"/>

                                <?php if( $searchIdx == 0  ) { ?>
                                    <button type="button" class="btn btn-sm btn-red js-add-keyword">+ 추가</button>
                                <?php }else{ ?>
                                    <button type="button" class="btn btn-sm btn-white js-remove-keyword">- 제거</button>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } ?>

                </td>
                <th>
                    생산상태 검색
                </th>
                <td>

                    <label class="checkbox-inline">
                        <input type="checkbox" name="isProduction[]" value="all" class="js-not-checkall" data-target-name="isProduction[]" <?=gd_isset($checked['isProduction']['all']); ?>> 전체
                    </label>

                    <label style="margin-left:15px;">
                        <input class="checkbox-inline" type="checkbox" name="isProduction[]" value="0"  <?=gd_isset($checked['isProduction']['0']); ?>> 생산미진행
                    </label>
                    <label style="margin-left:15px;">
                        <input class="checkbox-inline" type="checkbox" name="isProduction[]" value="1"  <?=gd_isset($checked['isProduction']['1']); ?>> 생산진행건
                    </label>
                    <label style="margin-left:15px;">
                        <input class="checkbox-inline" type="checkbox" name="isProduction[]" value="2"  <?=gd_isset($checked['isProduction']['2']); ?>> 생산완료건
                    </label>
                    
                    <!--
                    <label class="radio-inline">
                        <input type="radio" name="showMemo" value="y" <?=gd_isset($checked['showMemo']['y']); ?> />비고보기
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="showMemo" value="n" <?=gd_isset($checked['showMemo']['n']); ?> />비고제외
                    </label>
                    -->
                </td>
            </tr>
            <tr>
                <th>연도/시즌</th>
                <td >
                    연도 : <input type="text" name="projectYear" value="<?= gd_isset($search['projectYear']); ?>" class="form-control" placeholder="연도" style="width:60px" />
                    시즌 :
                    <select class="form-control" name="projectSeason">
                        <option value="">선택</option>
                        <?php foreach($seasonList as $codeKey => $codeValue) { ?>
                            <option value="<?=$codeKey?>" <?= $codeKey == $search['projectSeason'] ? 'selected':'' ; ?>><?=$codeKey?></option>
                        <?php } ?>
                    </select>
                </td>
                <th>
                    고객 납기일
                </th>
                <td>
                    <div class="input-group js-datepicker">
                        <input type="text" class="form-control width-xs dt-period" name="treatDate[]" value="<?=$search['treatDate'][0]; ?>">
                        <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                    </div>
                    ~
                    <div class="input-group js-datepicker">
                        <input type="text" class="form-control width-xs dt-period" name="treatDate[]" value="<?=$search['treatDate'][1]; ?>">
                        <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                    </div>

                    <div class="btn-group sl-dateperiod" data-toggle="buttons" data-target-name="treatDate[]" data-target-inverse="1">
                        <label class="btn btn-white btn-sm hand "><input type="radio" name="searchPeriod" value="0">오늘</label>
                        <label class="btn btn-white btn-sm hand "><input type="radio" name="searchPeriod" value="14">15일</label>
                        <label class="btn btn-white btn-sm hand "><input type="radio" name="searchPeriod" value="30">1개월</label>
                        <label class="btn btn-white btn-sm hand "><input type="radio" name="searchPeriod" value="60">2개월</label>
                        <label class="btn btn-white btn-sm hand "><input type="radio" name="searchPeriod" value="90">3개월</label>
                        <label class="btn btn-white btn-sm hand "><input type="radio" name="searchPeriod" value="120">4개월</label>
                        <label class="btn btn-white btn-sm hand "><input type="radio" name="searchPeriod" value="150">5개월</label>
                        <!--<label class="btn btn-white btn-sm hand "><input type="radio" name="searchPeriod" value="180">6개월</label>-->
                    </div>
                    
                    <div class="btn btn-sm btn-gray" onclick="$('.dt-period').val('')" style="display: inline-block">초기화</div>
                </td>
            </tr>
            <tr>
                <th>진행상태검색</th>
                <td >
                    <select class="form-control" name="procStatusKey">
                        <option value="">전체</option>
                        <option value="fabricStatus" <?='fabricStatus' === $search['procStatusKey'] ? 'selected':'' ; ?>>퀄리티</option>
                        <option value="btStatus" <?='btStatus' === $search['procStatusKey'] ? 'selected':'' ; ?>>BT</option>
                        <option value="estimateStatus" <?='estimateStatus' === $search['procStatusKey'] ? 'selected':'' ; ?>>가견적</option>
                        <option value="costStatus" <?='costStatus' === $search['procStatusKey'] ? 'selected':'' ; ?>>생산가</option>
                        <option value="orderStatus" <?='orderStatus' === $search['procStatusKey'] ? 'selected':'' ; ?>>가발주</option>
                        <option value="workStatus" <?='workStatus' === $search['procStatusKey'] ? 'selected':'' ; ?>>작업지시서</option>
                    </select>
                    <select class="form-control" name="procStatusValue">
                        <option value="">상태 전체</option>
                        <option value="-1" <?='-1' == $search['procStatusValue'] ? 'selected':'' ; ?>>미진행</option>
                        <option value="1" <?='1' == $search['procStatusValue'] ? 'selected':'' ; ?>>진행중</option>
                        <option value="2" <?='2' == $search['procStatusValue'] ? 'selected':'' ; ?>>완료</option>
                    </select>
                </td>
                <th>
                    승인요청건
                    <br>/ 타입제외여부
                </th>
                <td>
                    <label >
                        <input class="checkbox-inline" type="checkbox" name="isAccOnly[]" value="y"  <?=gd_isset($checked['isAccOnly']['y']); ?>> 승인요청건만보기
                    </label>

                    <label class="pdl10">
                        <input class="checkbox-inline" type="checkbox" name="isExcludeRtw[]" value="y"  <?=gd_isset($checked['isExcludeRtw']['y']); ?>> 기성복/샘플/추가 프로젝트 제외
                    </label>
                </td>
            </tr>
            <?php if( empty($requestParam['status']) || 'old' === $requestParam['status']  ) { ?>
                <tr>
                    <th>상태검색</th>
                    <td colspan="3">
                        <div class="checkbox">
                            <div >
                                <label class="checkbox-inline mgr10">
                                    <input type="checkbox" name="orderProgressFl[]" value="all" class="js-not-checkall" data-target-name="orderProgressFl[]" <?=gd_isset($checked['orderProgressFl']['all']); ?>> 전체
                                </label>
                                <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_STATUS as $k => $v){ ?>
                                    <label class="mgr10">
                                        <input class="checkbox-inline chk-progress" type="checkbox" name="orderProgressFl[]" value="<?=$k?>"  <?=gd_isset($checked['orderProgressFl'][$k]); ?>> <?=$v?>
                                    </label>
                                <?php } ?>
                                <div class="btn btn-gray btn-sm set-check-process">진행중상태만보기</div>
                            </div>
                        </div>

                    </td>
                </tr>
            <?php } ?>
            <tr>
                <th>
                    프로젝트 타입 검색
                </th>
                <td colspan="3">
                    <div class="checkbox ">
                        <div >
                            <label class="checkbox-inline mgr10">
                                <input type="checkbox" name="projectType[]" value="all" class="js-not-checkall" data-target-name="projectType[]" <?=gd_isset($checked['projectType']['all']); ?>> 전체
                            </label>
                            <?php foreach( \Component\Ims\ImsCodeMap::PROJECT_TYPE as $k => $v){ ?>
                                <label class="mgr10">
                                    <input class="checkbox-inline chk-progress" type="checkbox" name="projectType[]" value="<?=$k?>"  <?=gd_isset($checked['projectType'][$k]); ?>> <?=$v?>
                                </label>
                            <?php } ?>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="table-btn">
        <input type="submit" value="검색" class="btn btn-lg btn-black btn-search">
    </div>

    <!--검색 끝-->
</form>
