<?php

namespace frontend\controllers;

use Yii;
use common\models\Books;
use common\models\BooksSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\web\ForbiddenHttpException;
use yii\data\Sort;
use yii\helpers\FileHelper;

use common\models\Bookgenres;

/**
 * BooksController implements the CRUD actions for Books model.
 */
class BooksController extends Controller
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
                        'move-ajax' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Books models.
     *
     * @return string
     */

    public function beforeAction($action)
    {
        if ($action->id === 'index' || $action->id === 'view') {
            return parent::beforeAction($action);
        }

        $canView = Yii::$app->user->can('view-employee-pages');

        if ($canView) {
            return parent::beforeAction($action);
        } else {
            throw new ForbiddenHttpException;
        }
    }

    public function actionIndex($author = null)
    {
        $searchModel = new BooksSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ($author !== null) {
            $dataProvider->query->andWhere(['author' => $author]);
        }

        $dataProvider->pagination->pageSize = 12;

        $sort = new Sort([
            'attributes' => [
                'title',
                'author',
            ],
        ]);

        $dataProvider->setSort($sort);

        return $this->render('index', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel, 'sort' => $sort,]);
    }


    /**
     * Displays a single Books model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionView($id)
    {
        $user = Yii::$app->user; // get the user

        // Check if the user is authenticated
        if ($user->isGuest || (!Yii::$app->user->can('view-employee-pages'))) {
            return $this->render('individualbookview', [
                'model' => $this->findModel($id),
            ]);
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    /**
     * Creates a new Books model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */


    public function actionCreate()
    {
        
        $model = new Books();

        // used to load the form data into the model object, $model->load() includes $model->validate(), $model->save() to create an id
        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $selectedGenres = Yii::$app->request->post('Books')['selectedGenres']; // manual post as this field is not directly linked

            if (is_array($selectedGenres)) {
                foreach ($selectedGenres as $genreId) {
                    $bookGenre = new Bookgenres();
                    $bookGenre->book_id = $model->id;
                    $bookGenre->genre_id = $genreId;
                    $bookGenre->save();
                }
            }

            $uploadedFiles = UploadedFile::getInstances($model, 'file');

            $imagePaths = [];

            // Create a directory for the current book model using its ID
            $directoryPath = 'uploads/' . $model->id . '/';

            if (!file_exists($directoryPath)) {
                mkdir($directoryPath, 0777, true); // Create the book directory if it doesn't exist
            }

            foreach ($uploadedFiles as $uploadedFile) {

                // Generate a random MD5 string as the image name
                $imageName = md5(uniqid(rand(), true));

                // Append the original file extension to the image name
                $imageName .= '.' . $uploadedFile->extension;

                $imagePath = $directoryPath . $imageName;

                // Check if the file already exists, generate a new MD5 string until it is unique
                while (file_exists($imagePath)) {
                    $imageName = md5(uniqid(rand(), true));
                    $imageName .= '.' . $uploadedFile->extension;
                    $imagePath = $directoryPath . $imageName;
                }

                // Save the file with the generated unique image name
                $uploadedFile->saveAs($imagePath);

                // Store the image path in the array
                $imagePaths[] = $imagePath;
            }

            // Serialize the array of image paths
            $model->images = serialize($imagePaths);
            $model->save();

            return $this->redirect(['view', 'id' => $model->id]);   
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Books model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionUpdate($id)
    {
        
        $model = $this->findModel($id);


        if ($model->load(Yii::$app->request->post())) {

            // Get the selected genres if any
            if (isset($selectedGenres)) {
                $selectedGenres = Yii::$app->request->post('Books')['selectedGenres'];

                // Delete the existing Bookgenres records for the current book
                Bookgenres::deleteAll(['book_id' => $model->id]);

                // Iterate over the selected genre IDs and create Bookgenres records
                foreach ($selectedGenres as $genreId) {
                    $bookGenre = new Bookgenres();
                    $bookGenre->book_id = $model->id;
                    $bookGenre->genre_id = $genreId;
                    $bookGenre->save();
                }
            }

            $uploadedFiles = UploadedFile::getInstances($model, 'file');
            $imagePaths = [];
            $bookDirectory = 'uploads/' . $model->id . '/'; // Create a directory for the current book model

            if (!file_exists($bookDirectory)) {
                mkdir($bookDirectory, 0777, true); // Create the book directory if it doesn't exist
            }

            foreach ($uploadedFiles as $uploadedFile) {
                // Generate a random MD5 string as the image name
                $imageName = md5(uniqid(rand(), true));

                // Append the original file extension to the image name
                $imageName .= '.' . $uploadedFile->extension;

                $imagePath = $bookDirectory . $imageName;

                // Check if the file already exists, generate a new MD5 string until it is unique
                while (file_exists($imagePath)) {
                    $imageName = md5(uniqid(rand(), true));
                    $imageName .= '.' . $uploadedFile->extension;
                    $imagePath = $bookDirectory . $imageName;
                }

                // Save the file with the generated unique image name
                $uploadedFile->saveAs($imagePath);

                // Store the image path in the array
                $imagePaths[] = $imagePath;
            }

            // Get the existing image paths from the database and combine them with the new paths
            $existingImagePaths = $model->images ? unserialize($model->images) : [];
            $imagePaths = array_merge($existingImagePaths, $imagePaths);

            // Serialize the array of image paths
            $model->images = serialize($imagePaths);
            $model->save();

            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionMoveAjax($id, $index)
    {   

        $model = $this->findModel($id);
        $images = unserialize($model->images);

        if ($index >= 0 && $index < count($images)) {

            $newIndex = $index + 1;

            if ($newIndex >= count($images)) {
                $newIndex = 0;
            }

            $temp = $images[$index];
            $images[$index] = $images[$newIndex];
            $images[$newIndex] = $temp;


            $model->images = serialize($images);
            $model->save();

        } else { // if index < 0 or index > count($images)
            throw new ForbiddenHttpException();
        }

        return json_encode(['success' => true, 'index' => $newIndex]);

    }



    // Removes a specified image from the array of images in the db
    public function actionRemoveImage($id, $index)
    {    
        $model = $this->findModel($id);
        $images = unserialize($model->images);
    
        // Check if the index is within the valid range
        if ($index >= 0 && $index < count($images)) {
    
            // Get the image filename from the array
            $imageFilename = basename(str_replace('\\', '/', $images[$index]));
    
            // Remove the image at the specified index
            array_splice($images, $index, 1);
    
            // Update the images field with the new order
            $model->images = serialize($images);
            $model->save();
    
            // Delete from folder
            $imagePath = Yii::getAlias('@app/web/uploads/' . $model->id . '/' . $imageFilename);
    
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    
        return $this->redirect(['update', 'id' => $model->id]);
    }

    /**
     * Deletes an existing Books model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionDelete($id)
    {   
        $bookDirectory = 'uploads/' . $id . '/'; 

        if (is_dir($bookDirectory)) {
            $files = glob($bookDirectory . '*'); // Get a list of all files in folder
            
            foreach ($files as $file) {
                unlink($file); // Delete each file
            }
            
            rmdir($bookDirectory); // Delete empty directory
        }
        
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Books model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Books the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    
    protected function findModel($id)
    {
        if (($model = Books::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
