<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\web\UploadedFile;

/**
 * This is the model class for table "bn_upload_file".
 *
 * @property int $id
 * @property int $store_id
 * @property string $file_url 文件url
 * @property string $extension 文件扩展名
 * @property string $type 文件类型：
 * @property int $size 文件大小，字节
 * @property int $add_time
 * @property int $is_delete
 * @property int $group_id 分组id
 * @property int $mch_id 商户id
 */
class UploadFiles extends \yii\db\ActiveRecord
{
    public $files;//图片名称
    public function __construct(array $config = [])
    {
        $this->files = UploadedFile::getInstance($this,'file');
        parent::__construct($config);
    }

    /**
     * 设置数据表名
     */
    public static function tableName()
    {
        return 'bn_upload_file';
    }

    /**
     * 设置图片的验证规则
     */
    public function rules(){
        return [
            [['file'],'file', 'skipOnEmpty' => false, 'extensions' => 'jpg, png, gif', 'mimeTypes'=>'image/jpeg, image/png, image/gif', 'maxSize'=>1024*1024*10, 'maxFiles'=>1, 'on'=>['upload']],
        ];
    }

    /**
     * 上传场景
     * @return array
     */
    public function scenarios()
    {
        return [
            'upload' => ['files'], // 添加上传场景
        ];
    }

    /**
     * 属性标签
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => '仓库ID',
            'file' => '文件url',
            'extension' => '文件扩展名',
            'type' => '文件类型：',
            'size' => '文件大小，字节',
            'add_time' => '添加时间',
            'is_delete' => '是否删除',
            'group_id' => '分组ID',
            'mch_id' => '商户ID',
        ];
    }

    /**
     * 上传单个文件到阿里云
     * @return array 上传是否成功
     */
    public function uploadFile(){
        $res['error'] = 1;
        if ($this->validate()) {
            $uploadPath = dirname(dirname(__FILE__)).'/../web/uploads/'; // 取得上传路径
            if (!file_exists($uploadPath)) {
                @mkdir($uploadPath, 0777, true);
            }
            $ext = $this->files->getExtension();        // 获取文件的扩展名
            $randNum = $this->getRandNumber();          // 生成一个随机数，为了重命名文件
            $imageName = date("YmdHis").$randNum.'.'.$ext;
            $ossFile = 'file/'.date("Ymd").'/'.$imageName;
            $filePath = $uploadPath.$imageName;         // 生成文件的绝对路径
            if ($e=$this->files->saveAs($filePath)){        // 上传文件到服务器
                $fileData['file'] = $ossFile;
                $fileData['extension'] = $filePath;
                $fileData['type'] = $ext;
                $fileData['add_time'] = time();
                $fileData['store_id'] = Yii::$app->user->id;
                //把文件的上传信息写入数据库并上传阿里oss
                $trans = Yii::$app->db->beginTransaction();   // 开启事务
                try{
                    $save_file = Yii::$app->db->createCommand()->insert(self::tableName(), $fileData)->execute();
                    $new_id = Yii::$app->db->getLastInsertID(); //获取新增文件的id，用于返回。
                    if ($save_file) {
                        $oss_upload = Yii::$app->Aliyunoss->upload($ossFile, $filePath); //文件上传到阿里云oss

                        if ($oss_upload) {
                            $res['error'] = 0;
                            $res['id'] = $new_id;
                            $res['file'] = $oss_upload;
                            $trans->commit();          // 提交事务
                        } else {
                            unlink($filePath);         // 删除服务器上的文件
                            $trans->rollBack();         // 事务回滚
                        }
                    }
                    unlink($filePath);             // 插入数据库失败，删除服务器上的文件
                    $trans->rollBack();
                } catch(Exception $e) {
                    unlink($filePath);             // 删除服务器上的文件
                    $trans->rollBack();
                }
            }
        }
        return $res;
    }


    /**
     * 删除阿里云oss里存储的文件和数据库里边保存到文件上传信息
     * @param $fileId
     * @return boolean   删除是否成功
     */
    public function deleteFile($fileId)
    {
        $res['error'] = 1;    // 1表示默认有错误。
        $fileInfo = Yii::$app->db->createCommand('select *from '.self::tableName().' where id=:id')->bindParam(':id', $fileId)->queryOne();
        if (count($fileInfo) > 0) {                     // 如果找到了文件的记录
            $ossFile = $fileInfo['file'];            // 获取ossfile
            $realFile = $fileInfo['extension'];          // 获取服务器上的文件
            $owner = $fileInfo['store_id'];               // 获取上传图片用户的id
            $operator = Yii::$app->user->id;            // 获取删除图片的用户

            if ($owner != $operator) {
                $res['err_msg'] = '您删除的图片不存在';
                return $res;
            }

            $trans = Yii::$app->db->beginTransaction(); // 开启事务
            try {
                $delStatus = Yii::$app->db->createCommand()->delete('file', 'id = ' . $fileId)->execute();
                if ($delStatus) {
                    if (Yii::$app->Aliyunoss->delete($ossFile)) {
                        @unlink($realFile);
                        $res['error'] = 0;
                        $trans->commit();
                    }
                }
                $trans->rollBack();
            } catch (Exception $e) {
                $res['err_msg'] = '删除失败';
                $trans->rollBack();
            }

        } else {
            $res['err_msg'] = '图片不存在，请重试';
        }

        return $res;
    }

    /**
     * 生成随机数
     * @return string 随机数
     */
    protected function getRandNumber()
    {
        $array = array();
        while (count($array) < 10) {
            $array[] = rand(1, 10);
            $array = array_unique($array);
        }
        return implode("", $array);
    }
}
