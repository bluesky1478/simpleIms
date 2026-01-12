<?php include 'library_all.php'?>
<?php include 'library.php'?>
<?php include 'library_nk.php'?>

    <section id="imsApp">

        <div class="page-header form-inline crm">
            <h3>{% tabList[tabMode] %}  ({% customer.customerName %}社)</h3>
            <form id="formMemberSearch" novalidate="novalidate">
                <div class="pull-right pdr0">
                    <input type="button" class="btn close" value="x" @click="self.close()">
                </div>
            </form>
        </div>

        <div class="relative">
            <ul class="nav nav-tabs mgb20" role="tablist">
                <li role="presentation" :class="tabKey === tabMode?'active':''" @click="changeTab(tabKey)" v-for="(tabInfo, tabKey) in tabList">
                    <a href="#" data-toggle="tab" >{% tabInfo %}</a>
                </li>
            </ul>

            <!--고객 기본정보에서 수정/저장 버튼-->
            <div v-show="(('basic' === tabMode) || ('mall' === tabMode) || ('meeting' === tabMode) || ('confirm' === tabMode))">
                <div class="btn btn-red" style="position: absolute;top:7px;right:0" v-show="!isModify" @click="isModify = true">
                    수정
                </div>

                <div style="position: absolute;top:7px;right:0" class="dp-flex dp-flex-gap5">
                    <div class="btn btn-red"  v-show="isModify" @click="save();">저장</div>
                    <div class="btn btn-white"  v-show="isModify" @click="isModify = false">수정취소</div>
                </div>
            </div>
        </div>

        <div class="row" v-show="'basic' === tabMode">
            <?php include 'template/customer_view_basic_v2.php'?>
        </div>
        <div class="row" v-show="'style' === tabMode">
            <?php include 'template/customer_view_style.php'?>
        </div>
        <div class="row" v-show="'comment' === tabMode">
            <?php include 'template/ims_view_cust_comment.php'?>
        </div>
        <div class="row" v-show="'project' === tabMode">
            <?php include 'template/customer_view_project.php'?>
        </div>
        <div class="row" v-show="'mall' === tabMode">
            <?php include 'template/customer_view_mall.php'?>
        </div>
        <div class="row" v-show="'stored' === tabMode">
            <?php include 'template/customer_view_stored.php'?>
        </div>
        <div class="row" v-show="'sample' === tabMode">
            <?php include 'template/customer_view_sample.php'?>
        </div>
        <div class="row" v-show="'estimate' === tabMode">
            <?php include 'template/customer_view_estimate.php'?>
        </div>
        <div class="row" v-show="'prj_issue' === tabMode">
            <?php include 'template/customer_view_issue.php'?>
        </div>
    </section>

<?php include 'customer_view_script_v3.php'?>
