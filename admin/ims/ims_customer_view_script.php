<script type="text/javascript">
    const sno = '<?=$requestParam['sno']?>';
    $(appId).hide();
    const tabList = {
        prod_wear: '업체 스타일',
    };
    $(() => {
        const tabMode = 'prod_wear';


        //초기화
        ImsBoneService.serviceStart('getGoodsStockTotalInfoDetail',{goodsNo:goodsNo}, serviceData);
    });
</script>

