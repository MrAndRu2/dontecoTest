<?php

namespace app\controllers;

use Yii;
use app\models\Worker;
use yii\web\Controller;

class WorkerController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionGetByName()
    {

        $name = Yii::$app->request->getBodyParam('name', '');
        
        $data = Worker::find()
        ->where("name like '%$name%'")
        ->one();

        if(!$data){
            $answer = [
               'success' => false,
               'status_code' => 404,
               'message' => 'Пользователь не найден' 
            ];
        }else{
            $answer = [
                'success' => true,
                'worker' => $data->getAttributes(),
            ];
        }

        echo json_encode($answer);
        exit; 
    }
}