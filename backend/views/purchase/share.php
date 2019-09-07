<style>
    html {
        background-color: #ffffff;
    }

    .container {
        line-height: 32px;
        text-align: center;
        font-size: 13px;
        padding: 10px;
    }

    .container img {
        max-width: 220px;
        background-color: #ffffff;
    }

</style>

<div class=" container">
    <div>采购员/供应商：<?= $agent_name ?></div>
    <div>采购单号：<?= $purchase_no ?></div>
    <div>计划交货日期：<?= $plan_date ?></div>
    <div><img src="/admin/qr-code/purchase-code?id=<?=Yii::$app->request->get('id') ?>"></div>
    <div>请使用微信扫描二维码分享给供应商</div>
</div>
