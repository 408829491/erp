<?php

namespace app\utils;

use yii\httpclient\Client;

class GeoDeUtils {

    private static $geoKey = '0d70e2ee6f05fe498133acdffcdf7ba7';

    private static $getAddressUrl = 'https://restapi.amap.com/v3/geocode/regeo';

    private static $findListByKeyword = 'https://restapi.amap.com/v3/assistant/inputtips?parameters';

    private static $directionDriving = 'https://restapi.amap.com/v3/direction/driving?parameters';

    // 根据关键字搜索相关地址列表
    public static function  findListByKeyword($keyword, $adcode) {
        $client = new Client();
        $request = $client->createRequest()
            ->setMethod('get')
            ->setUrl(self::$findListByKeyword)
            ->setData(['key'=>self::$geoKey, 'keywords'=>$keyword, 'city'=>$adcode]);
        return $request->send()->getData()['tips'];
    }

    // 逆地理编码，根据坐标获取详细地址
    public static function getAddress($lng, $lat) {
        $client = new Client();
        $request = $client->createRequest()
            ->setMethod('get')
            ->setUrl(self::$getAddressUrl)
            ->setData(['key'=>self::$geoKey, 'location'=>floatval($lng).','.floatval($lat), 'radius'=>1000, 'extensions'=>'base', 'batch'=>false, 'roadlevel'=>0]);

        return $request->send()->getData();
    }

    // 逆地理编码，根据坐标获取详细地址和附近地区
    public static function getAddressAndPoi($lng, $lat) {
        $client = new Client();
        $request = $client->createRequest()
            ->setMethod('get')
            ->setUrl(self::$getAddressUrl)
            ->setData(['key'=>self::$geoKey, 'location'=>floatval($lng).','.floatval($lat), 'radius'=>1000, 'extensions'=>'all', 'batch'=>false, 'roadlevel'=>0]);

        return $request->send()->getData();
    }

    // 驾车规划
    public static function directionDriving($lng1, $lat1, $lng2, $lat2) {
        $client = new Client();
        $request = $client->createRequest()
            ->setMethod('get')
            ->setUrl(self::$directionDriving)
            ->setData(['key'=>self::$geoKey, 'origin'=>floatval($lng1).','.floatval($lat1), 'destination'=>floatval($lng2).','.floatval($lat2), 'extensions'=>'base', 'strategy'=>9]);

        return $request->send()->getData();
    }

    // 根据两个坐标计算距离
    public static function calculateDistance($lng1, $lat1, $lng2, $lat2) {
        $R = 6378137; // 地球半径
        $lat1 = $lat1 * pi() / 180.0;
        $lat2 = $lat2 * pi() / 180.0;
        $a = $lat1 - $lat2;
        $b = ($lng1 - $lng2) * pi() / 180.0;
        $sa2 = sin($a / 2.0);
        $sb2 = sin($b / 2.0);

        return 2 * $R * asin(sqrt($sa2 * $sa2 + cos($lat1) * cos($lat2) * $sb2 * $sb2));
    }
}