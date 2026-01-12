<script type="text/javascript">
    let uploadFileList = null;
    let externSelectDocument = function(itemDocData, docData, parentApp){
        docData.sendEmail = itemDocData.docData.companyEmail;
    }
    let externMountedFnc = function(){
    }
    let externBeforeSaveProc = function(items){
        //포트폴리오 이미지 파일 업로드
        let fileField = [
            'imageThumbnail',
            'imageDetail',
        ];
        //데이터 업로드
        uploadFileList = [];
        for(let styleIdx in items.docData.portData){ //style
            for(let fileIdx in items.docData.portData[styleIdx]){ //port data
                fileField.forEach((fieldName)=>{
                    let fileInfo = items.docData.portData[styleIdx][fileIdx];
                    if( !$.isEmpty(fileInfo[fieldName]) ){
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
    }
    let externAfterSaveProc = function(items, resultData){
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
                    .then(response=>{
                        //console.log('success :)');
                    })
                    .catch(error=>{
                        //console.log('oops..');
                    });
            }
        }
    }
    let externVueMethod = {
        selectedStyle : function(items, styleNo){
            items.docData.selectedStyleNo = styleNo;
        },
        //코멘트 등록
        regComment : function(item, selectedStyleNo, index){
            let param ={
                mode : 'savePortfolioComment',
                sno : documentSno,
                selectedStyleNo : selectedStyleNo,
                index : index,
                contents : item.comment,
            };
            $.post('work_ps.php',param, function(json){
                //console.log(json);
                item.showCommentReg = 0;
                item.comment = '';
                item.commentList.splice(0, item.commentList.length);
                item.commentList = json.data.commentList; //교체.
            });
        },
    };

    let defaultPortData = JSON.parse('<?=$defaultPortData?>');

    let setDefaultPortData = function(styleItems, styleIndex){
        let styleType = styleItems[styleItems.length-1].styleType;
        let defaultData = $.copyObject(defaultPortData[styleIndex]);
        let charCode = styleType.charCodeAt(0) + 1;
        defaultData.styleType = String.fromCharCode(charCode);
        return defaultData;
    }

</script>

<div class="col-xs-12" >
    <div class="table-title ">
        <div class="flo-left" >포트폴리오 정보</div>
        <div class="flo-right " >
            <button type="button" class="btn btn-red btn-sm "  >고객 화면 미리보기</button>
        </div>
    </div>
    <div>
        <table class="table table-cols">
            <colgroup>
                <col class="width-md" />
                <col  />
                <col class="width-md" />
                <col  />
            </colgroup>
            <tr>
                <th>피드백 요청일 / 상태</th>
                <td>
                    <div class=" input-group" style="width:120px; display:inline-block; float:left; margin-right:15px" >
                        <datepicker @update-date="updateDate" v-model="items.docData.feedbackDt" :data-item="'[\'feedbackDt\']'" class="form-control" ></datepicker>
                    </div>
                    <div v-show="items.version > 0">
                        <label class="radio-inline" >
                            <input type="radio" :name="'feedbackStatus'" v-model="items.docData.feedbackStatus" value="0" />미확정
                        </label>
                        <label class="radio-inline" >
                            <input type="radio" :name="'feedbackStatus'" v-model="items.docData.feedbackStatus" value="1" />확정
                        </label>
                    </div>
                </td>
                <th>
                    포트폴리오 발송
                </th>
                <td>
                    <div class="form-inline">
                        수신 Email : <input name="text" v-model="items.docData.sendEmail" class="form-control" style="width:200px">
                        <div type="button" class="btn btn-sm btn-white" @click="sendMail(items.docData, items.sno)"  v-show="items.version > 0">+ 발송</div>
                        <a href="<?=$customerPreviewLink?>" v-show="items.version > 0" target="_blank"><div type="button" class="btn btn-sm btn-white" >미리보기</div></a>
                        <span style="margin-left:10px; color:red" v-show="!$.isEmpty(items.docData.sendDt)"><b>{% items.docData.sendDt %} 에 발송하였습니다.</b></span>
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    스타일
                    <div type="button" class="btn btn-sm btn-white" @click="addListData('portData', items.docData.portData[items.docData.selectedStyleNo],  setDefaultPortData(  items.docData.portData[items.docData.selectedStyleNo], items.docData.selectedStyleNo   )  )">+ 추가</div>
                </th>
                <td colspan="3">
                    <ul class="style-ul">
                        <?php foreach($styleList as $styleKey => $styleName) { ?>
                        <li>
                            <a @click="selectedStyle(items, <?=$styleKey?>)" :class="items.docData.selectedStyleNo == <?=$styleKey?> ? 'active' : '' " style="cursor:pointer;"><?=$styleName?></a>
                        </li>
                        <?php } ?>
                    </ul>
                </td>
            </tr>
        </table>
    </div>
    <div>
        <table class="table table-cols"  v-for="(styleItems, styleIndex) in items.docData.portData" :key="styleIndex" v-show="items.docData.selectedStyleNo == styleIndex">
            <colgroup>
                <col class="width-md" />
                <col class="col-xs-3"  />
                <col  />
            </colgroup>
            <tbody v-for="(item, index) in styleItems" :key="index" >
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
                <td>{% item.styleName %}</td>
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
                    <div v-show="item.status == 0">-</div>
                    <div v-show="item.status == 1" style="color:#c40000;font-size:15px; border:solid 1px; padding:5px; width:100px;text-align:center">
                        수정요청
                    </div>
                    <div v-show="item.status == 2" style="font-weight:bold;background-color:#1e7e34; color:#fff;font-size:15px; border:solid 1px; padding:5px; width:100px;text-align:center">
                        확정
                    </div>
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
                            <col class="width-md" />
                            <col />
                            <col class="width-xs" />
                        </colgroup>
                        <tr v-for="(comment, commentIndex) in item.commentList">
                            <td class="text-center">
                                <div>[{% comment.writeManagerNm %}]</div>
                                <small class="text-muted">{% comment.regDt %}</small>
                            </td>
                            <td>
                                <span v-html="$.nl2br(comment.contents)"></span>
                                <img src="<?= PATH_ADMIN_GD_SHARE ?>img/icon_new.png">
                            </td>
                        </tr>
                    </table>
                    <div class="form-inline" v-show="1 == item.showCommentReg" style="padding:5px">
                        <textarea class="form-control comment-text-area" v-model="item.comment" style="width:85%; height:60px"></textarea>
                        <div class="btn btn-lg btn-red comment-reg-btn" @click="regComment(item, styleIndex , index)">코멘트 저장</div>
                    </div>
                </td>
            </tr>
            <tr >
                <td colspan="3" class="text-center">
                    <div v-show="(styleItems.length-1) === index">
                        <div type="button" class="btn btn-sm btn-red" @click="addListData('portData', styleItems, setDefaultPortData( styleItems, styleIndex ) )">+ {% item.styleName %} 스타일 추가</div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>