<tr v-if="document.approvalType.includes('ework')">
    <th>
        작업지시서
    </th>
    <td class="new-style">
        <div class="font-11 btn btn-black-line hover-btn cursor-pointer mgr5" @click="window.open(`<?=$eworkUrl?>?sno=${style.sno}&mode=${document.approvalType}`, 'popupCertKey', 'width=1400, height=950');">
            작업지시서 열기<i class="fa fa-external-link" aria-hidden="true"></i>
        </div>

    </td>
</tr>