<?php
namespace eponimash\attachfile\behaviors;
use Yii;
use yii\base\Exception;
use yii\web\UploadedFile;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use eponimash\attachfile\models\File;


class AttachedFilesBehavior extends Behavior
{
    public $path = '/files/';

    public $multiple;
    public $attribute;

    /**
     * @return array
     */
    public function events()
    {
        return array_merge( parent::events(),
            [
                ActiveRecord::EVENT_BEFORE_VALIDATE =>'beforeValidate',
                ActiveRecord::EVENT_AFTER_FIND   => 'afterFind',
                ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
                ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
                ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
            ]
        );
    }

    /**
     * @throws \Exception
     */
    public function beforeValidate(){
        $uploadFiles = UploadedFile::getInstances($this->owner, $this->attribute);
        $this->owner->{$this->attribute} = $this->createFiles($uploadFiles);
    }

    /**
     * @param $uploadFiles
     * @return File
     * @throws \Exception
     */
    public function createFiles($uploadFiles){
        $files = [];
        foreach ($uploadFiles as  $uploadFile){
            $file = new File();
            $file->model_name = $this->getModelName();
            $file->path = $uploadFile->tempName;
            $file->mime = $uploadFile->extension;
            $file->name = $uploadFile->baseName;
            $files[] = $file;
        }

        return $files;
    }

    /**
     * @param $file
     * @return string
     */
    public function getFullName($file){
        return $file->name .'.'.$file->mime;
    }

    /**
     * @throws Exception
     */
    public function afterSave(){
        foreach ($this->owner->{$this->attribute} as $file){

            $fullName = $this->getFullName($file);
            $path = Yii::getAlias('@common').$this->path;

            if (!file_exists($path)){
                throw new Exception('Директории не существует!');
            }
            move_uploaded_file($file->path, $path.$fullName);
            /*if (!move_uploaded_file($file->path, $path.$fullName)){
                throw new Exception('Файл не скопирован!');
            }*/

            $file->pathStorage = '@common'.$this->path;

            $file->model_id = $this->getOwnerId();
            $file->save();
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getOwnerId(){
        if (!$id = $this->owner->id) {//
            throw new \Exception('У владельца должен быть Id');
        }
        return $id;
    }

    /**
     * Возращает название по которому
     * @return string
     */
    public function getModelName(){
        return $this->owner->tableName().'.'.$this->attribute;
    }

    /**
     * Находит и загружает для $owner прикрепленные файлы
     */
    public function afterFind(){
        $this->owner->{$this->attribute} = File::find()
                                            ->where(['model_id' => $this->getOwnerId()])
                                            ->andWhere(['model_name' => $this->getModelName()])->all();
    }

    /**
     * @return mixed
     */
    public function getFile(){
        return $this->owner->{$this->attribute}[0];
    }

    /**
     * @return mixed
     */
    public function getFiles(){
        return $this->owner->{$this->attribute};
    }

    /**
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function afterDelete(){
        foreach (File::find(['model_id' => $this->getOwnerId()])->all() as $file){
            $this->deleteFile($file);
        }
    }

    /**
     * @param $file
     */
    public function deleteFile($file){
        unlink($file->getFullPath());
        $file->delete();
    }
}
