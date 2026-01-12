
$.extend({

    /**
     * PHP setColWidth() JS 버전
     * - fieldData에서 col 값(숫자)이 있는 것들은 합산
     * - col이 비어있는 항목 개수만큼 남은 폭을 균등 배분해서 채움(반올림)
     * - 원본 배열을 변경하고 싶으면 mutate=true
     */
    setColWidth : function(maxWidth, fieldData, mutate = true){
        const data = mutate ? fieldData : fieldData.map(v => ({ ...v }));

        let colTotalWidth = 0;
        let emptyColCount = 0;

        for (const each of data) {
            const col = each?.col;

            // PHP empty() 유사 처리: null/undefined/''/0/'0' 모두 empty 취급
            if (col !== undefined && col !== null && col !== '' && col !== 0 && col !== '0') {
                colTotalWidth += Number(col) || 0;
            } else {
                emptyColCount++;
            }
        }

        // 빈 col이 없으면 그대로 반환
        if (emptyColCount === 0) return data;

        const defaultColWidth = Math.round((maxWidth - colTotalWidth) / emptyColCount);

        for (const each of data) {
            const col = each?.col;
            if (col === undefined || col === null || col === '' || col === 0 || col === '0') {
                each.col = defaultColWidth;
            }
        }

        return data;
    },

    /**
     * 빈값 여부 확인 + 채우기
     * @param str
     * @param replaceStr
     * @returns {*}
     */
    isset : function(str, replaceStr) {
        if( $.isEmpty(str) ){
            return replaceStr;
        }else{
            return str;
        }
    },

    /**
     * 자바스크립트용 ucfirst
     * @param str
     * @returns {string}
     */
    ucfirst : function(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    },

    /**
     * 태그 제거
     * @param html
     * @param useSpace
     * @returns {string|*}
     */
    stripHtml : function(html,useSpace = false) {
        if (html === undefined || html === null) return '';
        // 태그를 제거할 때 빈 문자열로 할지, 한 칸 공백으로 할지 결정
        const replacement = useSpace ? ' ' : '';
        // 태그 제거 후, 공백 옵션이 true일 경우 연속된 공백은 하나로 정리
        let result = html.replace(/<[^>]*>?/gm, replacement);
        if (useSpace) {
            result = result.replace(/\s+/g, ' ').trim();
        }
        return result;
    },

    /**
     * 날짜 형식 변경
     * @param dateString
     * @returns {*}
     */
    formatStringDate:function(dateString) {
    return dateString.replace(/(\d{4})(\d{2})(\d{2})/, "$1-$2-$3");
    },
    formatStringDateShort:function(dateStr) {
        return dateStr.slice(2, 4) + '/' + dateStr.slice(4, 6) + '/' + dateStr.slice(6);
    },
    formatStrToDate : (str) => {
        return str.slice(0, 2) + "/" + str.slice(2, 4) + "/" + str.slice(4, 6);
    },
    /**
     * 객체 빈값 체크
     * checkList = [ { key:'', name:'', type:'' } ]
     */
    checkObjectEmptyData:function(object, checkList){
        let rslt = true;
        checkList.forEach((each)=>{
            //console.log(each);
            /*console.log('object : ', object);
            console.log('key : ', each.key);
            console.log('object value : ',object[each.key]);*/
            //if( typeof each.type != 'undefined' ){
            if( 'array' === each.type ){
                if(0>=object[each.key].length){
                    rslt = each.name;
                    return false;
                }
            }else{
                if($.isEmpty(object[each.key])){
                    rslt = each.name;
                    return false;
                }
            }


        });
        return rslt;
    },

    /**
     * 이메일 형식 체크
     * @param email
     * @returns {boolean}
     */
    validateEmail:function(email) {
        const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return re.test(email);
    },

    /**
     * 랜덤 정수 생성
     * @param min
     * @param max
     * @returns {*}
     */
    getRandomInt:function(min, max){
        return Math.floor(Math.random() * (max - min + 1)) + min;
    },

    /**
     * 숫자만 반환
     * @param mixedData
     * @returns {number}
     */
    setNumber:function(mixedData){
        const data = Number(mixedData);
        if(isNaN(data)){
            return 0;
        }else{
            return data;
        }
    },

    /**
     * 객체에서 필요한 키값들만 가져오기
     * @param inpObj
     * @param availList
     * @returns {*}
     */
    getObjAvailElement:function(inpObj, availList){
        const selectPropertiesFilter = (obj, keys) =>
            Object.fromEntries(Object.entries(obj).filter(([key]) => keys.includes(key)));
        return selectPropertiesFilter(inpObj, availList);
    },

    /**
     * 객체 데이터를 URL 쿼리 스트링 만들기
     * @param obj
     * @returns {string}
     */
    objectToQueryString:function(obj) {
        const queryString = Object.entries(obj).map(([key, value]) => {
            if(Array.isArray(value)){
                const rtValue = JSON.stringify(value);
                return `${encodeURIComponent(key)}=${rtValue}`;
            }else{
                return `${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
            }
        }).join('&');
        return queryString;
    },

    /**
     * 단일 배열초기화
     * @param arr
     */
    clearArray:function(arr){
        if (Array.isArray(arr)) {
            for (let i = 0; i < arr.length; i++) {
                arr[i] = ''; // 빈 문자열로 설정
            }
        }
    },
    /**
     * 단일 객체 초기화
     * @param obj
     */
    clearObject:function(obj){
        if (obj !== null && typeof obj === 'object' && !Array.isArray(obj)) {
            for (const key in obj) {
                if (obj.hasOwnProperty(key)) {
                    obj[key] = ''; // 빈 문자열로 설정
                }
            }
        }
    },

    /**
     * 객체 혹은 배열 초기화
     * @param obj
     */
    clearArrayOrObject:function(obj){
        let rslt = '';
        if (Array.isArray(obj)) {
            $.clearArray(obj);
            rslt = 'array';
        } else if (obj !== null && typeof obj === 'object') {
            $.clearObject(obj);
            rslt = 'object';
        }
        return rslt;
    },

    refineDateToStr:function(dataList){
        for(let idx in dataList){
            if( dataList[idx] instanceof Date ){
                dataList[idx] = moment(dataList[idx]).format('YYYY-MM-DD');
            }
        }
        return dataList;
    },

    isNumeric:function(value) {
        return !isNaN(parseFloat(value)) && isFinite(value);
    },

    /**
     * 숫자 포맷 변경
     * @param val
     * @returns {string}
     */
    setNumberFormat:function(val){
        if( $.isNumeric(val) ){
            return (val+'').number_format();
        }else{
            return '0';
        }
    },

    /**
     * 객체 복사
     * @param obj
     * @returns {any}
     */
    copyObject:function(obj){
        //console.log('복사 전 데이터', obj);
        return JSON.parse(JSON.stringify(obj));
    },
    copyObjectDeep:function(obj){
        return _.shuffle(obj);
    },
    /**
     * Object를 복사하고 값은 초기화
     * @param obj
     * @param resetIgnore
     * @returns {*}
     */
    copyAndInitListData:function(obj, resetIgnore){
        let listData = $.copyObject(obj);
        for(let key in listData){
            if( typeof(resetIgnore) != 'undefined' && resetIgnore.includes(key) ){
                continue;
            }

            if( Array.isArray(listData[key]) ){
                listData[key] = [];
            }else{
                listData[key] = '';
            }

        }
        return listData;
    },


    /**
     * Object를 Parameter로
     * @param $obj
     * @returns {string}
     */
    arrayToParam:function(array){
        var resultArray = [];
        for(var key in array){
            resultArray.push( key + '=' + array[key] );
        }
        return resultArray.join('&');
    },

    /**
     * Key Value형태를 가지고 있는 어레이를 쿼리 스트링으로 변경
     * @param arr
     * @returns {*}
     */
    arrayKeyValueToQueryString:function(arr) {
    return arr
        .filter(item => item.name) // name이 있는 것만
        .map(item => {
            let key = encodeURIComponent(item.name);
            let value = item.value !== undefined ? encodeURIComponent(item.value) : '';
            return key + '=' + value;
        })
        .join('&');
    },

    /**
     * 공백문자 체크
     * usage : $.isEmpty(변수);
     * @param strInput
     * @returns {boolean}
     */
    isEmpty:function(strInput){
        if( (typeof strInput == "undefined") || strInput === "" || strInput == null ){
            return true;
        }else{
            return false;
        }
    },
    /**
     * 공백문자 체크 (0값 까지 체크한다)
     * @param strInput
     * @returns {boolean}
     */
    isEmpty2:function(strInput){
        if( (typeof strInput == "undefined") || strInput === "" || strInput == null || 0 === strInput || '0' === strInput ){
            return true;
        }else{
            return false;
        }
    },

    /**
     * 0 + 날짜등 까지 모두 체크
     * @param strInput
     * @returns {boolean}
     */
    isEmptyAll:function(strInput){
        if( (typeof strInput == "undefined") || strInput === "" || strInput == null || 0 === strInput || '0' === strInput || '0000-00-00' === strInput || false === strInput || Object.keys(strInput).length === 0 ){
            return true;
        }else{
            return false;
        }
    },
    
    
    /**
     * 오브젝트 체크
     * @param obj
     * @returns {boolean}
     */
    isEmptyObject:function(obj){
        return Object.keys(obj).length === 0;
    },
    nl2br:function(strInput){
        if( strInput != null ){
            return strInput.replace(/\n/g, "<br />");
        }else{
            return "";
        }
    },

    getChangesObject : function(obj1, obj2) {
        const changes = {};
        for (const key in obj1) {
            if (obj1.hasOwnProperty(key)) {
                if (typeof obj1[key] === 'object' && typeof obj2[key] === 'object' && !Array.isArray(obj1[key])) {
                    const nestedChanges = $.getChangesObject(obj1[key], obj2[key]);
                    if (Object.keys(nestedChanges).length > 0) {
                        changes[key] = nestedChanges;
                    }
                } else if (obj1[key] !== obj2[key]) {
                    changes[key] = obj2[key];
                }
            }
        }
        for (const key in obj2) {
            if (obj2.hasOwnProperty(key) && !obj1.hasOwnProperty(key)) {
                changes[key] = obj2[key];
            }
        }
        return changes;
    },

    ajaxAlertMsg:function(data){
        if( 200 == data.code ){
            $.msg(data.message, "", "success");
        }else{
            $.msg(data.message, "", "error");
        }
    },

    postAsync : async function(url, param, msgType, isPreload){
        let setPreload = true;
        if(!$.isEmpty(isPreload)){
            setPreload = isPreload;
        }

        if(setPreload) $('#layerDim').show();

        let jsonResult = null;
        try {
            await $.post(url,param, function(json){
                if( typeof json !== 'object' ){
                    console.log(json);
                    json = JSON.parse(json);
                }
                jsonResult = json;
                if( 200 !== json.code ){
                    if( typeof msgType != 'undefined' ){
                        $.msg(json.message, "", "warning");
                    }else{
                        alert(json.message);
                    }
                }
            });
        }catch (e){
            $('#layerDim').hide();
            //alert('오류가 발생했습니다. 개발팀에 문의하세요.');
            console.log('오류 발생',e);
        }
        if(setPreload) $('#layerDim').hide();
        return jsonResult;
    },

    postAsyncPreloader : async function(url, param){
        $('#layerDim').removeClass('display-none');
        let jsonResult = null;
        await $.post(url,param, function(json){
            jsonResult = json;
            $('#layerDim').addClass('display-none');
        });
        return jsonResult;
    },

    msg:function(title, text, icon){
        let option = {
            title: '<span style="font-size:20px;">'+title+'</span>',
            html: '<span style="font-size:15px;">'+text+'</span>',
            icon : icon,
            confirmButtonText: '확인',
            cancelButtonText: '아니오',
            width : 600
            /*button: {
                text: "확인",
                closeModal: true,
            },*/
        };
        return Swal.fire(option);
    },

    msgConfirm:(title, text, inpConfirmButtonText)=>{
        let confirmButtonText = '예';
        if(typeof inpConfirmButtonText != 'undefined' && !$.isEmpty(inpConfirmButtonText)){
            confirmButtonText = inpConfirmButtonText;
        }

        let option = {
            title: '<span style="font-size:20px;">'+title+'</span>',
            html: '<span style="font-size:15px;">'+text+'</span>',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            icon : 'question',
            width : 600,
            showCancelButton : true,
            confirmButtonText : confirmButtonText,
            cancelButtonText: '아니오',
        };
        return Swal.fire(option);
    },

    msgPrompt:function(title, msg, inputPlaceholder, callBack, falseCallBack){
        var option = {
            title: '<span style="font-size:20px;">'+title+'</span>',
            html: '<span style="font-size:15px;">'+msg+'</span>',
            input:'text',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            icon: 'question',
            inputPlaceholder : inputPlaceholder,
            width : 600,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '확인',
            cancelButtonText: '취소',
            didClose : ()=>{
            },
        };
        Swal.fire(option).then( function(result){
            if (result.isConfirmed) {
                if( typeof(callBack) != 'undefined' ){
                    callBack(result);
                }
            }else{
                if( typeof(falseCallBack) != 'undefined' ){
                    falseCallBack();
                }
            }
        });
    },

    msgTextarea:function(title, placeholder){
        let option = {
            title: '<span style="font-size:18px;">'+title+'</span>',
            html: '<span style="font-size:13px;">'+placeholder+'</span>',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            icon : 'question',
            width : '500px',
            showCancelButton : true,
            confirmButtonText : '확인',
            cancelButtonText: '취소',
            //inputLabel: title,
            input: 'textarea',
            inputPlaceholder: placeholder,
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            customClass: {
                input: 'custom-swal-textarea' // 커스텀 클래스 지정
            },
        };
        return Swal.fire(option);
    },

    /**
     * 숫자만 반환
     * @param str
     * @returns {RegExpMatchArray}
     */
    getOnlyNumber:function(str){
        return (''+str).match(/(\d+\.\d+|\d+)/g);
    },

    /**
     * 날짜 형식 변경
     * @param dateStr
     * @returns {string}
     */
    formatShortDate : (dateStr) => {
        let rslt = '-';
        if( typeof dateStr === 'string' && !$.isEmpty(dateStr) && '0000-00-00' !== dateStr && '0000-00-00 00:00:00' !== dateStr ){
            const days = ['일', '월', '화', '수', '목', '금', '토'];

            // 날짜 파싱
            const dateParts = dateStr.split(' ')[0].split('-');
            const year = dateParts[0].substring(2); // 뒤의 두 자리만 가져옴
            const month = dateParts[1];
            const day = dateParts[2];

            // 요일 계산
            const date = new Date(dateStr);
            const dayOfWeek = days[date.getDay()];

            rslt = `${year}/${month}/${day}(${dayOfWeek})`
        }
        return rslt;
    },
    formatShortDateWithoutWeek : (dateStr) => {
        let rslt = '';
        if( !$.isEmpty(dateStr) && '0000-00-00' !== dateStr && '0000-00-00 00:00:00' !== dateStr ){
            // 날짜 파싱
            const dateParts = dateStr.split(' ')[0].split('-');
            const year = dateParts[0].substring(2); // 뒤의 두 자리만 가져옴
            const month = dateParts[1];
            const day = dateParts[2];
            // 요일 계산
            rslt = `${year}/${month}/${day}`
        }
        return rslt;
    },
    formatShortTime : (dateStr) => {
        let rslt = '';
        if( !$.isEmpty(dateStr) && '0000-00-00' !== dateStr && '0000-00-00 00:00:00' !== dateStr ){
            rslt = dateStr.split(' ')[1];
        }
        return rslt;
    },

    /**
     * 날짜 더하기
     * @param dateStr
     * @param addCnt
     * @returns {string}
     */
    dateAdd : (dateStr, addCnt) =>{
        const date = new Date(dateStr);
        if( isNaN(date) ){
            return dateStr;
        }else{
            try{
                date.setDate(date.getDate() + addCnt);
                return date.toISOString().split('T')[0];
            }catch(e){
                return dateStr;
            }
        }
    },

    /**
     * 날짜 더하기
     * @param dateStr
     * @param diffDays
     * @returns {string}
     */
    addDays : (dateStr, diffDays) =>{
        const [y, m, d] = dateStr.split('-').map(Number);

        // 로컬 기준 자정으로 처리 (DST 이슈 줄이기 위해 정오 기준을 쓰는 경우도 있지만, 한국이면 거의 문제 없음)
        const dt = new Date(y, m - 1, d);
        dt.setDate(dt.getDate() + diffDays);

        const yy = dt.getFullYear();
        const mm = String(dt.getMonth() + 1).padStart(2, '0');
        const dd = String(dt.getDate()).padStart(2, '0');

        return `${yy}-${mm}-${dd}`;
    },

    /**
     * 승인상태 아이콘 진행 상태
     * @param status
     * @returns {*}
     */
    getAcceptName : (status) => {
        const acceptMap = {
            'n' : "준비",
            'r' : "<span class='sl-blue'>승인요청</span>",
            'p' : "<span class='sl-green'>완료</span>",
            'y' : "<span class='sl-green'>완료</span>",
            'f' : "<span class='text-danger'>반려</span>",
            'x' : "해당없음",
        }
        return acceptMap[status];
    },
    getAcceptName2 : (status) => {
        //console.log(status);
        const acceptMap = {
            'n' : {name:"준비",color:'',bgColor:'gray-button'},
            'r' : {name:"진행",color:'sl-orange',bgColor:'bg-info'},
            'f' : {name:"반려",color:'text-danger',bgColor:'bg-danger'},
            'p' : {name:"완료",color:'sl-green',bgColor:'bg-success'},
            'x' : {name:"해당없음",color:'sl-blue',bgColor:'gray-button'},
            '' : {name:"준비",color:'',bgColor:'gray-button'},
        }
        return acceptMap[status];
    },

    getAssortAcceptNameColor : (status) => {
        const acceptMap = {
            'n' : {name:"대기",'color':''},
            'r' : {name:"고객입력요청",'color':'sl-orange'},
            'x' : {name:"고객입력완료(검토필)",'color':'sl-blue'},
            'f' : {name:"고객입력완료(검토필)",'color':'sl-blue'},
            'p' : {name:"완료",'color':'sl-green'},
        }
        return acceptMap[status];
    },

    getProjectScheduleIcon : (status) => {
        const procIcon = {
            0 : '<span class="text-muted">-</span>',
            1 : '<i class="fa fa-play sl-blue" aria-hidden="true" style=""></i> 파일등록',
            2 : '<i class="fa fa-play sl-blue" aria-hidden="true" style=""></i> 결재단계',
            3 : '<i class="fa fa-play sl-blue" aria-hidden="true" style=""></i> 결재완료',
            4 : '<i class="fa fa-play sl-blue" aria-hidden="true" style=""></i> 발송완료',
            9 : '<span class="text-green">PASS</span>',
            10 : '<i class="fa fa-lg fa-check-circle text-green" aria-hidden="true"></i>',
        };
        return procIcon[status];
    },

    getStatusIcon : (status) => {
        const procIcon = {
            0 : '<span class="text-muted">-</span>',
            1 : '<i class="fa fa-play sl-blue" aria-hidden="true" style=""></i>',
            2 : '<i class="fa fa-lg fa-check-circle text-green" aria-hidden="true"></i>',
        };
        return procIcon[status];
    },
    getStatusIcon2 : (status) => {
        const procIcon = {
            0 : '<span class="text-muted">-</span>',
            1 : '<i class="fa fa-lg fa-play-circle sl-blue" aria-hidden="true" style=""></i>',
            2 : '<i class="fa fa-lg fa-check-circle text-green" aria-hidden="true"></i>',
        };
        return procIcon[status];
    },

    getProcColor : (status) => {
        const procColor = {
            0 : '', //미확정
            1 : 'sl-blue', //진행중
            2 : 'text-green', //확정
            3 : 'text-green', //리오더
            4 : 'text-danger', //반려
            5 : 'text-danger',
        };
        return procColor[status];
    },

    getProcColor2 : (status) => {
        const procColor = {
            0 : '',            //미요청
            1 : 'sl-blue',     //요청
            2 : 'sl-blue',     //처리중
            3 : 'text-green',  //처리완료
            4 : 'text-danger', //처리불가
            5 : 'text-green',  //확정
            6 : 'text-danger', //반려
        };
        return procColor[status];
    },

    /**
     * 오늘 날짜
     * @returns {string}
     */
    todayYmd : () =>{
        const d = new Date();
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${day}`;
    },

    /**
     * 날짜 차이 계산
     * @param firstDateStr
     * @param secondDateStr
     * @returns {string|number}
     */
    diffDate : (firstDateStr, secondDateStr) =>{
        const firstDate = new Date(firstDateStr);
        const secondDate = new Date(secondDateStr);
        if( 'Invalid Date' == firstDate || null === firstDateStr || 'Invalid Date' == secondDate || null === secondDateStr ){
            return '';
        }
        // 날짜 차이 계산
        let diff = firstDate - secondDate; // 밀리초 단위 차이
        let sign = diff < 0 ? -1 : 1; // 차이의 부호를 결정 (지남: -1, 남음: +1)
        // 날짜 구성 요소 계산
        return Math.round(Math.abs(diff) / (1000 * 60 * 60 * 24)) * sign;
    },

    /**
     * 남은일 수
     * @param targetDateStr
     * @param isCss
     * @returns {string}
     */
    remainDate : (targetDateStr, isCss) => {
        const now = new Date();
        const targetDate = new Date(targetDateStr);

        if( 'Invalid Date' == targetDate || null === targetDateStr ){
            return '';
        }

        // 날짜 차이 계산
        let diff = targetDate - now; // 밀리초 단위 차이
        let sign = diff < 0 ? -1 : 1; // 차이의 부호를 결정 (지남: -1, 남음: +1)

        // 날짜 구성 요소 계산
        let days = Math.round(Math.abs(diff) / (1000 * 60 * 60 * 24));
        let months = 0;
        while (days >= 30) {
            let daysInMonth = new Date(targetDate.getFullYear(), targetDate.getMonth(), 0).getDate();
            if (days >= daysInMonth) {
                days -= daysInMonth;
                months += 1;
                targetDate.setMonth(targetDate.getMonth() + sign);
            } else {
                break;
            }
        }

        // 결과 문자열 생성
        let resultParts = [];
        if (months > 0) resultParts.push(`${months}개월`);
        if (days > 0 || resultParts.length === 0) resultParts.push(`${days}일`);

        let result = resultParts.join(' ') + (sign === 1 ? ' 남음' : ' 지남');
        if( '0일 지남' === result || '0일 남은' === result  ){
            result = 'D-DAY';
        }
        if( isCss ){
            if( sign === 1  ){
                result = `<span class="sl-green">${result}</span>`;
            }else{
                result = `<span class="text-danger">${result}</span>`;
            }
        }

        return result.trim();
    },

    /**
     * 남은일수 (표기 변경)
     * @param targetDateStr
     * @param isCss
     * @returns {string}
     */
    remainDate2 : (targetDateStr, isCss) => {
        const now = new Date();
        const targetDate = new Date(targetDateStr);

        if( 'Invalid Date' == targetDate || null === targetDateStr ){
            return '';
        }

        // 날짜 차이 계산
        let diff = targetDate - now; // 밀리초 단위 차이
        let sign = diff < 0 ? -1 : 1; // 차이의 부호를 결정 (지남: -1, 남음: +1)

        // 날짜 구성 요소 계산
        let days = Math.round(Math.abs(diff) / (1000 * 60 * 60 * 24));

        // 결과 문자열 생성
        let resultParts = [];

        if (days > 0 || resultParts.length === 0) resultParts.push(`${days}일`);

        let result = resultParts.join(' ') + (sign === 1 ? ' 남음' : ' 지남');
        if( '0일 지남' === result || '0일 남은' === result  ){
            result = 'D-DAY';
        }
        if( isCss ){
            if( sign === 1  ){
                result = `<span class="sl-green">${result}</span>`;
            }else{
                result = `<span class="text-danger">${result}</span>`;
            }
        }

        return result.trim();
    },

    remainDateWithoutPast : (targetDateStr, isCss) => {
        const now = new Date();
        const targetDate = new Date(targetDateStr);

        if( 'Invalid Date' == targetDate || null === targetDateStr ){
            return '';
        }

        // 날짜 차이 계산
        let diff = targetDate - now; // 밀리초 단위 차이
        let sign = diff < 0 ? -1 : 1; // 차이의 부호를 결정 (지남: -1, 남음: +1)

        // 날짜 구성 요소 계산
        let days = Math.round(Math.abs(diff) / (1000 * 60 * 60 * 24));
        let months = 0;
        while (days >= 30) {
            let daysInMonth = new Date(targetDate.getFullYear(), targetDate.getMonth(), 0).getDate();
            if (days >= daysInMonth) {
                days -= daysInMonth;
                months += 1;
                targetDate.setMonth(targetDate.getMonth() + sign);
            } else {
                break;
            }
        }

        // 결과 문자열 생성
        let resultParts = [];
        if (months > 0) resultParts.push(`${months}개월`);
        if (days > 0 || resultParts.length === 0) resultParts.push(`${days}일`);

        let result = resultParts.join(' ') + (sign === 1 ? ' 남음' : ' 지남');
        if( '0일 지남' === result || '0일 남은' === result  ){
            result = 'D-DAY';
        }
        if( isCss ){
            if( sign === 1  ){
                result = `<span class="sl-green">${result}</span>`;
            }else{
                result = ``;
            }
        }

        return result.trim();
    },

    copyClipBoard(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';  // 화면에서 이동하지 않도록 고정
        textArea.style.top = '-1000px';     // 화면 밖으로 위치시켜 사용자에게 보이지 않도록 함

        if( $('#modalProduction').is(':visible') ){
            const targetDiv = document.getElementById("modalProduction");
            targetDiv.appendChild(textArea);
        }else{
            document.body.appendChild(textArea);
        }
        textArea.focus();
        textArea.select();
        try {
            const successful = document.execCommand('copy');
            if( successful ){
                $.msg('내용이 클립보드에 복사 되었습니다.','','success');    
            }else{
                $.msg('내용 클립보드 복사 실패.','','warning');
            }
        } catch (err) {
            console.error('Fallback: Failed to copy: ', err);
        }

        if( $('#modalProduction').is(':visible') ){
            const targetDiv = document.getElementById("modalProduction");
            targetDiv.appendChild(textArea);
        }else{
            document.body.appendChild(textArea);
        }
    },
    isObject(value) {
        return value !== null && typeof value === 'object' && value.constructor === Object;
    },

    getToday() {
        const today = new Date();
        return today.toISOString().slice(0, 10);
    },

    getObjectDiff(obj1, obj2) {
        const diff = {};
        const allKeys = new Set([...Object.keys(obj1), ...Object.keys(obj2)]);
        allKeys.forEach((key) => {
            const val1 = obj1[key];
            const val2 = obj2[key];

            // 둘 다 객체이고 null이 아닐 때 (주의: typeof null === 'object' 이므로 체크)
            if (
                typeof val1 === "object" && val1 !== null &&
                typeof val2 === "object" && val2 !== null
            ) {
                const nestedDiff = $.getObjectDiff(val1, val2);
                if (Object.keys(nestedDiff).length > 0) {
                    diff[key] = nestedDiff;
                }
            } else if (val1 !== val2) {
                diff[key] = val2;
            }
        });
        return diff;
    },

    getMargin(cost, price) {
        return Number(price)>0?Math.round(100-(Number(cost)/Number(price)*100)):0;
    },

    /**
     * 큰 숫자 한글 변환
     */
    numberToKorean(num){
        const unit = ['', '만', '억', '조'];
        let unitPos = 0;
        let koreanNum = '';

        while (num > 0) {
            let section = num % 10000;

            // 만 단위 이상만 표시하고, 1000~9999 (1천~9천)은 표시하지 않음
            if (section !== 0 && !(unitPos === 0 && section < 10000 && section >= 1000)) {
                const formattedSection = unitPos > 0
                    ? section.toLocaleString()
                    : section;

                koreanNum = formattedSection + unit[unitPos] + koreanNum;
            }

            num = Math.floor(num / 10000);
            unitPos++;
        }

        return koreanNum === '' ? '-' : koreanNum;
    },

});

$(function(){
    //공급사면 불필요한 메뉴 가리기
   if( $(location).attr('pathname').indexOf('/provider/') != -1 ){
       $('#menu_policy').hide();
       $('#menu_goods').hide();
       $('#menu_order').hide();
       $('#menu_board').hide();
       $('#menu_scm').hide();
   }
});

var CommonUtil = {
    /**
     * 문서 열기
     */
    openDocument : function( docDept, docType, sno ){
        sno = (typeof sno != 'undefined') ? sno : '';
        var openParam = [];
        openParam['docDept'] = docDept;
        openParam['docType'] = docType;
        openParam['sno'] = sno;
        var url = '../work/project_doc_reg.php?' + $.arrayToParam(openParam);
        window.open(url, 'popup_factory_reg', 'width=1400, height=900, scrollbars=yes');
    },

};


/**
 * 재정의
 * @param fileCd
 * @param addParam
 */
function layer_add_info(fileCd, addParam) {
    if ($.type(addParam) != 'object') var addParam = {};

    if (addParam['layerFormID'] == undefined) addParam['layerFormID'] = 'addSearchForm';
    if (addParam['dataFormID'] == undefined) addParam['dataFormID'] = 'info_' + fileCd;
    if (addParam['parentFormID'] == undefined) addParam['parentFormID'] = fileCd + 'Layer';
    if (addParam['dataInputNm'] == undefined) addParam['dataInputNm'] = '';
    if (addParam['reqUrl'] == undefined) addParam['reqUrl'] = '';

    var loadChk = $('#' + addParam['layerFormID']).length;
    var title = '';
    var dataInputNm = addParam['dataInputNm'];

    switch (fileCd) {
        case 'scm':
            title = "고객사 선택";
            addParam['dataInputNm'] = dataInputNm != '' ? dataInputNm : fileCd + "No";
            addParam['size'] = "wide";
            break;
        case 'brand':
            title = "브랜드";
            addParam['size'] = "wide";
            addParam['dataInputNm'] = dataInputNm != '' ? dataInputNm : fileCd + "Cd";
            break;
        case 'goods':
            title = "상품";
            addParam['size'] = "wide";
            break;
        case 'category':
            title = "카테고리";
            addParam['size'] = "wide";
            break;
        case 'category_batch':
            title = "카테고리 일괄선택";
            addParam['size'] = "wide";
            break;
        case 'member_group':
            title = "회원 등급";
            addParam['dataInputNm'] = dataInputNm != '' ? dataInputNm : 'memberGroupNo';
            break;
        case 'coupon':
            title = "쿠폰 선택";
            addParam['size'] = "wide";
            addParam['dataInputNm'] = dataInputNm != '' ? dataInputNm : fileCd + "No";
            break;
        case 'delivery':
            title = "배송비 선택";
            addParam['dataInputNm'] = dataInputNm != '' ? dataInputNm : fileCd + "No";
            addParam['size'] = "wide";
            break;
        case 'board':
            title = "게시판";
            addParam['dataInputNm'] = dataInputNm != '' ? dataInputNm : fileCd + "No";
            break;
        case 'goods_option':
            title = "옵션선택";
            break;
        case 'excel':
            title = "엑셀 다운로드";
            addParam['size'] = "wide";
            break;
        case 'sms_contents':
            title = "SMS / LMS 문구";
            break;
        case 'must_info':
            title = "상품 필수정보";
            break;
        case 'display_main':
            title = "기존 진열 상품 선택";
            break;
        case 'detail_info':
            title = addParam['detailInfoTitle'];
            break;
        case 'hscode':
            title = addParam['detailInfoTitle'];
            addParam['size'] = "wide";
            break;
        case 'purchase':
            title = "매입처";
            addParam['size'] = "wide";
            addParam['dataInputNm'] = dataInputNm != '' ? dataInputNm : fileCd + "No";
            break;
        case 'naver_stats':
            title = "네이버 쇼핑 노출상품 현황";
            addParam['size'] = "wide";
            break;
        case 'daum_stats':
            title = "쇼핑하우 노출상품 현황";
            addParam['size'] = "wide";
            break;
        case 'event_select':
            //기획전 관련 설정, 상품일괄관리에서 검색용 으로사용
            title = "기획전 선택";
            addParam['size'] = "wide";
            break;
        case 'comeback_coupon_result':
            addParam['size'] = 'wide';
            break;
        //주문리스트 그리드 설정
        case 'order_grid_config' :
        //상품리스트 그리드 설정
        case 'goods_grid_config' :
            title = "조회항목 설정";
            break;
        case 'goods_option_grid_config' :
            title = "조회항목 설정";
            break;
        case 'goods_restock_batch':
            addParam['size'] = 'wide';
            break;
        case 'goods_benefit':
            title = "상품 혜택";
            addParam['size'] = "wide";
            break;
        case 'manage':
            title = "운영자선택";
            break;
        case 'excel_order_draft':
            title = "발주서 다운로드";
            addParam['size'] = "wide";
            break;
        case 'admin_log_excel':
            title = "개인정보접속기록 엑셀 다운로드 내역";
            addParam['size'] = "wide";
            break;
        case 'excel_service_privacy':
            title = "개인정보수집 동의상태 변경내역";
            addParam['size'] = "wide";
            break;
        default:
            title = "";
    }
    if (addParam['layerTitle'] == undefined) {
        addParam['layerTitle'] = title;
    }

    const reqUrl = $.isEmpty(addParam['reqUrl'])?'../share/layer_' + fileCd + '.php':addParam['reqUrl'];

    $.ajax({
        url: reqUrl,
        type: 'get',
        data: addParam,
        async: false,
        success: function (data) {
            if (loadChk == 0) {
                data = '<div id="' + addParam['layerFormID'] + '">' + data + '</div>';
            }
            var layerForm = data;
            var configure = {
                title: addParam['layerTitle'],
                size: get_layer_size(addParam['size']),
                message: $(layerForm),
                closable: true
            };
            if (typeof addParam['events'] == 'object') {
                BootstrapDialog.show($.extend({}, configure, addParam['events']));
            } else {
                BootstrapDialog.show(configure);
            }

        }
    });
}

/**
 * 카테고리 연결하기 Ajax layer
 */
function layer_register(typeStr, mode, isDisabled) {
    var addParam = {
        "mode": mode,
    };

    if (typeStr == 'scm') {
        $('input:radio[name=scmFl]:input[value=y]').prop("checked", true);
    }else if (typeStr == 'scmOrder') {
        $('input:radio[name=scmFl]:input[value="1"]').prop("checked", true);
        typeStr = 'scm';
    }

    if (!_.isUndefined(isDisabled) && isDisabled == true) {
        addParam.disabled = 'disabled';
    }
    layer_add_info(typeStr,addParam);
}


function formatDate(date) {
    let d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join('-');
}

function isFullWidth(cp) {
    // CJK / 전각 범위
    return (
        cp >= 0x1100 && (
            cp <= 0x115F ||
            cp === 0x2329 || cp === 0x232A ||
            (0x2E80 <= cp && cp <= 0xA4CF && cp !== 0x303F) ||
            (0xAC00 <= cp && cp <= 0xD7A3) ||
            (0xF900 <= cp && cp <= 0xFAFF) ||
            (0xFE10 <= cp && cp <= 0xFE19) ||
            (0xFE30 <= cp && cp <= 0xFE6F) ||
            (0xFF00 <= cp && cp <= 0xFF60) ||
            (0xFFE0 <= cp && cp <= 0xFFE6)
        )
    );
}

function measureExcelUnits(str) {
    if (str == null) return 0;
    let maxLine = 0;
    for (const line of String(str).split('\n')) {
        let units = 0;
        for (const ch of line) {
            const cp = ch.codePointAt(0);
            if (ch === ' ') { units += 0.75; continue; }
            if (isFullWidth(cp)) units += 2;
            else units += 1; // 영문/숫자/기타
        }
        if (units > maxLine) maxLine = units;
    }
    return Math.ceil(maxLine);
}