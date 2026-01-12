<div class="col-xs-12 new-style" v-show="'warn' === tabMode">
    <div class="table-title gd-help-manual">
        <div class="flo-left area-title">
            <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
            생산시 유의 사항
        </div>
        <div class="flo-right">
        </div>
    </div>

    <div class="clear-both"></div>

    <div class="mgt20">
        <div class="table-title gd-help-manual">
            <div class="flo-left">1. QC샘플 요청 정보</div>
            <div class="flo-right"></div>
        </div>
        <div >
            <table class="table table-cols table-pd-5  xsmall-picker table-center " >
                <colgroup>
                    <col class="w-10p">
                    <col class="w-60p">
                    <col >
                </colgroup>
                <tr>
                    <th >고객 컨펌 유무</th>
                    <th >사이즈 수량</th>
                    <th >비고</th>
                </tr>
                <tr>
                    <td >
                        <div v-if="'y' === mainData.ework.data.produceWarning.customerConfirm">
                            유(있음)
                        </div>
                        <div v-if="'y' !== mainData.ework.data.produceWarning.customerConfirm">
                            무(없음)
                        </div>
                    </td>
                    <td >
                        <table class="table-td-height0 table-th-height0 table-center table table-cols ">
                            <tr>
                                <td v-for="(each, eachIndex) in mainData.product.sizeList">
                                    <span :class="mainData.ework.data.produceWarning.sampleSizeCnt[eachIndex]>0?'text-danger':''">
                                        {% each %}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td v-for="(each, eachIndex) in mainData.product.sizeList" style="border-bottom:none !important;">
                                    <span :class="mainData.ework.data.produceWarning.sampleSizeCnt[eachIndex]>0?'text-danger':''">
                                        {% mainData.ework.data.produceWarning.sampleSizeCnt[eachIndex] %}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td >
                        {% mainData.ework.data.produceWarning.customerConfirmMemo %}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!--2. 스타일 변경 내용-->
    <div class="mgt20">
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                2. 스타일 변경 내용
                <span class="text-danger">(QC샘플로 변경사항 확인)</span>
            </div>
            <div class="flo-right"></div>
        </div>
        <div >
            <table class="table table-cols  table-pd-2 table-th-height30 table-td-height30  xsmall-picker table-center table-fixed" >
                <colgroup>
                    <col class="w-5p">
                    <col class="w-10p">
                    <col class="w-13p">
                    <col >
                    <col >
                </colgroup>
                <tr>
                    <th >NO</th>
                    <th >변경일</th>
                    <th >구분</th>
                    <th style="text-align: left !important; padding-left:5px!important;">변경 전 / 요청사항</th>
                    <th style="text-align: left !important; padding-left:5px!important;">변경 후</th>
                </tr>
                <tr v-for="(each, index) in mainData.ework.data.produceWarning.contents1">
                    <td>
                        {% index+1 %}
                    </td>
                    <td>
                        {% mainData.ework.data.produceWarning.contents1[index].changeDt %}
                    </td>
                    <td>
                        <!--구분-->
                        {% mainData.ework.data.produceWarning.contents1[index].div %}
                    </td>
                    <td style="text-align: left !important; padding-left:5px!important;">
                        <!--변경 전 / 요청사항-->
                        {% mainData.ework.data.produceWarning.contents1[index].memo1 %}
                    </td>
                    <td style="text-align: left !important; padding-left:5px!important;">
                        <!--변경 후-->
                        {% mainData.ework.data.produceWarning.contents1[index].memo2 %}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!--3. 고객사 요청사항 / 확정되지 않은 사양-->
    <div class="mgt20">
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                3. 고객사 요청사항 / 확정되지 않은 사양
            </div>
            <div class="flo-right"></div>
        </div>
        <div >
            <table class="table table-cols table-pd-2 table-th-height30 table-td-height30 xsmall-picker table-center table-fixed" >
                <colgroup>
                    <col class="w-5p">
                    <col class="w-13p">
                    <col >
                    <col class="w-13p">
                </colgroup>
                <tr>
                    <th >NO</th>
                    <th >구분</th>
                    <th style="text-align: left !important; padding-left:5px!important;">내용</th>
                    <th >확정 예정일</th>
                </tr>
                <tr v-for="(each, index) in mainData.ework.data.produceWarning.contents2">
                    <td>
                        {% index+1 %}
                    </td>
                    <td>
                        <!--구분-->
                        {% mainData.ework.data.produceWarning.contents2[index].div %}
                    </td>
                    <td style="text-align: left !important; padding-left:5px!important;">
                        <!--변경 전 / 요청사항-->
                        {% mainData.ework.data.produceWarning.contents2[index].memo1 %}
                    </td>
                    <td >
                        <!--변경 후-->
                        {% mainData.ework.data.produceWarning.contents2[index].memo2 %}
                    </td>
                </tr>
            </table>

        </div>
    </div>

    <!--4. 원부자재 비축 요청-->
    <div class="mgt20">
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                4. 원부자재 비축 요청
            </div>
            <div class="flo-right"></div>
        </div>
        <div >
            <table class="table table-cols  table-pd-2 table-th-height30 table-td-height30  xsmall-picker table-center table-fixed" >
                <colgroup>
                    <col class="w-5p">
                    <col class="w-10p">
                    <col >
                    <col class="w-13p">
                    <col class="w-13p">
                    <col class="w-13p">
                    <col class="w-13p">
                </colgroup>
                <tr>
                    <th >NO</th>
                    <th >부착 위치</th>
                    <th >비축 자재명</th>
                    <th >혼용율</th>
                    <th >색상</th>
                    <th >비축 수량</th>
                    <th >사용 예정</th>
                </tr>
                <tr v-for="(each, index) in mainData.ework.data.produceWarning.storedFabric">
                    <td>
                        {% index+1 %}
                    </td>
                    <td>
                        {% mainData.ework.data.produceWarning.storedFabric[index].attached %}
                    </td>
                    <td>
                        {% mainData.ework.data.produceWarning.storedFabric[index].fabricName %}
                    </td>
                    <td>
                        {% mainData.ework.data.produceWarning.storedFabric[index].fabricMix %}
                    </td>
                    <td>
                        {% mainData.ework.data.produceWarning.storedFabric[index].fabricColor %}
                    </td>
                    <td>
                        {% mainData.ework.data.produceWarning.storedFabric[index].fabricCnt %}
                    </td>
                    <td >
                        {% mainData.ework.data.produceWarning.storedFabric[index].fabricMemo %}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!--5. 비고-->
    <div class="mgt20">
        <div class="table-title gd-help-manual">
            <div class="flo-left">
                5. 비고
            </div>
            <div class="flo-right"></div>
        </div>
        <div >
            <table class="table table-cols  table-pd-2 table-th-height30 table-td-height30  xsmall-picker table-center table-fixed" >
                <colgroup>
                    <col class="w-5p">
                    <col class="w-13p">
                    <col >
                </colgroup>
                <tr>
                    <th >NO</th>
                    <th >구분</th>
                    <th >내용</th>
                </tr>
                <tr v-for="(each, index) in mainData.ework.data.produceWarning.contents3">
                    <td>
                        {% index+1 %}
                    </td>
                    <td>
                        {% mainData.ework.data.produceWarning.contents3[index].div %}
                    </td>
                    <td style="text-align: left !important; padding-left:5px!important;">
                        {% mainData.ework.data.produceWarning.contents3[index].memo1 %}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
