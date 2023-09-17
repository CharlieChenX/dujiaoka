<div class="goods-msg">
    <div class="goods-name">
        <svg style="vertical-align: middle;" t="1602941112468" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="1512" width="25" height="25" data-spm-anchor-id="a313x.7781069.0.i14">
            <path d="M727.04 750.592h-68.608v-81.92H686.08V249.856L512 99.328 337.92 253.952v414.72h28.672v81.92H296.96l-40.96-40.96V235.52l13.312-30.72 215.04-190.464h54.272l215.04 186.368 14.336 30.72v478.208z" fill="#3C8CE7" p-id="1513" data-spm-anchor-id="a313x.7781069.0.i12" class=""></path>
            <path d="M869.376 638.976l-147.456-18.432-35.84-40.96V350.208l69.632-28.672 147.456 147.456 12.288 28.672v99.328l-46.08 41.984zM768 543.744l65.536 8.192v-35.84L768 449.536v94.208zM154.624 638.976l-46.08-40.96v-99.328l12.288-28.672 147.456-147.456 69.632 28.672v229.376l-35.84 40.96-147.456 17.408z m35.84-123.904v35.84L256 542.72v-94.208l-65.536 66.56z" fill="#3C8CE7" p-id="1514" data-spm-anchor-id="a313x.7781069.0.i15" class=""></path>
            <path d="M512 465.92m-67.584 0a67.584 67.584 0 1 0 135.168 0 67.584 67.584 0 1 0-135.168 0Z" fill="#3C8CE7" p-id="1515" data-spm-anchor-id="a313x.7781069.0.i16" class=""></path>
            <path d="M479.232 660.48h58.368v233.472h-58.368zM391.168 723.968h58.368v157.696h-58.368zM461.824 922.624h58.368v88.064h-58.368zM574.464 748.544h58.368v188.416h-58.368z" fill="#00EAFF" p-id="1516" data-spm-anchor-id="a313x.7781069.0.i17" class="selected"></path>
        </svg>
        <span>
            {{ $gd_name }}
            @if($type == \App\Models\Goods::AUTOMATIC_DELIVERY)
            <span class="small-tips tips-green">{{ __('goods.fields.automatic_delivery') }}</span>
            @elseif($type == \App\Models\Goods::AUTOMATIC_WEBSITE)
            <span class="small-tips tips-green">{{ __('goods.fields.automatic_website') }}</span>
            @else
            <span class="small-tips tips-yellow">{{ __('goods.fields.manual_processing') }}</span>
            @endif
            <span class="small-tips tips-blue">{{__('goods.fields.in_stock')}}({{ $in_stock }})</span>
            @if($buy_limit_num > 0)
            <span class="small-tips tips-red"> {{__('dujiaoka.purchase_limit')}}({{ $buy_limit_num }})</span>
            @endif
        </span>
    </div>
    <div class="price">
        <span class="price-sign">￥</span>
        <span class="price-num">{{ $actual_price }}</span>
        @if((int)$retail_price)
        <span class="price-c">[<del>￥{{ $retail_price }}</del>]</span>
        @endif
    </div>
    <!--批发价-->
    @if(!empty($wholesale_price_cnf) && is_array($wholesale_price_cnf))
        <div class="sale">
            @foreach($wholesale_price_cnf as $ws)
                <span class="small-tips tips-pink">
                {{ __('luna.goods_disc_1') }}{{ $ws['number'] }}{{ __('luna.goods_disc_2') }}{{ $ws['price']  }}{{ __('luna.goods_disc_3') }}
            </span>
            @endforeach
        </div>
    @endif

    @include('luna.components.website')
    
    <input class="pay-num" name="by_amount" id="orderNumber"
           required lay-verify="required|order_number"
           type="hidden" value="1">
    <div class="entry">
        <span class="l-msg">{{ __('luna.buy_email') }}：</span>
        <label class="input">
            <input type="text" name="email"
                   required lay-verify="required|email"
                   placeholder="{{ __('luna.buy_email_tips') }}">
        </label>
    </div>
    <!--优惠码-->
    @if(isset($open_coupon))
        <div class="entry">
            <span class="l-msg">{{ __('luna.buy_disc') }}：</span>
            <label class="input">
                <input type="text" name="coupon_code"
                       placeholder="{{ __('luna.buy_disc_tips') }}">
            </label>
        </div>
    @endif

    @if($type == \App\Models\Goods::MANUAL_PROCESSING && is_array($other_ipu))
        @include('luna.components.manual')
    @endif

    @if(dujiaoka_config_get('is_open_search_pwd') == \App\Models\Goods::STATUS_OPEN)
        <div class="entry">
            <span class="l-msg">{{ __('luna.buy_pass') }}：</span>
            <label class="input">
                <input type="text" name="search_pwd" value=""
                       required lay-verify="required"
                       placeholder="{{ __('luna.buy_pass_tips') }}">
            </label>
        </div>
    @endif

    <!--验证码-->
    @if(dujiaoka_config_get('is_open_img_code') == \App\Models\Goods::STATUS_OPEN)
        <div class="entry code">
            <span class="l-msg">{{ __('luna.buy_code') }}：</span>
            <label class="input">
                <input type="text" name="img_verify_code" value="" required
                       lay-verify="required" placeholder="{{ __('luna.buy_code_tips') }}">
            </label>
            <img class="captcha-img" onclick="refresh()"
                 src="{{ captcha_src('buy') . time() }}"
                 alt="">
            <script>
                function refresh() {
                    $('img[class="captcha-img"]').attr('src', '{{ captcha_src('buy') }}' + Math.random());
                }
            </script>
        </div>
    @endif
    @if(dujiaoka_config_get('is_open_geetest') == \App\Models\Goods::STATUS_OPEN)
        <div class="entry code">
            <span class="l-msg">{{ __('dujiaoka.behavior_verification') }}：</span>
            <span id="geetest-captcha"></span>
            <span id="wait-geetest-captcha"
                  class="show">{{ __('luna.buy_loading_verification') }}</span>
        </div>
    @endif

    <!--支付方式-->
    <div class="pay notSelection">
        <input type="hidden" name="payway" lay-verify="payway"
               value="{{ $payways[0]['id'] ?? 0 }}">
        @if ($actual_price > 0.0001)
        @foreach($payways as $key => $way)
            <div class="pay-type @if($key == 0) pay-select @endif"
                 data-type="{{ $way['pay_check'] }}" data-id="{{ $way['id'] }}"
                 data-name="{{ $way['pay_name'] }}">
            </div>
        @endforeach
        @endif
    </div>
</div>
