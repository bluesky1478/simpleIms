<script type="text/javascript">
    let externSelectDocument = function(itemDocData, docData, parentApp){
    }
    let externMountedFnc = function(){
    }
    let externVueMethod = {};
</script>

<div class="col-xs-12" >
    <div class="table-title ">
        <div class="flo-left" >포트폴리오 정보</div>
        <div class="flo-right " >
            <button type="button" class="btn btn-red btn-sm "  >고객 화면 미리보기</button>
        </div>
    </div>
    <div>
        <table class="table table-cols">
            <colgroup>
                <col class="width-md" />
                <col class="col-xs-3"  />
                <col class="col-xs-3" />
                <col  />
            </colgroup>

            <tbody v-for="(item, index) in items.docData.portData" :key="index">
            <tr>
                <th colspan="4" class="text-right">
                    <div style="float:left">
                        {% index+1 %}. {% item.styleName %}
                    </div>
                    <div>
                        <div type="button" class="btn btn-sm btn-white" @click="addListData('portData', items.docData.portData)" v-show="(items.docData.portData.length-1) === index">+ 추가</div>
                        <div type="button" class="btn btn-sm btn-white" @click="removeListData(items.docData.portData, index)" v-show="items.docData.portData.length > 1">- 삭제</div>
                    </div>
                </th>
            </tr>
            <tr>
                <th>스타일</th>
                <td><input type="text" class="form-control" v-model="item.styleName"></td>
                <td rowspan="4" class="text-center text-muted" style="background: #f0f0f0;border-right:solid 1px #e6e6e6">
                    <div v-if="$.isEmpty(item.imageThumbnail)">썸네일 이미지 미리보기</div>
                    <div style="width:400px; height:250px" class="" v-else>
                        <img :src="item.imageThumbnail" style="height:100%; " >
                    </div>
                </td>
                <td rowspan="4" class="text-center text-muted"  style="background: #f0f0f0; ">
                    <div v-if="$.isEmpty(item.imageDetail)">디테일 이미지 미리보기</div>
                    <div style="width:600px; height:250px" class="" v-else>
                        <img :src="item.imageDetail" style="height:100%; " >
                    </div>
                </td>
            </tr>
            <tr>
                <th>타입</th>
                <td>
                    <input type="text" class="form-control" v-model="item.styleType">
                </td>
            </tr>
            <tr>
                <th>썸네일 이미지</th>
                <td >
                    <input :type="'file'" accept="image/*" @change="uploadFile(item,'imageThumbnail',event)">
                </td>
            </tr>
            <tr>
                <th>디테일 이미지</th>
                <td>
                    <input :type="'file'" accept="image/*" @change="uploadFile(item,'imageDetail',event)">
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>