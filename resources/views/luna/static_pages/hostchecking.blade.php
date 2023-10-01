@extends('luna.layouts.default')

@section('content')
    <body>
        正在检测目标服务器,请不要刷新或关闭页面...
    </body>
@endsection

@section('js')
    <script>
        var getting = {
            url:'{{ url('check-order-status', ['orderSN' => $order_sn]) }}',
            dataType:'json',
            success:function(res) {
                if (res.code == 400010) {
                    // wait
                }
                if (res.code == 400001) {
                    window.clearTimeout(timer);
                    layer.alert("{{ __('dujiaoka.prompt.order_is_expired') }}", {
                        icon: 2
                    }, function () {
                        window.location.href = '/'
                    });
                }
                if (res.code == 400005) {
                    window.clearTimeout(timer);
                    layer.alert("目标服务器检查失败,请联系站长. ", {
                        icon: 2
                    }, function () {
                        window.location.href = '/'
                    });
                }
                if (res.code == 400000) {
                    window.clearTimeout(timer);
                    // 重定向到结账页
                    window.location.href = '{{ url('bill', ['orderSN' => $order_sn]) }}';
                }
            }
        };
        var timer = window.setInterval(function(){$.ajax(getting)},5000);
    </script>
@endsection
