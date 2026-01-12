<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
            <h3 class="pdt50">기획서 결재 요청</h3>

            <div style="position:absolute; top:6px; right:20px">
                <table class="table table-cols apply-table " style="width:350px; ">
                    <tbody>
                        <tr>
                            <th class="text-center">작성</th>
                            <th class="text-center">관리자
                                <div class="accept-button-area"><!----> <!----></div>
                            </th>
                            <th class="text-center">대표
                                <div class="accept-button-area"><!----> <!----></div>
                            </th>
                        </tr>
                        <tr>
                            <td class="text-center">홍길동</td>
                            <td class="text-center">문상범
                                <div class="rounded-circle bg-success">승인</div>
                            </td>
                            <td class="text-center">서재훈
                                <div class="rounded-circle bg-success">승인</div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center pd0 text-muted2 font-11" style="height:25px !important;">24/04/01</td>
                            <td class="text-center pd0 text-muted2 font-11" style="height:25px !important;">24/04/05</td>
                            <td class="text-center pd0 text-muted2 font-11" style="height:25px !important;">24/04/08</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </form>

    <div class="">
        <!-- 기본 정보 -->
        <div>
            <table class="table table-cols table-pd-5" style="border-top:none !important;">
                <colgroup>
                    <col class="width-sm">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th >
                        첨언
                        <div class="btn btn-white btn-sm">첨언등록</div>
                    </th>
                    <td>
                        <ul>
                            <li class="pd5">
                                <i class="fa fa-dot-circle-o" aria-hidden="true"></i> 첨언내용1 (24/04/12 문상범)
                            </li>
                            <li class="pd5">
                                <i class="fa fa-dot-circle-o" aria-hidden="true"></i> 첨언내용2 (24/04/12 홍길동)
                            </li>
                            <li class="pd5">
                                <i class="fa fa-dot-circle-o" aria-hidden="true"></i> 첨언내용3 (24/04/01 한동경)
                            </li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th>
                        내용
                    </th>
                    <td class="mgb150">
                        기획서 확인하시고 승인 요청 드립니다.
                    </td>
                </tr>
                <tr>
                    <th>
                        연결 프로젝트
                    </th>
                    <td>
                       <a href="#" class="text-danger" @click="openProjectView(137)">23121900</a>
                    </td>
                </tr>
                <tr>
                    <th>
                        첨부
                    </th>
                    <td>
                        <a href="#" class="text-blue">다운로드</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="dp-flex" style="justify-content: center">
            <div class="btn btn-accept hover-btn btn-lg mg5">승인</div>
            <div class="btn btn-reject hover-btn btn-lg mg5">반려</div>
            <div class="btn btn-white hover-btn btn-lg mg5" @click="self.close()">닫기</div>
        </div>

    </div>

</section>

<script type="text/javascript">

    $(appId).hide();

    $(()=>{
        //Load Data.
        const customerSno = '<?=$requestParam['customerSno']?>';
        const projectSno = '<?=$requestParam['projectSno']?>';
        const styleSno = '<?=$requestParam['styleSno']?>';
        const eachSno = '<?=$requestParam['eachSno']?>';
        ImsService.getDataParams(DATA_MAP.FILE_HISTORY, {
            customerSno : customerSno,
            projectSno : projectSno,
            styleSno : styleSno,
            eachSno : eachSno,
            fileDiv : '<?=$requestParam['fileDiv']?>',
        } ).then((data)=>{
            console.log(data);
            const initParams = {
                data : {
                    items : data.data,
                },
            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        });
    });
</script>
