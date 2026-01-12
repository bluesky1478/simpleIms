<div class="table-title gd-help-manual">
    <div class="flo-left area-title">
        업무 스케쥴
    </div>
    <div class="flo-right">
        <div class="btn btn-white" @click="setModifyMode()" v-show="!isModify">수정</div>
        <div class="btn btn-red btn-red2" @click="saveProject()" v-show="isModify">저장</div>
        <div class="btn btn-white" @click="cancelProjectSave()" v-show="isModify">수정취소</div>
    </div>
</div>

<div >
        <table class="table table-cols w100 table-default-center table-pd-0 table-td-height30 table-th-height30">
            <!--<colgroup>
                <col class="width-xs"/>
                <col class="width-md"/>
            </colgroup>-->
            <tr>
                <th rowspan="2" style="border-right:solid 1px #aaaaaa">단계</th>
                <th class="center" colspan="4" style="border-right:solid 1px #aaaaaa">제안 단계</th>
                <th class="center" colspan="2" style="border-right:solid 1px #aaaaaa">샘플 단계</th>
                <th class="center" colspan="1" style="border-right:solid 1px #aaaaaa">발주 준비</th>
                <th class="center" colspan="7">D/L 관리</th>
            </tr>
            <tr>
                <th class="center">기획서</th>
                <th class="center">제안서</th>
                <th class="center text-danger">제안서 발송</th>
                <th class="center text-danger" style="border-right:solid 1px #aaaaaa">제안서<br> 확정요청</th>
                <th class="center text-danger">샘플 발송</th>
                <th class="center text-danger" style="border-right:solid 1px #aaaaaa">샘플<br> 확정요청</th>
                <th class="center text-danger" style="border-right:solid 1px #aaaaaa">고객 발주<br>(사이즈별 수량)</th>
                <th class="center">생산가<br>(가견적)</th>
                <th class="center">판매가</th>
                <th class="center">원단 수배<br>시작일 D/L</th>
                <th class="center">발주 D/L</th>
                <th class="center">공장<br>납기일</th>
                <th class="text-danger">고객 희망<br>납기일</th>
                <th class="text-danger">생산 기간</th>
            </tr>
            <tr>
                <th style="border-right:solid 1px #aaaaaa">예정일</th>
                <td>24/12/18(수)</td>
                <td>24/12/30(월)</td>
                <td class="bg-light-yellow">24/12/31(화)</td>
                <td class="bg-light-yellow" style="border-right:solid 1px #aaaaaa">25/01/06(월)</td>
                <td class="bg-light-yellow">25/01/27(월)</td>
                <td class="bg-light-yellow" style="border-right:solid 1px #aaaaaa">25/02/03(월)</td>
                <td class="bg-light-yellow" style="border-right:solid 1px #aaaaaa">25/02/06(목)</td>
                <td class="">25/01/27(월)</td>
                <td class="" >25/02/03(월)</td>
                <td class="">25/01/03(금)</td>
                <td class="" rowspan="2">
                    25/02/13(목)
                    <div class="sl-green">99일 남음</div>
                </td>
                <td class="" rowspan="2">
                    25/05/14(수)
                    <div v-html="$.remainDate2('2025-05-14', true)"></div>
                </td>
                <td class="bg-light-yellow" rowspan="2">
                    25/06/13(금)
                    <div v-html="$.remainDate2('2025-06-13', true)"></div>
                </td>
                <td class="bg-light-yellow" rowspan="2">
                    120일
                </td>
            </tr>
            <tr>
                <th style="border-right:solid 1px #aaaaaa">완료일</td>
                <td></td>
                <td></td>
                <td></td>
                <td style="border-right:solid 1px #aaaaaa"></td>
                <td class="text-muted">해당 없음</td>
                <td class="text-muted" style="border-right:solid 1px #aaaaaa">해당 없음</td>
                <td class="text-muted" style="border-right:solid 1px #aaaaaa">해당 없음</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th style="border-right:solid 1px #aaaaaa">결재</td>
                <td></td>
                <td></td>
                <td></td>
                <td style="border-right:solid 1px #aaaaaa"></td>
                <td></td>
                <td style="border-right:solid 1px #aaaaaa"></td>
                <td style="border-right:solid 1px #aaaaaa"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th style="border-right:solid 1px #aaaaaa">파일</td>
                <td></td>
                <td></td>
                <td></td>
                <td style="border-right:solid 1px #aaaaaa"></td>
                <td></td>
                <td style="border-right:solid 1px #aaaaaa"></td>
                <td style="border-right:solid 1px #aaaaaa"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>