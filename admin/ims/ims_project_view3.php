<?php include 'library_all.php'?>
<?php include 'library.php'?>

<style>
    .page-header { margin-bottom:10px };
</style>

<section id="imsApp" class="project-view">
    <form id="frm">
        <div class="page-header js-affix">
            <h3>
                <span class="text-blue">{% items.customerName %} {% project.projectYear %} {% project.projectSeason %}</span> 프로젝트 상세정보
                <span class="text-danger" style="font-weight:normal" v-show="!$.isEmpty(project.projectNo)">({% project.projectStatusKr %}-{% project.projectNo %})</span>
            </h3>
            <div class="btn-group">

                <input type="button" value="수정" class="btn btn-white btn-red btn-red-line2" >
                
                <input type="button" value="변경이력" class="btn btn-white" @click="openUpdateHistory(project.sno, 'project')" >
                <?php if( !empty($requestParam['popup']) ) { ?>
                    <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
                <?php }else{ ?>
                    <input type="button" value="목록" class="btn btn-white" @click="window.history.back()" >
                <?php } ?>
                <?php if($isDev) { ?>
                    <div class="btn btn-white" @click="copyProject(project.sno)" style="padding-top:7px">
                        <i class="fa fa-files-o" aria-hidden="true"></i> 프로젝트 복사
                    </div>
                <?php } ?>
            </div>
        </div>
    </form>

    <div class="row ">
        <div class="col-xs-12" style="padding-bottom:0!important;">
            <div class="panel panel-default">
                <div class="panel-heading" style="border-bottom:none!important;">
                    <span style="font-size:15px;font-weight: bold">프로젝트번호 : </span>
                    <span style="font-size:15px;font-weight: bold" class="text-danger">{% project.projectNo %} (신규)</span>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="font-16"><b class="text-danger">고객납기일 : {% project.customerDeliveryDt %} ( <span v-html="project.customerDeliveryRemainDt"></span> )</b></span>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span><b>고객발주일: {% project.customerOrderDt %}</b></span>
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
            <li role="presentation" :class="'basic' === tabMode?'active':''" @click="changeTab('basic')" id="tab2">
                <a href="#tab2" data-toggle="tab" >기본정보</a>
            </li>
            <li role="presentation" :class="'style' === tabMode?'active':''" @click="changeTab('style')" id="tab3">
                <a href="#tab3" data-toggle="tab" >스타일 관리 ({% productList.length %})</a>
            </li>
            <li role="presentation" :class="'comment' === tabMode?'active':''" @click="changeTab('comment')" id="tab4">
                <a href="#tab4" data-toggle="tab" >프로젝트 코멘트</a>
            </li>
            <li role="presentation" :class="'meeting' === tabMode?'active':''" @click="changeTab('meeting')" id="tab1">
                <a href="#tab1" data-toggle="tab" >고객 코멘트</a>
            </li>
            <li role="presentation" :class="'todo' === tabMode?'active':''" @click="changeTab('todo')" id="tab5">
                <a href="#tab5" data-toggle="tab" >TODO LIST</a>
            </li>
            <li role="presentation" :class="'oldbasic' === tabMode?'active':''" @click="changeTab('oldbasic')" id="tab5">
                <a href="#tab5" data-toggle="tab" >전체정보</a>
            </li>
        </ul>
    </div>

    <section class="row" v-show="'basic' === tabMode">
        <?php include 'template/ims_project_view_basic_tmp.php'?>
    </section>

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

</section>

<?php include 'script/ims_project_view_script3.php'?>

