<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PurchaseOffer */

$this->title = 'Create Purchase Offer';
$this->params['breadcrumbs'][] = ['label' => 'Purchase Offers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchase-offer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
