<?php

namespace frontend\controllers;

use Yii;

use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\db\Query;
use yii\data\Pagination;

use common\models\User;


class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = new Query();
        $query->select(['b.id', 'b.title', 'b.author', 'COUNT(*) AS frequency'])
          ->from(['returnedbooks tb'])
          ->join('JOIN', 'books b', 'tb.book_id = b.id')
          ->groupBy('tb.book_id')
          ->orderBy(['frequency' => SORT_DESC]);

          

        $countQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 10, // Set the number of items per page to 10
        ]);
        $books = $query->offset($pages->offset)
        ->limit($pages->limit)
        ->all();

        if (empty($books)) {
            $query = new Query();
            $query->select(['*'])
                ->from(['books']);

            $countQuery = clone $query;
            $pages = new Pagination([
                'totalCount' => $countQuery->count(),
                'pageSize' => 10,
            ]);
            $books = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
    
        }

        return $this->render('index', ['books' => $books, 'pages' => $pages,]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */

    /*public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            $logged_in_user_id = Yii::$app->user->identity->id;
            $user = User::findOne(['id' => $logged_in_user_id]);

            if (!empty($user->secret_key)) {
                return $this->render('/user/2fa_check');
            } else {
                return $this->goBack();
            }
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }*/

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $logged_in_user_id = $model->getUserForTwoFa()->id;
            $user = User::findOne(['id' => $logged_in_user_id]);

            if (!empty($user->secret_key)) {
                // Store the user's identity in a session variable
                Yii::$app->session->set('pending_2fa_user_id', $logged_in_user_id);
                return $this->render('/user/2fa_check');
            } else {
                // Perform the actual login here
                Yii::$app->user->login($model->getUserForTwoFa());
                return $this->goBack();
            }
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionSignup()
    {
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post()) && $model->signup()){
            Yii::$app->session->setFlash('success', 'You have successfully registered');
            return $this->actionIndex();
        }

        return $this->render('signup', [
            'model' => $model
        ]);
    }


    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays about page + contact form
     *
     * @return string
     */
    public function actionAbout()
    {
        $model = new ContactForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('about', [
            'model' => $model,
        ]);

    }

}
