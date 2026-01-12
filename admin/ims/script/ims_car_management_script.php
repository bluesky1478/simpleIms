<script type="text/javascript">
    const mainListPrefix = 'car_management';

    const listSearchDefaultData = {
        multiKey : [{
            key : 'managerNm',
            keyword : '',
        }],
        multiCondition : 'OR',

        sRadioSchCarSno : 'all',
        sRadioSchMaintainType : 'all',
        sTextboxRangeStartSchDriveDt : '',
        sTextboxRangeEndSchDriveDt : '',
        sTextboxRangeStartSchMaintainDt : '',
        sTextboxRangeEndSchMaintainDt : '',
        sTextboxRangeStartSchRegDt : '',
        sTextboxRangeEndSchRegDt : '',

        page : 1,
        pageNum : 20,
        sort : 'D,desc' //정렬
    };

    //List 갱신 (해당 화면 메인 리스트 갱신 함수 정의)
    const getListData = async (params, listPrefix)=>{
        //운행기록 리스트 가져오기 :  페이지 이동할때마다 호출하는 메소드
        params.mode = 'getListEtcCarDrive';
        // params.sRadioSchCarSno = vueApp.oCarInfo.sno;
        return ImsNkService.getList('etcCarDrive', params);
    };

    $(()=>{
        const serviceData = {};
        ImsBoneService.setData(serviceData,{
            iTabMenuNum : 1,
            isModify : false,
            oUpsertFormCar : { sno:0, carType:'리스(업무용승용차)', carTypeEtc:'리스(업무용승용차)', carName:'', carNumber:'', totalCheckDt:'', carImage:'', },
            oUpsertFormMaintain : { sno:0 },
            oUpsertFormAddr : { sno:0, addrType:'거래처', addrTypeEtc:'거래처', topYn:'2', addrName:'', addrAddr:'', },
            oUpsertFormDrive : { sno:0 },
            aoCarFlds : [],
            aoCarList : [],
            iCntTotalMaintain : 0,
            aoMaintainFlds : [],
            aoMaintainList : [],
            aoAddrFlds : [],
            aoAddrList : [],
            oRegistAddrInfoStart :{'addrName':'','addrAddr':''},
            oRegistAddrInfoArrive :{'addrName':'','addrAddr':''},
        });
        ImsBoneService.setMounted(serviceData, ()=>{
            //차량리스트 가져오기 -> 뿌리기
            vueApp.getListCar();
            //주소지 가져오기
            vueApp.getAddrList();
            //정비리스트 가져오기
            vueApp.getListMaintain();
            //select2가 modal창 안에 있으면 제대로 동작안함. 아래 라인 추가필요
            $('#oUpsertFormDriveStartAddrSno').select2({ dropdownParent:$('#modalUpsertDrive')});
            $('#oUpsertFormDriveArriveAddrSno').select2({ dropdownParent:$('#modalUpsertDrive')});

        });
        ImsBoneService.setMethod(serviceData, {
            changeTabmenu : (iNum) => {
                vueApp.iTabMenuNum = iNum;
                vueApp.searchCondition.page = 1;
                vueApp.searchCondition.pageNum = 20;
                vueApp.searchCondition.sort = 'D,desc';
                $.each(vueApp.searchCondition.multiKey, function(key, val) {
                    vueApp.searchCondition.multiKey[key].key = 'managerNm';
                    vueApp.searchCondition.multiKey[key].keyword = '';
                });
                if (iNum == 1) {
                    vueApp.searchCondition.sRadioSchMaintainType = 'all';
                    vueApp.searchCondition.sTextboxRangeStartSchMaintainDt = '';
                    vueApp.searchCondition.sTextboxRangeEndSchMaintainDt = '';
                    vueApp.refreshList(1);
                } else if (iNum == 2) {
                    vueApp.searchCondition.sTextboxRangeStartSchDriveDt = '';
                    vueApp.searchCondition.sTextboxRangeEndSchDriveDt = '';
                    vueApp.getListMaintain();
                }
            },
            getListCar : ()=> {
                ImsNkService.getList('etcCar', {}).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        vueApp.aoCarFlds = data.fieldData;
                        vueApp.aoCarList = data.list;
                    });
                });
            },

            changeCarType : ()=> { //차량등록 : 명의구분 바꿨을때 기타textbox
                vueApp.oUpsertFormCar.carTypeEtc = vueApp.oUpsertFormCar.carType;
                if (vueApp.oUpsertFormCar.carType=='') {
                    vueApp.$refs.carInfoCarType.style.display='inline';
                    vueApp.$refs.carInfoCarType.focus();
                } else {
                    vueApp.$refs.carInfoCarType.style.display='none';
                }
            },
            changeMaintainType : ()=> { //정비upsert : 구분 바꿨을때 기타textbox
                vueApp.oUpsertFormMaintain.maintainTypeEtc = vueApp.oUpsertFormMaintain.maintainType;
                if (vueApp.oUpsertFormMaintain.maintainType=='') {
                    vueApp.$refs.carInfoMaintainType.style.display='inline';
                    vueApp.$refs.carInfoMaintainType.focus();
                } else {
                    vueApp.$refs.carInfoMaintainType.style.display='none';
                }
            },
            changeAddrType : ()=> { //주소지upsert : 구분 바꿨을때 기타textbox
                vueApp.oUpsertFormAddr.addrTypeEtc = vueApp.oUpsertFormAddr.addrType;
                if (vueApp.oUpsertFormAddr.addrType=='') {
                    vueApp.$refs.carInfoAddrType.style.display='inline';
                    vueApp.$refs.carInfoAddrType.focus();
                } else {
                    vueApp.$refs.carInfoAddrType.style.display='none';
                }
            },
            changeDriveType : ()=> { //운행upsert : 구분 바꿨을때 기타textbox
                vueApp.oUpsertFormDrive.driveTypeEtc = vueApp.oUpsertFormDrive.driveType;
                if (vueApp.oUpsertFormDrive.driveType=='') {
                    vueApp.$refs.carInfoDriveType.style.display='inline';
                    vueApp.$refs.carInfoDriveType.focus();
                } else {
                    vueApp.$refs.carInfoDriveType.style.display='none';
                }
            },

            //주소지 가져오기
            getAddrList : ()=>{
                ImsNkService.getList('etcCarAddr', {}).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        vueApp.aoAddrFlds = data.fieldData;
                        vueApp.aoAddrList = data.list;
                    });
                });
            },
            //upsert modal(등록,수정(==상세)) 띄우기
            openUpsertCarModal : (sno)=>{
                if (sno == 0) vueApp.isModify = true;
                else vueApp.isModify = false;

                if (sno === 0) { //등록시
                    vueApp.oUpsertFormCar.sno = 0;
                    vueApp.oUpsertFormCar.carType = '리스(업무용승용차)';
                    vueApp.oUpsertFormCar.carTypeEtc = '리스(업무용승용차)';
                    vueApp.oUpsertFormCar.carName = '';
                    vueApp.oUpsertFormCar.carNumber = '';
                    vueApp.oUpsertFormCar.totalCheckDt = '<?=date('Y-m-d')?>';
                    vueApp.oUpsertFormCar.carImage = '';
                    vueApp.$refs.carInfoCarType.style.display='none';
                    $('#modalUpsertCar').modal('show');
                } else { //수정시
                    ImsNkService.getList('etcCar', { 'upsertSnoGet':Number(sno) }).then((data)=> {
                        $.imsPostAfter(data, (data) => {
                            vueApp.oUpsertFormCar = data.info;

                            vueApp.oUpsertFormCar.carTypeEtc = vueApp.oUpsertFormCar.carType;
                            //구분이 기타일때
                            let bFlagEtc = true;
                            $.each(vueApp.$refs.carInfoCarTypeSelect.options, function(key, val) {
                                if (vueApp.oUpsertFormCar.carType == this.value) {
                                    bFlagEtc = false;
                                    return false;
                                }
                            });
                            if (bFlagEtc === true || vueApp.oUpsertFormCar.carType === '') {
                                vueApp.oUpsertFormCar.carType = '';
                                vueApp.$refs.carInfoCarType.style.display='inline';
                            } else {
                                vueApp.$refs.carInfoCarType.style.display='none';
                            }

                            $('#modalUpsertCar').modal('show');
                        });
                    });
                }
            },
            openUpsertMaintainModal : (sno, iCarSno=0)=>{
                if (sno == 0) vueApp.isModify = true;
                else vueApp.isModify = false;

                let oParam = { 'upsertSnoGet':Number(sno) };
                if (iCarSno > 0) oParam.sRadioSchCarSno = iCarSno;
                ImsNkService.getList('etcCarMaintain', oParam).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        vueApp.oUpsertFormMaintain = data.info;
                        vueApp.oUpsertFormMaintain.maintainTypeEtc = vueApp.oUpsertFormMaintain.maintainType;
                        if (vueApp.oUpsertFormMaintain.sno == 0) {
                            vueApp.$refs.carInfoMaintainType.style.display='none';
                        } else {
                            //구분이 기타일때
                            let bFlagEtc = true;
                            $.each(vueApp.$refs.carInfoMaintainTypeSelect.options, function(key, val) {
                                if (vueApp.oUpsertFormMaintain.maintainType == this.value) {
                                    bFlagEtc = false;
                                    return false;
                                }
                            });
                            if (bFlagEtc === true || vueApp.oUpsertFormMaintain.maintainType === '') {
                                vueApp.oUpsertFormMaintain.maintainType = '';
                                vueApp.$refs.carInfoMaintainType.style.display='inline';
                            } else {
                                vueApp.$refs.carInfoMaintainType.style.display='none';
                            }
                        }
                        $('#modalUpsertMaintain').modal('show');
                    });
                });
            },
            openUpsertAddrModal : (sno)=>{
                if (sno == 0) vueApp.isModify = true;
                else vueApp.isModify = false;

                if (sno === 0) { //등록시
                    vueApp.oUpsertFormAddr.sno = 0;
                    vueApp.oUpsertFormAddr.addrType = '거래처';
                    vueApp.oUpsertFormAddr.addrTypeEtc = '거래처';
                    vueApp.oUpsertFormAddr.topYn = '2';
                    vueApp.oUpsertFormAddr.addrName = '';
                    vueApp.oUpsertFormAddr.addrAddr = '';
                    vueApp.$refs.carInfoAddrType.style.display='none';
                    $('#modalUpsertAddr').modal('show');
                } else { //수정시(==상세)
                    ImsNkService.getList('etcCarAddr', {'upsertSnoGet':Number(sno)}).then((data)=> {
                        $.imsPostAfter(data, (data) => {
                            vueApp.oUpsertFormAddr = data.info;

                            vueApp.oUpsertFormAddr.addrTypeEtc = vueApp.oUpsertFormAddr.addrType;
                            //구분이 기타일때
                            let bFlagEtc = true;
                            $.each(vueApp.$refs.carInfoAddrTypeSelect.options, function(key, val) {
                                if (vueApp.oUpsertFormAddr.addrType == this.value) {
                                    bFlagEtc = false;
                                    return false;
                                }
                            });
                            if (bFlagEtc === true || vueApp.oUpsertFormAddr.addrType === '') {
                                vueApp.oUpsertFormAddr.addrType = '';
                                vueApp.$refs.carInfoAddrType.style.display='inline';
                            } else {
                                vueApp.$refs.carInfoAddrType.style.display='none';
                            }
                            $('#modalUpsertAddr').modal('show');
                        });
                    });
                }
            },
            openUpsertDriveModal : (sno, iCarSno=0)=>{
                vueApp.$refs.textRecentKm.value = '';
                vueApp.oRegistAddrInfoStart.addrName = '';
                vueApp.oRegistAddrInfoStart.addrAddr = '';
                vueApp.oRegistAddrInfoArrive.addrName = '';
                vueApp.oRegistAddrInfoArrive.addrAddr = '';

                if (sno == 0) vueApp.isModify = true;
                else vueApp.isModify = false;

                let oParam = { 'upsertSnoGet':Number(sno) };
                if (iCarSno > 0) oParam.sRadioSchCarSno = iCarSno;
                ImsNkService.getList('etcCarDrive', oParam).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        vueApp.oUpsertFormDrive = data.info;
                        vueApp.oUpsertFormDrive.driveTypeEtc = vueApp.oUpsertFormDrive.driveType;
                        if (vueApp.oUpsertFormDrive.sno == 0) {
                            vueApp.$refs.carInfoDriveType.style.display='none';
                        } else {
                            //구분이 기타일때
                            let bFlagEtc = true;
                            $.each(vueApp.$refs.carInfoDriveTypeSelect.options, function(key, val) {
                                if (vueApp.oUpsertFormDrive.driveType == this.value) {
                                    bFlagEtc = false;
                                    return false;
                                }
                            });
                            if (bFlagEtc === true || vueApp.oUpsertFormDrive.driveType === '') {
                                vueApp.oUpsertFormDrive.driveType = '';
                                vueApp.$refs.carInfoDriveType.style.display='inline';
                            } else {
                                vueApp.$refs.carInfoDriveType.style.display='none';
                            }
                        }
                        $('#modalUpsertDrive').modal('show');
                    });
                });
            },
            //정비리스트 modal 띄우기
            getListMaintain : ()=>{
                ImsNkService.getList('etcCarMaintain', vueApp.searchCondition).then((data)=> {
                    $.imsPostAfter(data, (data) => {
                        vueApp.iCntTotalMaintain = Number(data.page.recode.total);
                        vueApp.aoMaintainFlds = data.fieldData;
                        vueApp.aoMaintainList = data.list;
                    });
                });
            },

            //등록/수정
            saveCar : ()=>{
                if (vueApp.oUpsertFormCar.carTypeEtc === null || vueApp.oUpsertFormCar.carTypeEtc === '') {
                    $.msg('명의구분을 입력하세요','','error');
                    return false;
                }
                if (vueApp.oUpsertFormCar.carName === null || vueApp.oUpsertFormCar.carName === '') {
                    $.msg('차종을 입력하세요','','error');
                    return false;
                }

                vueApp.oUpsertFormCar.carType = vueApp.oUpsertFormCar.carTypeEtc;
                $.imsPost('setSimpleDbTable', {'data':vueApp.oUpsertFormCar, 'table_number':4}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        $('#modalUpsertCar').modal('hide');
                        vueApp.isModify = false;
                        vueApp.getListCar();
                    });
                });
            },
            saveMaintain : ()=>{
                if (vueApp.oUpsertFormMaintain.maintainTypeEtc === null || vueApp.oUpsertFormMaintain.maintainTypeEtc === '') {
                    $.msg('정비구분을 입력하세요','','error');
                    return false;
                }
                if (vueApp.oUpsertFormMaintain.maintainDt === null || vueApp.oUpsertFormMaintain.maintainDt === '' || vueApp.oUpsertFormMaintain.maintainDt === '0000-00-00') {
                    $.msg('정비일자를 선택하세요','','error');
                    return false;
                }

                vueApp.oUpsertFormMaintain.maintainType = vueApp.oUpsertFormMaintain.maintainTypeEtc;
                $.imsPost('setSimpleDbTable', {'data':vueApp.oUpsertFormMaintain, 'table_number':5}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        $('#modalUpsertMaintain').modal('hide');
                        vueApp.isModify = false;
                        //정비등록/수정시 정비정보 바뀌어야함
                        vueApp.getListCar();
                        vueApp.getListMaintain();
                    });
                });
            },
            saveAddr : ()=>{
                if (vueApp.oUpsertFormAddr.addrName === null || vueApp.oUpsertFormAddr.addrName === '') {
                    $.msg('주소지 명칭을 입력하세요','','error');
                    return false;
                }

                vueApp.oUpsertFormAddr.addrType = vueApp.oUpsertFormAddr.addrTypeEtc;
                $.imsPost('setSimpleDbTable', {'data':vueApp.oUpsertFormAddr, 'table_number':6}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        $('#modalUpsertAddr').modal('hide');
                        vueApp.isModify = false;
                        vueApp.getAddrList();
                    });
                });
            },
            saveDrive : ()=>{
                if (vueApp.oUpsertFormDrive.startAddrSno === null || vueApp.oUpsertFormDrive.startAddrSno == '0') {
                    $.msg('출발지를 선택하세요','','error');
                    return false;
                }
                if (vueApp.oUpsertFormDrive.arriveAddrSno === null || vueApp.oUpsertFormDrive.arriveAddrSno == '0') {
                    $.msg('도착지를 선택하세요','','error');
                    return false;
                }
                if (vueApp.oUpsertFormDrive.startAddrSno == '-1' && vueApp.oRegistAddrInfoStart.addrName == '') {
                    $.msg('주소지 직접입력시 명칭을 입력하셔야 합니다.','','error');
                    return false;
                }
                if (vueApp.oUpsertFormDrive.arriveAddrSno == '-1' && vueApp.oRegistAddrInfoArrive.addrName == '') {
                    $.msg('주소지 직접입력시 명칭을 입력하셔야 합니다.','','error');
                    return false;
                }
                if (vueApp.oUpsertFormDrive.driveKm === null || vueApp.oUpsertFormDrive.driveKm === '') {
                    $.msg('주행거리(Km)를 입력하세요','','error');
                    return false;
                }

                vueApp.oUpsertFormDrive.driveType = vueApp.oUpsertFormDrive.driveTypeEtc;
                $.imsPost('setSimpleDbTable', {'data':vueApp.oUpsertFormDrive, 'registAddrInfoStart':vueApp.oRegistAddrInfoStart, 'registAddrInfoArrive':vueApp.oRegistAddrInfoArrive, 'table_number':7}).then((data)=>{
                    $.imsPostAfter(data,(data)=>{
                        $('#modalUpsertDrive').modal('hide');
                        vueApp.isModify = false;
                        //운행등록/수정시 총 주행거리 바뀌어야함
                        vueApp.getListCar();
                        vueApp.refreshList(vueApp.searchCondition.page);
                        if (vueApp.oUpsertFormDrive.startAddrSno == '-1' || vueApp.oUpsertFormDrive.arriveAddrSno == '-1') {
                            vueApp.getAddrList();
                        }
                    });
                });
            },

            removeDrive : (sno)=>{
                $.msgConfirm('해당 운행건을 정말 삭제하시겠습니까?', '복구가 불가능합니다.').then((confirmData)=> {
                    if (true === confirmData.isConfirmed) {
                        ImsNkService.setDelete('eeeee', sno).then(()=>{
                            vueApp.getListCar();
                            listService.refreshList(vueApp.searchCondition.page);
                        });
                    }
                });
            },
            removeMaintain : (sno)=>{
                $.msgConfirm('해당 정비건을 정말 삭제하시겠습니까?', '복구가 불가능합니다.').then((confirmData)=> {
                    if (true === confirmData.isConfirmed) {
                        ImsNkService.setDelete('fffff', sno).then(()=>{
                            vueApp.getListCar();
                            vueApp.getListMaintain();
                        });
                    }
                });
            },

            uploadCarImageFile : (sFileType)=>{
                const fileInput = vueApp.$refs[sFileType+'Element'];
                if (fileInput.files.length > 0) {
                    const formData = new FormData();
                    formData.append('upfile', fileInput.files[0]);
                    $.ajax({
                        url: '<?=$nasUrl?>/img_upload.php?projectSno='+vueApp.oUpsertFormCar.sno,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(result){
                            const rslt = JSON.parse(result);
                            vueApp.oUpsertFormCar[sFileType] = '<?=$nasUrl?>'+rslt.downloadUrl;
                        }
                    });
                }
            },
            //엑셀 다운로드
            listDownload : ()=>{
                const downloadSearchCondition = $.copyObject( vueApp.searchCondition );
                const queryString = $.objectToQueryString(downloadSearchCondition);
                location.href='car_management.php?simple_excel_download='+vueApp.iTabMenuNum+'&' + queryString;
            },
        });

        const listService = new ImsListService(mainListPrefix, listSearchDefaultData, getListData);
        listService.init(serviceData);
    });
</script>