<?php

namespace frontend\controllers;

use common\models\Returnedbooks;
use common\models\Books;
use common\models\Takenbooks;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use yii\web\ForbiddenHttpException;

/**
 * ReturnedbooksController implements the CRUD actions for Returnedbooks model.
 */
class ReturnedbooksController extends Controller
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
                        'return-books' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Returnedbooks models.
     *
     * @return string
     */

    public function actionShowMyReturnedBooks()
    {
        $id = Yii::$app->user->identity->id;

        $query = Returnedbooks::find()->andWhere(['reader_id' => $id])
            ->with('book')
            ->orderBy(['date_returned' => SORT_DESC]);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('myreturnedbooks', [
            'dataProvider' => $dataProvider,
            'reader_id' => $id,
        ]);
    }

    public function actionShowReaderReturnedBooks($id = null)
    {

        if (Yii::$app->user->identity->id == $id) {
            return $this->actionShowMyReturnedBooks($id);
        }

        if (!isset($id)) { // null

            if (Yii::$app->user->can('view-employee-pages')) {
                Yii::$app->session->setFlash('error', 'Please select the reader first');
                return $this->redirect(Yii::$app->request->referrer);

            } else {
                throw new ForbiddenHttpException;
            }
        }

        if (!Yii::$app->user->can('view-employee-pages')) { // $id isn't the logged in user but is not null
            throw new ForbiddenHttpException;
        }

        $query = Returnedbooks::find()->andWhere(['reader_id' => $id])
            ->with('book')
            ->orderBy(['date_returned' => SORT_DESC]);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('readerreturnedbooks', [
            'dataProvider' => $dataProvider,
            'reader_id' => $id,
        ]);
    }

    /**
     * Displays a single Returnedbooks model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionReturnBooks()
    {
        if (!Yii::$app->user->can('view-employee-pages')) {
            throw new ForbiddenHttpException;
        }

        $quantity_to_return = Yii::$app->request->post('quantity_to_return');
        $books_to_return = [];

        foreach ($quantity_to_return as $model_id => $quantity) {

            if ($quantity <= 0) {
                continue;
            }

            $taken_book = $this->updateTakenBooks($model_id, $quantity);

            if ($taken_book === null) {
                continue;
            }

            $book_id = $taken_book->book_id;
            $reader_id = $taken_book->reader_id;
            $date_taken = $taken_book->date_taken;
            $date_to_return = $taken_book->date_to_return;

            $book_to_update = Books::findOne(['id' => $book_id]);
            $book_to_update->number_available += $quantity;

            $book_to_update->save();

            $books_to_return[$model_id] = [
                'book_id' => $book_id,
                'reader_id' => $reader_id,
                'book_quantity' => $quantity,
                'date_taken' => $date_taken,
                'date_to_return' => $date_to_return,
            ];
        }

        $this->addToReturnedBooks($books_to_return);

        return $this->redirect(Yii::$app->request->referrer);
    }

    private function updateTakenBooks($model_id, $quantity)
    {
        $taken_book = Takenbooks::findOne(['id' => $model_id]);

        $quantity = intval($quantity);

        $taken_book->book_quantity -= $quantity;

        if ($taken_book->book_quantity == 0) {
            $taken_book->delete();

        } else {
            $taken_book->save();
        }

        return $taken_book;
    }

    private function addToReturnedBooks($books_to_return)
    {
        date_default_timezone_set('Europe/Helsinki');

        foreach ($books_to_return as $order_number => $returned_book) {
            $book_id = $returned_book['book_id'];
            $reader_id = $returned_book['reader_id'];
            $book_quantity = $returned_book['book_quantity'];
            $date_taken = $returned_book['date_taken'];
            $date_to_return = $returned_book['date_to_return'];

            $model = new Returnedbooks();
            $model->book_id = $book_id;
            $model->reader_id = $reader_id;
            $model->book_quantity = $book_quantity;
            $model->date_taken = $date_taken;
            $model->date_to_return = $date_to_return;
            $model->date_returned = date('Y-m-d H:i:s'); // Current date and time

            $model->save();
        }
    }

    /**
     * Finds the Returnedbooks model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Returnedbooks the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Returnedbooks::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
