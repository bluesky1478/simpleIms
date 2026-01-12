<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    TODO : Sample 작업.
</section>

<script type="text/javascript">

    $(appId).hide();
    const sno = '<?=$requestParam['sno']?>'; //sampleSno

    $(()=>{

        const serviceData = {
            serviceValue : {
                viewMode : 'v', //이 화면에서 사용하는 변수 ( 화면모드 : m 수정 , v 보기모드 )
                editorSet : false,
            },serviceMounted : (vueInstance)=>{
                //신규 등록
                if( $.isEmpty(vueInstance.mainData.sno) ){
                    vueInstance.mainData.customerSno = '<?=$requestParam['customerSno']?>';
                    vueInstance.mainData.projectSno = '<?=$requestParam['projectSno']?>';
                    vueInstance.mainData.issueType = '<?=$requestParam['issueType']?>';
                    vueInstance.mainData.inboundType = '';
                    vueInstance.viewMode = 'm';
                    ImsBoneService.setEditor('editor');
                    vueApp.editorSet = true;
                }

                //Dropzone
                $('.set-dropzone').addClass('dropzone');
                ImsService.setDropzone(vueInstance, 'fileData', (tmpFile, dropzoneId)=>{
                    vueInstance.mainData.fileData.memo = '<span class="text-danger font-11">저장되지 않음 (반드시 저장해주세요)</span>';
                    vueInstance.mainData.fileData.files = tmpFile;
                }); //첨부 등록.

                ImsService.setDropzone(vueInstance, 'fileDataView'); //첨부 보기.

            },serviceMethods : {
                setViewMode : (viewMode)=>{
                    console.log(viewMode);
                    vueApp.viewMode = viewMode;
                    if( false === vueApp.editorSet && 'm' === vueApp.viewMode ){
                        //Editor
                        ImsBoneService.setEditor('editor');
                        vueApp.editorSet = true;
                    }
                }
            }
        }

        ImsBoneService.serviceBegin(DATA_MAP.CUST_ISSUE,{sno:sno},serviceData);

    });

</script>


<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/smart/js/service/HuskyEZCreator.js" charset="utf-8"></script>