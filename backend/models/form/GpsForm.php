<?php

namespace app\models\form;

use app\models\Model;
use yii\httpclient\Client;

class GpsForm extends Model
{
    public $account;
    public $password;

    public function rules()
    {
        return [
            [['account',], 'default', 'value' => ''],
            [['password',], 'default', 'value' => ''],
        ];
    }

    public function init()
    {
        $this->account = 'bonongkeji';//设备账户
        $this->password = '123456';//设备密码
    }


    /**
     * 获取gps所有设备定位数据列表
     * @return array
     */
    public function getGpsData()
    {
        $url = 'http://api.gpsoo.net/1/account/monitor';
        $data = [
            'access_token' => $this->getAccessToken(),
            'time' => time(),
            'target' => $this->account,
            'account' => $this->account,
            'map_type'=>'GOOGLE'
        ];
        return $this->request($url, $data);
    }


    /**
     * @param $url
     * @param $data
     * @return mixed
     */
    public function request($url, $data = [])
    {
        $client = new Client(['baseUrl' => $url]);
        $response = $client->createRequest()
            ->setData($data)
            ->send();
        $responseData = $response->getData();
        return $responseData;
    }


    /**
     * 获取access_token
     * @return mixed
     */
    public function getAccessToken()
    {
        $time = time();
        $signature = $this->signature($time);
        $url = 'http://api.gpsoo.net/1/auth/access_token';
        $data = [
            'account' => $this->account,
            'time' => $time,
            'signature' => $signature
        ];
        $data = $this->request($url, $data);
        return $data['access_token'];
    }

    /**
     * 生成签名
     * @param $data
     * @return string
     */
    public function signature($time)
    {
        return md5(md5($this->password) . $time);
    }

}
