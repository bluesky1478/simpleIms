<th>
    <i class='fa fa-info-circle' aria-hidden="true"></i>
    <?=$vTitle?>
</th>
<td>
    <div v-html="getCodeMap()['<?=$code?>'][<?=$vModel?>]"></div>
</td>
