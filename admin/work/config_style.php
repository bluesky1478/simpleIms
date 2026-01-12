
<?php include 'import_lib.php'?>

<div class="page-header js-affix">
    <h3><?= end($naviMenu->location); ?></h3>
    <div class="btn-group">
        <input type="button" value="스타일등록" class="btn btn-red-line btn-prd-reg"/>
    </div>
</div>


<div id="work-app" style="width:50%">

    <div class="search-detail-box">
        <table class="table table-cols">
            <colgroup>
                <col class="width-md"/>
                <col>
                <col />
                <col/>
            </colgroup>
            <tr>
                <th>스타일명</th>
                <td colspan="3">
                    <div class="form-inline">
                        <input type="text" class="form-control" placeholder="스타일명 검색" v-model="search.styleName"  @keyup.enter="getSearchList" />
                    </div>
                </td>
            </tr>
            <tr>
                <th>시즌</th>
                <td colspan="3">
                    <?php foreach($SEASON_TYPE as $seasonKey => $season) { ?>
                        <label class="radio-inline">
                            <input type="radio" value="<?=$seasonKey?>"  name="season" v-model="search.season"  /><?=$season?>
                        </label>
                    <?php } ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="table-btn">
        <input type="button" value="검색" class="btn btn-lg btn-black" @click="getSearchList" id="btn-search">
    </div>


    <table class="table table-rows accept-config-list" >
        <colgroup>
            <col class="width-xs"/>
            <col class="width-md"/>
            <col />
            <col class="width-md"/>
            <col class="width-xs"/>
        </colgroup>
        <thead>
        <tr>
            <th>번호</th>
            <th>시즌</th>
            <th>스타일명</th>
            <th>등록일/수정일</th>
            <th>수정</th>
        </tr>
        </thead>
        <tbody class="order-list display-none" id="table-data" >
            <tr v-for="(item, index) in items" class="data-row">
                <td class="font-num text-center">
                    {% index + 1 %}
                </td>
                <td class="text-center">
                    {% item.seasonKr %}
                </td>
                <td class="text-center">
                    <div @click="openPopupStyle(item.sno)" style="cursor: pointer">{% item.styleName %}</div>
                </td>
                <!--<td class="text-left pdl10">
                    <small>{% item.specCheckList %}</small>
                </td>-->
                <td class="text-center ">
                    <div class="">
                        {% item.regDt %}
                    </div>
                    <div class="text-muted">
                        {% item.modDt %}
                    </div>
                </td>
                <td class="text-center">
                    <div class="btn btn-sm btn-white" @click="openPopupStyle(item.sno)">수정</div>
                </td>
            </tr>
            <tr v-if="0 >= items.length">
                <td colspan="99" class="text-center">데이터가 없습니다.</td>
            </tr>
        </tbody>
        <tbody class="order-list" id="search-info">
            <tr>
                <td colspan="99" class="text-center">데이터 조회 중입니다...</td>
            </tr>
        </tbody>
    </table>

    <div class="table-action clearfix">
        <div class="pull-left"></div>
        <div class="pull-right"></div>
    </div>

</div>


<div id="layerDim">
    <div class="sl-pre-loader">
        <div class="throbber-loader"> </div>
    </div>
</div>


<script type="text/javascript">

    let workApp = null;

    let clickSearchBtn = ()=>{
        $('#btn-search').click();
    }

    let openPopup = function(openSno){
        let sno = '';
        if(!$.isEmpty(openSno)){
            sno = '?sno=' + openSno;
        }
        window.open('./popup/popup_style.php' + sno , 'popup_style', 'width=1400, height=910, resizable=yes, scrollbars=yes');
    }

    let getList = async function(searchData){
        if( $.isEmpty(searchData) ){
            searchData  = {mode : 'getStyle'}
        }else{
            searchData.mode = 'getStyle';
        }
        return await $.postWork(searchData);
    };

    let searchBegin = () =>{
        $('#layerDim').removeClass('display-none');
        $('#search-info').removeClass('display-none');
        $('#table-data').addClass('display-none');
    }
    let searchEnd = () =>{
        $('#layerDim').addClass('display-none');
        $('#search-info').addClass('display-none');
        $('#table-data').removeClass('display-none');
    }

    let vueInit = (jsonData) =>{
        console.log('vueInit');
        console.log(jsonData);
        workApp = new Vue({
            el: '#work-app',
            delimiters: ['{%', '%}'],
            data : {
                items:jsonData.data,
                search : {
                    styleName : '',
                    season : 0,
                }
            },
            methods : {
                getSearchList : function(){
                    let myApp = this;
                    searchBegin();
                    getList(this.search).then((jsonData)=>{
                        myApp.items = jsonData.data; //교체
                        searchEnd();
                    });
                },
                openPopupStyle : function(sno){
                    openPopup(sno);
                },
            },
            mounted : function(){
                this.$nextTick(function () {
                    searchEnd();
                });
            },
        });
    };

    //VUE LIST Setting
    getList().then((jsonData)=>{
        $('#layerDim').removeClass('display-none');
        vueInit(jsonData);
    });

    $(function(){
        //요청사항 등록
        $('#reqReg').click(function(){
            var params = $('#frmReg').serialize();
            $.post('./work_ps.php', params, function (result) {
            }).done(function(result){
                location.reload();
            });
        });

        $('.btn-prd-reg').click(function(){
            openPopup();
        });

    });

</script>


