<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<link rel="stylesheet" href="/admin/script/dropzone5/dropzone.min.css" type="text/css">
<script src="/admin/script/dropzone5/dropzone.min.js"></script>

<script type="text/javascript">
    const fileListTemplate = `
    <div class="">
        <ul class="ims-file-list" >
            <li class="hover-btn" v-for="(file, fileIndex) in file.files">
                <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
            </li>
        </ul>
    </div>`;
    const simpleFileOnlyNotHistoryTemplate = `
    <div class="">
        <ul class="ims-file-list" >
            <li class="hover-btn" v-for="(file, fileIndex) in file.files">
                <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
            </li>
        </ul>
    </div>`;
    const simpleFileOnlyHistoryTemplate = `
    <div class="dp-flex dp-flex-gap5">
        <ul class="ims-file-list" >
            <li class="hover-btn" v-for="(file, fileIndex) in file.files">
                <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
            </li>
        </ul>
        <div class="btn btn-sm btn-white" @click="openFileHistory2(params, file_div)" v-if="file.files.length > 0">이력</div>
    </div>
    `;

    const simpleUploadNotHistoryTemplate = `
    <div class="mgt10">
        <span v-html="file.memo"></span>
        <ul class="ims-file-list" >
            <li class="hover-btn" v-for="(file, fileIndex) in file.files">
                <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
            </li>
        </ul>
        <form :id="id" class="set-dropzone mgt5" @submit.prevent="uploadFiles" >
            <div class="fallback">
                <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
            </div>
        </form>
    </div>`;

    const simpleFileOnlyTemplate = `
    <div class="mgt10">
        <ul class="ims-file-list" >
            <li class="hover-btn" v-for="(file, fileIndex) in file.files">
                <a :href="'<?=$nasDownloadUrl?>name='+file.fileName+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
            </li>
        </ul>
        <form :id="id" class="set-dropzone mgt5 display-none" @submit.prevent="uploadFiles" v-show="false" style="" >
            <div class="fallback">
                <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
            </div>
        </form>
        <div class="btn btn-white" style="padding-top:10px;height:42px!important;" @click="openFileHistory(project.sno, id)" v-show="file.files.length > 0">이력</div>
    </div>`;
    const simpleUploadTemplate = `
    <div class="mgt10" v-if='!$.isEmpty(file)'>
        <ul class="ims-file-list" >
            <li class="hover-btn" v-for="(file, fileIndex) in file.files">
                <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
            </li>
        </ul>
        <form :id="id" class="set-dropzone mgt5" @submit.prevent="uploadFiles" v-show="!accept">
            <div class="fallback">
                <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
            </div>
        </form>
        <div class="btn btn-white" style="padding-top:10px;height:42px!important;" @click="openFileHistory(project.sno, id)" v-show="!$.isEmpty(file.files) && file.files.length > 0">이력</div>
    </div>`;

    /*<li ><div class="btn btn-sm btn-white">일괄다운</div></li>*/
    const uploadTemplate = `
    <div class="mgt10" v-if="!$.isEmpty(file)">
        <div class="ims-file-list-title">
            <span><b>{% file.title %}</b></span>
            <span>{% file.memo %}</span>
        </div>

        <ul class="ims-file-list" >
            <li class="hover-btn" v-for="(file, fileIndex) in file.files">
                <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
            </li>
        </ul>

        <div class="dp-flex dp-flex-gap5 mgt5">
            <form :id="id" class="set-dropzone " @submit.prevent="uploadFiles" v-show="!accept">
                <div class="fallback">
                    <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
                </div>
            </form>
            <form v-show="accept" class="disabled-dropzone dropzone"><div class="dz-default dz-message"><span class="text-muted">승인완료 파일 업로드 불가.</span></div></form>
            <div class="btn btn-white" style="padding-top:10px;height:42px!important;" @click="openFileHistory(project.sno, id)">이력</div>
        </div>

    </div>`;
    const uploadTemplate2 = `
    <div class="mgt10" >
        <div class="ims-file-list-title">
            <span v-if="typeof file != 'undefined' && null !== file"><b>{% file.title %}</b></span>
            <span v-if="typeof file != 'undefined' && null !== file">{% file.memo %}</span>
        </div>
        <ul class="ims-file-list" v-if="typeof file != 'undefined' && null !== file">
            <li class="hover-btn" v-for="(file, fileIndex) in file.files">
                <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
            </li>
        </ul>
        <form :id="id" class="set-dropzone mgt5" @submit.prevent="uploadFiles" v-show="!accept">
            <div class="fallback">
                <input name="upfile" type="file" multiple @change="file.files = $event.target.files" />
            </div>
        </form>
        <form v-show="accept" class="mgt5 disabled-dropzone dropzone"><div class="dz-default dz-message"><span class="text-muted">승인완료 파일 업로드 불가.</span></div></form>
        <div class="btn btn-white " style="padding-top:10px;height:42px!important;" @click="openFileHistory2(params, id)">이력</div>
    </div>`;

    const acceptTemplate = `
    <div>
        <section v-show="1==type">
        <span class="font-16 pd5" style="background-color:#eff7ff">{% title %}: <b :class="setAcceptClass(project[field])" v-html="project[field+'Kr']"></b></span>
        <div class="mgt5">
            <?php if( $isAuth ) { ?>
            <div class="btn btn-accept hover-btn " @click="setConfirmY(field,project)" v-show="'p'!==project[field]">승인</div>
            <div class="btn btn-reject hover-btn"  @click="setConfirmN(field,project)" v-show="'p'!==project[field]">반려</div>
            <div class="btn btn-reject hover-btn"  @click="setConfirmN(field,project)" v-show="'p'===project[field]">승인번복</div>
            <?php } ?>
            <div class="btn btn-white btn-history" @click="openStatusHistory(project.sno, field)" >이력</div>
        </div>
        </section>
        <section v-show="2==type">
        <div class="mgt5">
            <?php if( $isAuth ) { ?>
            <div class="btn btn-accept hover-btn " @click="setConfirmY(field,project)" v-show="'p'!==project[field]">승인</div>
            <div class="btn btn-reject hover-btn"  @click="setConfirmN(field,project)" v-show="'p'!==project[field]">반려</div>
            <div class="btn btn-reject hover-btn"  @click="setConfirmN(field,project)" v-show="'p'===project[field]">승인번복</div>
            <?php } ?>
            <div class="btn btn-white btn-history" @click="openStatusHistory(project.sno, field)" >이력</div>
            <span class="mgl15 font-16 pd5" style="background-color:#eff7ff">{% title %}: <b :class="setAcceptClass(project[field])" v-html="project[field+'Kr']"></b></span>
        </div>
        </section>
    </div>
    `;
    const acceptTemplate2 = `
    <div>
        <section>
        <div class="mgt5">
            <?php if( $isAuth ) { ?>
            <div class="btn btn-accept hover-btn " @click="ImsService.setConfirmPass(field,condition,after,before,memo)" v-show="'p'!==condition[field]">승인</div>
            <div class="btn btn-reject hover-btn"  @click="ImsService.setConfirmFail(field,condition,after,before)" v-show="'p'!==condition[field]">반려</div>
            <div class="btn btn-reject hover-btn"  @click="ImsService.setConfirmFail(field,condition,after,before)" v-show="'p'===condition[field]">승인번복</div>
            <?php } ?>
            <div class="btn btn-white btn-history" @click="openStatusHistory(condition, field)" >이력</div>
            <span class="mgl15 font-13 pd5" style="background-color:#eff7ff">{% title %}: <b :class="setAcceptClass(condition[field])" v-html="condition[field+'Kr']"></b></span>
        </div>
        </section>
    </div>
    `;


    Vue.component("simple-file-only",{
        delimiters: ['{%', '%}'],
        props:['file','id', 'project', 'accept'],
        template:simpleFileOnlyTemplate,
    });

    Vue.component("simple-file-upload",{
        delimiters: ['{%', '%}'],
        props:['file','id', 'project', 'accept'],
        template:simpleUploadTemplate,
    });

    Vue.component("simple-file-not-history-upload",{
        delimiters: ['{%', '%}'],
        props:['file','id'],
        template:simpleUploadNotHistoryTemplate,
    });

    Vue.component("simple-file-only-not-history-upload",{
        delimiters: ['{%', '%}'],
        props:['file'],
        template:simpleFileOnlyNotHistoryTemplate,
    });

    Vue.component("simple-file-only-history-upload",{
        delimiters: ['{%', '%}'],
        props:['file','params', 'file_div'],
        template:simpleFileOnlyHistoryTemplate,
    });

    Vue.component("file-upload",{
        delimiters: ['{%', '%}'],
        props:['file','id', 'project', 'accept'],
        template:uploadTemplate,
    });

    Vue.component("file-upload2",{
        delimiters: ['{%', '%}'],
        props:['file','id', 'params', 'accept'],
        template:uploadTemplate2,
    });

    /*단순 승인*/
    Vue.component("ims-accept",{
        delimiters: ['{%', '%}'],
        props:['title', 'field', 'project', 'type'],
        template:acceptTemplate,
    });

    Vue.component("ims-accept2",{
        delimiters: ['{%', '%}'],
        props:['title', 'field', 'condition', 'after', 'before', 'memo'],
        template:acceptTemplate2,
    });


    /* 파일 리스트 */
    const simpleFileListTemplate = `
    <div v-if="files.length > 0">
        <ul class="ims-file-list">
            <li class="hover-btn" v-for="(file, fileIndex) in files">
                <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">{% fileIndex+1 %}. {% file.fileName %}</a>
            </li>
        </ul>
    </div>
    <div v-else>
        -
    </div>
    `;
    Vue.component("simple-file-list",{
        delimiters: ['{%', '%}'],
        props:['files'],
        template:simpleFileListTemplate,
    });

    /* 파일 리스트 */
    const simpleFileListTemplate2 = `
    <div class="cursor-pointer hover-btn btn btn-white font-11"
    style="height:25px!important;padding:4px 6px 2px 6px!important;"
    v-for="(file, fileIndex) in files" @click="openUrl('simple-file', '<?=$nasUrl?>' + file.filePath)"
    v-if="files.length > 0">보기</div>
    `;
    Vue.component("simple-file-list2",{
        delimiters: ['{%', '%}'],
        props:['files'],
        template:simpleFileListTemplate2,
    });


    /*미니 이력 버튼*/
    Vue.component("mini-file-history",{
        delimiters: ['{%', '%}'],
        props:['params', 'file_div'],
        template:`<div class="btn btn-white font-11" style="height:25px!important;padding:4px 6px 2px 4px!important;" @click="openFileHistory2(params, file_div)">이력</div>`,
    });

    /* 코멘트 */
    const commentCntTemplate = `
    <span v-if="data.commentCnt > 0" class="relative">
        <div style="position:absolute; top:-5px;left:5px; font-size: 14px !important; color:#FF6347"  class="font-12">
            <i class="fa fa-circle" aria-hidden="true"></i>
        </div>
        <div style="position:absolute; top:0;left:0; color:#fff;font-size: 8px !important; text-align: center; width:22px"  class="font-12">
            {% data.commentCnt %}
        </div>
    </span>
    `;
    Vue.component("comment-cnt",{
        delimiters: ['{%', '%}'],
        props:['data'],
        template:commentCntTemplate,
    });

    /* 코멘트 */
    const commentCntTemplate2 = `
    <span v-if="data > 0" >
        <div style="position:absolute; top:-8px;right:-5px; font-size: 14px !important; color:#FF6347"  class="font-12">
            <i class="fa fa-circle" aria-hidden="true"></i>
        </div>
        <div style="position:absolute; top:-3px;right:-10px; color:#fff;font-size: 8px !important; text-align: center; width:22px"  class="font-12">
            {% data %}
        </div>
    </span>
    `;
    Vue.component("comment-cnt2",{
        delimiters: ['{%', '%}'],
        props:['data'],
        template:commentCntTemplate2,
    });

    /* 탭 */
    const tabTemplate = `
    <li role="presentation" :class="data.tabValue == data.tabMode?'active':''" @click="data.changeTab(data.tabValue, data.cookieName)" :id="'tab-'+data.tabValue">
        <a :href="'#tab-'+data.tabValue" data-toggle="tab" >{% data.tabName %}</a>
    </li>
    `;
    Vue.component("tab-component",{
        delimiters: ['{%', '%}'],
        props:['data'],
        template:tabTemplate,
    });

    /* 탭 */
    const tabTemplate2 = `
    <li role="presentation" :class="data.tabValue == data.tabMode?'active':''" @click="data.changeTab(data.tabValue, data.cookieName)" :id="'tab-'+data.tabValue" >
        <a :href="'#tab-'+data.tabValue" data-toggle="tab" style="border:solid 1px #E4E4E4 !important; border-top:none !important; border-bottom:solid 1px #888888!important;">{% data.tabName %}</a>
    </li>
    `;
    Vue.component("tab-component2",{
        delimiters: ['{%', '%}'],
        props:['data'],
        template:tabTemplate2,
    });


    /*모달*/
    Vue.component('ims-modal', {
        props: {
            visible: {
                type: Boolean,
                required: true,
            },
            title: {
                type: String,
                default: '',
            },
            width: {
                type: String,
                default: '90%',
            },
            maxWidth: {
                type: String,
                default: '400px',
            },
            showCloseButton: {
                type: Boolean,
                default: true,
            },
        },
        methods: {
            closeModal() {
                this.$emit('update:visible', false);
            },
            /*handleBackgroundClick(event) {
                if (event.target.classList.contains('ims-modal')) {
                    this.closeModal();
                }
            },*/
        },
        template: `
    <div v-if="visible" class="ims-modal" >
      <div class="ims-modal-content relative" :style="{ width: width, maxWidth: maxWidth }" @click.stop>
        <span v-if="'n' !== showCloseButton" class="ims-close-button" @click="visible=false">&times;</span>
        <h2 v-if="title" class="ims-modal-title">{{ title }}</h2>
          <div class="ims-modal-body">
          <slot>Default modal content</slot>
        </div>
        <div v-if="$slots.footer" class="ims-modal-footer">
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  `,
    });


    //결재 템플릿
    const approvalTemplate = `
    <div class="mgb10">
        <div class="table-title gd-help-manual mgt3">
            <div class="area-title">
                <i class="fa fa-play fa-title-icon" aria-hidden="true" ></i>
                결재 (<span v-html="$.getAcceptName(data.mainData.ework.data[field1+'Approval'])"></span>)
                <div class="btn btn-white btn-sm" @click="openApprovalHistory({styleSno:data.mainData.product.sno}, field2)">결재이력</div>
            </div>
        </div>
        
        <div>

            <div class="dp-flex">
                <div class="btn btn-blue-line hover-btn cursor-pointer mgt5"
                    v-if="'n' === data.mainData.ework.data[field1+'Approval'] || 1 >= data.approvalData[field2].length "
                     @click="openApprovalWrite(data.mainData.project.customerSno, data.mainData.project.sno, field2, data.mainData.product.sno)" >
                    결재 진행
                </div>
            </div>

            <table class="table table-pd-0 table-td-height30 table-th-height30 table-borderless table-cols " style="margin:0 !important;width:1px; border-top:solid 1px #d1d1d1"
                   v-if="'n' !== data.mainData.ework.data[field1+'Approval'] && data.approvalData[field2].length > 1 ">
                <tbody>
                <tr >
                    <th class="text-center" v-for="approval in data.approvalData[field2]">{% approval.appTitle %}</th>
                </tr>
                <tr>
                    <td class="text-center relative" v-for="approval in data.approvalData[field2]">
                        <div class="w-120px" >
                            <div class="font-13 pd5 hover-btn cursor-pointer" @click="openApprovalView(data.approvalData[field2][1].approvalSno)">
                                {% approval.name %}
                            </div>
                            <div class="font-9 pd3">&nbsp;{% $.formatShortDate(approval.completeDt) %}&nbsp;</div>
                            <div class="rounded-circle bg-success ims-approval-icon-position" v-if="'accept' === approval.status">승인</div>
                            <div class="rounded-circle bg-success ims-approval-icon-position" v-if="'complete' === approval.status">PASS</div>
                            <div class="rounded-circle bg-danger ims-approval-icon-position" v-if="'reject' === approval.status">반려</div>
                            <div class="" v-if="'' === approval.status"></div>
                            <div class="rounded-circle bg-danger ims-approval-icon-position" v-if="'ready' === approval.status">대기</div>
                        </div>

                        <div class="btn btn-sm btn-white mgb5" v-if="'proc' === approval.status && '<?=$managerInfo['sno']?>' == approval.managerSno"
                            @click="openApprovalView(approval.approvalSno)">
                            결재처리
                        </div>

                    </td>
                </tr>
                </tbody>
            </table>

            <div class="btn btn-red btn-red-line2 hover-btn cursor-pointer mgt5" v-if="'f' === data.mainData.ework.data[field1+'Approval']"
                 @click="openApprovalWrite(data.mainData.project.customerSno, data.mainData.project.sno, field2, data.mainData.product.sno)" >
                재결재 진행
            </div>

        </div>
    </div>
    `;

    Vue.component("ework-approval",{
        delimiters: ['{%', '%}'],
        props:['data', 'field1', 'field2'],
        template:approvalTemplate,
    });



    /*카테고리 선택*/
    Vue.component('CategorySelector', {
        delimiters: ['{%', '%}'],
        template: `
    <div >
        <div v-if="levels[0].length > 0">
            <select
                v-for="(options, index) in levels"
                :key="index"
                v-model="selecteds[index]"
                @change="onChange(index)"
                class="form-control mgl5"
                v-if="options.length"
            >
                <option value=""> {% index + 1 %}차 카테고리 선택</option>
                <option v-for="opt in options" :key="opt.id" :value="opt.id">{% opt.name %}</option>
            </select>
        </div>
        <div v-else>
분류 없음
        </div>
    </div>
  `,
        props:{
            init:{
                type:String,
                default:''
            },value:{
                type:Array,
                default : ()=>['', '', '', '']
            },
            rootParentId: {
                type: String,
                default: ''
            }
        },
        data() {
            return {
                selecteds: ['', '', '', ''], // 1~4차 선택값
                levels: [[], [], [], []]     // 1~4차 옵션 리스트
            };
        },
        created() {
            this.selecteds = [...this.value];
            this.fetchCategories(this.rootParentId, 0); // 1차 로딩
        },
        watch: {
            // 부모에서 v-model 값이 변경되면 내부 동기화
            value(newVal) {
                if (!Array.isArray(newVal)) return;
                if (newVal.join('|') !== this.selecteds.join('|')) {
                    this.selecteds = [...newVal];
                    // 경로에 맞춰 하위 로딩이 필요하면 주석 해제
                    // this.hydrateByPath();
                }
            },
            rootParentId(newVal) {
                // 전체 초기화 후 1차 로딩
                this.selecteds = ['', '', '', ''];
                this.levels = [[], [], [], []];
                this.fetchCategories(this.rootParentId, 0); // 1차 재로딩
            },
            // 내부 값이 바뀌면 부모로 'input' 이벤트로 내보내야 v-model이 동작함
            selecteds: {
                deep: true,
                    handler(v) {
                    this.$emit('input', [...v]);     // 중요: 새 배열로 복제해서 emit (반응성 보장)
                    this.$emit('change', [...v]);    // 선택적으로 change 이벤트도 제공
                }
            },
        },
        methods: {
            async fetchCategories(cateCd, levelIndex) {
                try {
                    //console.log('cateCd',cateCd);
                    //console.log('levelIndex',levelIndex);
                    const res = await $.imsPost('getGoodsCate',{
                        level:levelIndex,
                        cateCd:cateCd,
                    });
                    this.$set(this.levels, levelIndex, res.data || []);
                } catch (err) {
                    console.error(`카테고리 불러오기 실패 (level ${levelIndex})`, err);
                    this.$set(this.levels, levelIndex, []);
                }
            },
            async onChange(index) {
                const selectedId = this.selecteds[index];
                // 이후 단계 초기화
                for (let i = index + 1; i < 4; i++) {
                    this.selecteds[i] = '';
                    this.levels[i] = [];
                }
                // 다음 단계 로딩
                if (index < 3 && selectedId) {
                    await this.fetchCategories(selectedId, index + 1);
                }
            }
        }
    });
</script>