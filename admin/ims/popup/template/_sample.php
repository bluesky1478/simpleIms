<tr v-if="'sampleFile1' === document.approvalType">
    <th>
        샘플의뢰서(지시서) 정보
    </th>
    <td class="new-style">
        <?php if(\SiteLabUtil\SlCommonUtil::isDevId()) { ?>
            <!--<span class="text-muted">DEBUG 견적상태 : {% document.approvalType %}</span>-->
        <?php }?>

        {% (sample.prdYear+'').substr(-2) %} {% sample.prdSeason %}
        {% sample.productName %} 의

        <b>{% sample.sampleName %}</b> {% sample.sampleCount %}개

        <div v-if="!$.isEmpty(sample.fileList) && $.isObject(sample.fileList)" class="mgt5 dp-flex dp-flex-gap10">

            <div>
                <i class="fa fa-file-o mgr3 dp-flex-gap3" aria-hidden="true"></i>
                샘플의뢰서 :
            </div>

            <ul class="ims-file-list" >
                <li class="hover-btn" v-for="(file, fileIndex) in sample.fileList.sampleFile1.files">
                    <a :href="'<?=$nasDownloadUrl?>name='+encodeURIComponent(file.fileName)+'&path='+file.filePath" class="text-blue">
                        {% fileIndex+1 %}. {% file.fileName %}
                    </a>
                </li>
            </ul>
        </div>
    </td>
</tr>