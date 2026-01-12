<?php include 'library_all.php'?>
<?php include 'library.php'?>

<style>
    .page-header { margin-bottom:10px };
</style>

<div id="imsApp" class="project-view">

    <div id="move-gnb"></div>

    <form id="frm">
        <div class="page-header js-affix">
            <h3>
                <span class="text-blue cursor-pointer hover-btn" @click="openCustomer(items.sno)">
                    {% items.customerName %} {% project.projectYear %} {% project.projectSeason %}
                </span> 프로젝트 상세정보
                <span class="text-danger" style="font-weight:normal" v-show="!$.isEmpty(project.projectNo)">({% project.projectStatusKr %}-{% project.projectNo %})</span>
            </h3>
            <div class="btn-group">

                <input type="button" value="To-DoList 요청" class="btn btn-red btn-red-line2 btn-white" @click="openTodoRequestWrite(items.sno,project.sno)" >

                <?php if( !empty($requestParam['popup']) ) { ?>
                    <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
                <?php }else{ ?>
                    <input type="button" value="목록" class="btn btn-white" @click="window.history.back()" >
                <?php } ?>

            </div>
        </div>
    </form>

    <div class="row ">
        <div class="col-xs-12" style="padding:15px;">
            <div class="panel panel-default">
                <div class="panel-heading" style="border-bottom:none!important;">
                    <span style="font-size:15px;font-weight: bold">프로젝트번호 : </span>
                    <span style="font-size:15px;font-weight: bold" class="text-danger">{% project.projectNo %} ( {% project.projectTypeKr %} )</span>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="font-16"><b class="text-danger">이노버 희망 납기일 : {% project.msDeliveryDt %} ( <span v-html="project.customerDeliveryRemainDt"></span> )</b></span>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    등록일시 : <span>{% project.regDt %}</span>

                    <div class="pull-right">
                        <div class="form-inline">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--탭화면-->
    <div id="tabViewDiv">
        <ul class="nav nav-tabs mgb15" role="tablist">
            <?php if(!$imsProduceCompany) {?>
            <li role="presentation" :class="'basic' === tabMode?'active':''" @click="changeTab('basic')" id="tab2">
                <a href="#tab2" data-toggle="tab" >기본정보</a>
            </li>
            <li role="presentation" :class="'style' === tabMode?'active':''" @click="changeTab('style')" id="tab3">
                <a href="#tab3" data-toggle="tab" >스타일 관리 ({% productList.length %})</a>
            </li>
            <?php }?>
            <li role="presentation" :class="'comment' === tabMode?'active':''" @click="changeTab('comment')" id="tab4">
                <a href="#tab4" data-toggle="tab" >프로젝트 코멘트</a>
            </li>
            <?php if(!$imsProduceCompany) {?>
            <li role="presentation" :class="'meeting' === tabMode?'active':''" @click="changeTab('meeting')" id="tab1">
                <a href="#tab1" data-toggle="tab" >고객 코멘트</a>
            </li>
            <?php }?>
            <li role="presentation" :class="'todo' === tabMode?'active':''" @click="changeTab('todo')" id="tab6">
                <a href="#tab6" data-toggle="tab" >TODO LIST</a>
            </li>
            <li role="presentation" :class="'oldbasic' === tabMode?'active':''" @click="changeTab('oldbasic')" id="tab5">
                <a href="#tab5" data-toggle="tab" >기본정보</a>
            </li>
        </ul>
    </div>

    <div class="row" v-show="'basic' === tabMode">
        <?php include 'template/ims_project_view_basic.php'?>
    </div>

    <div class="row" v-show="'meeting' === tabMode">
        <?php include 'template/ims_project_view_meeting.php'?>
    </div>

    <div class="row" v-show="'style' === tabMode">
        <?php include 'template/ims_project_view_style.php'?>
    </div>

    <div class="row" v-show="'comment' === tabMode">
        <?php include 'template/ims_project_view_comment.php'?>
    </div>

    <div class="row" v-show="'oldbasic' === tabMode">
        <?php include 'template/ims_project_view_old_basic.php'?>
    </div>

    <div class="row" v-show="'todo' === tabMode">
        <?php include 'template/ims_project_view_todo.php'?>
    </div>

</div>

<?php include 'script/ims_project_view_script3.php'?>