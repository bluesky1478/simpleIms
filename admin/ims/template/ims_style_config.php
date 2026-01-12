<div class="relative overflow-hidden">

    <div class="col-xs-12">

        <div class="table-title gd-help-manual ">
            <div class="flo-left lineR ">제작 사이즈</div>
            <div class="flo-right"></div>
        </div>

        <div>

            <div class="">
                <div class="ims-prd-option-block-area mgt10">
                    <div class="ims-prd-option-block mgt2" v-for="(option, optionIdx) in product.sizeOption">
                        <input type="text" placeholder="size" class="form-control inline-block full-left" v-model="product.sizeOption[optionIdx]">
                        <i class="fa fa-trash hover-btn cursor-pointer fa-lg mgl5" aria-hidden="true" style="color:#8a8a8a; padding-top:7px;" @click="deleteElement(product.sizeOption, optionIdx)"></i>
                    </div>
                </div>
            </div>
            <div class="mgt10 mgb10 text-muted">
                <div class="btn btn-sm btn-white" @click="setStandard('top', product)">상의기본</div>
                <div class="btn btn-sm btn-white" @click="setStandard('bottom', product)">하의기본</div>
                <div class="btn btn-sm btn-white" @click="setStandard('bottomKepid', product)">한전하의</div>
            </div>

        </div>
    </div>

</div>

