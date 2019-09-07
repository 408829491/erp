<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/8/15
 * Time: 11:19
 */

namespace backend\controllers;


use Da\QrCode\QrCode;
use Yii;
use yii\web\Controller;
use Da\QrCode\Contracts\ErrorCorrectionLevelInterface;

class QrCodeController extends Controller
{
    public function actionPurchaseCode($id)
    {
        $url=Yii::$app->urlManager->createAbsoluteUrl(['purchase/provider-purchase-view','id'=>$id]);
        $qrCode = (new QrCode($url, ErrorCorrectionLevelInterface::HIGH))
            ->useEncoding('UTF-8')->setLogoWidth(60)->setSize(300)->setMargin(5);
        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();
    }

}