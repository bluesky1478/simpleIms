<div>
    <div class="table-title gd-help-manual">
        <div class="flo-left area-title ">
            <span class="godo">업무 스케쥴</span>
        </div>
        <div class="flo-right">
            <div class="btn btn-white" @click="setModifyMode()" v-show="!isModify">수정</div>
            <div class="btn btn-red btn-red2" @click="saveProject()" v-show="isModify">저장</div>
            <div class="btn btn-white" @click="cancelProjectSave()" v-show="isModify">수정취소</div>
        </div>
    </div>

    <div >
        <table class="table table-cols w100 table-default-center ">
            <tr>
                <th style="border-right:solid 1px #E6E6E6">제안 단계</th>
                <th style="border-right:solid 1px #E6E6E6">샘플 단계</th>
                <th style="border-right:solid 1px #E6E6E6">발주 관리</th>
                <th style="border-right:solid 1px #E6E6E6">Q&B</th>
                <th style="border-right:solid 1px #E6E6E6">견적 관리</th>
                <th style="border-right:solid 1px #E6E6E6">생산 발주 D/L</th>
                <th>고객 납기</th>
            </tr>
            <tr>
                <td style="height: 70px; border-right:solid 1px #E6E6E6" class="text-green">
                    <div class="">제안서 확정 완료</div>
                    <div class="line-gray w-95p mgt10"></div>
                    <div class="mgt5">4/4</div>
                </td>
                <td style="border-right:solid 1px #E6E6E6" class="text-danger">
                    <div class="cursor-pointer hover-btn" @click="isDetail=true">샘플 제작 중</div>
                    <div class="line-gray w-95p mgt10"></div>
                    <div class="mgt5">1/5</div>
                </td>
                <td style="height: 70px; border-right:solid 1px #E6E6E6" class="text-muted">
                    <div class="text-muted">대기 중</div>
                    <div class="line-gray w-95p mgt10"></div>
                    <div class="mgt5">0/5</div>
                </td>
                <td style="border-right:solid 1px #E6E6E6" class="text-danger">
                    <div class="cursor-pointer hover-btn">퀄리티 수배 중</div>
                    <div class="line-gray w-95p mgt10"></div>
                    <div class="mgt5">3/8</div>
                </td>
                <td style="border-right:solid 1px #E6E6E6" class="text-danger">
                    <div class="cursor-pointer hover-btn">가견적 확인 중</div>
                    <div class="line-gray w-95p mgt10"></div>
                    <div class="mgt5">4/4</div>
                </td>
                <td style="height: 70px; border-right:solid 1px #E6E6E6" class="">
                    <div class="">25/01/27(월)</div>
                    <div class="line-gray w-95p mgt10"></div>
                    <div class="mgt5 text-green">25일남음</div>
                </td>
                <td style="height: 70px; border-right:solid 1px #E6E6E6" class="">
                    <div class="">25/06/13(금)</div>
                    <div class="line-gray w-95p mgt10"></div>
                    <div class="mgt5 text-green">197일남음</div>
                </td>
            </tr>
        </table>
    </div>
</div>

<div v-show="isDetail">

    <div class="table-title gd-help-manual">
        <div class="flo-left area-title ">
            <span class="godo">세부 스케쥴</span>
        </div>
        <div class="flo-right">
        </div>
    </div>

    <div >
        <img class="w-100p" src="/admin/image/imgTmp.png">
    </div>

</div>
