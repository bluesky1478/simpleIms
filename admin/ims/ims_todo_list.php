<?php include 'library_all.php'?>
<?php include 'library.php'?>

<section id="imsApp" class="project-view">

    <form id="frm">
        <div class="page-header js-affix">
            <h3>ToDo List - {% getTabName(tabMode) %}</h3>
            <div class="btn-group">
                <input type="button" value="기안 등록" class="btn btn-red js-register" v-if="'approval' === tabMode" @click="openApprovalWrite()" />
                <input type="button" value="요청 등록" class="btn btn-red js-register" v-if="'request' === tabMode" @click="openTodoRequestWrite()" />
            </div>
        </div>

    </form>

    <!-- TODO 검색 화면
    //고객명, 프로젝트번호, 스타일코드
    //연도, 시즌
    -->

    <!--탭화면-->
    <div id="tabViewDiv" class="display-none">
        <ul class="nav nav-tabs mgb30" role="tablist">
            <li role="presentation" :class="'approval' === tabMode?'active':''" @click="changeTab('approval')" id="ims-tab-approval">
                <a href="#ims-tab-approval" data-toggle="tab" >결재관리</a>
            </li>
            <li role="presentation" :class="'inbox' === tabMode?'active':''" @click="changeTab('inbox')" id="ims-tab-request">
                <a href="#ims-tab-request" data-toggle="tab" >받은요청</a>
            </li>
            <li role="presentation" :class="'request' === tabMode?'active':''" @click="changeTab('request')" id="ims-tab-inbox">
                <a href="#ims-tab-inbox" data-toggle="tab" >나의요청</a>
            </li>
        </ul>
    </div>


    <div class="row" v-show="'approval' === tabMode"></div>

    <div class="row" v-show="'request' === tabMode">
        <?php include 'template/ims_todo_list_request.php'?>
    </div>

    <div class="row" v-show="'inbox' === tabMode">
        <?php include 'template/ims_todo_list_response.php'?>
    </div>

    <div style="margin-bottom:150px"></div>

</section>

<?php include 'script/ims_todo_list_script.php'?>

