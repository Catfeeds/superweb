<?php
/**
 * Created by PhpStorm.
 * User: lychee
 * Date: 2018/5/3
 * Time: 9:53
 */
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>订单预览</title>
    <link rel="stylesheet" href="https://cdn.bootcss.com/weui/1.1.2/style/weui.min.css">
    <link rel="stylesheet" href="https://cdn.bootcss.com/jquery-weui/1.2.0/css/jquery-weui.min.css">
</head>
<body>
<div class="weui-form-preview">
    <div class="weui-form-preview__hd">
        <label class="weui-form-preview__label">付款金额</label>
        <em class="weui-form-preview__value">$<?= $order->order_money ?></em>
    </div>
    <div class="weui-form-preview__bd">
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">详情</label>
            <span class="weui-form-preview__value"><?= $order->order_info ?></span>
        </div>

        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">支付方式</label>
            <span class="weui-form-preview__value">paypal</span>
        </div>

        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">下单日期</label>
            <span class="weui-form-preview__value"><?= date('Y-m-d H:i:s', $order->order_addtime) ?></span>
        </div>
    </div>
    <div class="weui-form-preview__ft">
        <a class="weui-form-preview__btn weui-form-preview__btn_default" href="javascript:">取消订单</a>
        <a id="pay" class="weui-form-preview__btn weui-form-preview__btn_primary" href="<?= \yii\helpers\Url::to(['pay/create', 'order'=> $order->order_sign]) ?>">支付</a>
    </div>
</div>

<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
</body>
</html>
<script>
    $('#pay').click(function(){
        $.showLoading("going to paypal");
    });
</script>

