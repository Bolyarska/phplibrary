<?php

namespace frontend\controllers;

use common\models\Bookgenres;
use common\models\Books;
use common\models\Genres;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use Yii;

/**
 * BookgenresController implements the CRUD actions for Bookgenres model.
 */
class BookgenresController extends Controller
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

    public function beforeAction($action)
    {   
        if ($action->id === 'bygenre') {
            return parent::beforeAction($action);
        }

        $canView = Yii::$app->user->can('view-employee-pages');

        if ($canView) {
            return parent::beforeAction($action);
        } else {
            throw new ForbiddenHttpException;
        }
    }

    /**
     * Updates an existing Bookgenres model.
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
     * Deletes an existing Bookgenres model.
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
     * Finds the Bookgenres model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Bookgenres the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    
    protected function findModel($id)
    {
        if (($model = Bookgenres::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionBygenre($genreId)
    {
        $genreName = Genres::find()->select('genre')->where(['id' => $genreId])->scalar();

        // Retrieve the books based on the genre ID
        $books = Books::find()
        ->leftJoin('bookgenres', 'books.id = bookgenres.book_id')
        ->where(['bookgenres.genre_id' => $genreId])
        ->all();

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $books,
        ]);
    
        return $this->render('bygenre', [
            'dataProvider' => $dataProvider,
            'books' => $books,
            'genreName' => $genreName,
        ]);
    }
}
