<?php
namespace Component\Ims\EnumType;

class PREPARED_TYPE
{
    const WORK   = ['title'=>'작지&사양서', 'typeName'=>'work'];
    const BT     = [
        'title'=>'BT',
        'typeName'=>'bt',
        'listTitle' => [
            '발송형태',
            '발송정보',
            '파일다운로드',
        ]
    ];
    const COST   = ['title'=>'견적확정', 'typeName'=>'cost'];
    const ESTIMATE = ['title'=>'가견적', 'typeName'=>'estimate'];
    const ORDER  = ['title'=>'가발주', 'typeName'=>'order'];

    const STATUS = [
        0 => '요청',
        1 => '처리중',
        2 => '처리완료',
        3 => '처리불가',
        4 => '승인',   //승인 -> 승인
        5 => '반려', //반려,번복 -> 다시해.
    ];
    const STATUS_COLOR = [
        0 => '요청',
        1 => '처리중',
        2 => '<span class="text-blue">처리완료</span>',
        3 => '<span class="text-danger">처리불가</span>',
        4 => '<span class="text-green">승인</span>',   //승인 -> 승인
        5 => '<span class="text-danger">반려</span>', //반려,번복 -> 다시해.
    ];
    const STATUS_ICON = [
        '' => '<span class="text-muted">없음</span>',
        0 => '<i class="fa fa-play sl-blue" aria-hidden="true" style=""></i>',
        1 => '<i class="fa fa-play sl-blue" aria-hidden="true" style=""></i>',
        2 => '<i class="fa fa-play sl-blue" aria-hidden="true" style=""></i>',
        3 => '<i class="fa fa-lg fa-times-circle text-danger" aria-hidden="true"></i>',
        4 => '<i class="fa fa-lg fa-check-circle text-green" aria-hidden="true"></i>',   //승인 -> 승인
        5 => '<i class="fa fa-lg fa-times-circle text-danger" aria-hidden="true"></i>', //반려,번복 -> 다시해.
    ];

    const ALL_TYPE_NAME = [
        'work',
        'bt',
        'cost',
        'estimate',
        'order',
    ];

}