<?php
/**
 * Created by PhpStorm.
 * User: MaGe
 * Date: 2019/4/19
 * Time: 21:31
 */

namespace api\modules\v1\controllers;

use common\models\User;
use ErrorException;
use Exception;
use Yii;
use yii\base\UserException;
use yii\filters\auth\QueryParamAuth;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\Response;

class Controller extends \yii\rest\Controller
{
    public $user;
    public function init(){
       parent::init();
        $access_token = Yii::$app->request->get('access-token');
        $this->user = User::findIdentityByAccessToken($access_token);
        Yii::$app->response->on(Response::EVENT_BEFORE_SEND, [$this, 'beforeSend']);
   }

    public function behaviors() {


        return ArrayHelper::merge (parent::behaviors(), [
            'authenticator' => [
                'class' => QueryParamAuth::className(),
                'optional' =>[
                    'login',
                    'index',
                    'index-page-no-validation',
                    'get-access-token',
                    'apply',
                    'upload',
                    'notify',
                ]
            ],
        ] );
    }

    /**
     * 格式化输出
     * @param $event
     */
    public function beforeSend($event)
    {
        $response = $event->sender;
        $message='ok';
        if ($response->statusCode>=400) {
            //异常处理
            if (true && $exception = Yii::$app->getErrorHandler()->exception) {
                $response->data = $this->convertExceptionToArray($exception);
            }
            //Model出错
            if ($response->statusCode==422) {
                $messages=[];
                foreach ($response->data as $v) {
                    $messages[] = $v['message'];
                }
                //请求错误时数据为  {"success":false,"data":{"name":"Not Found","message":"页面未找到。","code":0,"status":404}}
                $response->data = [
                    'code'=> '400',
                    'msg'=> implode("  ", $messages),
                    'data'=>$response->data
                ];
            }
            if ($response->statusCode == 401) {
                $response->data = [
                    'code' => '401',
                    'msg' => '登录验证失败',
                    'data' => []
                ];
            }
            $response->statusCode = 200;
        }
        elseif ($response->statusCode>=300) {
            $response->statusCode = 200;
            $response->data = $this->convertExceptionToArray(new ForbiddenHttpException(Yii::t('yii', 'Login Required')));
        }
        //返回数据格式
        $response->data = [
            'code' =>isset($response->data['code'])?$response->data['code']:$response->statusCode,
            'msg' =>isset($response->data['msg'])?$response->data['msg']:(isset($response->data['message'])?$response->data['message']:$message),
            'data' => isset($response->data['data'])?$response->data['data']:$response->data
        ];

        //定义数据输出格式
        $response->format = Response::FORMAT_JSON;
        //定义跨域访问
        Yii::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Origin', '*');
        Yii::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Credentials', 'true');

        //json-p 格式输出
        if (isset($_GET['callback'])) {
            $response->format = Response::FORMAT_JSONP;
            $response->data = [
                'callback' => $_GET['callback'],
                'data'=>$response->data,
            ];
        }
    }

    /**
     * 将异常转换为array输出
     * @param $exception
     * @return array
     */
    protected function convertExceptionToArray($exception)
    {
        if (!YII_DEBUG && !$exception instanceof UserException && !$exception instanceof HttpException) {
            $exception = new HttpException(500, Yii::t('yii', 'An internal server error occurred.'));
        }
        $array = [
            'name' => ($exception instanceof Exception || $exception instanceof ErrorException) ? $exception->getName() : 'Exception',
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ];
        if ($exception instanceof HttpException) {
            $array['status'] = $exception->statusCode;
        }
        if (YII_DEBUG) {
            $array['type'] = get_class($exception);
            if (!$exception instanceof UserException) {
                $array['file'] = $exception->getFile();
                $array['line'] = $exception->getLine();
                $array['stack-trace'] = explode("\n", $exception->getTraceAsString());
                if ($exception instanceof \yii\db\Exception) {
                    $array['error-info'] = $exception->errorInfo;
                }
            }
        }
        if (($prev = $exception->getPrevious()) !== null) {
            $array['previous'] = $this->convertExceptionToArray($prev);
        }
        return $array;
    }

}