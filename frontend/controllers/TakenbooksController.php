<?php

namespace frontend\controllers;

use common\models\Takenbooks;
use common\models\TakenbooksSearch;
use common\models\Savedbooks;
use common\models\User;
use common\models\Books;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use Yii;
use yii\web\ForbiddenHttpException;

/**
 * TakenbooksController implements the CRUD actions for Takenbooks model.
 */
class TakenbooksController extends Controller
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
                        'give-to-reader' => ['POST']
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Takenbooks models.
     *
     * @return string
     */

    public function actionIndex()
    {   
        if (Yii::$app->user->can( 'view-employee-pages' ))
        {
            $user = Yii::$app->user;
        
            // Check if the user is authenticated
            if ($user->isGuest) {
                // Redirect guests to the login page
                return $this->redirect(['site/login']);
            }
        
            $searchModel = new TakenbooksSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            $dataProvider->query->addOrderBy(['date_to_return' => SORT_ASC]);
        
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);

        } else {
            throw new ForbiddenHttpException;
        }
    }

    public function actionShowMyTakenBooks($id)
    {   
        if (Yii::$app->user->identity->id != $id) {
            throw new ForbiddenHttpException;
        }

        $query = Takenbooks::find()->andWhere(['reader_id' => $id])
            ->with('book')
            ->orderBy(['date_taken' => SORT_ASC]);

            $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => $query,
            ]);

            // Retrieve the actual data from the models
            $taken_books_array = [];
            foreach ($dataProvider->getModels() as $model) {
                $taken_books_array[] = [
                    'book_id' => $model->book_id,
                    'quantity' => $model->book_quantity,
                ];
            }
        
            return $this->render('mytakenbooks', [
                'dataProvider' => $dataProvider,
                'taken_books_array' => $taken_books_array,
            ]);
    }


    public function actionShowReaderTakenBooks($id=null)
    {   

        if (Yii::$app->user->identity->id == $id) {

            return $this->actionShowMyTakenBooks($id);

        } elseif (!isset($id)) { // null
            if (Yii::$app->user->can( 'view-employee-pages' ))
            
            {
                Yii::$app->session->setFlash('error', 'Please select the reader first');
                return $this->redirect(Yii::$app->request->referrer);
                
            } else {

                throw new ForbiddenHttpException;
            }

        } else { // $id isn't the logged in user but is not null

        // there's an id and the user is the currently logged in user or the user is admin/employee
            if (Yii::$app->user->can( 'view-employee-pages' ))
            {
                $query = Takenbooks::find()->andWhere(['reader_id' => $id])
                    ->with('book')
                    ->orderBy(['date_taken' => SORT_ASC]);

                $dataProvider = new \yii\data\ActiveDataProvider([
                    'query' => $query,
                ]);

                // Retrieve the actual data from the models
                $taken_books_array = [];
                foreach ($dataProvider->getModels() as $model) {
                    $taken_books_array[] = [
                        'book_id' => $model->book_id,
                        'quantity' => $model->book_quantity,
                    ];
                }
            
                return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'taken_books_array' => $taken_books_array,
                ]);

            } else {

                throw new ForbiddenHttpException;
            }
        }
    }

    public function actionGiveToReader($user_id)
    {   
        if (Yii::$app->request->isPost && Yii::$app->user->can( 'view-employee-pages' ))
        {
            date_default_timezone_set('Europe/Helsinki');
        
            $saved_books_array = Yii::$app->request->post('saved_books_array'); // [{"book_id":3,"quantity":1,"date_saved":"2023-07-14 11:15:03"}]

            $saved_books = json_decode($saved_books_array, true);

            $taken_books_array = [];

            foreach ($saved_books as $book) {
                $book_id = $book["book_id"];
                $book_quantity = $book["quantity"];
                $date_saved = $book["date_saved"];

                $model = new Takenbooks();
                $model->reader_id = $user_id;
                $model->book_id = $book_id;
                $model->book_quantity = $book_quantity;
                $model->date_saved = $date_saved;
                $model->date_taken = date('Y-m-d H:i:s'); // Current date and time

                // Set the date to return to 1 day from date taken

                $return_date = strtotime('+1 day');
                $model->date_to_return = date('Y-m-d H:i:s', $return_date);
                
                $model->save();

                $taken_books_array[$model->book_id] = $model->book_quantity; // format {"26":"1","27":"1"} - id : quantity

            }

            // Delete all savedbooks for the user
            Savedbooks::deleteAll(['reader_id' => $user_id]);
            Yii::$app->session->setFlash('success', 'Order complete!');
            return $this->redirect(Yii::$app->request->referrer);
        }
    
    }

    /**
     * Deletes an existing Takenbooks model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    
    public function actionDelete($id)
    {   
        if (Yii::$app->user->can( 'view-admin-pages' ))
        {
            $this->findModel($id)->delete();

            return $this->redirect(['index']);

        } else {
            throw new ForbiddenHttpException;
        }
    }

    /**
     * Finds the Takenbooks model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Takenbooks the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Takenbooks::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
