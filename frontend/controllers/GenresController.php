<?php

namespace frontend\controllers;

use common\models\Genres;
use common\models\GenresSearch;
use common\models\Bookgenres;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\data\Pagination;

/**
 * GenresController implements the CRUD actions for Genres model.
 */
class GenresController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Genres models.
     *
     * @return string
     */

    public function beforeAction($action)
    {
        if ($action->id === 'index') {
            return parent::beforeAction($action);
        }

        $canView = Yii::$app->user->can('view-employee-pages');

        if ($canView) {
            return parent::beforeAction($action);
        } else {
            throw new ForbiddenHttpException;
        }
    }

    public function actionIndex()
    {
        $searchModel = new GenresSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        $genres = $dataProvider->getModels();

        $genreIds = array_column($genres, 'id'); // array with the genre_ids from the Genres table

        $bookCounts = Bookgenres::find()
            ->select(['genre_id', 'count(*) as bookCount'])
            ->where(['genre_id' => $genreIds])
            ->groupBy('genre_id')
            ->asArray()
            ->all();

        $bookCountsByGenre = array_column($bookCounts, 'bookCount', 'genre_id');

        foreach ($genres as $genre) {
            $genre->bookCount = $bookCountsByGenre[$genre->id] ?? 0;
        }

        $dataProvider->pagination->pageSize = 12;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Genres model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Genres model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */

    public function actionCreate()
    {
        $model = new Genres();

        if ($this->request->isPost) {

            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Genres model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Genres model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Genres model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Genres the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Genres::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
