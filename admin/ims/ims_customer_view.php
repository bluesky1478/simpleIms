<?php include 'library_all.php'?>
<?php include 'library.php'?>

    <section id="imsApp">

        <div class="page-header form-inline crm">
            <h3>{% tabList[tabMode] %}  ({% customer.customerName %}社) </h3>
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


        <div class="row" v-show="'prod_wear' === tabMode">
            <?php include 'template/ims_customer_view_prod_wear.php'?>
        </div>

    </section>

<?php include 'ims_customer_view_script.php'?>