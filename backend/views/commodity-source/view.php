<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\CommoditySource */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Commodity Sources', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="commodity-source-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
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
        ],
    ]) ?>

</div>
