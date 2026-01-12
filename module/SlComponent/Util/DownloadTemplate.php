<?php
namespace SlComponent\Util;

use SlComponent\Database\DBUtil;
use SlComponent\Database\SearchVo;

/**
 * Sitelab Code Key List
 * Class SlCode
 * @package SlComponent\Util
 */
class DownloadTemplate {

    const sampleFabric  = '
    <tr >
        <th class="title" rowspan=5 >원단{% index1 %}</th>
        <td class="title">폭</td>
        <td >{% width1 %}</td>
        <th class="title" rowspan=5 >원단{% index2 %}</th>
        <th class="title">폭</th>
        <td >{% width2 %}</td>
    </tr>
    <tr >
        <th class="title">요척</th>
        <td >{% yochuck1 %}</td>
        <th class="title">요척</th>
        <td >{% yochuck2 %}</td>
    </tr>
    <tr >
        <td colspan=2 rowspan=3 ></td>
        <td colspan=2 rowspan=3 ></td>
    </tr>
    <tr ></tr>
    <tr ></tr>
    ';



}


