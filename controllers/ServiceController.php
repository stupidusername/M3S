<?php

namespace app\controllers;

use app\models\{Service, ServicesImageForm};
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ServiceController implements the CRUD actions for Service model.
 */
class ServiceController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            // Only logged in users are allowed to access the methods.
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            // The action delete is only accessible by POST.
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Service models.
     * @return string
     */
    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => Service::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Service model.
     * @param integer $id
     * @return string
     */
    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Service model. If creation is successful, the browser will
     * be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Service();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Service model. If update is successful, the browser
     * will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Service model. If deletion is successful, the
     * browser will be redirected to the 'index' page.
     * @param integer $id
     * @return yii\web\Response
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
    * Display a form to upload the services image file.
    * @return string
    */
    public function actionUploadImage() {
        $model = new ServicesImageForm();
        if (Yii::$app->request->isPost) {
            if ($model->upload()) {
                Yii::$app->session->setFlash('success', 'Success!');
            } else {
                Yii::$app->session->setFlash(
                    'error',
                    'An error has occurred.'
                );
            }
        }
        return $this->render('upload-image', ['model' => $model]);
    }

    /**
     * Deletes the services image. If deletion is successful, the browser will
     * be redirected to the 'upload-image' page.
     * @return yii\web\Response
     */
    public function actionDeleteImage() {
        unlink(Service::getImagePath());

        return $this->redirect(['upload-image']);
    }

    /**
     * Finds the Service model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Service the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Service::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
