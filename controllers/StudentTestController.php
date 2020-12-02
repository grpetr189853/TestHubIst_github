<?php

namespace app\controllers;

use app\models\Test;
use DateTime;
use Yii;
use app\models\StudentTest;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StudentTestController implements the CRUD actions for StudentTest model.
 */
class StudentTestController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all StudentTest models.
     * @return mixed
     */
    public function actionIndex($status)
    {
        $tests = Test::find()->all();
        if ($status == 'notpassed') {
            $dataProvider = new ActiveDataProvider([
                'query' => StudentTest::find()->where(['student_id' => Yii::$app->user->identity->getId()])->andWhere(['result' => null]),
            ]);
        } elseif ($status == 'notcompleted') {
            foreach ($tests as $test) {
                $dataProvider = new ActiveDataProvider([
                    'query' => StudentTest::find()->where(['student_id' => Yii::$app->user->identity->getId()])
                        ->andWhere(['>','attempts', '0'])->andWhere(['<', 'result', $test->minimum_score]),
                ]);
            }
        } elseif ($status == 'failed'){//TODO: check the failed student test tab it seems it returns falsy
            foreach ($tests as $test) {
                $dataProvider = new ActiveDataProvider([
                    'query' => StudentTest::find()->andWhere(['=','attempts','0'])
                        ->andWhere(['<', 'result', $test->minimum_score])->orWhere(['<','deadline',(new DateTime())->format('Y-m-d H:i:s')])->where(['student_id' => Yii::$app->user->identity->getId()]),
                ]);
//                var_dump(StudentTest::find()->andWhere(['=','attempts','0'])
//                    ->andWhere(['<', 'result', $test->minimum_score])->orWhere(['<','deadline',(new DateTime())->format('Y-m-d H:i:s')])->where(['student_id' => Yii::$app->user->identity->getId()])->prepare(\Yii::$app->db->queryBuilder)->createCommand()->rawSql);
//                die();
            }
        } elseif ($status == 'completed') {
            foreach ($tests as $test) {
                $dataProvider = new ActiveDataProvider([
                    'query' => StudentTest::find()->where(['student_id' => Yii::$app->user->identity->getId()])
                        ->andWhere(['>','attempts', '0'])->andWhere(['=', 'result', $test->minimum_score])->orWhere(['>=','deadline',(new DateTime())->format('Y-m-d H:i:s')]),
                ]);
            }
        }


        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StudentTest model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new StudentTest model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StudentTest();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing StudentTest model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing StudentTest model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the StudentTest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StudentTest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StudentTest::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
