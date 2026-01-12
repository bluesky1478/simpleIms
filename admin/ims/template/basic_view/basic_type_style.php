<div class="row relative">
    <div class="col-xs-12 mgt3" >
        <div class="relative" style="height:40px">
            <ul class="nav nav-tabs mgb0" role="tablist" ><!--제안서 이상 단계에서만 선택 가능-->
                <li role="presentation" :class="'basic' === styleTabMode?'active':''">
                    <a href="#" data-toggle="tab"  @click="changeStyleTab('basic')" >스타일</a>
                </li>
                <li role="presentation" :class="'sample' === styleTabMode?'active':''" v-if="Number(project.projectStatus) >= 40">
                    <a href="#" data-toggle="tab" @click="changeStyleTab('sample')">샘플</a>
                </li>
                <li role="presentation" :class="'estimate' === styleTabMode?'active':''" >
                    <a href="#" data-toggle="tab" @click="changeStyleTab('estimate')">고객 견적서</a>
                </li>
                <li role="presentation" :class="'assort' === styleTabMode?'active':''" v-if="Number(project.projectStatus) >= 40">
                    <a href="#" data-toggle="tab" @click="changeStyleTab('assort')">아소트</a>
                </li>
                <!--<li role="presentation" :class="'designGuide' === styleTabMode?'active':''" v-if="Number(project.projectStatus) >= 40">
                    <a href="#" data-toggle="tab" @click="changeStyleTab('designGuide')">작지/사양서</a>
                </li>-->
            </ul>

            <div class="" style="position: absolute; top:9px; right:0">

                <div class="btn btn-gray" @click="costReset(project.sno)">생산가 초기화</div>

                <div class="btn btn-blue" @click="openCommonPopup('customer_estimate', 1100, 850, {projectSno:project.sno})">고객 견적서 발송</div>

                <div class="btn btn-white hover-btn cursor-pointer" @click="showStyle=false" v-show="showStyle">
                    <i class="fa fa-chevron-up " aria-hidden="true" style="color:#7E7E7E"></i> 상품 숨기기
                </div>
                <div class="btn btn-white hover-btn cursor-pointer" @click="showStyle=true" v-show="!showStyle">
                    <i class="fa fa-chevron-down " aria-hidden="true" style="color:#7E7E7E"></i> 상품 보기
                </div>
                
            </div>
        </div>

        <div class="clear-both"></div>

        <!-- [ 스타일1 ] =========================================================  -->
        <?php include './admin/ims/template/basic_view/_style1.php'?>

        <!-- [ 샘플 ] =========================================================  -->
        <?php include './admin/ims/template/basic_view/_style_sample.php'?>

        <!-- [ 견적 ] =========================================================  -->
        <?php include './admin/ims/template/basic_view/_style_estimate.php'?>

        <!-- [ 아소트 ] =========================================================  -->
        <?php include './admin/ims/template/basic_view/_style_assort.php'?>
        
        <!-- [ 사양서 ] =========================================================  -->
        <?php include './admin/ims/template/basic_view/_style_design_guide.php'?>

    </div>
</div>
