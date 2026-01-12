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
                        <label class="radio-inline" >
                            <input type="radio" name="customerConfirm" value="y"
                                   @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)"
                                   v-model="mainData.ework.data.produceWarning.customerConfirm" style="margin:0!important;" />
                            <span class="font-11">유</span>
                        </label>
                        <label class="radio-inline" >
                            <input type="radio" name="customerConfirm" value="n"
                                   @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)"
                                   v-model="mainData.ework.data.produceWarning.customerConfirm" style="margin:0!important;" />
                            <span class="font-11">무</span>
                        </label>
                    </td>
                    <td >
                        <table class="table-td-height0 table-th-height0 table-center table table-cols ">
                            <tr>
                                <td v-for="each in mainData.product.sizeList">
                                    {% each %}
                                </td>
                            </tr>
                            <tr>
                                <td v-for="(each, eachIndex) in mainData.product.sizeList">
                                    <input type="text" class="form-control" v-model="mainData.ework.data.produceWarning.sampleSizeCnt[eachIndex]"
                                           @keyup="eworkUpdate('produceWarning',mainData.ework.data.produceWarning); vueApp.$forceUpdate();"
                                           @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)">
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td >
                        <input type="text" class="form-control" v-model="mainData.ework.data.produceWarning.customerConfirmMemo"
                               @keyup="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)" @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)">
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
                    <col class="w-8p">
                </colgroup>
                <tr>
                    <th >NO</th>
                    <th >변경일</th>
                    <th >구분</th>
                    <th >변경 전 / 요청사항</th>
                    <th >변경 후</th>
                    <th >추가/삭제</th>
                </tr>
                <tr v-for="(each, index) in mainData.ework.data.produceWarning.contents1">
                    <td>
                        {% index+1 %}
                    </td>
                    <td>
                        <date-picker v-model="mainData.ework.data.produceWarning.contents1[index].changeDt"
                                     value-type="format"
                                     format="YYYY-MM-DD"
                                     :editable="false"  placeholder="변경일" style="width:140px;font-weight: normal; "
                                     @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)">
                        </date-picker>
                    </td>
                    <td>
                        <!--구분-->
                        <input type="text" class="form-control" v-model="mainData.ework.data.produceWarning.contents1[index].div"
                               @keyup="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)"
                               @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)">
                    </td>
                    <td>
                        <!--변경 전 / 요청사항-->
                        <input type="text" class="form-control" v-model="mainData.ework.data.produceWarning.contents1[index].memo1"
                               @keyup="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)"
                               @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)">
                    </td>
                    <td>
                        <!--변경 후-->
                        <input type="text" class="form-control" v-model="mainData.ework.data.produceWarning.contents1[index].memo2"
                               @keyup="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)"
                               @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)">
                    </td>
                    <td>
                        <div class="btn btn-white btn-sm" @click="addElement(mainData.ework.data.produceWarning.contents1, mainData.ework.data.produceWarning.contents1[0], 'after')">추가</div>
                        <div class="btn btn-white btn-sm" v-if="mainData.ework.data.produceWarning.contents1.length > 1" @click="removeElementProduceWarn(mainData.ework.data.produceWarning.contents1, index)">삭제</div>
                        <div class="btn btn-white btn-sm" v-if="1 >= mainData.ework.data.produceWarning.contents1.length" disabled="disabled">삭제</div>
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
                    <col class="w-8p">
                </colgroup>
                <tr>
                    <th >NO</th>
                    <th >구분</th>
                    <th >내용</th>
                    <th >확정 예정일</th>
                    <th >추가/삭제</th>
                </tr>
                <tr v-for="(each, index) in mainData.ework.data.produceWarning.contents2">
                    <td>
                        {% index+1 %}
                    </td>
                    <td>
                        <!--구분-->
                        <input type="text" class="form-control" v-model="mainData.ework.data.produceWarning.contents2[index].div"
                               @keyup="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)"
                               @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)">
                    </td>
                    <td>
                        <!--변경 전 / 요청사항-->
                        <input type="text" class="form-control" v-model="mainData.ework.data.produceWarning.contents2[index].memo1"
                               @keyup="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)"
                               @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)">
                    </td>
                    <td>
                        <!--변경 후-->
                        <input type="text" class="form-control" v-model="mainData.ework.data.produceWarning.contents2[index].memo2"
                               @keyup="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)"
                               @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)">
                    </td>
                    <td>
                        <div class="btn btn-white btn-sm" @click="addElement(mainData.ework.data.produceWarning.contents2, mainData.ework.data.produceWarning.contents2[0], 'after')">추가</div>
                        <div class="btn btn-white btn-sm" v-if="mainData.ework.data.produceWarning.contents2.length > 1" @click="removeElementProduceWarn(mainData.ework.data.produceWarning.contents2, index)">삭제</div>
                        <div class="btn btn-white btn-sm" v-if="1 >= mainData.ework.data.produceWarning.contents2.length" disabled="disabled">삭제</div>
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
                    <col class="w-8p">
                </colgroup>
                <tr>
                    <th >NO</th>
                    <th >부착 위치</th>
                    <th >비축 자재명</th>
                    <th >혼용율</th>
                    <th >색상</th>
                    <th >비축 수량</th>
                    <th >사용 예정</th>
                    <th >추가/삭제</th>
                </tr>
                <tr v-for="(each, index) in mainData.ework.data.produceWarning.storedFabric">
                    <td>
                        {% index+1 %}
                    </td>
                    <td>
                        <input type="text" class="form-control" v-model="mainData.ework.data.produceWarning.storedFabric[index].attached"
                               @keyup="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)" @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)">
                    </td>
                    <td>
                        <input type="text" class="form-control" v-model="mainData.ework.data.produceWarning.storedFabric[index].fabricName"
                               @keyup="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)" @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)">
                    </td>
                    <td>
                        <input type="text" class="form-control" v-model="mainData.ework.data.produceWarning.storedFabric[index].fabricMix"
                               @keyup="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)" @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)">
                    </td>
                    <td>
                        <input type="text" class="form-control" v-model="mainData.ework.data.produceWarning.storedFabric[index].fabricColor"
                               @keyup="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)" @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)">
                    </td>
                    <td>
                        <input type="text" class="form-control" v-model="mainData.ework.data.produceWarning.storedFabric[index].fabricCnt"
                               @keyup="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)" @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)">
                    </td>
                    <td>
                        <input type="text" class="form-control" v-model="mainData.ework.data.produceWarning.storedFabric[index].fabricMemo"
                               @keyup="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)" @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)">
                    </td>
                    <td>
                        <div class="btn btn-white btn-sm" @click="addElement(mainData.ework.data.produceWarning.storedFabric, mainData.ework.data.produceWarning.storedFabric[0], 'after')">추가</div>
                        <div class="btn btn-white btn-sm" v-if="mainData.ework.data.produceWarning.storedFabric.length > 1"
                             @click="removeElementProduceWarn(mainData.ework.data.produceWarning.storedFabric, index)">삭제</div>
                        <div class="btn btn-white btn-sm" v-if="1 >= mainData.ework.data.produceWarning.storedFabric.length" disabled="disabled">삭제</div>
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
                    <col class="w-8p">
                </colgroup>
                <tr>
                    <th >NO</th>
                    <th >구분</th>
                    <th >내용</th>
                    <th >추가/삭제</th>
                </tr>
                <tr v-for="(each, index) in mainData.ework.data.produceWarning.contents3">
                    <td>
                        {% index+1 %}
                    </td>
                    <td>
                        <input type="text" class="form-control" v-model="mainData.ework.data.produceWarning.contents3[index].div"
                               @keyup="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)"
                               @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)">
                    </td>
                    <td>
                        <input type="text" class="form-control" v-model="mainData.ework.data.produceWarning.contents3[index].memo1"
                               @keyup="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)"
                               @change="eworkUpdate('produceWarning',mainData.ework.data.produceWarning)">
                    </td>
                    <td>
                        <div class="btn btn-white btn-sm" @click="addElement(mainData.ework.data.produceWarning.contents3, mainData.ework.data.produceWarning.contents3[0], 'after')">추가</div>
                        <div class="btn btn-white btn-sm" v-if="mainData.ework.data.produceWarning.contents3.length > 1"
                             @click="removeElementProduceWarn(mainData.ework.data.produceWarning.contents3, index)">삭제</div>
                        <div class="btn btn-white btn-sm" v-if="1 >= mainData.ework.data.produceWarning.contents3.length" disabled="disabled">삭제</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
