<?php

namespace frontend\controllers;

use Yii;
use common\models\User;
use common\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/**
 * UserController implements the CRUD actions for Users model.
 */
class UserController extends Controller
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
                        'deactivate-profile' => ['POST'],
                        'reactivate-profile' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Users models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $user = Yii::$app->user; // get the user
    
        // Check if the user is authenticated
        if ($user->isGuest) {
            // Redirect guests to the login page or show an error message
            return $this->redirect(['site/login']);
        }

        if (Yii::$app->user->can( 'view-admin-pages' ))
        {
            $user_type = $user->identity->type;

            $searchModel = new UserSearch();
            $dataProvider = $searchModel->search($this->request->queryParams);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);

        } else {
            throw new ForbiddenHttpException;
        }

    }

    public function actionEmployees()
    {

        $user = Yii::$app->user; // get the user
    
        // Check if the user is authenticated
        if ($user->isGuest) {
            // Redirect guests to the login page or show an error message
            return $this->redirect(['site/login']);
        }

        if (Yii::$app->user->can( 'view-admin-pages' ))
        {
        
            $user_type = $user->identity->type;
        
            $searchModel = new UserSearch();
            
            // Check if the 'type' parameter is set in the request
            $type = Yii::$app->request->getQueryParam('type');
            
            // Apply filtering based on the 'type' value
            $dataProvider = $searchModel->search($this->request->queryParams);
            $dataProvider->query->andFilterWhere(['type' => ['Employee', 'Administrator']]);

            $dataProvider->query->addOrderBy(['is_active' => SORT_DESC]);
        
            return $this->render('employees', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);

        } else {
            throw new ForbiddenHttpException;
        }
    }

    public function actionReaders() // contains employees as well (comes from the dataProvider)
    {

        $user = Yii::$app->user; // get the user
    
        if ($user->isGuest) {
            return $this->redirect(['site/login']);
        }
        
        if (Yii::$app->user->can( 'view-employee-pages' ))
        {
            $user_type = $user->identity->type;
        
            $searchModel = new UserSearch();
            
            $type = Yii::$app->request->getQueryParam('type');
            
            $dataProvider = $searchModel->search($this->request->queryParams);
            $dataProvider->query->andFilterWhere(['type' => ['Reader', 'Employee']]);

            $dataProvider->query->addOrderBy(['is_active' => SORT_DESC]);
        
            return $this->render('readers', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);

        } else {
            throw new ForbiddenHttpException;
        }
    }

    /**
     * Displays a single Users model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    
    public function actionView($id)
    {   
        if (Yii::$app->user->can( 'view-employee-pages' ))
        {
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);

        } else {
            throw new ForbiddenHttpException;
        }
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        if (Yii::$app->user->can( 'view-employee-pages' ))
        {
            $model = new User();

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

        } else {
            throw new ForbiddenHttpException;
        }
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {  
        if (Yii::$app->user->can( 'view-employee-pages' ))
        {
            $model = $this->findModel($id);

            if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }

            return $this->render('update', [
                'model' => $model,
            ]);

        } else {
            throw new ForbiddenHttpException;
        }
    }

    /**
     * Deletes an existing Users model.
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
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested profile does not exist.');
    }

    /**
     * Change User password.
     *
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionChangepassword()
    {
        $user = Yii::$app->user; // get the user
    
        // Check if the user is authenticated
        if ($user->isGuest) {
            // Redirect guests to the login page or show an error message
            return $this->redirect(['site/login']);
        }

        $id = \Yii::$app->user->id;

        
        $model = new \frontend\models\ChangePasswordForm(['id' => $id]);

        if ($model->load(\Yii::$app->request->post()) && $model->validate() && $model->changepassword()) {
            \Yii::$app->session->setFlash('success', 'Password Changed!');
        }

        return $this->render('changepassword', [
            'model' => $model,
        ]);
    }

    public function actionDeactivateProfile($id)
    {
        if (Yii::$app->user->can( 'view-employee-pages' ))
        {
            $model = $this->findModel($id);
            $model->is_active = 0;
            $model->save();

            Yii::$app->session->setFlash('success', 'Profile Deactivated');

            return $this->redirect(['view', 'id' => $model->id]);

        } else {

            throw new ForbiddenHttpException;
        }
    }

    public function actionReactivateProfile($id)
    {
        if (Yii::$app->user->can( 'view-employee-pages' ))
        {
            $model = $this->findModel($id);

            $model->is_active = 1;

            $model->save();

            Yii::$app->session->setFlash('success', 'Profile Reactivated');

            return $this->redirect(['view', 'id' => $model->id]);

        } else {

            throw new ForbiddenHttpException;
        }
    }

    public function actionTwoFactorAuth()
    {   
        $user_id = Yii::$app->user->identity->id;
        $user = User::findOne(['id' => $user_id]);

        return $this->render('2fa_auth_index', ['user' => $user]);

    }

    public function actionEnableTwoFactorAuth()
    {
        $user_id = Yii::$app->user->identity->id;
        $user = User::findOne(['id' => $user_id]);

        $_g2fa = new Google2FA();
        $user->secret_key = $_g2fa->generateSecretKey();
        $user->save();

        // Provide name of application (To display to user on app)
        $app_name = 'bibliotechka_test';

        // Generate a custom URL from user data to provide to qr code generator
        $qrCodeUrl = $_g2fa->getQRCodeUrl(
            $app_name,
            $user->email,
            $user->secret_key
        );

        // QR Code Generation using bacon/bacon-qr-code
        // Set up image rendered and writer
        $renderer = new ImageRenderer(
            new RendererStyle(250),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);

        // This option will create a string with the image data and base64 encode it
        $encoded_qr_data = base64_encode($writer->writeString($qrCodeUrl));

        // This will provide us with the current password
        $current_otp = $_g2fa->getCurrentOtp($user->secret_key);

        return $this->render('2fa_enable_view', ['encoded_qr_data' => $encoded_qr_data]);

    }

    public function actionVerifyCode()
    {   
        // Retrieve the user's identity (user ID) from the session
        $logged_in_user_id = Yii::$app->session->get('pending_2fa_user_id');

        if (!$logged_in_user_id) {
            // Handle the case where the user's identity is not found in the session.
            // This could indicate an issue or unauthorized access.
            return $this->redirect(['/site/login']); // Redirect to login page or handle the error as needed.
        }

        $user = User::findOne(['id' => $logged_in_user_id]);
        $code = Yii::$app->request->post('code');

        if ($user && $user->validate2FACode($code)) {
            // 2FA code is valid, log in the user
            Yii::$app->user->login($user);
        
            // Remove the stored user identity from the session
            Yii::$app->session->remove('pending_2fa_user_id');

            // success response
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true, 'message' => 'Correct code'];
        
        } else {

            //error response
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => false, 'message' => 'Incorrect code, try again.'];
        }
        
    }

    public function actionDisableTwoFactorAuth()
    {   
        $user_id = Yii::$app->user->identity->id;
        $user = User::findOne(['id' => $user_id]);
        $is_null = empty($user->secret_key);

        if ($is_null) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => false, 'message' => 'User doesn\'t have the 2-Step verification enabled.'];
        }

        $user->secret_key = NULL;
        $user->save();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success' => true, 'message' => '2-Step verification successfully disabled.'];
    }

}
