<script src="/geetest/js/jquery.min.js"></script>
<script src="/geetest/js/gt.js"></script>
<div id="geetest-captcha"></div>
{{--<p id="wait" class="show">正在加载验证码...</p>--}}
{{--@define use Illuminate\Support\Facades\Config--}}
{{--<script>--}}
{{--    var geetest = function(url) {--}}
{{--        var handlerEmbed = function(captchaObj) {--}}
{{--            $("#geetest-captcha").closest('form').submit(function(e) {--}}
{{--                var validate = captchaObj.getValidate();--}}
{{--                if (!validate) {--}}
{{--                    alert('{{ Config::get('geetest.client_fail_alert')}}');--}}
{{--                    e.preventDefault();--}}
{{--                }--}}
{{--            });--}}
{{--            captchaObj.appendTo("#geetest-captcha");--}}
{{--            captchaObj.onReady(function() {--}}
{{--                $("#wait")[0].className = "hide";--}}
{{--            });--}}
{{--            if ('{{ $product }}' == 'popup') {--}}
{{--                captchaObj.bindOn($('#geetest-captcha').closest('form').find(':submit'));--}}
{{--                captchaObj.appendTo("#geetest-captcha");--}}
{{--            }--}}
{{--        };--}}
{{--        $.ajax({--}}
{{--            url: url + "?t=" + (new Date()).getTime(),--}}
{{--            type: "get",--}}
{{--            dataType: "json",--}}
{{--            success: function(data) {--}}
{{--                initGeetest({--}}
{{--                    gt: data.gt,--}}
{{--                    challenge: data.challenge,--}}
{{--                    product: "{{ $product?$product:Config::get('geetest.product', 'float') }}",--}}
{{--                    offline: !data.success,--}}
{{--                    new_captcha: data.new_captcha,--}}
{{--                    lang: '{{ Config::get('geetest.lang', 'zh-cn') }}',--}}
{{--                    http: '{{ Config::get('geetest.protocol', 'http') }}' + '://',--}}
{{--                    width: '{{ config('geetest.width', '300px') }}',--}}
{{--                }, handlerEmbed);--}}
{{--            }--}}
{{--        });--}}
{{--    };--}}
{{--    (function() {--}}
{{--        geetest('{{ $url?$url:Config::get('geetest.url', 'geetest') }}');--}}
{{--    })();--}}
{{--</script>--}}
{{--<style>--}}
{{--    .hide {--}}
{{--        display: none;--}}
{{--    }--}}
{{--</style>--}}