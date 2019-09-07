<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '商品溯源';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="commodity-source-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('新增', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'inspect_date',
            'inspect_from',
            'inspect_man',
            'inspect_organization',
            'provider_id',
            'provider_name',
            'status',
            'is_delete',
            'trace_report_arr:ntext',
            'modify_time:datetime',
            'create_time:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
