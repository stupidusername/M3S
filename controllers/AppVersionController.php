<?php

namespace app\controllers;

use app\components\UploadHandler;
use app\models\AppVersion;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * AppVersionController implements the CRUD actions for AppVersion model.
 */
class AppVersionController extends Controller {

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
     * Lists all AppVersion models.
     * @return string
     */
    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => AppVersion::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AppVersion model.
     * @param integer $id ID of the model.
     * @return string
     */
    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AppVersion model. If creation is successful, the browser
     * will be redirected to the view page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new AppVersion();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AppVersion model. If update is successful, the
     * browser will be redirected to the view page.
     * @param integer $id ID of the model.
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AppVersion model. If deletion is successful, the
     * browser will be redirected to the index page.
     * @param integer $id ID of the model.
     * @return yii\web\Response
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
    * Handle the file upload.
    * @return string
    */
    public function actionUploadChunk() {
        $dir = Yii::getAlias('@webroot/'. AppVersion::UPDATES_FOLDER . '/');
        $uploadHandler = new UploadHandler([
            'upload_dir' => $dir,
            'accept_file_types' => '/./i',
            'replace_dots_in_filenames' => false,
        ]);
        return $uploadHandler->getBody();
    }

    /**
     * Finds the AppVersion model based on its primary key value. If the model
     * is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AppVersion The loaded model.
     * @throws NotFoundHttpException If the model cannot be found.
     */
    protected function findModel($id) {
        if (($model = AppVersion::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(
                'The requested page does not exist.'
            );
        }
    }
}
