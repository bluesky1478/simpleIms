<script type="text/javascript">
    //IMS 뼈대 서비스 (신속한 화면 구축)
    const ImsBoneService = {

        serviceStart : (dateMethod, params, serviceData)=>{
            $.imsPost(dateMethod,params).then((data)=>{
                $.imsPostAfter(data,(data)=>{
                    ImsBoneService.serviceBeginCommon(serviceData, data);
                });
            });
        },

        serviceBeginCommon : (serviceData, data)=>{
            //console.log('ImsBonService getData Fnc : ', dataName);
            console.log('ImsBonService getData: ', data);

            const initParams = {
                data : {
                    isDevId : '<?=$isDevId?>',
                    mainData : data,
                    orgInitData : $.copyObject(data),
                },
                methods : {
                    saveData : (target, saveData, afterFnc, beforeFnc)=>{
                        if( typeof beforeFnc != 'undefined' ){
                            if( false === beforeFnc() ){
                                return false;
                            }
                        }
                        $.imsPost('saveData',{
                            target : target,
                            saveData : saveData
                        }).then((data)=>{
                            if(200 === data.code){
                                const rsltData = data.data;
                                $.msg('저장 되었습니다.','', "success").then(()=>{
                                    if( typeof afterFnc != 'undefined' ){
                                        afterFnc(rsltData);
                                    }
                                });
                                try{
                                    parent.opener.location.reload(); //parent reload 기본.
                                }catch (e){}
                            }else{
                                $.msg(data.message,'', "warning");
                            }
                        });
                    },
                    refresh : (afterFnc)=>{
                        ImsService.getDataParams(dataName, loadParams).then((data)=>{
                            afterFnc(data);
                        });
                    },
                    saveRealTime : (target,key,keyValue,updateField,updateData)=>{
                        ///console.log('================');
                        const saveData = {
                            'mode':'saveRealTime',
                            'target':target,
                            'key':key, //key : warnBatek
                            'keyValue':keyValue, //key : currentValue
                            'updateField':updateField,
                            'updateData':updateData,
                            'dataMerge':'y', //기본 merge
                        }
                        //console.log(saveData);
                        /*$.imsPost('saveRealTime',saveData).then((data)=>{
                            console.log(data);
                        });*/
                        $.post('<?=$imsAjaxUrl?>',saveData, (data)=>{console.log(data)});
                    },
                    changeTab : function(tabName, cookieName){
                        //console.log('TEST');
                        vueApp.tabMode = tabName;
                        if( typeof cookieName != 'undefined' ){
                            $.cookie(cookieName, tabName);
                        }
                    },
                },
            };

            if(!$.isEmpty(serviceData.serviceValue)){
                for(const key in serviceData.serviceValue){
                    initParams.data[key] = serviceData.serviceValue[key];
                }
            }
            if(!$.isEmpty(serviceData.serviceMounted)){
                initParams.mounted = serviceData.serviceMounted;
            }
            if(!$.isEmpty(serviceData.serviceMethods)){
                initParams.methods = Object.assign(initParams.methods, serviceData.serviceMethods);
            }
            if(!$.isEmpty(serviceData.serviceComputed)){
                initParams.computed = serviceData.serviceComputed;
            }
            if(!$.isEmpty(serviceData.serviceWatch)){
                initParams.watch = serviceData.serviceWatch;
            }

            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK (bone service setting)');
        },

        //서비스 시작
        serviceBegin : async (dataName, loadParams, serviceData)=>{
            const beginData = ImsService.getDataParams(dataName, loadParams);
            beginData.then((data)=>{
                if( 200 === data.code ){
                    ImsBoneService.serviceBeginCommon(serviceData, data.data);
                }else{
                    $.msg(data.message, data.code, 'warning');
                }
            });
            return beginData;
        },
        //에디터 생성
        setEditor : (editorId)=>{
            const editorPath = '<?=PATH_ADMIN_GD_SHARE ?>script/smart';
            //코멘트.
            nhn.husky.EZCreator.createInIFrame({
                oAppRef: oEditors,
                elPlaceHolder: editorId,
                sSkinURI: editorPath + '/SmartEditor2Skin.html',
                htParams: {
                    bUseToolbar: true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
                    bUseVerticalResizer: true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
                    bUseModeChanger: true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
                    //aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
                    fOnBeforeUnload: function () {
                        $.ajax({
                            method: "GET",
                            url: "/share/editor_file_uploader.php",
                            data: {mode: 'deleteGarbage', uploadImages : uploadImages.join('^|^')},
                            cache: false,
                        }).success(function (data) {
                        }).error(function (e) {
                        });
                    }
                }, //boolean
                fOnAppLoad: function () {
                    //예제 코드
                    //oEditors.getById["editor"].exec("PASTE_HTML", ["로딩이 완료된 후에 본문에 삽입되는 text입니다."]);
                },
                fCreator: "createSEditor2"
            });
        },
        setData : (serviceData, data)=>{
            serviceData.serviceValue = data;
        },
        setMounted : (serviceData, mountedData)=>{
            serviceData.serviceMounted = mountedData;
        },
        setMethod : (serviceData, methodData)=>{
            serviceData.serviceMethods = methodData;
        },
        setComputed : (serviceData, computedData)=>{
            serviceData.serviceComputed = computedData;
        },
    }
    /**
     * IMS 리스트 처리 서비스
     */


</script>
