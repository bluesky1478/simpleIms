<!-- ############### 수정사항 ############### -->
<div class="col-xs-12 new-style" v-show="'modify' === tabMode">
    <div class="table-title gd-help-manual">
        <div class="flo-left area-title">
            <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
            작업지시서 내용 수정
        </div>
        <div class="flo-right pdb5">

            <div class="font-13 inline-block">
                <label class="radio-inline ">
                    <input type="radio" name="packingYn1" value="1" />전체
                </label>
                <label class="radio-inline ">
                    <input type="radio" name="packingYn1" value="0" checked selected />요청
                </label>
                <label class="radio-inline">
                    <input type="radio" name="packingYn1" value="y" />완료
                </label>
            </div>

            <div class="btn btn-red btn-red-line2 mgl15 inline-block">
                <i class="fa fa-plus text-danger"></i>
                수정사항 등록
            </div>
        </div>
    </div>

    <table class="table table-cols table-pd-0 table-center table-th-height30 table-td-height30">
        <colgroup>
            <col class="w-5p">
            <col class="w-5p">
            <col class="">
            <col class="w-10p">
            <col class="w-10p">
            <col class="w-10p">
        </colgroup>
        <tbody>
        <tr >
            <th >번호</th>
            <th >상태</th>
            <th >수정내용</th>
            <th >등록자</th>
            <th >처리자</th> <!--날짜+처리자 표현예정-->
            <th >최종확인</th>
        </tr>
        <tr v-for="idx in 5">
            <td >{% 5-idx+1 %}</td>
            <td >요청</td>
            <td style="text-align: left !important;">스펙 수정 필요{% 5-idx+1 %}</td>
            <td >기성용(24/10/20)</td>
            <td >임상협(24/10/21)</td>
            <td >홍길동(24/10/23)</td>
        </tr>
        </tbody>
    </table>
</div>