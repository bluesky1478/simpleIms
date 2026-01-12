<?php include 'library_all.php'?>
<?php include 'library.php'?>

<section id="imsApp">
    <form id="frm">
        <div class="page-header js-affix">
            <h3>고객사 등록</h3>
            <input type="button" value="목록" class="btn btn-white" @click="window.history.back()" >
            <input type="button" value="저장" class="btn btn-red btn-register" @click="save(items)" style="margin-right:75px">
        </div>
    </form>

    <div class="row">
        <!-- 기본 정보 -->
        <div class="col-xs-7">
            <div class="table-title gd-help-manual">
                <div class="flo-left">고객사 기본 정보</div>
                <div class="flo-right">
                    <!--
                    <button type="button" class="btn btn-red btn-sm js-orderInfoBtn">정보수정</button>
                    <button type="button" class="btn btn-red-box btn-sm js-orderInfoBtnSave js-orderViewInfoSave display-none" data-submit-mode="modifyOrderInfo">저장</button>
                    -->
                </div>
            </div>
            <div>
                <table class="table table-cols table-pd-5">
                    <colgroup>
                        <col class="width-sm">
                        <col>
                        <col class="width-sm">
                        <col>
                    </colgroup>
                    <tbody>
                    <tr>
                        <th class="sl-blue font-14">
                            고객사명(필수)
                        </th>
                        <td>
                            <input type="text" class="form-control sl-blue font-15 pd5" placeholder="고객사명" v-model="items.customerName" style="height:40px !important;">
                        </td>
                        <th class="text-danger font-14">
                            고객사 이니셜
                            <br><span class="font-13">(Style code / 필수)</span>
                        </th>
                        <td>
                            <input type="text" class="form-control text-danger font-15 boold" placeholder="StyleCode에 들어가는 고객사 이니셜" v-model="items.styleCode" style="height:40px !important;">
                        </td>
                    </tr>
                    <tr>
                        <th >영업담당자</th>
                        <td>
                            <select2 aclass="salesManagerSno" v-model="items.salesManagerSno"  style="width:100%" >
                                <?php foreach ($managerList as $key => $value ) { ?>
                                    <option value="<?=$key?>"><?=$value?></option>
                                <?php } ?>
                            </select2>
                        </td>
                        <th>고객상태</th>
                        <td >
                            <select class="form-control" v-model="items.customerDiv">
                                <option value="0">잠재고객</option>
                                <option value="1">신규고객</option>
                                <option value="2">기존고객</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>3PL</th>
                        <td>
                            <label class="radio-inline">
                                <input type="radio" name="use3pl" value="n"  v-model="items.use3pl" />사용안함
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="use3pl" value="y"  v-model="items.use3pl" />사용
                            </label>
                        </td>
                        <th>폐쇄몰</th>
                        <td>
                            <label class="radio-inline">
                                <input type="radio" name="useMall" value="n"  v-model="items.useMall" />사용안함
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="useMall" value="y"  v-model="items.useMall" />사용
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>고객 기타사항</th>
                        <td colspan="3">
                            <textarea class="form-control " rows="4" v-model="items.addedInfo.etc99"></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 담당자 정보 -->
        <div class="col-xs-7" >
            <div class="table-title gd-help-manual ">
                <div class="flo-left">
                    담당자 정보
                </div>
                <div class="flo-right"></div>
            </div>
            <div class="">
                <table class="table table-cols table-pd-5">
                    <colgroup>
                        <col class="width-md">
                        <col class="width-xl">
                        <col class="width-md">
                        <col class="width-xl">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>담당자명</th>
                        <td colspan="3">
                            <input type="text" class="form-control width-lg" placeholder="고객사 담당자명" v-model="items.contactName">
                        </td>
                    </tr>
                    <tr>
                        <th>직함</th>
                        <td>
                            <input type="text" class="form-control width-lg" placeholder="직함(ex 수석)" v-model="items.contactPosition">
                        </td>
                        <th>부서</th>
                        <td>
                            <input type="text" class="form-control " placeholder="부서명" v-model="items.contactDept">
                        </td>
                    </tr>
                    <tr>
                        <th>사무실 주소</th>
                        <td colspan="3">
                            <div class="form-inline mgb5">
                    <span title="우편번호를 입력해주세요!">
                        <input type="text" name="zonecode" id="zonecode" size="6" maxlength="5" class="form-control js-number" data-number="5" readonly v-model="items.contactZipcode"  />
                    </span>
                                <input type="button" onclick="postcode_search('zonecode', 'address', 'zipcode');" value="우편번호찾기" class="btn btn-gray btn-sm"/>
                            </div>
                            <div class="form-inline">
                    <span title="주소를 입력해주세요!">
                        <input type="text" name="address" id="address" class="form-control width-3xl " readonly v-model="items.contactAddress"  />
                    </span>
                                <span title="상세주소를 입력해주세요!" class="mgt5">
                        <input type="text" name="addressSub" id="addressSub" class="form-control width-2xl mgt5" placeholder="상세주소" v-model="items.contactAddressSub" />
                    </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>이메일</th>
                        <td colspan="3">
                            <input type="text" class="form-control width-lg" placeholder="이메일" v-model="items.contactEmail">
                        </td>
                    </tr>
                    <tr>
                        <th>휴대전화</th>
                        <td>
                            <input type="text" class="form-control width-lg" placeholder="휴대전화" v-model="items.contactMobile">
                        </td>
                        <th>내선번호</th>
                        <td>
                            <input type="text" class="form-control " placeholder="내선번호" v-model="items.contactNumber">
                        </td>
                    </tr>
                    <tr>
                        <th>성별</th>
                        <td>
                            <label class="radio-inline">
                                <input type="radio" name="contactGender" value="M"  v-model="items.contactGender" />
                                남성
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="contactGender" value="F"  v-model="items.contactGender" />
                                여성
                            </label>
                        </td>
                        <th>나이</th>
                        <td>
                            <input type="text" class="form-control " placeholder="나이" v-model="items.contactAge" >
                        </td>
                    </tr>
                    <tr>
                        <th>담당자 성향</th>
                        <td colspan="3">
                            <input type="text" class="form-control w100" placeholder="담당자 성향" v-model="items.contactPreference">
                        </td>
                    </tr>
                    <tr>
                        <th>담당자 메모</th>
                        <td colspan="3">
                            <textarea class="form-control " rows="4" v-model="items.contactMemo"></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-xs-7 text-center">
            <div class="btn btn-red btn-lg hover-btn cursor-pointer" @click="save(items)">저장</div>
            <div class="btn btn-white btn-lg hover-btn cursor-pointer" @click="window.history.back()">목록</div>
        </div>

    </div>
</section>

<script type="text/javascript">

    const uploadAfterAction = (tmpFile, dropzoneId)=>{
        const saveFileList = [];
        tmpFile.forEach((value)=>{
            saveFileList.push(value);
        });

        let promptValue = '';
        promptValue = window.prompt("메모입력 : ");

        $.imsPost('saveProjectFiles',{
            saveData : {
                customerSno : '<?=$requestParam['sno']?>',
                projectSno : '-<?=$requestParam['sno']?>', //customer를 프로젝트로.
                fileDiv : 'fileEtc3',
                fileList : saveFileList,
                memo : promptValue,
            }
        }).then((data)=>{
            if(200 === data.code) {
                vueApp.fileList[dropzoneId] = data.data[dropzoneId];
            }
        });
    }

    $(appId).hide();

    $(()=>{
        //Load Data.
        const sno = '<?=$requestParam['sno']?>';
        ImsService.getData(DATA_MAP.CUSTOMER,sno).then((data)=>{
            console.log(data.data);
            const initParams = {
                data : {
                    items : data.data,
                },
                methods : {
                    save : ImsCustomerService.saveCustomer,
                },
                mounted : (vueInstance)=>{
                    //ImsService.setDropzone(vueInstance, 'fileEtc3', uploadAfterAction); //계약서
                }
            };
            vueApp = ImsService.initVueApp(appId, initParams);
            console.log('Init OK');
        });
    });
</script>
