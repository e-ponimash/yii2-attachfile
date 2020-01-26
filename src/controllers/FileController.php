<?php

namespace eponimash\attachfile\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\UploadedFile;
use eponimash\attachfile\models\File;


class FileController extends \yii\web\Controller
{

    function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionLoad($id)
    {
        if (!$file = File::findOne(['id' => $id])){
            throw new \yii\web\HttpException(404, 'File not found');
        }

        $path = Yii::getAlias($file->getFullPath());

        if(file_exists($path)){
            header('Content-Type: image/jpg');
            readfile($path);
        }else{
            throw new \yii\web\HttpException(404, 'There is no images');
        }

    }

    public function actionSave()
    {
        // $attr = Yii::$app->request->post();
        $file = UploadedFile::getInstanceByName('file');
        $file_name = Yii::$app->imageUpload->uploadImage($file);

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'filename' => $file_name,
            'url' => Yii::$app->imageUpload->getUrlImage($file_name)
        ];
        // Yii::$app->session->set('file_name', $file_name);
        // return $this->redirect(['load', 'name' => $file_name]);
    }
}
