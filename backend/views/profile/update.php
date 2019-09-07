<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CommodityProfile */

$this->title = 'Update Commodity Profile: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Commodity Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="commodity-profile-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
