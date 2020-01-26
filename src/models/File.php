<?php

namespace eponimash\attachfile\models;

use Yii;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property string $model_name
 * @property int $model_id
 * @property string|null $checksum
 * @property string|null $mime
 * @property string $name
 * @property string $path
 */
class File extends \yii\db\ActiveRecord
{

    public $pathStorage;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['model_name', 'model_id', 'name', 'path'], 'required'],
            [['model_id'], 'default', 'value' => null],
            [['model_id'], 'integer'],
            [['model_name', 'checksum', 'mime', 'name', 'path'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_name' => 'Model Name',
            'model_id' => 'Model ID',
            'checksum' => 'Checksum',
            'mime' => 'Mime',
            'name' => 'Name',
            'path' => 'Path',
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->path = $this->pathStorage;
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function getFullPath(){
        return $this->path.$this->name.'.'.$this->mime;
    }

}