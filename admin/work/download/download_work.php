<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
    <title>Excel Download</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        br{mso-data-placement:same-cell;}
        .number-format1{mso-number-format:"0_\)\;\\\(0\\\)";}
        .number-format2{mso-number-format:"\@";}
        .title{font-weight:bold; background-color:#F6F6F6; text-align:center;}
        .text-center{text-align: center}
    </style>
</head>
<body>
<table border="1" style="width:1200px">
    <colgroup>
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
        <col style="width:57px" />
    </colgroup>
    <td class="text-center" colspan="21" height="20px">
        <b style="font-size:15px;">작업 지시서</b>
    </td>
    <tr>
        <th class="title" colspan="3">S/#</th>
        <td colspan="10">{% serial %}</td>
        <td colspan="8"></td>
    </tr>
    <tr>
        <th class="title" colspan="3">업체명.</th>
        <td colspan="4">{% companyName %}</td>
        <th class="title" colspan="3">생산처</th>
        <td colspan="4">{% companyName %}</td>
        <th class="title" colspan="3">작성일</th>
        <td colspan="4">{% regDt %}</td>
    </tr>
    <tr>
        <th class="title" colspan="3">제품명</th>
        <td colspan="4" >{% productName %}</td>
        <th colspan="3"  class="title">생산구분</th>
        <td colspan="4" >{% produceType %}</td>
        <th colspan="3"  class="title">의뢰일</th>
        <td colspan="4" >{% requestDt %}</td>
    </tr>
    <tr>
        <th colspan="3"  class="title">성별</th>
        <td colspan="4" >{% specType %}</td>
        <th colspan="3"  class="title">제조국</th>
        <td colspan="4" >{% produceCountry %}</td>
        <th colspan="3"  class="title">납기일</th>
        <td colspan="4" >{% completeDt %}</td>
    </tr>
    <tr>
        <td colspan="21" style="height:500px; " >
            {% sampleImage %}
        </td>
    </tr>
    <tr>
        <th colspan="3" class="title">구분</th>
        <th colspan="2" class="title">옵션</th>
        <th colspan="2" class="title">옵션</th>
        <th colspan="2" class="title">옵션</th>
        <th colspan="2" class="title">옵션</th>
        <th colspan="2" class="title">옵션</th>
        <th colspan="2" class="title">옵션</th>
        <th colspan="2" class="title">옵션</th>
        <th colspan="2" class="title">옵션</th>
        <th colspan="2" class="title">TTL</th>
    </tr>
    <tr>
        <th colspan="3" class="title">-</th>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
    </tr>
    <tr>
        <th colspan="3" class="title">합계</th>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
    </tr>

    <tr>
        <th colspan="21" class="title">마크정보</th>
    </tr>
    <tr>
        <th rowspan="2" class="title">1</th>
        <th class="title">위치</th>
        <td colspan="5">-</td>
        <th class="title">종류</th>
        <td colspan="4">-</td>
        <th class="title">색상</th>
        <td colspan="3">-</td>
        <th class="title">크기</th>
        <td colspan="4">-</td>
    </tr>
    <tr>
        <td colspan="20" style="padding:10px">마크 이미지</td>
    </tr>
    <tr>
        <th colspan="21" class="title">마크 작업 유의사항</th>
    </tr>
    <tr>
        <td colspan="21" rowspan="6" >{% markCaution %}</td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr>
        <th colspan="21" class="title">마크 라벨 위치</th>
    </tr>
    <tr>
        <td colspan="21" style="height:500px; " >
            {% sampleImage %}
        </td>
    </tr>
    <tr>
        <th colspan="21" class="title">마크 라벨 작업 유의사항</th>
    </tr>
    <tr>
        <td colspan="21" rowspan="6" >{% labelCaution %}</td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr>
        <th colspan="21" class="title">사이즈스펙</th>
    </tr>
    <tr>
        <td colspan="21" style="height:500px; " >
            {% sampleImage %}
        </td>
    </tr>
    <tr>
        <th colspan="21" class="title">[완제품 치수 정보] - 단위 cm</th>
    </tr>
    <tr>
        <th colspan="3" class="title">구분</th>
        <th colspan="2" class="title">편차</th>
        <th colspan="2" class="title">옵션</th>
        <th colspan="2" class="title">옵션</th>
        <th colspan="2" class="title">옵션</th>
        <th colspan="2" class="title">옵션</th>
        <th colspan="2" class="title">옵션</th>
        <th colspan="2" class="title">옵션</th>
        <th colspan="2" class="title">옵션</th>
        <th colspan="2" class="title">옵션</th>
    </tr>
    <tr>
        <th colspan="3" class="title">-</th>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
        <td colspan="2" >0</td>
    </tr>
    <tr>
        <th colspan="21" class="title">사이즈 스펙 측정 작업 유의사항</th>
    </tr>
    <tr>
        <td colspan="21" rowspan="6" >{% sizeCaution %}</td>
    </tr>
</table>
</body>
</html>