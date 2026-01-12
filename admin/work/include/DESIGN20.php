<form name="frmOrder" method="post" class="frm-order" id="documentApp" @submit.stop.prevent="onSubmit(items)"  >

    <?php include 'accept_history.php'; ?>
    <?php include 'manager_list.php'?>
    <?php include 'mail_history.php'?>

    <div class="page-header js-affix">
        <h3><?=$title?></h3>
        <div class="btn-group">
            <input type="submit" value="저장" class="btn btn-red btn-reg-control"  @click="officialSave(items)" />
        </div>
    </div>
    <div class="row">

        <?php include 'accept_area.php'?>
        <!--CONTENTS BEGIN-->

        <div class="col-xs-12" >
            <div class="table-title ">
                <div class="flo-left" >포트폴리오 정보</div>
                <div class="flo-right " >
                    <button type="button" class="btn btn-red btn-sm "  @click="window.open('<?=$documentCustomerPreviewUrl?>')">고객 화면 미리보기</button>
                </div>
            </div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md" />
                        <col  />
                    </colgroup>
                    <tr>
                        <th>피드백 요청일 / 상태</th>
                        <td >
                            <date-picker v-model="items.docData.feedbackDt" value-type="format" format="YYYY-MM-DD" placeholder="0000-00-00"  :lang="lang"></date-picker>
                            <div v-show="items.version > 0" class="godo  ">
                                <div v-if="'y' === items.isCustomerApplyFl">
                                    <span class="success font-15" >포트폴리오 고객 확정 완료</span><span class="text-muted">( {% items.isCustomerApplyDt %} )</span></div>
                                <div class="font-15 normal" v-else>포트폴리오 고객 미확정 상태</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            스타일 추가
                        </th>
                        <td >

                            <select2 class="js-example-basic-single" style="width:150px"  id="select-style">
                                <option value="">선택</option>
                                <?php foreach ($styleList as $styleKey => $styleName ) { ?>
                                    <option value="<?=$styleKey?>"><?=$styleName?></option>
                                <?php } ?>
                            </select2>

                            <div type="button" class="btn btn-sm btn-white" @click="addStyle()">+ 추가</div>

                        </td>
                    </tr>
                    <tr>
                        <th>
                            포트폴리오 리스트
                        </th>
                        <td  style="" class="pd5">
                            <table class="table table-cols" style="margin-bottom:0px">
                                <colgroup>
                                    <col />
                                    <col style="width:70px" />
                                    <col />
                                    <col />
                                    <col style="width:150px;"/>
                                    <col style="width:350px;" />
                                    <col style="width:100px" />
                                    <col style="width:100px" />
                                    <col style="width:70px" />
                                </colgroup>
                                <tr>
                                    <th class="text-center">스타일</th>
                                    <th class="text-center">번호</th>
                                    <th class="text-center">썸네일</th>
                                    <th class="text-center">타입</th>
                                    <th class="text-center">상태</th>
                                    <th>코멘트</th>
                                    <th class="text-center">상세보기</th>
                                    <th class="text-center">동일스타일추가</th>
                                    <th class="text-center">삭제</th>
                                </tr>
                                <tbody v-for="(styleItems, styleIndex) in items.docData.portData">
                                    <tr v-for="(item, index) in styleItems">
                                        <td class="text-center infinity" v-if="0 === index" :rowspan="styleItems.length">
                                            <b class="font-17" style="color:#515151">{% item.styleName %}</b>
                                            <div class="success" v-if="true == items.docData.styleData[styleIndex]" >고객 확정 완료</div>
                                        </td>
                                        <td class="text-center">{% index+1 %}</td>
                                        <td class="text-center" style="height:70px">
                                            <img :src="item.imageThumbnail" style="max-width:150px; max-height:70px; cursor: pointer;" @click="showDetail(styleIndex,index)" class="hover-btn">
                                        </td>
                                        <td class="text-center">
                                            <div @click="showDetail(styleIndex,index)" style="cursor: pointer;" class="hover-btn">{% item.styleType %}</div>
                                        </td>
                                        <td class="text-center" style="cursor: pointer;" >
                                            <select :class="'form-control color-status color-status'+item.status" v-model="item.status" style="height:30px!important;width:100%; text-align:center" v-if="items.version > 0" >
                                                <option value="0" class="normal">미정</option>
                                                <option value="1" class="modify-req">수정요청</option>
                                                <option value="3" class="modify-complete">수정완료</option>
                                                <option value="2" class="success">확정</option>
                                            </select>
                                        </td>
                                        <td>
                                            <ul @click="showDetail(styleIndex,index)" style="cursor: pointer;" class="hover-btn">
                                                <li v-for="(comment, commentIndex) in item.commentList" v-if=" (item.commentList.length-1) < (commentIndex+3) " style="border-bottom:dotted 1px #e1e1e1; padding:2px;">
                                                    <span style="font-family: GmarketSansMedium; color:#919191">{% shortComment(comment.contents) %}</span>
                                                    <img src="/admin/gd_share/img/icon_new.png" v-if="'y' === comment.isNew">
                                                </li>
                                            </ul>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn btn-white btn-sm" @click="showDetail(styleIndex,index)">상세보기</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn btn-white btn-sm" @click="addSameStyle(styleItems)"> + 동일스타일추가</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn btn-white btn-sm" @click="removeListData(styleItems, index)">- 삭제</div>
                                        </td>
                                    </tr>
                                </tbody>

                                <tbody>
                                    <tr v-show="0 >= items.docData.portData.length">
                                        <td colspan="99" class="text-center">
                                            데이터가 없습니다.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr >
                        <th>
                            <div>포트폴리오 상세</div>
                            <div><div class="btn btn-white btn-sm" @click="()=>{showSelectedStyle=''}">상세 닫기</div></div>
                        </th>
                        <td colspan="3">
                            <table class="table table-cols"  v-for="(styleItems, styleIndex) in items.docData.portData" :key="styleIndex" v-show="showSelectedStyle == styleIndex">
                                <colgroup>
                                    <col class="width-md" />
                                    <col class="col-xs-3"  />
                                    <col  />
                                </colgroup>
                                <tbody v-for="(item, index) in styleItems" :key="index" v-show="showSelectedIndex == index">
                                <tr>
                                    <th colspan="3" class="text-right" style="color:#fff; background-color:#3a3a3a; font-size:15px;">
                                        <div style="float:left">
                                            {% index+1 %}. {% item.styleName %} - {% item.styleType %}
                                        </div>
                                        <div>
                                            <div type="button" class="btn btn-sm btn-white" @click="removeListData(styleItems, index)" >- 삭제</div>
                                        </div>
                                    </th>
                                </tr>
                                <tr>
                                    <th>스타일</th>
                                    <td>
                                        <div class="font-15">{% item.styleName %}</div>
                                    </td>
                                    <td rowspan="4" class="text-center text-muted" style="background: #f9f9f9;">
                                        <div v-if="$.isEmpty(item.imageThumbnail)">썸네일 이미지 미리보기</div>
                                        <div class="" v-else>
                                            <img :src="item.imageThumbnail" style="max-width:95%; max-height:300px" >
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>타입</th>
                                    <td>
                                        <input type="text" class="form-control" v-model="item.styleType">
                                    </td>
                                </tr>
                                <tr>
                                    <th>썸네일 이미지</th>
                                    <td >
                                        <input :type="'file'" :id="'file-thumbnail-' + styleIndex + index " accept="image/*" @change="uploadFile(item,'imageThumbnail',event)">
                                    </td>
                                </tr>
                                <tr>
                                    <th>디테일 이미지</th>
                                    <td >
                                        <input :type="'file'" :id="'file-detail-' + index " accept="image/*" @change="uploadFile(item,'imageDetail',event)">
                                    </td>
                                </tr>
                                <tr>
                                    <th>디테일 이미지</th>
                                    <td colspan="2" style="background: #f9f9f9;height:100px">
                                        <div v-if="$.isEmpty(item.imageDetail)" class="text-muted text-center">디테일 이미지 미리보기</div>
                                        <div class="text-center" v-else>
                                            <img :src="item.imageDetail" style="max-width:100%; " >
                                        </div>
                                    </td>
                                </tr>
                                <tr v-show="items.version > 0">
                                    <th>확정 상태</th>
                                    <td colspan="2">
                                        <select :class="'form-control color-status color-status'+item.status" v-model="item.status" style="height:30px!important;width:110px; text-align:center">
                                            <option value="0" class="normal">미정</option>
                                            <option value="1" class="modify-req">수정요청</option>
                                            <option value="3" class="modify-complete">수정완료</option>
                                            <option value="2" class="success">확정</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr v-show="items.version > 0">
                                    <th>
                                        코멘트/수정요청
                                        <div type="button" class="btn btn-sm btn-white" @click="item.showCommentReg = 1">+ 코멘트</div>
                                    </th>
                                    <td colspan="2">
                                        <table class="table table-cols" style="border-top:none">
                                            <colgroup>
                                                <col style="width:50px" />
                                                <col class="width-md" />
                                                <col />
                                                <col class="width-xs" />
                                            </colgroup>
                                            <tr v-for="(comment, commentIndex) in item.commentList">
                                                <td class="text-center">
                                                    {% commentIndex + 1 %}
                                                </td>
                                                <td class="text-left">
                                                    <div>[{% comment.writeManagerNm %}]</div>
                                                    <small class="text-muted">{% comment.regDt %}</small>
                                                </td>
                                                <td>
                                                    <span v-html="$.nl2br(comment.contents)"></span>
                                                    <img src="<?= PATH_ADMIN_GD_SHARE ?>img/icon_new.png" v-if="'y' === comment.isNew">
                                                </td>
                                            </tr>
                                        </table>
                                        <div class="form-inline" v-show="1 == item.showCommentReg" style="padding:5px">
                                            <textarea class="form-control comment-text-area" v-model="item.comment" style="width:85%; height:60px"></textarea>
                                            <div class="btn btn-lg btn-red comment-reg-btn" @click="regComment(item, styleIndex , index)">코멘트 저장</div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>

        </div>

        <!--CONTENTS END-->
    </div>
    <div class="w100 text-right">
        <input type="submit" value="저장" class="btn btn-lg btn-red btn-reg-control"  style="width:300px; font-size:15px; border-radius: 10px"  @click="officialSave(items)" />
    </div>
</form>


<script type="text/javascript">
    let uploadFileList = [];
    let fileUploadCnt = 0;
    let externMountedFnc = function(){}

    let shortComment = function(temp){
        let length = 30; // 표시할 글자수 기준
        if (temp.length > length) {
            temp = temp.substr(0, length-2) + '...';
        }
        return temp;
    }

    let externVueMethod = {
        /**
         * 저장
         */
        onSubmit : function (items) {

            //신규일 경우 파일이 있는지 체크
            let isError = false;
            let errorMsg = false;
            if( $.isEmpty(documentSno) && 0 >= items.docData.portData.length ){
                errorMsg = '1개 이상의 스타일을 등록해야 합니다.';
                isError = true;
            }

            //스타일 타입 체크
            if( !isError ){
                items.docData.portData.forEach((portData)=>{
                    if( !$.isEmpty(portData) ){
                        portData.forEach((eachData)=>{
                            if( $.isEmpty(eachData.styleType) ) {
                                isError = true;
                                errorMsg = '스타일 타입은 필수 입니다.';
                            }
                        });
                    }
                });
            }

            //에러 체크
            if( isError ){
                $.msg(errorMsg, '', 'warning');
                return false;
            }

            $('#layerFileDim').removeClass('display-none');
            //포트폴리오 이미지 파일 업로드
            let fileField = [
                'imageThumbnail',
                'imageDetail',
            ];
            //데이터 업로드 파일 체크
            uploadFileList = [];
            for(let styleIdx in items.docData.portData){ //style
                for(let fileIdx in items.docData.portData[styleIdx]){ //port data
                    fileField.forEach((fieldName)=>{
                        let fileInfo = items.docData.portData[styleIdx][fileIdx];
                        if( !$.isEmpty(fileInfo[fieldName+'File']) && (typeof fileInfo[fieldName+'File'].type !== 'undefined')  ){
                            //console.log('FIleInfo : ' + styleIdx + ' :: ' + fileIdx + ' : ' + fieldName + ' // ' + !$.isEmpty(fileInfo[fieldName+'File']) + ' // ' + fileInfo[fieldName+'File'].type );
                            //console.log( fileInfo[fieldName] );
                            uploadFileList.push({
                                file : fileInfo[fieldName+'File'],
                                fieldName : fieldName,
                                styleIdx : styleIdx,
                                fileIdx : fileIdx,
                            });
                            items.docData.portData[styleIdx][fileIdx][fieldName+'File'] = '';
                        }
                    });
                }
            }

            //저장 후 처리 .
            let afterSaveProc = function (items, resultData){

                //신규인데 파일이 없는 경우 갱신
                if( 'new' === resultData.saveMode && 0 >= uploadFileList.length){
                    location.href='document_reg.php?projectSno=' + projectSno + '&sno=' + resultData.sno + 'docDept='+DOC_DEPT+'&docType='+DOC_TYPE ;
                    return false;
                }

                //고객 상태에 대한 처리.
                $.postWork({
                    'mode' : 'setCustomerApply',
                    'sno' : documentSno
                }).then((jsonResult)=>{
                    items.isCustomerApplyFl = jsonResult.data.isCustomerApplyFl;
                    items.isCustomerApplyDt = jsonResult.data.isCustomerApplyDt;
                    items.docData.styleData = jsonResult.data.docData.styleData;
                });

                //일반 파일 올리기
                for(let idx in uploadFileList){
                    let fileData = uploadFileList[idx];
                    if( !$.isEmpty(fileData.file) ){
                        let form = new FormData();
                        form.append('mode', 'uploadPortDataFile');
                        form.append('file', fileData.file);
                        form.append('sno', resultData.sno);
                        form.append('styleIdx', fileData.styleIdx);
                        form.append('fileIdx', fileData.fileIdx);
                        form.append('fieldName', fileData.fieldName);
                        axios.post('work_ps.php', form, { 'Content-Type': 'multipart/form-data' })
                            .then((jsonResult)=>{
                                fileUploadCnt++;
                                console.log('success... : ' + fileUploadCnt + ' :: ' + uploadFileList.length);
                                //최종적으로 재저장.
                                //console.log( jsonResult.data );
                                workApp.items.docData.portData[fileData.styleIdx][fileData.fileIdx][fileData.fieldName] = jsonResult.data.data.path; //변경.
                                workApp.items.docData.portData[fileData.styleIdx][fileData.fileIdx][fileData.fieldName+'File'] = jsonResult.data.data;

                                if( fileUploadCnt === uploadFileList.length ){
                                    //console.log('==== AFTER SAVE ====');
                                    let params = {
                                        mode : 'saveDocument',
                                        projectSno : projectSno,
                                        docDept : DOC_DEPT,
                                        docType : DOC_TYPE,
                                        docData : items['docData'],
                                        sno : documentSno,
                                    }
                                    //console.log(params);
                                    WorkDocument.save(params).then(()=>{
                                        $('#layerFileDim').addClass('display-none');
                                        location.href='document_reg.php?projectSno=' + projectSno + '&sno=' + documentSno + 'docDept='+DOC_DEPT+'&docType='+DOC_TYPE;
                                        console.log('최종 완료');
                                    });
                                }
                            })
                            .catch(error=>{
                                console.log('oops..');
                                console.log(error);
                                //$.msg('파일 업로드 중 오류 발생','','error');
                            });
                    }
                }

                $('#layerDim').addClass('display-none');
                if( 0 >= uploadFileList.length ){
                    $('#layerFileDim').addClass('display-none');
                }

            };

            WorkDocument.saveDocument(DOC_DEPT, DOC_TYPE, items, afterSaveProc);
        },
        /**
         * 코멘트 등록
         */
        regComment : function(item, selectedStyleNo, index){
            showDim();
            let param ={
                mode : 'savePortfolioComment',
                sno : documentSno,
                selectedStyleNo : selectedStyleNo,
                index : index,
                contents : item.comment,
            };
            //console.log('코멘트 저장 파라미터');
            //console.log(param);
            $.post('work_ps.php',param, function(json){
                //console.log(json);
                //item.showCommentReg = 0;
                if( $.isEmpty(item.commentList) ){
                    item.commentList = [];
                }
                item.comment = '';
                if( !$.isEmpty(item.commentList) ){
                    item.commentList.splice(0, item.commentList.length);
                }
                item.commentList = json.data.commentList; //교체.
                hideDim();
            });
        },
        /**
         * 스타일 추가
         */
        addStyle : function(){
            $('#layerDim').removeClass('display-none');
            let myApp = this;
            let styleNo = $('#select-style').val();
            let styleName = $('#select-style option:selected').text();

            if( $.isEmpty(styleNo) ){
                $('#layerDim').addClass('display-none');
                $.msg('스타일을 선택해주세요.', '', 'warning');
            }else{
                $.postWork({
                    mode:'getDefaultPortData',
                    styleNo:styleNo,
                    styleName:styleName,
                }).then((jsonResult)=>{
                    if( $.isEmpty(myApp.items.docData.portData[styleNo]) ){
                        myApp.items.docData.portData[styleNo] = [];
                    }
                    myApp.items.docData.portData[styleNo].push(jsonResult.data);
                    myApp.$forceUpdate();
                    $('#select-style').val('').select2();
                    $('#layerDim').addClass('display-none');

                    myApp.showDetail(styleNo, myApp.items.docData.portData[styleNo].length-1 );

                });
            }
        },
        addSameStyle : function(styleItems){
            let firstStatus = styleItems[0].status;
            styleItems[0].status = 0;
            this.addListData2(styleItems, ['styleName','status']);
            styleItems[0].status = firstStatus;
            this.showSelectedIndex = styleItems.length-1;
        },
        /**
         * 상세보기
         * @param styleIndex
         * @param index
         */
        showDetail : function(styleIndex,index){
            this.showSelectedStyle = styleIndex;
            this.showSelectedIndex = index;
            $("html, body").animate({ scrollTop: $(document).height()-600 }, 500);
        },
    };


</script>