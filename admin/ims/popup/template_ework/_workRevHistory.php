<!-- ############### 변경히스토리 ############### -->
<div class="col-xs-12 new-style" v-show="'revHistory' === tabMode">
    <div class="table-title gd-help-manual">
        <div class="flo-left area-title">
            <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
            변경 히스토리
        </div>
        <div class="flo-right pdb5">
            <div class="btn btn-red btn-red-line2">
                <i class="fa fa-plus text-danger"></i>
                변경점 등록
            </div>
        </div>
    </div>

    <table class="table table-cols table-pd-0 table-center table-th-height30 table-td-height30">
        <colgroup>
            <col class="w-5p">
            <col class="">
            <col class="w-7p">
            <col class="w-7p">
        </colgroup>
        <tbody>
        <tr >
            <th >번호</th>
            <th >업데이트 내용</th>
            <th >날짜</th>
            <th >처리자</th>
        </tr>
        <tr v-for="idx in 5">
            <td >{% 5-idx+1 %}</td>
            <td style="text-align: left !important;">메인 파일 등록됨 {% 5-idx+1 %}</td>
            <td >24/10/28</td>
            <td >홍길동</td>
        </tr>
        </tbody>
    </table>
</div>