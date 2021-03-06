<?php


namespace app\controllers;

use app\models\Question;
use yii\helpers\Html;
use yii\web\Controller;

class QuestionController extends Controller
{

    public function actions()
    {
        return [
            'image-upload' => [
                'class' => 'vova07\imperavi\actions\UploadFileAction',
                'url' => '/files' ,
                'path' => '@app/files',
            ],
            'file-delete' => [
                'class' => 'vova07\imperavi\actions\DeleteFileAction',
                'url' => '/files',
                'path' => '@app/files',
            ],
        ];
    }

    public function actionOptionField($i, $key, $number)
    {
        $model = new Question();

        return $this->renderPartial('new_answer_field', array(
            'model' => $model,
            'i' => $i,
            'number' => $number,
            'key' => $key
        ));
    }

    /**
     * Экшн validateData осуществляет ajax валидацию динамически добавляемых Question форм.
     */
    public function actionValidateData()
    {
        if (isset($_POST['scenario'], $_POST['name'], $_POST['value'])) {
            $model = new Question(['scenario' => $_POST['scenario']]);
            if ($model->hasProperty($_POST['name'])) {
                $model[$_POST['name']] = $_POST['value'];
            } else if ($model->hasAttribute($_POST['name'])) {
                $model->setAttribute($_POST['name'], $_POST['value']);
            }
            $model->validate();
            echo Html::error($model, $_POST['name']);
            \Yii::$app->end();
        } else {
            $this->redirect(array(
                'site/index'
            ));
        }
    }

}