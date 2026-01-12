<script type="text/javascript">
    //초기 ScmSno
    const listPrefix = 'stockManage';
    const initScmSno = <?=$firstScm?>;

    const defaultMultiKey1 = {
        key : '<?=gd_isset($requestParam['key'][0],'goods.goodsNm')?>',
        keyword : '<?=gd_isset($requestParam['keyword'][0],'')?>',
    };
    const defaultMultiKey2= {
        key : 'goods.goodsNo',
        keyword : '',
    };

    const commonSearchDefault = {
        scmSno : initScmSno,
        scmCate : ['', '', '', ''],
        multiKey : [
            $.copyObject(defaultMultiKey1),
            $.copyObject(defaultMultiKey2),
        ],
        multiCondition : 'OR',
    };

    function openDetail(goodsNo, optionCode){
        const win = popup({
            url: `<?=$myHost?>/erp/stock_manage_popup.php?goodsNo=${goodsNo}&optionCode=${optionCode}`,
            target: `ims-stock-detail-${goodsNo}`,
            width: 1350,
            height: 900,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win.focus();
    }

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

    const viewMethods = {
        setInitScm:(scmSno)=>{
            $(".scm-choice").prop("checked", false);
            $('#rdo-scm-'+scmSno).prop("checked", true);
        },
        changeScm:(scmSno)=>{
            $('#rdo-scm-'+scmSno).click();
            vueApp.conditionReset();
            vueApp.searchCondition.scmSno = scmSno;
            getMainData();
        },
        changeCate:()=>{
            console.log(vueApp.searchCondition.scmCate);
        },
        conditionReset : () =>{
            vueApp.searchCondition = $.copyObject(commonSearchDefault);
        },
        refreshStoredStock : ()=>{
            $.msgConfirm('창고 재고를 업데이트 하시겠습니까?', '').then((confirmData)=> {
                if (true === confirmData.isConfirmed) {
                    $('#layerDim').show();
                    $.postAsync('./erp_ps.php',{
                        mode:'syncProduct',
                    }).then((data)=>{
                        if( 200 === data.code ){
                            $.msg('업데이트 완료.','','info').then(()=>{
                                location.reload();
                            });
                        }
                    });

                }
            });
        },
        //재고 코멘트 등록
        saveStockComment:(scmNo, each)=>{
            $.imsPost('saveStockComment',{scmNo:scmNo, comment:each.comment}).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    each.comment = '';
                    getStockComment();
                });
            });
        },
        //재고 코멘트 삭제
        delStockComment:(eachSno)=>{
            $.msgConfirm('코멘트를 삭제 하시겠습니까?','').then(function(result) {
                if (result.isConfirmed) {
                    const params = {sno:eachSno};
                    console.log('debug',params);
                    $.imsPost('delStockComment',params).then((data)=>{
                        $.imsPostAfter(data,(data)=>{
                            console.log(data);
                            getStockComment();
                        });
                    });
                }
            });
        },
        procAsianaDelivery:(eachSno)=>{
            $.msgConfirm('발송 처리 하시겠습니까?','').then(function(result) {
                if (result.isConfirmed) {
                    $.imsPost('procAsianaDelivery',{}).then((data)=>{
                        $.imsPostAfter(data,(data)=>{
                            $.msg('발송완료','','success').then(()=>{
                                location.reload();
                            });
                        });
                    });
                }
            });
        },
    }

    /**
     * 코멘트 데이터 갱신
     */
    async function getStockComment() {
        //검색값을 저장.
        $.imsPost('getStockComment',{}).then((data)=>{
            $.imsPostAfter(data,(data)=>{
                console.log(data); //코멘트 갱신
                vueApp.commentMap = data;
                //getUnLinkData(vueApp.searchCondition.scmSno); //미연결 상품 가져오기
            });
        });
    }

    /**
     * 메인데이터 갱신
     */
    async function getMainData() {
        //검색값을 저장.
        $.cookie(listPrefix+'ImsSearchCondition', JSON.stringify(vueApp.searchCondition));
        const promise = $.imsPost('getGoodsStockTotalInfo',vueApp.searchCondition);
        promise.then((data)=>{
            $.imsPostAfter(data,(data)=>{
                vueApp.mainData = data; //메인데이터 갱신
                //getUnLinkData(vueApp.searchCondition.scmSno); //미연결 상품 가져오기
            });
        });
        return await promise;
    }

    /**
     * 미연결 데이터 가져오기
     */ 
    async function getUnLinkData(scmSno) {
        const promise = $.imsPost('getGoodsStockUnlink',{'scmSno':scmSno});
        promise.then((data)=>{
            $.imsPostAfter(data,(data)=>{
                vueApp.unlinkList = data; //메인데이터 갱신
            });
        });
        return await promise;
    }

    async function detailDownload() {
        const downloadSearchCondition = $.copyObject( vueApp.searchCondition );
        const queryString = $.objectToQueryString(downloadSearchCondition);
        location.href=`stock_manage.php?simple_excel_download=1&` + queryString;
    }

    async function summaryDownload() {
        const fileName = '재고현황';
        const table = document.getElementById('stock-simple-table');

        const wb = new ExcelJS.Workbook();
        const ws = wb.addWorksheet('Sheet1');

        const occupied = new Set();
        const colUnits = [];
        let excelRow = 1;

        const rows = Array.from(table.rows);
        for (const tr of rows) {
            let excelCol = 1;
            const cells = Array.from(tr.cells);

            for (const td of cells) {
                while (occupied.has(`${excelRow},${excelCol}`)) excelCol++;
                const rs = td.rowSpan || 1;
                const cs = td.colSpan || 1;
                const text = (td.textContent || '').trim();
                const cell = ws.getCell(excelRow, excelCol);


                // 정렬(가로/세로 가운데)
                cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };

                // ▼ B열(2)만 텍스트 형식으로 강제
                if (excelCol === 2) {
                    cell.value = String(text);
                    cell.numFmt = '@';
                }else if (excelCol === 3 && excelRow > 1) {
                    cell.alignment = { horizontal: 'left', vertical: 'middle', wrapText: true };
                    cell.value = String(text);
                } else {
                    const raw = (text ?? '').trim();

                    // 퍼센트: "12.3%" 등
                    if (/^[-+]?[\d.,]+%$/.test(raw)) {
                        const num = Number(raw.replace(/,/g, '').replace('%', '')) / 100;
                        cell.value = num;
                        cell.numFmt = '0.##%';

                        // 숫자: 천단위 콤마/소수 지원 ("1,234" "1234.56" "-7,890.1")
                    } else if (/^[-+]?((\d{1,3}(,\d{3})+)|\d+)(\.\d+)?$/.test(raw)) {
                        const num = Number(raw.replace(/,/g, ''));
                        cell.value = num;
                        cell.numFmt = raw.includes('.') ? '#,##0.##########' : '#,##0';

                        // 그 외는 문자열로
                    } else {
                        cell.value = String(text);
                    }
                }

                const greyFill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFEDEDED' } };
                if('basic' === vueApp.showType){
                    if( excelCol >= 7 && ( 0===excelRow%2 ) ){
                        cell.fill = greyFill;
                    }
                }else{
                    if( excelCol >= 7 && ( 2===excelRow || 2===(excelRow%7)) ){
                        cell.fill = greyFill;
                    }
                }

                // 열 너비 계산 (전각=2, 공백=0.75, 가장 긴 줄 기준) + 여백 2
                const need = measureExcelUnits(text) + 2;
                if (cs > 1) {
                    const per = Math.ceil(need / cs);
                    for (let j = 0; j < cs; j++) {
                        const idx = excelCol + j;
                        colUnits[idx] = Math.max(colUnits[idx] || 0, per);
                    }
                } else {
                    colUnits[excelCol] = Math.max(colUnits[excelCol] || 0, need);
                }


                if (rs > 1 || cs > 1) {
                    ws.mergeCells(excelRow, excelCol, excelRow + rs - 1, excelCol + cs - 1);
                    for (let r = excelRow; r < excelRow + rs; r++) {
                        for (let c = excelCol; c < excelCol + cs; c++) {
                            if (r === excelRow && c === excelCol) continue;
                            occupied.add(`${r},${c}`);
                        }
                    }
                }
                excelCol += cs;
            }
            excelRow++;
        }

        // 열 너비 적용
        const maxIndex = Math.max(...Object.keys(colUnits).map(Number), 1);
        const MIN_W = 8, MAX_W = 80;
        ws.columns = Array.from({ length: maxIndex }, (_, i) => ({
            key: `C${i+1}`,
            width: Math.min(Math.max(colUnits[i+1] || MIN_W, MIN_W), MAX_W)
        }));

        // (선택) 행 높이 기본값
        for (let r = 1; r < excelRow; r++) ws.getRow(r).height = 20;

        const buf = await wb.xlsx.writeBuffer();
        saveAs(new Blob([buf]), `${fileName}.xlsx`);
    }

    const tabList = {
        list: '업체별 관리',
        report: '리포트',
    };

    $(()=>{
        const serviceData = {};
        let initSearchParams = $.copyObject(commonSearchDefault);
        initSearchParams.scmSno=initScmSno;

        <?php if('y' === $imsPageReload) { ?>
        try{
            initSearchParams = $.copyObject(JSON.parse($.cookie(listPrefix+'ImsSearchCondition')));
        }catch(e){
            $.removeCookie(listPrefix+'ImsSearchCondition');
        }
        <?php } ?>

        const tabMode = $.isEmpty($.cookie('stockManageTab'))?'list':$.cookie('stockManageTab');

        //화면 사용 데이터 설정
        ImsBoneService.setData(serviceData,{
            rootParentId : '<?=$scmMapCate[$firstScm]?>',
            scmMap : JSON.parse('<?=json_encode($scmMap)?>'),
            scmMapCate : JSON.parse('<?=json_encode($scmMapCate)?>'),
            searchCondition : $.copyObject(commonSearchDefault),
            unlinkList : [],
            reportList : [],
            commentMap : [],
            thirdPartyCategory : [],
            latestUpdateDate : '-',
            showType : 'basic',
            reportComment : false,
            tabMode : tabMode,
        });
        ImsBoneService.setMethod(serviceData, viewMethods);
        ImsBoneService.setMounted(serviceData, (vueInstance)=>{
            vueApp.searchCondition = initSearchParams;
            vueApp.setInitScm(vueApp.searchCondition.scmSno);

            $.imsPost2('getLatestUpdateDate',{},(data)=>{
                vueApp.latestUpdateDate = data;
                $('#latestUpdateInfo').appendTo("#latestUpdateInfoTarget");
            });

            $.imsPost2('getStockReport',{},(data)=>{
                console.log(data.mainReport);
                vueApp.reportList = data.mainReport;
            });

            getStockComment();

        });

        //초기화
        ImsBoneService.serviceStart('getGoodsStockTotalInfo',initSearchParams, serviceData);
    });

    /*
    1. 데이터를 가져온다.
    2. 가져와야지 가져온걸로 Vue셋팅 가져온다.
    1. 그냥 아무것도 없는 상태에서 뷰셋팅 한다.
    2. 마운트할 때 리프레시한다.
    3. 메인데이터는 평소에도 그냥 리프레시만 하면 갱신되게 한다.
     */
</script>