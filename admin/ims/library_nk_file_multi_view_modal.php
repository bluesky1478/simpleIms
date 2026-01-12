<div class="modal fade" id="modalAttachFileMultiView" tabindex="-1" role="dialog"  aria-hidden="true" >
    <div class="modal-dialog" role="document" style="top:0px; width:calc(100vw - 50px);  margin:10px 25px;">
        <div class="modal-content" style="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1"><i class="fa fa-times fa-lg" aria-hidden="true" style="color:#3f3f3f!important;"></i></button>
                <span class="modal-title font-18 bold" >
                    파일 전체열람
                </span>
            </div>
            <div id="modalAttachFileMultiViewBody" class="modal-body" style="padding:0px;"></div>
            <div class="modal-footer ">
                <div class="btn btn-gray" data-dismiss="modal">닫기</div>
            </div>
        </div>
    </div>
</div>
<script>
    function openMultiFileView(aFileList) {
        document.getElementById('modalAttachFileMultiViewBody').innerHTML = '';
        let oFrame = {};
        $.each(aFileList, function(key, val) {
            if (val != '') {
                oFrame = document.createElement('iframe');
                oFrame.setAttribute("id","iframe_multi_file_"+key);
                oFrame.style.width = "calc(100% - 30px)";
                oFrame.style.height = 'calc(100vh - 140px)';
                oFrame.style.border = '1px #ccc solid';
                oFrame.style.margin = '5px 15px';
                oFrame.src = val;

                document.getElementById('modalAttachFileMultiViewBody').appendChild(oFrame);
                //iframe 내부 element 제어하는 방법은???
                // console.log('aaaa', document.getElementById("iframe_multi_file_"+key).contentWindow.document.body.style.backgroundColor = 'lightblue');
                // document.getElementById("multi_file_"+key).contentWindow.document.body.innerHTML = 'lightblue'
                // oFrame.contentWindow.document.body.style.width = '100%';
                // setTimeout(() => {
                //     console.log('aaaa', document.getElementById("multi_file_"+key).contentWindow.getElementsByTagName(body));
                // }, 1000);
            }
        });

        $('#modalAttachFileMultiView').modal('show');
    }
</script>