<!--TODOLIST-->
<?php foreach($dpTodoInfoList as $masterTodoKey => $masterTodoList ){ ?>
<div class="row mgt20">

    <?php foreach($masterTodoList as $todoInfoKey => $todoInfo ){ ?>

        <div class="col-xs-6 new-style">
            <div class="table-title gd-help-manual">
                <div class="flo-left area-title">
                    <i class="fa <?=$todoInfo['icon']?> fa-title-icon" aria-hidden="true" style="font-size:14px !important;"></i>
                    <?=$todoInfo['title']?> <span class="font-13">- {% todoList.<?=$todoInfo['dept']?>.list.length %}가지 할일이 있습니다.
                </div>
                <div class="flo-right">

                    <span class="radio-inline">
                        <label class="radio-inline noto font-14">
                            <input type="radio" :name="'todoInfo<?=$todoInfo['dept']?>_'+tabMode"  value="list" class="mgt5" v-model="todoList.<?=$todoInfo['dept']?>.status" /> 할일 목록
                        </label>
                        <label class="radio-inline noto font-14" >
                            <input type="radio" :name="'todoInfo<?=$todoInfo['dept']?>_'+tabMode"  value="completeList" class="mgt5" v-model="todoList.<?=$todoInfo['dept']?>.status" /> 완료건
                        </label>

                        <label class="radio-inline noto font-14" >
                            <div class="btn btn-white" @click="openTodoRequestWrite(items.sno, project.sno, '<?=$todoInfo['link']?>')">
                                요청
                            </div>
                        </label>
                    </span>
                </div>
            </div>

            <table class="table table-cols table-default-center mgt5 table-td-height0" id="main-table">
                <colgroup>
                    <col class="w-2p" /><!--번호-->
                    <col class="w-2p" /><!--요청자-->
                    <col class="w-9p" /><!--요청자-->
                    <col class="width-sx"/><!--대상-->
                    <col class="w-40p" /><!--제목-->
                    <col class="w-8p"/><!--결과-->
                    <col class="w-10p"/><!--등록일-->
                </colgroup>
                <thead>
                <tr v-show="'list' === todoList.<?=$todoInfo['dept']?>.status">
                    <th>번호</th>
                    <th>요청번호</th>
                    <th>요청자</th>
                    <th>대상</th>
                    <th>제목</th>
                    <th>상태</th>
                    <th>등록일</th>
                </tr>
                <tr v-show="'list' !== todoList.<?=$todoInfo['dept']?>.status">
                    <th style="background-color:#e5f0ff !important; ">번호</th>
                    <th style="background-color:#e5f0ff !important; ">요청번호</th>
                    <th style="background-color:#e5f0ff !important; ">요청자</th>
                    <th style="background-color:#e5f0ff !important; ">대상</th>
                    <th style="background-color:#e5f0ff !important; ">제목</th>
                    <th style="background-color:#e5f0ff !important; ">상태</th>
                    <th style="background-color:#e5f0ff !important; ">등록일</th>
                </tr>
                </thead>
                <?php foreach( $todoInfo['listType'] as $listType ){ ?>
                <tbody v-show="todoList.<?=$todoInfo['dept']?>.<?=$listType?>.length > 0 && '<?=$listType?>' === todoList.<?=$todoInfo['dept']?>.status ">
                <tr v-for="(todoData, todoIndex) in todoList.<?=$todoInfo['dept']?>.<?=$listType?>" v-show="todoList.<?=$todoInfo['dept']?>.maxRow > todoIndex">
                    <td >{% todoList.<?=$todoInfo['dept']?>.<?=$listType?>.length - todoIndex %}</td>
                    <td >{% todoData.sno %}</td>
                    <td >
                        {% todoData.regManagerNm %}
                    </td><!--요청자-->
                    <td >{% todoData.dpTargetName %}</td><!--대상자-->
                    <td class="pdl5 text-left ">
                        <span class="cursor-pointer hover-btn relative" @click="openTodoRequest(todoData.sno, todoData.resSno)" >
                            {% todoData.subject %}

                            <span class="font-11 text-muted" v-if="!$.isEmpty(todoData.expectedDt) && '0000-00-00' !== todoData.expectedDt ">
                            (~{% $.formatShortDate(todoData.expectedDt)%}까지)
                            </span>

                            <!--<span v-if="todoData.commentCnt > 0">
                                <div style="position:absolute; top:-5px;right:-15px; font-size: 14px !important; color:#f78800"  class="font-12">
                                    <i class="fa fa-circle" aria-hidden="true"></i>
                                </div>
                                <div style="position:absolute; top:-1px;right:-14px; color:#fff; font-size: 9px !important; text-align: center; width:10px"  class="font-12">
                                    {% todoData.commentCnt %}
                                </div>
                            </span>-->

                            <comment-cnt :data="todoData" ></comment-cnt>

                        </span>
                    </td><!--제목-->
                    <td >{% todoData.statusKr %}</td><!--상태-->
                    <td >{% $.formatShortDate(todoData.regDt) %}</td><!--등록일-->
                </tr>
                <tr v-show="todoList.<?=$todoInfo['dept']?>.<?=$listType?>.length > todoList.<?=$todoInfo['dept']?>.maxRow" >
                    <td colspan="99" style="border-bottom: none">
                        <div class="btn btn-sm btn-gray-line cursor-pointer hover" @click="todoList.<?=$todoInfo['dept']?>.maxRow += 5">+more</div>
                    </td>
                </tr>
                </tbody>
                <tbody v-show="0 >= todoList.<?=$todoInfo['dept']?>.<?=$listType?>.length && '<?=$listType?>' === todoList.<?=$todoInfo['dept']?>.status ">
                <tr>
                    <td colspan="99">데이터 없음</td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

    <?php } ?>

</div>

<?php } ?>