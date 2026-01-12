<?php include './admin/ims/library_all.php'?>
<?php include './admin/ims/library.php'?>

<style>
    .ch-table th { text-align: center }
    .ch-table td { text-align: center }
</style>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix" style="margin-bottom:0 !important; vertical-align: bottom">
            <?php if( empty($requestParam['sno']) ) { ?>
                <h3 class="">요청 등록</h3>
            <?php }else { ?>
                <h3 class="">요청 수정</h3>
            <?php } ?>
            <div class="btn-group font-18 bold">
            </div>
        </div>
    </form>

    <div class="">
        <!-- 기본 정보 -->
        <div>
            <table class="table table-cols table-pd-5" style="border-top:none !important;">
                <colgroup>
                    <col class="width-sm">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th>
                        제목
                    </th>
                    <td>
                        <input type="text" placeholder="제목" class="form-control" v-model="document.subject" style="height:30px">
                    </td>
                </tr>
                <tr>
                    <th>
                        요청자
                    </th>
                    <td>
                        <?=$managerInfo['managerNm']?>
                    </td>
                </tr>
                <?php if( empty($requestParam['sno']) ) { ?>
                    <tr>
                        <th>
                            요청 대상
                        </th>
                        <td class="pd0">
                            <div class="font-14 pd10">
                                <label class="radio-inline" >
                                    <input type="radio" name="reqType"  value="team" v-model="reqType" @click="clearReqList()"/> 팀별요청
                                </label>
                                <label class="radio-inline" >
                                    <input type="radio" name="reqType"  value="private" v-model="reqType" @click="clearReqList()"/> 개별요청
                                </label>
                                <label class="radio-inline" >
                                    <input type="radio" name="reqType"  value="self" v-model="reqType" @click="clearReqList()"/> 나에게요청
                                </label>
                            </div>

                            <!--팀별요청-->
                            <div class="mgt10" v-if="'team' === reqType">
                                <?php foreach($teamManagerList as $teamManagerKey => $teamManager) { ?>
                                    <?php if('02001005' !== $teamManager['teamCode'] ) { ?>
                                    <label class="checkbox-inline" style="margin-left:20px !important; width:90px">
                                        <input type="checkbox" name="teamManager<?=$teamManagerKey?>" value="<?=$teamManager['teamCode']?>" data-valkr="<?=$teamManager['teamName']?>" class="chk-manager" @change="checkManager" v-model="reqManagers">
                                        <?=$teamManager['teamName']?>
                                    </label>
                                    <?php } ?>
                                <?php } ?>
                            </div>

                            <!--개별요청-->
                            <div class="mgt10 " v-if="'private' === reqType">
                                <?php foreach($teamManagerList as $teamManagerKey => $teamManager) { ?>
                                    <?php foreach($teamManager['managers'] as $eachManagerSno => $eachManager) { ?>
                                        <label class="checkbox-inline" style="margin-left:20px !important; width:120px">
                                            <input type="checkbox" name="teamManager<?=$teamManagerKey?>" value="<?=$eachManagerSno?>" data-valkr="<?=$eachManager?>" class="chk-manager" @change="checkManager" v-model="reqManagers">
                                            <?=$eachManager.'<span class="text-muted">('.$teamManager['teamName'].')</span>'?>
                                        </label>
                                    <?php } ?>
                                <?php } ?>
                            </div>

                            <!--나에게요청-->
                            <div class="mgt10 " v-if="'self' === reqType">
                                <label class="checkbox-inline" style="margin-left:20px !important; width:120px">
                                    <label class="checkbox-inline" style="margin-left:20px !important; width:120px">
                                        <input type="checkbox" name="teamManagerSelf" value="<?=\Session::get('manager.sno')?>" data-valkr="<?=\Session::get('manager.managerNm')?>" class="chk-manager" @change="checkManager" v-model="reqManagers">
                                        <?=\Session::get('manager.managerNm')?>
                                    </label>
                                </label>
                            </div>

                            <div class="dp-flex dp-flex-wrap mgt10 mgb10 mgl10" >
                                <?php foreach($teamManagerList as $teamManagerKey => $teamManager) { ?>
                                    <div class="sl-badge mgt5 mgr5" v-show="reqManagers.includes('<?=$teamManager['teamCode']?>')"><?=$teamManager['teamName']?></div>
                                    <?php foreach($teamManager['managers'] as $eachManagerSno => $eachManager) { ?>
                                        <div class="sl-badge mgt5 mgr5" v-show="reqManagers.includes('<?=$eachManagerSno?>')"><?=$eachManager?></div>
                                    <?php } ?>
                                <?php } ?>
                            </div>

                        </td>
                    </tr>
                <?php }else{ ?>

                    <tr >
                        <th>
                            요청 대상
                        </th>
                        <td style="padding:0!important;">

                            <table class="table table-rows-soft table-center border-0 table-pd-0 mg0 ims-list-sub-table table-borderless"  style="border:none !important;">
                                <colgroup>
                                    <col style="width:100px!important;">
                                    <col class="" v-for="resTarget in document.targetManagerList">
                                </colgroup>
                                <tr>
                                    <td style="background-color:#f1f1f1;border-top:none !important;" class="bold border-0 border-top-0">대상자명</td>
                                    <td v-for="resTarget in document.targetManagerList" style="background-color:#f1f1f1;border-top:none !important;">
                                        {% resTarget.name %}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color:#f1f1f1" class="bold border-0">상태</td>
                                    <td v-for="resTarget in document.targetManagerList">{% resTarget.statusKr %}</td>
                                </tr>
                                <tr>
                                    <td style="background-color:#f1f1f1" class="bold">예정일</th>
                                    <td v-for="resTarget in document.targetManagerList" class="font-12">
                                        {% $.formatShortDate(resTarget.expectedDt) %}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color:#f1f1f1" class="bold">완료일</td>
                                    <td v-for="resTarget in document.targetManagerList" class="font-12">
                                        {% $.formatShortDate(resTarget.completeDt) %}
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                <?php } ?>
                <tr>
                    <th>
                        <?php if( !empty($requestParam['projectSno']) ) { ?>
                            고객/프로젝트
                        <?php }else{ ?>
                            문의 타입
                        <?php } ?>
                    </th>
                    <td>
                        <?php if( !empty($requestParam['projectSno']) ) { ?>
                        <span class="text-blue">{% document.customerName %}</span>
                        <span class="cursor-pointer hover-btn text-danger" @click="openProjectViewAndSetTabMode(document.projectSno,'basic')">{% document.projectSno %}({% document.projectYear %}{% document.projectSeason %})</span>
                        <?php }else{ ?>
                            일반문의
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        완료 희망일
                    </th>
                    <td>
                        <date-picker value-type="format" format="YYYY-MM-DD" :lang="lang" :editable="false"  placeholder="완료 희망일" style="width:140px;font-weight: normal" v-model="document.hopeDt"></date-picker>

                        <span class="pdl30">
                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(document, 'hopeDt',0)">오늘</div>
                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(document, 'hopeDt',1)">+1</div>
                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(document, 'hopeDt',2)">+2</div>
                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(document, 'hopeDt',3)">+3</div>
                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(document, 'hopeDt',4)">+4</div>
                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(document, 'hopeDt',5)">+5</div>
                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(document, 'hopeDt',10)">+10</div>
                            <div class="btn btn-sm btn-white" @click="ImsService.setSearchDateSingle(document, 'hopeDt',15)">+15</div>
                        </span>

                    </td>
                </tr>
                <tr>
                    <th>
                        요청 내용
                    </th>
                    <td class="mgb150 mgt15" style="height:150px; vertical-align: top">
                        <textarea class="form-control h100" placeholder="요청내용 작성" v-model="document.contents"></textarea>
                    </td>
                </tr>
                <tr >
                    <th>
                        첨부
                    </th>
                    <td>
                        <ul class="ims-file-list" >
                            <li class="hover-btn" v-for="(file, fileIndex) in document.todoFile1">
                                <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
                            </li>
                        </ul>

                        <form id="todoFile1" class="set-dropzone mgt5" @submit.prevent="uploadFiles" >
                            <div class="fallback">
                                <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                            </div>
                        </form>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="dp-flex" style="justify-content: center">
            <?php if( empty($requestParam['sno']) ) { ?>
                <div class="btn btn-accept hover-btn btn-lg mg5" @click="save()">저장</div>
            <?php }else{ ?>
                <div class="btn btn-accept hover-btn btn-lg mg5" @click="save()">수정</div>
            <?php } ?>
            <div class="btn btn-white hover-btn btn-lg mg5" @click="self.close()">닫기</div>
        </div>

    </div>

</section>

<script type="text/javascript">

    const sno = '<?=gd_isset($requestParam['sno'],0)?>';
    const teamSno = '<?=gd_isset($requestParam['teamSno'],0)?>';
    console.log('<?=$requestParam['customerSno']?>');

    $(appId).hide();

    $(()=>{
        const initParams = {
            data : {
                reqType : 'team', //team, private
                document : {},
                reqManagers : [],
                projectList : [],
                projectListSearchCondition : {customerSno : <?=gd_isset($requestParam['customerSno'],0)?>, sort : 'P2,desc'},
            },
            mounted : (vueInstance)=>{
                ImsTodoService.getData(sno).then((data)=>{
                    vueInstance.document = $.copyObject(data);
                    //고객 자동 셋팅
                    /*if($.isEmpty(vueInstance.document.customerSno) || 0 >= vueInstance.document.customerSno ){
                        vueInstance.document.customerSno = <?=gd_isset($requestParam['customerSno'],0)?>;
                    }*/
                    vueInstance.document.todoType = 'todo';
                    /*setTimeout(()=>{
                        //고객 자동 셋팅
                        if($.isEmpty(data.projectSno) || 0 >= data.projectSno ){
                            vueApp.document.projectSno = <?=gd_isset($requestParam['projectSno'],0)?>;
                        }else{
                            vueApp.document.projectSno = data.projectSno;
                        }
                    },250);*/
                    //if( 0 >= sno ) vueApp.selectedCustomer(<?=$requestParam['customerSno']?>);
                    console.log(data);

                    //팀 자동 셋팅
                    if( teamSno > 0 ){
                        vueApp.reqManagers.push(teamSno);
                    }

                    $('.set-dropzone').addClass('dropzone');
                    ImsService.setDropzone(vueApp, 'todoFile1', (tmpFile)=>{
                        const saveFileList = [];
                        tmpFile.forEach((value)=>{
                            saveFileList.push(value);
                        });
                        vueApp.document.todoFile1 = saveFileList;

                        //TO-DO : 파일만 올려도 저장되고 싶은 니즈가 있다면 주석 풀기.
                        /*if( sno > 0 ){
                            $.imsPost('saveTodo',{
                                document : vueApp.document,
                                reqManagers : vueApp.reqManagers,
                            });
                        }*/

                    }); //QB의뢰서 파일

                    //프로젝트 셋팅
                    <?php if( !empty($requestParam['projectSno']) ) { ?>
                    ImsService.getData(DATA_MAP.PROJECT,<?=$requestParam['projectSno']?>).then((data)=>{
                        if(200===data.code){
                            console.log('project data : ', data.data.project);
                            //console.log('project data : ', data.data.customer.customerName);
                            vueApp.document.projectSno = <?=$requestParam['projectSno']?>;
                            vueApp.document.projectNo = data.data.project.projectNo;
                            vueApp.document.projectYear = data.data.project.projectYear;
                            vueApp.document.projectSeason = data.data.project.projectSeason;
                            vueApp.document.customerName = data.data.customer.customerName;
                        }
                    });
                    <?php } ?>

                    //고객 셋팅(프로젝트 셋팅 안되었으면)
                    <?php if( empty($requestParam['projectSno']) && $requestParam['customerSno'] ) { ?>
                    ImsService.getData(DATA_MAP.CUSTOMER,sno).then((data)=>{
                        if(200===data.code){
                            console.log('cust data : ', data.data);
                        }
                    });
                    <?php } ?>


                }); //기본데이터 설정
            },
            methods : {
                clearReqList:(reqType)=>{
                    vueApp.reqManagers=[];
                    $('.chk-manager').prop('checked',false);
                },
                checkManager:()=>{
                    /*vueApp.reqManagers = [];
                    $('.chk-manager').each(function(){
                        if( $(this).is(':checked') ){
                            const managerSno = $(this).val();
                            const managerName = $(this).data('valkr');
                            vueApp.reqManagers.push({
                                name : managerName,
                                sno : managerSno,
                            });
                        }
                    });*/
                },
                /*selectedCustomer:(customerSno)=>{
                    if( customerSno > 0 ){
                        vueApp.projectListSearchCondition.customerSno = customerSno;
                        ImsService.getList('project',vueApp.projectListSearchCondition).then((data)=>{
                            vueApp.projectList = data.data.list;
                            console.log(vueApp.projectList);
                        });
                    }else{
                        //select2 초기화
                        vueApp.projectList = [];
                    }
                },*/
                save:()=>{

                    $.imsPost('saveTodo',{
                        document : vueApp.document,
                        reqManagers : vueApp.reqManagers,
                    }).then((data)=>{
                        if(200 === data.code){
                            try{
                                parent.opener.refreshTodoRequestList();
                            }catch(e){
                                console.log(e);
                            }

                            $.msg('저장 되었습니다.','','success').then(()=>{
                                <?php if( empty($requestParam['sno']) ) { ?>
                                self.close();
                                <?php }else{ ?>
                                window.history.back();
                                <?php } ?>
                            });
                        }else{
                            $.msg(data.message,'','warning');
                        }
                    });
                    //후처리 (신규면 -> 닫고/리스트 셋팅)
                    //내용 수정이면 리스트 셋팅
                }
            }
        };
        vueApp = ImsService.initVueApp(appId, initParams);
        console.log('Init OK');

    });
</script>