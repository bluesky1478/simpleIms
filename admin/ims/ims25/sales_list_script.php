<script type="text/javascript">

    $('title').html('영업 - 프로젝트리스트');

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const mainListPrefix = 'project';
    //전체 검색 조건
    listSearchDefaultData.pageNum = 100;
    listSearchDefaultData.sort = 'P1,desc'; //정렬 기본
    //진행타입
    listSearchDefaultData.bidType2 = 'all';
    listSearchDefaultData.bizPlanYn = 'all';
    //주문상태
    /*listSearchDefaultData.orderProgressChk = [
        '15','20','30','31','40','41','50','60'
    ];*/
    listSearchDefaultData.orderProgressChk = [
        '150'
    ];
    //영업상태
    listSearchDefaultData.salesStatusChk = [];
    listSearchDefaultData.extDesigner = "";
    listSearchDefaultData.designWorkType = "all";
    listSearchDefaultData.salesManagerSno = "all";
    listSearchDefaultData.designManagerSno = "all";

    //다른 탭 검색 조건
    const tabConditionDefault = {
        'tab1': { //입찰관리 (프로젝트관리)
            orderProgressChk: ['10'], //입찰관리 ( 진행 타입 : 입찰 , 영업대기10 -   ) , 11보류 제외
            sort: 'P1,desc', //등록일순
            multiCondition : 'OR',
            bidType2 : 'all',
            bizPlanYn : 'all',
            multiKey : [
                $.copyObject(defaultMultiKey1),
                $.copyObject(defaultMultiKey2),
            ],
        },
        'tab2': {
            orderProgressChk: ['10'], //영업대기
            sort: 'P1,desc', //등록일순
            multiCondition : 'OR',
            bidType2 : 'all',
            bizPlanYn : 'all',
            multiKey : [
                $.copyObject(defaultMultiKey1),
                $.copyObject(defaultMultiKey2),
            ],
        },
    };

    let listUpdateMulti = [];
    let listUpdateMultiOrigin = [];

    const getListData = async (params, listPrefix)=>{
        //console.log('List Prefix : ', listPrefix);
        console.log('리스트 갱신 함수 실행', params);
        let oPost = $.imsPost('getSalesList', params);
        oPost.then((data)=>{
            $.imsPostAfter(data, (data)=> {
                listUpdateMulti = data.listUpdateMulti;
                listUpdateMultiOrigin = data.listUpdateMultiOrigin;
            });
        });
        return oPost;
    };

    const getSalesAnotherList = async (tabName)=>{
        console.log('영업 다른 리스트 갱신 함수 실행', tabName);
        if(!$.isEmpty(vueApp.anotherList[tabName])){
            $.imsPost('getSalesAnotherList', vueApp.anotherList[tabName].condition).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    console.log('결과',data);
                    vueApp.anotherList[tabName].field = data.field;
                    vueApp.anotherList[tabName].list = data.list;
                });
            });
        }
    };
    $(()=>{

        const serviceData = {};
        //console.log('검색 기본 조건 : ',listSearchDefaultData );

        //기초 데이터 구조
        const initData = {
            visibleTodayTodo : true,
            isModify : false, //수정버튼 표시여부
            tabMode : 'tab2',
            anotherList : {},
        };

        for(let i=1; 2>=i; i++){
            const listKey = 'tab'+i;
            initData.anotherList[listKey] = {
                field : [],
                list : [],
                condition : $.copyObject(tabConditionDefault[listKey]),
            }
        }
        console.log(initData.anotherList);

        ImsBoneService.setData(serviceData,initData);

        /*메소드 정의*/
        ImsBoneService.setMethod(serviceData, {
            changeTab: (tabName) => {
                vueApp.tabMode = tabName
            },
            //엑셀 다운로드
            listDownload : (type)=>{
                //Not Ajax.
                const downloadSearchCondition = $.copyObject( vueApp.searchCondition );
                const queryString = $.objectToQueryString(downloadSearchCondition);
                location.href=`ims_list_sales.php?simple_excel_download=${type}&` + queryString;
            },
            setModify : (bool)=>{
                vueApp.isModify = bool;
            },
            //리스트 일괄수정
            save : ()=>{
                //수정하지 않은 값은 update하지 않도록 배열정리
                let aSendData = ImsNkService.refineMultiUpdate(listUpdateMulti, listUpdateMultiOrigin, ['sno', 'customerSno']);
                if (aSendData.length === 0) {
                    $.msg('수정한 값이 없습니다','','error');
                    return false;
                }
                //값 전달
                $.imsPost('updateProjectMulti',{
                    'project' : aSendData,
                }).then((data)=>{
                    if (data.code == 200) {
                        $.msg('저장 완료','','success').then(()=>{
                            vueApp.isModify = false;
                            listService.refreshList(vueApp.searchCondition.page);
                        });
                    } else {
                        alert('수정실패');
                    }
                });
            },
            setSales : (sales, tabName) =>{
                if('all' === tabName){
                    vueApp.searchCondition = $.copyObject(listSearchDefaultData);
                    vueApp.searchCondition.salesManagerSno = sales;
                    vueApp.refreshList(1);
                }else{
                    vueApp.anotherList[tabName].condition.salesManagerSno = sales;
                    getSalesAnotherList(tabName);
                }
            },
            setDesigner : (designer) =>{
                //TODO : 단일로 바꾸련다.
                vueApp.searchCondition = $.copyObject(listSearchDefaultData);
                vueApp.searchCondition.extDesigner = designer;
                vueApp.refreshList(1);
                //vueApp.searchCondition.extDesigner = '';
            },
            //기타 리스트 조건 초기화
            anotherConditionReset : (tabName)=>{
                vueApp.anotherList[tabName].condition = $.copyObject(tabConditionDefault[tabName]);
                getSalesAnotherList(tabName);
            }
        });

        ImsBoneService.setMounted(serviceData, ()=>{
            //프로젝트 등록 이벤트
            $('#btn-reg-project').click(()=>{
                $('#btn-reg-project-hide').click();
            });
        });

        /*(전체) 리스트 서비스 시작*/
        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData, ()=>{
            getListAfter();
            //영업대기 리스트
            getSalesAnotherList('tab2');
        }); //style , styleSearchCondition
        listService.init(serviceData);

    });
</script>