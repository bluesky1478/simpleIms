<?php include 'library_all.php'?>
<?php include 'library.php'?>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .bootstrap-filestyle input{display: none }
        .ims-product-image .bootstrap-filestyle {display: table; width:83% ; float: left}

        .mx-input {padding:0 12px !important; font-size: 14px !important; }
        .pd-custom { padding:10px 15px 15px 15px !important; }
        .gd-help-manual { font-size:16px !important;}
        .ims-style-attribute-table td{border-bottom: none !important;}
    </style>

    <section id="imsApp">
        <form id="frm">
            <div class="page-header js-affix" style="margin-bottom: 0 !important;">
                <h3>
                <span class="sl-purple cursor-pointer hover-btn" @click="openCustomer(items.sno)">
                    {% items.customerName %}
                </span>
                    {% product.productName %}
                </h3>
                <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
                <input type="button" value="저장" class="btn btn-red btn-register" @click="save(product)" style="margin-right:178px">
                <input type="button" value="변경이력" class="btn btn-white" @click="openUpdateHistory(product.sno, 'product')" style="margin-right:75px">
            </div>
        </form>

        <div class="">
            <section >
                <?php include 'template/ims_style_basic.php'?>
            </section>
        </div>

    </section>


<?php include 'script/ims_style_script.php'?>