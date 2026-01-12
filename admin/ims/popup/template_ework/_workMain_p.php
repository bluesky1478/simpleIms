<div class="col-xs-12 relative new-style" v-show="'main' === tabMode">

    <div class="dp-flex">
        <!--결재라인-->
        <ework-approval :data="{mainData:mainData, approvalData:approvalData}" :field1="'main'" :field2="'eworkMain'"></ework-approval>

        <div class="mgl10" v-for="myApproval in approvalData.eworkMain" v-if="!$.isEmpty(myApproval.reason)">
            전결사유 : {% myApproval.reason %} ({% myApproval.name %})
        </div>

        <div class="mgl30">
            <?php if( \SiteLabUtil\SlCommonUtil::isImsAdmin() ) { ?>
            <div class="btn btn-red btn-red-line2 " @click="afterApprovalModify()">결재 후 수정(사유필수)</div>
            <?php } ?>
        </div>
    </div>

    <div class="btn btn-white" @click="openUpdateHistory(styleSno, 'ework')" style="position: absolute;top:0;right:20px">
        작업지시서 변경 이력
    </div>

    <div class="table-title gd-help-manual mgt10">
        <div class="flo-left area-title">
            <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
            메인 도안
        </div>
        <div class="flo-right">
        </div>
    </div>
    <div>
        <table class="table table-cols table-pd-5 table-td-height30 table-th-height30">
            <colgroup>
                <col class="w-8p">
                <col class="width-md">
                <col class="w-10p">
                <col class="width-md">
                <col class="w-10p">
                <col class="width-md">
            </colgroup>
            <tbody>
            <tr>
                <th rowspan="6">사양서 썸네일</th>
                <td rowspan="6">

                    <div style="width:250px">

                        <img src="/data/commonimg/ico_noimg_75.gif" class="middle w-100p" v-if="0 >= mainData.ework.fileList.filePrd.length">

                        <ul v-if="mainData.ework.fileList.filePrd.length > 0">
                            <li class="" v-for="(file, fileIndex) in mainData.ework.fileList.filePrd" >
                                <div style="width:100%; height:auto;max-width:100%; max-height:100%;">
                                    <img :src="`<?=$nasUrl?>${file.filePath}`" class="w-100p hover-btn cursor-pointer"
                                         @click="window.open(`<?=$nasUrl?>${file.filePath}`,'img_thumbnail','width=1200,height=1200')">
                                </div>
                            </li>
                        </ul>

                    </div>

                </td>
                <th >
                    S/#
                </th>
                <td colspan="99">
                    {% mainData.product.styleCode %}
                </td>
            </tr>
            <tr >
                <th >
                    업체명
                </th>
                <td>
                    {% mainData.customer.customerName %}
                </td>
                <th >
                    생산처
                </th>
                <td>
                    {% mainData.product.produceCompanyKr %}
                </td>
                <th >
                    작성일
                </th>
                <td>
                    {% $.formatShortDate(mainData.ework.data.writeDt) %}
                </td>
            </tr>
            <tr >
                <th >
                    제품명
                </th>
                <td>
                    {% mainData.product.productName %}
                </td>
                <th >
                    생산구분
                </th>
                <td>
                    {% mainData.product.produceTypeKr %}
                </td>
                <th >
                    납기일
                </th>
                <td>
                    {% $.formatShortDate(mainData.product.msDeliveryDt) %}
                </td>
            </tr>
            <tr >
                <th >
                    성별
                </th>
                <td>
                    {% mainData.product.prdGenderKr %}
                </td>
                <th >
                    제조국
                </th>
                <td colspan="3">
                    {% mainData.product.produceNational %}
                </td>
            </tr>
            <tr>
                <td rowspan="2" colspan="2" class="pd0">
                    원단사양/원단컬러
                    <div v-html="$.nl2br(mainData.ework.data.prdFabricInfo)"></div>
                </td>
                <th>
                    썸네일 파일
                    <mini-file-history :file_div="'filePrd'" :params="mainData.product"></mini-file-history>
                </th>
                <td colspan="99">
                    <simple-file-list :files="mainData.ework.fileList.filePrd"></simple-file-list>
                </td>
            </tr>
            <tr >
                <th >
                    메인 도안
                    <mini-file-history :file_div="'fileMain'" :params="mainData.product"></mini-file-history>
                </th>
                <td colspan="99">
                    <simple-file-list :files="mainData.ework.fileList.fileMain"></simple-file-list>
                </td>
            </tr>
            <tr >
                <td colspan="99" class="text-center">
                    <ul v-if="mainData.ework.fileList.fileMain.length > 0">
                        <li class="" v-for="(file, fileIndex) in mainData.ework.fileList.fileMain">
                            <div style="width:100%; height:auto;max-width:100%; max-height:100%;">
                                <img :src="`<?=$nasUrl?>${file.filePath}`" class="hover-btn cursor-pointer" @click="window.open(`<?=$nasUrl?>${file.filePath}`,'img_thumbnail','width=1200,height=1200')">
                            </div>
                        </li>
                    </ul>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>