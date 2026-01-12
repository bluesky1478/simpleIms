<?php
use Component\Ims\ImsDBName;
?>
<script type="text/javascript">
    const mainListPrefix = 'basic_proposal_guide';
    const listSearchDefaultData = {
        multiKey : [{
            key : 'a.guideName',
            keyword : '',
        }],
        multiCondition : 'OR',
        page : 1,
        pageNum : 255,
        sort : 'sortNum,asc' //정렬
    };

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        params.mode = 'getListBasicFormProposalGuide';
        return ImsNkService.getList('basicFormProposalGuide', params);
    };

    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            isModify : true,
            bFlagRunSearch : false,
        });
        ImsBoneService.setMethod(serviceData, {
            uploadProposalGuideFile : (iKey)=>{
                const fileInput = vueApp.$refs.fileGuideImage[iKey];
                if (fileInput.files.length > 0) {
                    const formData = new FormData();
                    formData.append('upfile', fileInput.files[0]);
                    $.ajax({
                        url: '<?=$nasUrl?>/img_upload.php?projectSno=0',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(result){
                            const rslt = JSON.parse(result);
                            vueApp.listData[iKey].guideFileUrl = '<?=$nasUrl?>'+rslt.downloadUrl;
                        }
                    });
                }
            },

            save : ()=>{
                //검색하면 모든리스트가 나오는게 아니라서 순서update 안한다.
                $.each(vueApp.searchCondition.multiKey, function (key, val) {
                    if (this.keyword != '') {
                        vueApp.bFlagRunSearch = true;
                        return false;
                    }
                });
                //유효성 검사
                let bFlagErr = false;
                $.each(vueApp.listData, function (key, val) {
                    if (this.guideName == '') {
                        bFlagErr = true;
                        return false;
                    }
                });
                if (bFlagErr === true) {
                    $.msg('양식명을 입력하세요','','error');
                    return false;
                }

                $.imsPost('setBasicProposalGuide', {'list':vueApp.listData, 'bFlagRunSearch':vueApp.bFlagRunSearch}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        let sAppendMsg = '';
                        if (vueApp.bFlagRunSearch === true) sAppendMsg = '검색 후 저장하시면 순서가 변경되지 않습니다.';
                        $.msg('저장 완료',sAppendMsg,'success').then(()=>{
                            vueApp.refreshList(vueApp.searchCondition.page);
                            //vueApp.isModify = false;
                        });
                    });
                });
            },
            deleteRow : (sno, iKey)=>{
                if (sno == '') {
                    vueApp.deleteElement(vueApp.listData, iKey);
                } else {
                    $.msgConfirm('정말 삭제 하시겠습니까? (복구 불가능)','').then(function(result){
                        if( result.isConfirmed ){
                            ImsNkService.setDelete('iiiii', sno).then(()=>{
                                vueApp.deleteElement(vueApp.listData, iKey);
                            });
                        }
                    });
                }
            },
        });

        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData);
        listService.init(serviceData);
    });

</script>