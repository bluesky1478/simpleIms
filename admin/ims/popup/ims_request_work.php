<tr>
    <th >고객사명</th>
    <td>
        <span class="font-16">{% items.customerName %}</span>
    </td>
    <th >고객 납기일</th>
    <td>
        <span class="font-16 text-danger"><b>{% project.customerDeliveryDtShort %}</b></span>
        <span v-html="project.customerDeliveryRemainDt"></span>
    </td>
</tr>
<tr>
    <th >영업 담당자</th>
    <td>
        <span class="font-16">{% project.salesManagerNm %}</span>
    </td>
    <th >디자인 담당자</th>
    <td>
        <span class="font-16">{% project.designManagerNm %}</span>
    </td>
</tr>
