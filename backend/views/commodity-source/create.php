<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CommoditySource */

$this->title = 'Create Commodity Source';
$this->params['breadcrumbs'][] = ['label' => 'Commodity Sources', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="commodity-source-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
