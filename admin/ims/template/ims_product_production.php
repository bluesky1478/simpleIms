<div class="col-xs-12 pd-custom" style="padding-top:0 !important;">
    <div  style="" >

        <div class="btn btn-gray" @click="ImsProductService.openNewProduction(product.sno)">생산정보 등록</div>
        <div class="btn btn-red btn-red-line2" @click="ImsProductionService.setProduceStatusBatch(99)">생산완료처리</div>

        <div>
            <?php include 'ims_product_production_list_template.php'?>
        </div>
    </div>
</div>

<?php include 'ims_product_production_detail_template.php'?>

