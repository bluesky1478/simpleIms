<?php include 'library_all.php'?>
<?php include 'library.php'?>

<section id="imsApp" class="project-view">

    <form id="frm">
        <div class="page-header js-affix">
            <h3> 이노버 요청 리스트 ( <span class="sl-blue">{% getTabName(tabMode) %}</span> )</h3>
            <div class="btn-group font-20 pdt10">
                <!-- 처리 요청 건 -> 스케쥴입력 : 0건, 퀄리티&BT요청 : 0건, 가견적: 0건, 생산가확정: 0건, 원부자재선행: 0건 -->
            </div>
        </div>
    </form>

    <!-- TODO 검색 화면
    //고객명, 프로젝트번호, 스타일코드
    //연도, 시즌
    -->

    <!--탭화면-->
    <!--<div id="tabViewDiv">
        <ul class="nav nav-tabs mgb30" role="tablist">
            <li role="presentation" :class="'qb' === tabMode?'active':''" @click="changeTab('qb')" id="ims-tab-basic">
                <a href="#tab-status-order" data-toggle="tab" >퀄리티&BT요청</a>
            </li>
            <li role="presentation" :class="'estimate' === tabMode?'active':''" @click="changeTab('estimate')" id="ims-tab-style">
                <a href="#tab-status-cancel" data-toggle="tab" >가견적</a>
            </li>
            <li role="presentation" :class="'cost' === tabMode?'active':''" @click="changeTab('cost')" id="ims-tab-fabric">
                <a href="#tab-status-cancel" data-toggle="tab" >생산가 확정</a>
            </li>
        </ul>
    </div>-->

    <div class="row" v-show="'produce' === tabMode">
        <?php include 'template/ims_request_list_produce.php'?>
    </div>

    <div class="row" v-show="'qb' === tabMode">
        <?php include 'template/ims_request_list_qb.php'?>
    </div>

    <!--<div class="row" v-show="'estimate' === tabMode">
        <?php /*include 'template/ims_request_list_estimate.php'*/?>
    </div>-->

    <div class="row" v-show="'cost' === tabMode">
        <?php include 'template/ims_request_list_cost.php'?>
    </div>

    <div style="margin-bottom:150px"></div>

</section>

<?php include 'script/ims_request_list_script.php'?>

