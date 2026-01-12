<div class="col-xs-12 relative new-style" v-show="'main' === tabMode">

    <div v-if="'p' == mainData.ework.data.materialApproval">
        <div class="mgt20 mgb30">
            <?php if( \SiteLabUtil\SlCommonUtil::isImsAdmin() ) { ?>
            <div class="btn btn-red" @click="afterApprovalRecover()" v-if="'p' === mainData.ework.data.materialApproval">
                <i aria-hidden="true" class="fa fa-exclamation-triangle"></i>
                수정완료 결재 복구
            </div>
            <?php } ?>
        </div>
    </div>
    <div v-else class="">
        <!--결재라인-->
        <ework-approval :data="{mainData:mainData, approvalData:approvalData}" :field1="'main'" :field2="'eworkMain'"></ework-approval>
    </div>

    <div class="btn btn-white" @click="openUpdateHistory(styleSno, 'ework')" style="position: absolute;top:0;right:20px">
        작업지시서 변경 이력
    </div>

    <div class="table-title gd-help-manual mgt10">
        <div class="flo-left area-title">
            <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
            메인 도안 ( 최적 사이즈 : 710 X 850 (4:5비율) )
        </div>
        <div class="flo-right">
        </div>
    </div>
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
                <select class="form-control"
                        v-model="mainData.product.produceCompanySno"
                        @change="prdUpdate('produceCompanySno',mainData.product.produceCompanySno)"
                >
                    <option value="0">미정</option>
                    <?php foreach ($produceCompanyList as $key => $value ) { ?>
                        <option value="<?=$key?>"><?=$value?></option>
                    <?php } ?>
                </select>
            </td>
            <th >
                작성일
            </th>
            <td>
                <date-picker v-model="mainData.ework.data.writeDt"
                             value-type="format"
                             format="YYYY-MM-DD"
                             :lang="lang"
                             :editable="false"  placeholder="작성일" style="width:140px;font-weight: normal; "
                             @change="eworkUpdate('writeDt',mainData.ework.data.writeDt)">
                </date-picker>

                <div class="btn btn-sm btn-white hover-btn mgl20" @click="setNow(mainData.ework.data, 'writeDt'); eworkUpdate('writeDt',mainData.ework.data.writeDt)">오늘</div>

            </td>
        </tr>
        <tr >
            <th >
                제품명
            </th>
            <td>
                <input type="text" class="form-control" placeholder="제품명"
                       v-model="mainData.product.productName"
                       @change="prdUpdate('productName',mainData.product.productName)">
            </td>
            <th >
                생산구분
            </th>
            <td>
                <select class="form-control"
                        v-model="mainData.product.produceType"
                        @change="prdUpdate('produceType',mainData.product.produceType)">
                    <?php foreach ($prdType as $key => $value ) { ?>
                        <option value="<?=$key?>"><?=$value?></option>
                    <?php } ?>
                </select>
            </td>
            <th >
                납기일
            </th>
            <td>
                <date-picker v-model="mainData.product.msDeliveryDt"
                             value-type="format"
                             format="YYYY-MM-DD"
                             :lang="lang"
                             :editable="false"  placeholder="이노버 납기일" style="width:140px;font-weight: normal; "
                             @change="prdUpdate('msDeliveryDt',mainData.product.msDeliveryDt)">
                </date-picker>
            </td>
            <!--
            <th >
                의뢰일
            </th>
            <td>
                <span class="font-16" v-if="!$.isEmpty(mainData.ework.data.requestDt) && '0000-00-00' !== mainData.ework.data.requestDt">
                    <date-picker v-model="mainData.ework.data.requestDt"
                                 value-type="format"
                                 format="YYYY-MM-DD"
                                 :lang="lang"
                                 :editable="false"  placeholder="의뢰일" style="width:140px;font-weight: normal; "
                                 @change="eworkUpdate('requestDt',mainData.ework.data.requestDt)">
                    </date-picker>
                    <div class="btn btn-sm btn-white hover-btn mgl20" @click="setNow(mainData.ework.data, 'requestDt'); eworkUpdate('requestDt',mainData.ework.data.requestDt)">오늘</div>
                </span>
                <span class="font-16" v-if="!$.isEmpty(mainData.ework.data.requestDt) || '0000-00-00' !== mainData.ework.data.requestDt">
                    발주시 자동 등록
                </span>
            </td>
            -->
        </tr>
        <tr >
            <th >
                성별
            </th>
            <td>
                <select class="form-control"
                        v-model="mainData.product.prdGender"
                        @change="prdUpdate('prdGender',mainData.product.prdGender)">
                    <option value="">구분없음</option>
                    <?php foreach($codeGender as $codeKey => $codeValue) { ?>
                        <option value="<?=$codeKey?>"><?=$codeValue?></option>
                    <?php } ?>
                </select>
            </td>
            <th >
                제조국
            </th>
            <td colspan="99">
                <select class="form-control"
                        v-model="mainData.product.produceNational"
                        @change="prdUpdate('produceNational',mainData.product.produceNational)">
                    <option value="">미정</option>
                    <?php foreach ($prdNational as $key => $value ) { ?>
                        <option value="<?=$value?>"><?=$value?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td rowspan="2" colspan="2" class="pd0">
                원단사양/원단컬러
                <textarea class="form-control" rows="6"
                          v-model="mainData.ework.data.prdFabricInfo"
                          @keyup="eworkUpdate('prdFabricInfo',mainData.ework.data.prdFabricInfo)" @blur="eworkUpdate('prdFabricInfo',mainData.ework.data.prdFabricInfo)"
                          placeholder="원단사양/원단컬러" v-model="mainData.ework.prdFabricInfo"></textarea>
            </td>

            <th>
                썸네일 파일
                <mini-file-history :file_div="'filePrd'" :params="mainData.product"></mini-file-history>
            </th>
            <td colspan="99">
                <simple-file-list :files="mainData.ework.fileList.filePrd"></simple-file-list>
                <form id="filePrd" class="set-dropzone mgt5" @submit.prevent="uploadFiles" >
                    <div class="fallback">
                        <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                    </div>
                </form>
                <div class="btn btn-sm btn-gray mgt15" @click="delFile('filePrd')">- 파일삭제</div>
            </td>
            <!--<th>
                원단설명<br>(고객 확인용, 필수)
            </th>
            <td colspan="99">
                <textarea class="form-control" rows="6"
                          v-model="mainData.ework.data.prdFabricInfo"
                          @keyup="eworkUpdate('prdFabricInfo',mainData.ework.data.prdFabricInfo)" @blur="eworkUpdate('prdFabricInfo',mainData.ework.data.prdFabricInfo)"
                          placeholder="원단사양/원단컬러" v-model="mainData.ework.prdFabricInfo"></textarea>
            </td>-->
        </tr>
        <tr >
            <th >
                메인 도안
                <mini-file-history :file_div="'fileMain'" :params="mainData.product"></mini-file-history>
            </th>
            <td colspan="99">
                <simple-file-list :files="mainData.ework.fileList.fileMain"></simple-file-list>
                <form id="fileMain" class="set-dropzone mgt5" @submit.prevent="uploadFiles" >
                    <div class="fallback">
                        <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                    </div>
                </form>
                <div class="btn btn-sm btn-gray mgt15" @click="delFile('fileMain')">- 파일삭제</div>

                <div>
                    <div>
                        파일 순서 변경
                        <draggable v-model="mainData.ework.fileList.fileMain" @end="eworkUpdate('fileMain',mainData.ework.fileList.fileMain)">
                            <div v-for="(file,fileIndex) in mainData.ework.fileList.fileMain" :key="fileIndex" class="cursor-pointer">
                                {% fileIndex+1 %} : {% file.fileName %}
                            </div>
                        </draggable>
                    </div>
                </div>
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

