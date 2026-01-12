<script type="text/javascript">
    let externSelectDocument = function(itemDocData, docData, parentApp){
        for(let idx in itemDocData['docData']['hopeData']){
            docData.designDirection.push(
                {
                    styleName : itemDocData['docData']['hopeData'][idx].style,
                    styleType : '',
                    recommendCnt : itemDocData['docData']['hopeData'][idx].count,
                    description : '',
                }
            );
        }
    }
    let externMountedFnc = function(){
        setDropzone(0, 'fileDefault');
        dropzoneInstanceList.push(new Dropzone( "div#my-dropzone1" , dropzoneOptionList[0]));
    }
    let externVueMethod = {};
</script>

<div class="col-xs-12" >
    <div class="table-title ">
        <div class="flo-left" >디자인 방향</div>
        <div class="flo-right " ></div>
    </div>
    <div>
        <table class="table table-cols">
            <colgroup>
                <col/>
                <col/>
                <col/>
                <col/>
                <col/>
            </colgroup>
            <tr>
                <th>번호</th>
                <th>스타일</th>
                <th>제안서 타입</th>
                <th>제안 예상 수량</th>
                <th>추가설명</th>
            </tr>
            <tbody v-for="(item, index) in items.docData.designDirection" :key="index">
                <tr>
                    <td>{% index+1 %}</td>
                    <td>
                        <input type="text" class="form-control" v-model="item.styleName">
                    </td>
                    <td>
                        <input type="text" class="form-control" v-model="item.styleType">
                    </td>
                    <td>
                        <input type="text" class="form-control" v-model="item.recommendCnt">
                    </td>
                    <td>
                        <input type="text" class="form-control" v-model="item.description">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table class="table table-cols">
            <colgroup>
                <col class="width-md"/>
                <col/>
            </colgroup>
            <tr>
                <th>첨부파일</th>
                <td>
                    <div id="my-dropzone1" class="set-dropzone"></div>
                    <div>
                        <ul >
                            <li v-for="(file, fileIndex) in items.docData.fileDefault" :key="fileIndex">
                                첨부{% (fileIndex+1) %}. <a :href="file.downloadPath" class="import-blue" >{%file.name%}</a>
                                <a href="#" @click="removeFile(items.docData.fileDefault, fileIndex)">삭제</a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>


