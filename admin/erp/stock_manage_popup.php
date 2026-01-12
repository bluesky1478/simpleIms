<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>
<?php include './admin/ims/library_bone.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">

    <form id="frm">
        <div class="page-header js-affix">
            <h3>상품 옵션 상세</h3>
            <input type="button" value="닫기" class="btn btn-white" @click="self.close()" >
        </div>
    </form>

    <div class="dp-flex" style="justify-content: space-between">
        <div class="font-18 bold noto">
            <?=$goodsNm?> 옵션 정보
            <div class="dp-flex dp-flex-gap10">
                <div class="notice-info" style="color:#000; font-size:13px;">
                    <span class="sl-blue bold">판매수량</span>:폐쇄몰 판매수량
                </div>
                <div class="notice-info" style="color:#000; font-size:13px;">
                    <span class="sl-blue bold">출고예약</span>:출고 예약 대기 건
                </div>
                <div class="notice-info" style="color:#000; font-size:13px;">
                    <span class="sl-blue bold">창고수량</span>:창고 현재 전산 수량
                </div>
            </div>

        </div>
        <!--(여기서도 출고 정보 보면 괜찮 ?)-->
        <div class="dp-flex">
            <div class="dp-flex" v-show="isAddCnt">
                <div class="dp-flex mini-picker">
                    입고일:
                    <date-picker v-model="addDt" value-type="format" format="YYYY-MM-DD" placeholder="입고일"></date-picker>
                </div>
                <div class="dp-flex">내용: <input type="text" v-model="addContents" class="form-control w-80p"></div>
            </div>
            <div class="btn btn-red" v-show="isAddCnt" @click="saveInputHistory()">입고이력등록진행</div>
            <div class="btn btn-white" v-show="isAddCnt" @click="isAddCnt=false">입고이력등록취소</div>
            <div class="btn btn-white" v-show="!isAddCnt" @click="isAddCnt=true">입고이력등록</div>
            <div class="btn btn-white" v-show="!isModify" @click="isModify=true">수정</div>
            <div class="btn btn-white" v-show="isModify" @click="refineCnt()">판매수량 정제</div>
            <div class="btn btn-red btn-red-line2" v-show="isModify" @click="save()">저장</div>
            <div class="btn btn-white" v-show="isModify" @click="isModify=false">취소</div>
        </div>
    </div>

    <div class="ta-r" v-show="isAddCnt">
        <div class="font-11">
            <i class="fa fa-info-circle" aria-hidden="true"></i>
            창고에 입고되지 않은 입고수량을 등록합니다. (분류패킹으로 직접 발송 시)
        </div>
    </div>


    <div class="mgt5">
        <table class="table table-rows ch-table table-th-height0 table-td-height0 table-pd-2 mgb10">
            <colgroup>
                <col class="w-2p"/><!--CHK-->
                <col class="w-4p"/><!--옵션번호-->
                <col class="w-9p"/><!--옵션명-->
                <col class="w-4p"/><!--판매수량-->
                <col class="w-4p"/><!--출고예약-->
                <col class="w-4p"/><!--창고수량-->
                <col class="w-4p"/><!--총입고-->
                <col class="w-4p"/><!--총출고-->
                <col class="w-3p"/><!--순서-->
                <col class="w-14p"/><!--코드-->
                <col class="w-4p"/><!--수량-->
                <col class="w-4p"/><!--다른상품에맵핑-->
                <col class="w-4p"/><!--분류-->
                <!--<col class="w-4p"/>시즌-->
                <!--<col class="w-7p"/>타입-->
                <col class="w-4p"/><!--색상기타-->
                <col class="w-4p"/><!--년도-->
                <col class="w-4p"/><!--입고-->
                <col class="w-4p"/><!--출고-->
                <col class="w-25p"/><!--코드품명-->
            </colgroup>
            <tr>
                <th>CK</th>
                <th>옵션번호</th>
                <th>옵션명</th>
                <th>판매수량</th>
                <th>출고예약</th>
                <th>창고수량</th>
                <th>총입고</th>
                <th>총출고</th>
                <th>순서</th>
                <th>연결코드</th>
                <th>수량</th>
                <th>나눔</th>
                <th>분류</th>
                <th>시즌</th>
                <!--<th>타입</th>-->
                <!--<th>색상</th>-->
                <th>생산</th>
                <th>입고</th>
                <th>출고</th>
                <th>코드명</th>
            </tr>
            <tbody v-for="(item, itemIndex) in mainData" class="hover-light" v-if="'all' === '<?=$requestParam['optionCode']?>' || ('o'+item.sno) == '<?=$requestParam['optionCode']?>'">
            <tr v-if="0 >= item['3pl'].length">
                <?php include 'stock_manage_popup_template1.php'?>
                <td colspan="99">
                    상품코드 미연결
                </td>
            </tr>
            <tr v-for="(tp, tpIndex) in item['3pl']" v-if="0 == tpIndex && item['3pl'].length > 0">
                <?php include 'stock_manage_popup_template1.php'?>
                <?php include 'stock_manage_popup_template2.php'?>
            </tr>
            <tr v-for="(tp, tpIndex) in item['3pl']" v-if="tpIndex > 0&& item['3pl'].length > 1">
                <?php include 'stock_manage_popup_template2.php'?>
            </tr>
            </tbody>
        </table>
    </div>
    
    <div>
        <div class="btn btn-blue-line" @click="link()">창고 코드 연결</div>
    </div>

    <div class="mgt20 ">

        <div class="relative">
            <ul class="nav nav-tabs mgb20" role="tablist">
                <li role="presentation" :class="tabKey === tabMode?'active':''" @click="changeTab(tabKey)" v-for="(tabInfo, tabKey) in tabList">
                    <a href="#" data-toggle="tab" >{% tabInfo %}</a>
                </li>
            </ul>
        </div>

        <div v-show="'inOutHistory' === tabMode">
            <?php include 'stock_manage_popup_inout.php'?>
        </div>
        <div v-show="'unLink' === tabMode">
            <?php include 'stock_manage_popup_unlink.php'?>
        </div>

    </div>

</section>

<script type="text/javascript">
    let listUpdateMulti = null;
    let listUpdateMultiOrigin = null;

    const goodsNo='<?=$requestParam['goodsNo']?>';
    const scmSno='<?=$scmSno?>';

    const tabList = {
        /*inOutCalc: '입/출고 월집계',*/
        inOutHistory: '입/출고이력',
        unLink: '미연결 코드',
    };

    //입/출고이력 검색 기본값
    const ioSearchDefault = {
        multiKey : [{
            key : 'ioHis.thirdPartyProductCode',
            keyword : '<?=gd_isset($requestParam['keyword'][0],'')?>',
        }],
        multiCondition : 'OR',
        scmSno : scmSno,
        goodsNo : goodsNo,
        inOutType : 2,
        startDt : '',
        endDt : '',
        sort : 'IO1,desc',
        page : 1,
        pageNum : 20,
    };
    //미연결 검색 기본값
    const unlinkSearchDefault = {
        multiKey : [{
            key : '<?=gd_isset($requestParam['key'][0],'productName')?>',
            keyword : '',
        }],
        multiCondition : 'OR',
        scmSno : scmSno,
        goodsNo : goodsNo,
        attr : [{1:'',2:'',3:'',4:'',5:''}],
    };

    //초기
    const viewMethods = {
        save : ()=>{
            $.imsPost('saveGoods3plProduct',{
                data:vueApp.mainData,
                delCode:vueApp.delCode,
            }).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    $.msg('저장 되었습니다.','','success').then(()=>{
                        location.reload();
                    });
                });
            });
        },
        add : (itemIndex)=>{
            vueApp.mainData[itemIndex]['3pl'].push({sno:'',code:''});
        },
        /**
         * 연결코드 삭제
         * @param itemIndex
         * @param tpIndex
         * @param tpSno
         */
        unlink : (itemIndex,tpIndex,tpSno)=>{
            vueApp.delCode.push(tpSno);
            vueApp.mainData[itemIndex]['3pl'].splice(tpIndex,1);
        },
        link : ()=>{
            if($.isEmpty(vueApp.linkGoods) || 0 >= vueApp.linkCode.length ){
                $.msg('상품 또는 연결된 코드가 선택되지 않음','','warning');
            }else{
                $.imsPost('link3plCode',{
                    goodsNo:goodsNo,
                    linkGoods:vueApp.linkGoods,
                    linkCode:vueApp.linkCode,
                }).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        $.msg('저장 되었습니다.','','success').then(()=>{
                            location.reload();
                        });
                    });
                });
            }
        },
        /**
         * 수량 정제
         */
        refineCnt : ()=>{
            vueApp.mainData.forEach((data)=>{
                const totalCnt = data.realCnt - data.reserveCnt;
                data.stockCnt = 0 > totalCnt ? 0 : totalCnt;
            });
        },
        ioConditionReset : () =>{
            vueApp.ioSearchCondition = $.copyObject(ioSearchDefault);
        },
        unlinkConditionReset : () =>{
            vueApp.searchCondition = $.copyObject(unlinkSearchDefault);
        },
        searchCodeHistory : (inOutType, code) =>{
            vueApp.ioConditionReset(); //Reset
            vueApp.ioSearchCondition.inOutType = inOutType;
            vueApp.ioSearchCondition.multiKey[0]['key'] = 'ioHis.thirdPartyProductCode';
            vueApp.ioSearchCondition.multiKey[0]['keyword'] = code;
            getStockInOutList(); //입출고 이력 가져오기
            vueApp.tabMode = 'inOutHistory';//Tab이동
            window.scrollTo(0, document.body.scrollHeight);
        },
        listDownload : ()=>{
            const downloadSearchCondition = $.copyObject( vueApp.ioSearchCondition );
            const queryString = $.objectToQueryString(downloadSearchCondition);
            location.href=`stock_manage_popup.php?simple_excel_download=1&` + queryString;
        },
        saveInputHistory : ()=>{
            const params = {
                addDt : vueApp.addDt,
                addContents : vueApp.addContents,
                addCode : [],
            };
            let isContinue = false;
            vueApp.mainData.forEach((each)=>{
                for(const idx in each['3pl']){
                    if( !$.isEmpty(each['3pl'][idx].inputHisCnt) && each['3pl'][idx].inputHisCnt > 0 ){
                        params.addCode.push({
                            code : each['3pl'][idx].code,
                            qty : each['3pl'][idx].inputHisCnt,
                        });
                        isContinue=true;
                    }
                }
            });
            if( isContinue ){
                //console.log(params);
                $.imsPost('saveInputHistory',params).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        $.msg('등록이 완료되었습니다.', "", "success").then(()=>{
                            location.reload();
                        });
                    });
                });
            }else{
                $.msg('등록 코드/수량 없음', "", "warning");
            }
        }
    };

    $(appId).hide();


    $(()=>{
        const serviceData = {};
        //화면 사용 데이터 설정
        ImsBoneService.setData(serviceData,{
            delCode : [],
            isModify : false,
            isAddCnt : false,
            addDt : '',
            addContents : '',
            tabMode : 'inOutHistory',
            searchCondition : $.copyObject(unlinkSearchDefault),
            ioSearchCondition : $.copyObject(ioSearchDefault),
            unlinkList : [], //미연결 코드 리스트
            linkGoods : '',
            linkCode : [],

            //inoutPageHtml : '',
            stockInOutData : {
                pageEx : '',
                page : {
                    recode : {amount:0},
                },
                list : []
            }, //입출고 데이터
        });
        ImsBoneService.setMethod(serviceData, viewMethods);
        ImsBoneService.setMounted(serviceData, ()=>{
            if( vueApp.mainData.length > 0 ){
                <?php if('y' === $imsPageReload) { ?>
                    try{
                        vueApp.searchCondition = $.copyObject(JSON.parse($.cookie('unlinkSearchCondition')));
                    }catch (e){
                        vueApp.searchCondition = $.copyObject(unlinkSearchDefault);//오류시 초기화
                        $.removeCookie('unlinkSearchCondition');
                    }
                <?php } ?>
                getUnLinkData(); //미연결 상품 가져오기
                getStockInOutList(); //입출고 이력 가져오기
            }
        });
        //초기화
        ImsBoneService.serviceStart('getGoodsStockTotalInfoDetail',{goodsNo:goodsNo}, serviceData);
    });


    /**
     * 미연결 코드 데이터
     * @returns {Promise<*>}
     */
    async function getUnLinkData() {
        //검색값을 저장.
        const promise = $.imsPost('getGoodsStockUnlink',vueApp.searchCondition);
        promise.then((data)=>{
            $.imsPostAfter(data,(data)=>{
                vueApp.unlinkList = data; //메인데이터 갱신
                tabList.unLink = '미연결 코드(' + vueApp.unlinkList.length + ')';
            });
        });
        return await promise;
    }

    /**
     * 입출고 리스트를 가져온다.
     * @returns {Promise<*>}
     */
    async function getStockInOutList() {
        //console.log( vueApp.ioSearchCondition );
        //검색값을 저장.
        const promise = $.imsPost('getStockInOutList',vueApp.ioSearchCondition);
        promise.then((data)=>{
            $.imsPostAfter(data,(data)=>{
                vueApp.stockInOutData = data;
                //Paging Event
                vueApp.$nextTick(function () {
                    $('#inout-page .pagination').find('a').each(function(){
                        $(this).off('click').on('click',function(){
                            vueApp.ioSearchCondition.page = $(this).data('page');
                            getStockInOutList();
                        });
                    });
                    //refreshAfterFnc();
                });
                //console.log('stock', vueApp.stockInOutData );
            });
        });
        return await promise;
    }

    /**
     * 출고 예약 열기
     * @param goodsNo
     * @param optionCode
     */
    function openReserved(goodsNo, optionCode){
        const win = popup({
            url: `<?=$myHost?>/erp/stock_reserved_popup.php?goodsNo=${goodsNo}&optionCode=${optionCode}`,
            target: `ims-stock-reserved-${goodsNo}`,
            width: 1000,
            height: 800,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }
    
</script>
