<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>

<!-- CDN -->
<script src="https://cdn.jsdelivr.net/npm/exceljs/dist/exceljs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>

<div class="page-header js-affix affix " style="width: 1694px; left: 211px; padding-bottom: 0 !important; margin-bottom: 0 !important;" id="affix-menu">
    <!--틀고정-->
    <section id="affix-show-type2" style="margin: 0 !important; display: block;">
        <h3>
            <div class="dp-flex flex-gap-10">
                고객사 폐쇄몰 재고 관리
                <span class="font-13 dp-flex mgl20">최근 재고 업데이트 : <span id="latestUpdateInfoTarget"></span></span>
            </div>
        </h3>
    </section>
</div>

<section id="imsApp" class="project-view pdt20">
    <div id="latestUpdateInfo">
        {% latestUpdateDate %}
    </div>

    <div class="relative mgb20">
        <ul class="nav nav-tabs mgb20" role="tablist">
            <li role="presentation" :class="tabKey === tabMode?'active':''" @click="changeTab(tabKey,'stockManageTab')" v-for="(tabInfo, tabKey) in tabList">
                <a href="#" data-toggle="tab" >{% tabInfo %}</a>
            </li>
        </ul>
    </div>

    <div v-show="'report' === tabMode">
        <?php include 'stock_manage_report.php'?>
    </div>

    <div v-show="'list' === tabMode">
        <?php include 'stock_manage_list.php'?>
    </div>

</section>

<?php include 'stock_manage_script.php'?>