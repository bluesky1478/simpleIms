<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_bone.php'?>

<section id="imsApp" class="project-view">

    <form id="frm">
        <div class="page-header js-affix">
            <h3>스케쥴 관리</h3>
            <div class="btn-group font-20 pdt10">
            </div>
        </div>
    </form>

    <div class="row" >
        <div class="col-xs-12" >
            
            스케쥴을 관리하자
            
        </div>
    </div>

    <div style="margin-bottom:150px"></div>

</section>

<script type="text/javascript">
    $(()=>{
        $(appId).hide();
        const serviceData = {};

        ImsBoneService.setMounted(serviceData, ()=>{
            console.log('test mounted..');
        });

        ImsBoneService.serviceBegin(DATA_MAP.PROJECT,{sno:1},serviceData);
    });
</script>