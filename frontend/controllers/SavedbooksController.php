<?php

namespace frontend\controllers;

use Yii;

use common\models\Savedbooks;
use common\models\Takenbooks;
use common\models\SavedbooksSearch;
use common\models\Books;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;

/**
 * SavedbooksController implements the CRUD actions for Savedbooks model.
 */

class SavedbooksController extends Controller
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
                        'reinstate-saved-books' => ['POST'],
                        'add-to-basket' => ['POST'],
                        'create' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Savedbooks models.
     *
     * @return string
     */

    public function actionIndex()
    {   
        if (!Yii::$app->user->can( 'view-employee-pages' )) {   
            throw new ForbiddenHttpException;
        }

        $searchModel = new SavedbooksSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'saved_books_array' => 'null',
        ]);
    }

    public function actionShowMySavedBooks()
    {   
        $id = Yii::$app->user->identity->id;

        $query = Savedbooks::find()->andWhere(['reader_id' => $id])
            ->with('book')
            ->orderBy(['date_saved' => SORT_DESC]);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date_saved' => SORT_ASC,
                ],
            ],
        ]);

        // Retrieve the actual data from the models
        $saved_books_array = []; // [{"book_id":3,"quantity":1,"date_saved":"2023-07-14 11:00:45"}]
        
        foreach ($dataProvider->getModels() as $model) {
            $saved_books_array[] = [
                'book_id' => $model->book_id,
                'quantity' => $model->book_quantity,
                'date_saved' => $model->date_saved,
            ];
        }
        
        return $this->render('mysavedbooks', [
            'dataProvider' => $dataProvider,
            'saved_books_array' => $saved_books_array, // [{"book_id":3,"quantity":1,"date_saved":"2023-07-14 11:00:45"}]
            'reader_id' => $id,
        ]);
    }

    public function actionShowReaderSavedBooks($id=null)
    {   
        if (Yii::$app->user->identity->id == $id) {
            return $this->actionShowMySavedBooks($id);
        }

        if (!isset($id)) { // null

            if (Yii::$app->user->can( 'view-employee-pages' )) {
                Yii::$app->session->setFlash('error', 'Please select the reader first');
                return $this->redirect(Yii::$app->request->referrer);
                
            } else {
                throw new ForbiddenHttpException;
            }

        }

        if (!Yii::$app->user->can('view-employee-pages')) { // $id isn't the logged in user but is not null
            throw new ForbiddenHttpException;
        }

        $query = Savedbooks::find()->andWhere(['reader_id' => $id])
            ->with('book')
            ->orderBy(['date_saved' => SORT_DESC]);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date_saved' => SORT_ASC,
                ],
            ],
        ]);

        // Retrieve the actual data from the models
        $saved_books_array = []; // [{"book_id":3,"quantity":1,"date_saved":"2023-07-14 11:00:45"}]
        foreach ($dataProvider->getModels() as $model) {
            $saved_books_array[] = [
                'book_id' => $model->book_id,
                'quantity' => $model->book_quantity,
                'date_saved' => $model->date_saved,
            ];
        }

        return $this->render('readersavedbooks', [
            'dataProvider' => $dataProvider,
            'saved_books_array' => $saved_books_array, // [{"book_id":3,"quantity":1,"date_saved":"2023-07-14 11:00:45"}]
            'reader_id' => $id,
        ]);
    }

    public function actionToggleUserSession() // fix name
    {   
        if (!Yii::$app->request->isPost) { // not in the rules -> check done here instead
            throw new MethodNotAllowedHttpException('This action is only accessible through POST requests.');
        }

        if (Yii::$app->user->can('view-employee-pages' ))
        {   
            $id = Yii::$app->request->post('id');

            $session = Yii::$app->session;
            $selectedUserId = $session->get('user_id_in_basket');

            if ($selectedUserId === $id) {
                // If the clicked user ID is already selected, remove it from the session
                $session->remove('user_id_in_basket'); // becomes null
                Yii::$app->session->setFlash('success', 'User no longer selected');
            } else {
                // Set the clicked user ID as the selected ID in the session
                $session->set('user_id_in_basket', $id);
                Yii::$app->session->setFlash('success', 'User selected successfully!');
            }
        
            Yii::info($session->get('user_id_in_basket'));
        
            return $this->redirect(Yii::$app->request->referrer);

        } else {
            throw new ForbiddenHttpException;
        }
        
    }

    public function actionAddToBasket($book_id, $book_title) // book_title not needed
    {   
        $book_quantity_in_basket = Yii::$app->session->get("book_quantity_in_basket", 0);

        // Check if the book quantity in the basket is already 10
        if ($book_quantity_in_basket == 10) {
            Yii::$app->session->setFlash('error', 'You can only have a maximum of 10 books in your basket.');
            return $this->redirect(Yii::$app->request->referrer);
        }

        $book = Books::findOne($book_id);
        $numberAvailable = $book->number_available;
    
        $individual_title_count = Yii::$app->session->get("individual_title_count", []);
        $books_in_basket = Yii::$app->session->get("books_in_basket", []);
    
        if (isset($individual_title_count[$book_id]) && $individual_title_count[$book_id] + 1 > $numberAvailable) {
            Yii::$app->session->setFlash('error', 'Insufficient availability, please try again later.');
            return $this->redirect(Yii::$app->request->referrer);
        }
    
        $individual_title_count[$book_id] = isset($individual_title_count[$book_id]) ? $individual_title_count[$book_id] + 1 : 1;
    
        if (isset($books_in_basket[$book_id])) {
            $books_in_basket[$book_id]['quantity']++;
        } else {
            $books_in_basket[$book_id] = [
                'book' => $book,
                'quantity' => 1,
            ];
        }
    
        $book_quantity_in_basket = Yii::$app->session->get("book_quantity_in_basket", 0) + 1;
        
        Yii::$app->session->set("book_id_in_basket", $book_id);
        Yii::$app->session->set("book_title_in_basket", $book_title);
        Yii::$app->session->set("book_quantity_in_basket", $book_quantity_in_basket);
        Yii::$app->session->set("individual_title_count", $individual_title_count);
        Yii::$app->session->set("books_in_basket", $books_in_basket);
    
        Yii::$app->session->setFlash('success', 'The book has been successfully added to the basket.');
    
        return $this->redirect(Yii::$app->request->referrer);
    }
    
    public function actionUserbasket()
    {   
        $user = Yii::$app->user; // get the user
    
        // Check if the user is authenticated
        if ($user->isGuest) {
            // Redirect guests to the login page
            return $this->redirect(['site/login']);
        }

        $user_id_in_basket = Yii::$app->session->get("user_id_in_basket"); // can be null
        $book_quantity_in_basket = Yii::$app->session->get("book_quantity_in_basket");
        $books_in_basket = Yii::$app->session->get("books_in_basket");
    
        return $this->render('userbasket', [
            'user_id_in_basket' => $user_id_in_basket,
            'book_quantity_in_basket' => $book_quantity_in_basket,
            'books_in_basket' => $books_in_basket,
        ]);
    }

    public function actionSavechanges()
    {

        $user = Yii::$app->user; // get the user
    
        // Check if the user is authenticated
        if ($user->isGuest) {
            // Redirect guests to the login page or show an error message
            return $this->redirect(['site/login']);
        }
    
        $books_in_basket = Yii::$app->session->get("books_in_basket", []);
        $individual_title_count = Yii::$app->session->get("individual_title_count");
        $book_quantity_in_basket = Yii::$app->session->get("book_quantity_in_basket");
        
        $new_book_quantity_in_basket = 0;

        $errorMessage = '';

        $bookIds = array_keys($books_in_basket);

        $books_in_basket_to_check = Books::find() // array of book models that are in books_in_basket - to check for availability in the db
        ->where(['id' => $bookIds])
        ->all();

        foreach ($books_in_basket_to_check as $book) {
            $book_id = $book->id;
            $numberAvailable = $book->number_available;
            // Get the new quantity from the request
            $newQuantity = Yii::$app->request->post('_quantity')[$book_id] ?? 0;

            if ($newQuantity == 0) {
                unset($books_in_basket[$book_id]);

            } elseif ($newQuantity > $numberAvailable) {
                $errorMessage = 'Insufficient Availability';
            } else {
                // Update the quantity with the new value only if there is availability
                $books_in_basket[$book_id]['quantity'] = $newQuantity;
            }

            $new_book_quantity_in_basket += $newQuantity;
            $individual_title_count[$book_id] = $newQuantity;
                
        }

        $book_quantity_in_basket = $new_book_quantity_in_basket;

        if ($book_quantity_in_basket == 0) {
            $individual_title_count = [];
        }

        if ($errorMessage !== '') {

            Yii::$app->session->setFlash('error', $errorMessage);

            Yii::$app->session->set('books_in_basket', $books_in_basket);
            Yii::$app->session->set("book_quantity_in_basket", $book_quantity_in_basket);
            Yii::$app->session->set("individual_title_count", $individual_title_count);

            $books_in_basket = Yii::$app->session->get("books_in_basket", []);
            $individual_title_count = Yii::$app->session->get("individual_title_count");
            $book_quantity_in_basket = Yii::$app->session->get("book_quantity_in_basket");

        } else {
            
            Yii::$app->session->setFlash('success', 'Changes saved successfully');

            Yii::$app->session->set('books_in_basket', $books_in_basket);
            Yii::$app->session->set("book_quantity_in_basket", $book_quantity_in_basket);
            Yii::$app->session->set("individual_title_count", $individual_title_count);

        }

        return $this->redirect(['savedbooks/userbasket']);
    }

    /**
     * Creates a new Savedbooks model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */

    public function actionCreate()
    {   

        $user = Yii::$app->user; // get the user
    
        // Check if the user is authenticated
        if ($user->isGuest) {
            // Redirect guests to the login page
            return $this->redirect(['site/login']);
        }

        date_default_timezone_set('Europe/Helsinki');

        $user_id_in_basket = Yii::$app->session->get("user_id_in_basket", Yii::$app->user->identity->id);
        $book_quantity_in_basket = Yii::$app->session->get("book_quantity_in_basket");

        if (!Yii::$app->user->can( 'view-employee-pages' )){

            if ($book_quantity_in_basket > 10) {
                Yii::$app->session->setFlash('error', 'You\'re trying to save more than the maximum number of books per reader. Please adjust your order or contact us if you need to make a bigger one.');
                return $this->redirect(Yii::$app->request->referrer);
            }

            $totalSavedBooks = Savedbooks::find()->where(['reader_id' => $user_id_in_basket])->sum('book_quantity');

            if ($totalSavedBooks >= 10) {
                Yii::$app->session->setFlash('error', 'Your\'ve already reached a maximum number of 10 books saved per reader. Please contact us if you need to make a bigger order.');
                return $this->redirect(Yii::$app->request->referrer);
            } elseif ($totalSavedBooks + $book_quantity_in_basket > 10) {
                Yii::$app->session->setFlash('error', 'Your order would exceed the maximum number of 10 books per reader. Please select a maximum of ' . (10 - $totalSavedBooks) . ' books or contact us if you need to make a bigger order.');
                return $this->redirect(Yii::$app->request->referrer);
            }

            $totalTakenBooks = Takenbooks::find()->where(['reader_id' => $user_id_in_basket])->sum('book_quantity');

            if ($totalTakenBooks >= 10) {
                Yii::$app->session->setFlash('error', 'Your order would exceed the maximum number of 10 books in your possession. Please contact us if you need to make a bigger order.');
                return $this->redirect(Yii::$app->request->referrer);
            } elseif ($totalTakenBooks + $book_quantity_in_basket > 10) {
                Yii::$app->session->setFlash('error', 'Your order would exceed the maximum number of 10 books in your possession. Please select a maximum of ' . (10 - $totalTakenBooks) . ' books, return a book or contact us if you need to make a bigger order.');
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        $books_in_basket = Yii::$app->session->get("books_in_basket");

        $saved_books_array = [];

        foreach ($books_in_basket as $book_id =>$book) { // {"26":{"book":{"selectedGenres":null,"file":null},"quantity":"1"},"27":{"book":{"selectedGenres":null,"file":null},"quantity":"1"}}
            $model = new Savedbooks();
            $model->reader_id = $user_id_in_basket;
            $model->book_id = $book_id;

            $model->book_quantity = $book['quantity'];
            $model->date_saved = date('Y-m-d H:i:s'); // Current date and time

            // Set the expiration time to 24 hours from the current time

            $expirationTime = strtotime('+24 hours');
            $model->expiration_time = date('Y-m-d H:i:s', $expirationTime);
            
            $model->save();

            $saved_books_array[$model->book_id] = $model->book_quantity; // format {"26":"1","27":"1"} - id : quantity
        }

        //Remove the saved books in the user's session (SUBSEQUENT ATTEMPTS TO RETRIEVE THEM RETURN NULL)

        Yii::$app->session->remove("book_id_in_basket");
        Yii::$app->session->remove("book_title_in_basket");
        Yii::$app->session->remove("book_quantity_in_basket");
        Yii::$app->session->remove("individual_title_count");
        Yii::$app->session->remove("books_in_basket");

        // Push books to be removed from the DB
        foreach ($saved_books_array as $book_id => $quantity) { 
            $book = Books::findOne($book_id);
            $book->number_available -= $quantity; // should always be left as >= 0
            $book-> save();
        }

        // Redirect the user to his savedbooks page
        Yii::$app->session->setFlash('success', 'Books saved successfully!');

        return $this->redirect(['savedbooks/show-reader-saved-books', 'id' => $user_id_in_basket]);

    }

    public function actionReinstateSavedBooks($user_id=null)
    {   
        if ($user_id != null) {
            // Fetch the saved books of the specific user
            $saved_books = SavedBooks::find()->where(['reader_id' => $user_id])->all();

            foreach ($saved_books as $saved_book) {
                $book_id = $saved_book->book_id;
                $quantity = $saved_book->book_quantity;

                $book = Books::findOne($book_id);

                // Add the quantity back to the available books
                $book->number_available += $quantity;
                $book->save();

            }

            // Delete all savedbooks for the user
            Savedbooks::deleteAll(['reader_id' => $user_id]);

            return $this->redirect(Yii::$app->request->referrer);

        }

        // Fetch all saved books
        $saved_books = SavedBooks::find()->all();

        foreach ($saved_books as $saved_book) {
            $book_id = $saved_book->book_id;
            $quantity = $saved_book->book_quantity;

            $book = Books::findOne($book_id);

            // Add the quantity back to the available books
            $book->number_available += $quantity;
            $book->save();

        }

        Savedbooks::deleteAll();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Deletes an existing Savedbooks model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionDelete($id)
    {
        if (!Yii::$app->user->can( 'view-admin-pages' )) {
            throw new ForbiddenHttpException;
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
            
    }

    /**
     * Finds the Savedbooks model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Savedbooks the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Savedbooks::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
