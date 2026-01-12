<script type="text/javascript">
    const layerAddMemberDefault = {
        visible: false,
        loading: false,
        addScheduleKr : [],
        addManagerList : [],
        salesManager : 0,
        designManager : 0,
        regMessageClass : 'text-muted',
    };

    /**
     *  프로젝트 VIEW 메소드
     */
    const viewMethods2 = {

        /**
         * 공통 이메일 팝업 오픈
         * @param {String} type - 
         * 발송 타입 (제안서 'proposal', 아소트 'assort', 사양서 'designGuide') = customerOrderEmail customer_guide
         * @param {String} fileUrl - (선택) 파일 경로
         * @param {String} receiver - (선택) 수신자명 (없으면 저장된 값 or 고객기본정보 사용)
         * @param {String} email - (선택) 이메일 (없으면 저장된 값 or 고객기본정보 사용)
         */
        openEmailPopup: function(type, fileUrl, receiver, email) {
            //console.log(type);
            //console.log(fileUrl);
            // 1. 타입 및 파일 경로 설정
            this.emailPopConfig.type = type;
            this.emailPopConfig.fileUrl = JSON.stringify(fileUrl || ''); // undefined 방지

            // 2. 수신자명 우선순위 결정
            // (직접 넘긴 값 > 기존에 저장해둔 값 > 고객 마스터 정보)
            this.emailPopConfig.receiver = receiver
                || this.mainData.assortReceiver
                || this.customer.contactName;

            // 3. 이메일 우선순위 결정
            this.emailPopConfig.email = email
                || this.mainData.assortEmail
                || this.customer.contactEmail;

            // 4. 팝업 노출
            this.emailPopVisible = true;
        },
        
        //이메일 발송 이력 열기
        openSendHistory : (type)=>{
            vueApp.sendHistoryType=type;
            vueApp.sendHistoryVisible=true;
        },

        //QB정보 열기
        openQb : ()=>{
            vueApp.qbLayer.loading = true;
            vueApp.qbLayer.loading = false;
            vueApp.qbLayer.visible = true;
        },

        //추가.
        openAddManager : ()=>{
            //console.log('# openAddManager');
            if( 0 >= vueApp.chkSchedule.length ){
                $.msg('스케쥴을 선택해주세요.','','warning');
                return false;
            }
            vueApp.layerAddMember.loading = false;
            vueApp.layerAddMember.visible = true;
            vueApp.chkSchedule.forEach((schedule)=>{
                //console.log( '선택 스케쥴 - ' , schedule , ' :' , scheduleMap[schedule] );
                vueApp.layerAddMember.addScheduleKr.push(scheduleMap[schedule]);
            });
            return false;
        },
        addProjectManager() {
            const salesManager = vueApp.layerAddMember.salesManager;
            const designManager = vueApp.layerAddMember.designManager;
            if( salesManager != 0 && !vueApp.layerAddMember.addManagerList.includes(salesManager) ){
                vueApp.layerAddMember.addManagerList.push(salesManager);
            }//영업 담당자 추가
            if( designManager != 0 && !vueApp.layerAddMember.addManagerList.includes(designManager) ){
                vueApp.layerAddMember.addManagerList.push(designManager);
            }//디자인 담당자 추가
            vueApp.layerAddMember.salesManager = 0;
            vueApp.layerAddMember.designManager = 0;
        },
        regProjectManager() {

            //참여자 선택 했으면
            vueApp.addProjectManager();

            if( 0 >= vueApp.layerAddMember.addManagerList.length ){
                vueApp.layerAddMember.regMessageClass = 'text-danger bold';
            }else{
                console.log('스케쥴',vueApp.chkSchedule);
                vueApp.chkSchedule.forEach((scheduleDiv)=>{
                    console.log('기존 매니저',vueApp.mainData[scheduleDiv+'AddManager']);
                    if( $.isEmpty(vueApp.mainData[scheduleDiv+'AddManager']) ){
                        vueApp.mainData[scheduleDiv+'AddManager'] = [];
                    }
                    vueApp.layerAddMember.addManagerList.forEach((managerInfo)=>{
                        if(0 != managerInfo){
                            const managerParseInfo = managerInfo.split(":");
                            const findManagerSno = vueApp.mainData[scheduleDiv+'AddManager'].find(v => v.managerSno == managerParseInfo[0]);
                            if($.isEmpty(findManagerSno)){
                                vueApp.mainData[scheduleDiv+'AddManager'].push({
                                    managerSno : managerParseInfo[0],
                                    managerNm : managerParseInfo[1],
                                });
                            }
                        }
                    });
                });
                //닫고 초기화
                vueApp.chkSchedule = [];
                vueApp.layerAddMember = $.copyObject(layerAddMemberDefault);
                vueApp.save();
            }
        },
        closeAddMember() {
            vueApp.chkSchedule = [];
            vueApp.layerAddMember = $.copyObject(layerAddMemberDefault);//초기화
            vueApp.layerAddMember.visible = false;
        },

        /**
         * 자동 스케쥴 설정 (DL기준)
         */
        setAutoScheduleByDeadLine : (option)=>{
            $.imsPost('getSimpleProject',{sno:sno}).then((data)=>{
                $.imsPostAfter(data,(project)=>{
                    $.msgConfirm('값이 없는 스케쥴에만 적용됩니다.','변경된 값은 저장해야 적용됩니다.<br>계속 하시겠습니까?').then(function(result){
                        if( result.isConfirmed ){
                            for(let key in vueApp.scheduleConfig){
                                if(!$.isEmptyAll(vueApp.scheduleConfig[key].deadLine) && $.isEmptyAll(project['ex'+$.ucfirst(key)]) ){
                                    vueApp.mainData['ex'+$.ucfirst(key)] = $.addDays(vueApp.scheduleConfig[key].deadLine, option*-1);
                                }
                            }
                        }
                    });
                });
            });
        },

        /**
        *  진행 타입에따른 프로젝트 타입 자동 변경
        */
        setPrjTypeByBidType : ()=>{
            const typeMap = {
                'single': '0',
                'bid': '2',
                'costBid': '2',
            };
            vueApp.mainData.projectType = typeMap[vueApp.mainData.bidType2];
        },

        /**
         * 보류 유찰 처리
         */ 
        setProjectHold : (projectStatus)=>{
            $.msgTextarea('유찰/보류 사유 입력','사유 입력 후 확인 버튼을 눌러주세요').then((confirmMsg)=>{
                if( confirmMsg.isConfirmed ){
                    $.imsPost2('setStatus',{
                        projectSno : sno,
                        projectStatus : projectStatus
                    },(data)=>{
                        vueApp.saveRealTime('projectExt','projectSno',sno,'holdMemo',confirmMsg.value);
                        $.msg('처리되었습니다.','','success').then(()=>{
                            location.reload();
                        });
                    });
                }
            });
        },

        /**
         * 예정일 초기화
         * @param row
         * @param scheduleKeys
         */
        /**
         * schedule-area(현재 PHP $scheduleList)에 있는 스케쥴 전체의 "예정일"만 ''로 초기화
         * @param {Object} row          - mainData (현재 행/프로젝트 데이터)
         * @param {Array<String>} keys  - ["plan","proposal", ...] (이 영역의 스케쥴 타입들)
         */
        resetAreaExpectedDates(row, keys) {
            if (!row || !Array.isArray(keys) || keys.length === 0) return;

            // 필요 없으면 confirm 제거하세요.
            if (!window.confirm('해당 영역의 스케쥴 예정일을 모두 빈값으로 초기화할까요?')) return;

            keys.forEach((type) => {
                row['ex'+$.ucfirst(type)] = '';
                console.log('Row:',row['ex'+$.ucfirst(type)]);
                //console.log('Type:','ex'+$.ucfirst(type));
                /*const expectedKey = this._findExpectedKey(row, type);

                if (!expectedKey) {
                    console.warn(`[reset] 예정일 키를 못찾음: type=${type}`, row);
                    return;
                }
                // ✅ Vue2: 반응성 보장
                this.$set(row, expectedKey, '');*/
            });
        },

        /**
         * mainData(row) 안에서 해당 스케쥴(type)의 "예정일" 필드명을 찾아서 반환
         * - 프로젝트마다 네이밍이 다를 수 있어서 후보 + fallback 탐색
         */
        _findExpectedKey(row, type) {
            if (!row || !type) return null;
            const U = type.charAt(0).toUpperCase() + type.slice(1);

            // 1) 흔한 후보들 우선 탐색
            const candidates = [
                `${type}ExpectedDt`,
                `${type}ExpectDt`,
                `expected${U}Dt`,
                `expect${U}Dt`,
                `${type}ExpectedDate`,
                `${type}ExpectDate`,
                `expected${U}Date`,
                `expect${U}Date`,
            ];

            for (let i = 0; i < candidates.length; i++) {
                const k = candidates[i];
                if (Object.prototype.hasOwnProperty.call(row, k)) return k;
            }

            // 2) fallback: row 전체 key에서 Expect/Expected + Dt/Date 패턴으로 탐색
            const keys = Object.keys(row);
            const re1 = new RegExp(`^${type}(Expected|Expect).*(Dt|Date)$`, 'i');  // planExpectedDt ...
            const re2 = new RegExp(`^(expected|expect)${U}.*(Dt|Date)$`, 'i');     // expectedPlanDt ...
            const excludes = /(Complete|Done|Finish|Comment|Cnt|Count|deadLine|Delay|tx)/i;

            for (let i = 0; i < keys.length; i++) {
                const k = keys[i];
                if ((re1.test(k) || re2.test(k)) && !excludes.test(k)) return k;
            }

            return null;
        },

        /**
         * [공통] 문자열 정제 함수
         * 1. |||선택||| -> 빈값으로 제거
         * 2. |||값1||||||값2||| -> 값1, 값2 형태로 변환
         */
        cleanText: function(text) {
            if (!text) return '';
            let cleaned = String(text);

            // [중요] 체크박스의 '선택' 값은 아예 제거 (빈 문자열로 만듦)
            cleaned = cleaned.replaceAll('|||선택|||', '');

            // 나머지 구분자 처리 (|||||| -> , )
            cleaned = cleaned.replaceAll('||||||', ', ').replaceAll('|||', '');

            return cleaned.trim();
        },

        /**
         * [TYPE 1] 단일 항목 값 가져오기
         */
        getSalesPlanVal: function(groupName, questionName) {
            try {
                if (!this.salesPlan || !this.salesPlan.fill_detail) return '';
                const group = this.salesPlan.fill_detail[groupName];
                if (!group) return '';

                const question = group[questionName];
                if (!question) return '';

                // [변경] 값을 합치기 전에 미리 정제하고, 빈 값은 제외함
                const values = Object.values(question)
                    .map(v => this.cleanText(v)) // 1. 정제 (선택 -> 빈값)
                    .filter(v => v !== '');      // 2. 빈값 제거

                return values.join(', ');
            } catch (e) {
                return '';
            }
        },

        /**
         * [TYPE 2] 특정 그룹 내 지정한 여러 질문들을 합쳐서 보여주기 (예: 근무 환경, 세탁 환경)
         */
        getSalesPlanMix: function(groupName, questions) {
            try {
                if (!this.salesPlan || !this.salesPlan.fill_detail) return '';
                const group = this.salesPlan.fill_detail[groupName];
                if (!group) return '';

                let results = [];
                questions.forEach(qName => {
                    if (group[qName]) {
                        // [변경] 각 질문의 값들도 미리 정제하여 빈 값이면 건너뜀
                        const rawVal = Object.values(group[qName]).join(' '); // 같은 질문 내 값 합치기
                        const val = this.cleanText(rawVal);

                        if(val) results.push(`${qName}: ${val}`);
                    }
                });
                return results.join(' / ');
            } catch (e) {
                return '';
            }
        },

        /**
         * [TYPE 3] 특정 그룹 내의 "선택된 모든 항목" 보여주기
         * 변경점: 항목 간의 구분을 ' / ' 대신 '<br>' 태그로 변경하여 줄바꿈 처리
         */
        getSalesPlanAll: function(groupName) {
            try {
                if (!this.salesPlan || !this.salesPlan.fill_detail) return '';
                const group = this.salesPlan.fill_detail[groupName];
                if (!group) return '';

                let results = [];

                Object.keys(group).forEach(qName => {
                    const question = group[qName];
                    const rawValues = Object.values(question);

                    const hasData = rawValues.some(v => v && String(v).trim().length > 0);
                    if (!hasData) return;

                    const displayValues = rawValues
                        .map(v => this.cleanText(v))
                        .filter(v => v !== '');

                    if (displayValues.length === 0) {
                        results.push(qName);
                    } else {
                        const valStr = displayValues.join(', ');
                        if (valStr === qName) {
                            results.push(qName);
                        } else {
                            results.push(qName + ' : ' + valStr);
                        }
                    }
                });

                // [수정됨] 줄바꿈 태그로 연결
                return results.join('<br>');
            } catch (e) {
                return '';
            }
        },

        /**
         * [TYPE 4] JSON 리스트 형태 데이터 가져오기 (예: 의사 결정 라인)
         */
        getSalesPlanJsonList: function(groupName) {
            try {
                if (!this.salesPlan || !this.salesPlan.fill_json) return '';

                const list = this.salesPlan.fill_json[groupName];
                if (!list || !Array.isArray(list)) return '';

                let results = list.map(item => {
                    // [변경] 각 컬럼 값 정제 후 합치기
                    const rowValues = Object.values(item)
                        .map(v => this.cleanText(v))
                        .filter(v => v !== '')
                        .join(' / ');
                    return rowValues;
                });

                return results.join('<br>');
            } catch (e) {
                return '';
            }
        },


    };
</script>