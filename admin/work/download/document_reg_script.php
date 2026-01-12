<script type="text/javascript">

    const DOC_DEPT = '<?=$docDept?>';
    const DOC_TYPE = '<?=$docType?>';

    let documentSno = '<?=$requestParam['sno']?>';
    let beforeInitData = null;

    //set dropzone
    let dropzoneOption = {
        //url: 'work_ps.php?mode=upload_file',          //업로드할 url (ex)컨트롤러)
        init: function () {
        },
        autoProcessQueue: false,    // 자동업로드 여부 (true일 경우, 바로 업로드 되어지며, false일 경우, 서버에는 올라가지 않은 상태임 processQueue() 호출시 올라간다.)
        clickable: true,            // 클릭가능여부
        maxFiles: 10,                // 업로드 파일수
        maxFilesize: 20,            // 최대업로드용량 : 10MB
        parallelUploads: 99,        // 동시파일업로드 수(이걸 지정한 수 만큼 여러파일을 한번에 컨트롤러에 넘긴다.)
        addRemoveLinks: true,       // 삭제버튼 표시 여부
        dictRemoveFile: '삭제</div>',     // 삭제버튼 표시 텍스트
        uploadMultiple: true,       // 다중업로드 기능
        dictDefaultMessage:'<span style="font-size:1em;color: #8C8C8C">여기에 파일을 올려놓거나 클릭하세요.</span> '
    };
    let dropzoneOptionList = [];
    let setDropzone = (index, fieldName) => {
        //console.log('setDrop');
        dropzoneOptionList.push($.copyObject(dropzoneOption));
        dropzoneOptionList[index].url = 'work_ps.php?mode=uploadDocumentFile&fieldName=' + fieldName;
        dropzoneOptionList[index].fieldName = fieldName;
    };

    $(function(){

        setDefaultJqueryEvent();

        /**
         * VueJS Method
         */
        let vueMethod = {
            /**
             * 미팅보고서선택
             */
            selectDocument : function( listDocData, docData) {
                let parentApp = this;
                //console.log( listDocData );
                //console.log(listDocData.sno);

                let documentPromise = WorkDocument.getDocument(DOC_DEPT, DOC_TYPE, listDocData.sno);
                documentPromise.then((jsonData)=>{

                    let itemSrcData = jsonData.data;

                    //선택한 문서 데이터 넣기
                    let ignoreFieldList = [
                    ];
                    for(let key in itemSrcData.docData){
                        //console.log(itemDocData);
                        if( !ignoreFieldList.includes(key) && (typeof docData[key] != 'undefined')  ){
                            docData[key] = itemSrcData.docData[key];
                        }
                    }
                    docData['loadDocumentVersion'] = itemSrcData.version;
                    docData['loadDocumentName'] = itemSrcData.docName;
                    docData['loadDocumentSno'] = itemSrcData.sno;

                    //console.log('======');
                    //console.log(itemSrcData);

                    externSelectDocument( itemSrcData, docData, parentApp );

                    $('#documentSelectModal').modal('hide');
                });
            },
            /**
             * 파일 첨부
             */
            uploadFile: function(item, target, e){
                let file = e.target.files;
                let imageUrl = URL.createObjectURL(file[0]);
                item[target] = imageUrl;
                let targetFile = target + 'File';
                item[targetFile] = file[0];
            },
        };

        try {
            for(let idx in externVueMethod){
                vueMethod[idx] = externVueMethod[idx];
            }
        }catch(e){}

        /**
         * VueJS App
         * @param initData
         */
        let mountedFnc = (el) => {
            externMountedFnc(el);
        }
        WorkDocument.initDocument('#documentApp' , mountedFnc, vueMethod);
    });

</script>


