<?php
namespace frontend\controllers;
use Yii;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['index','update','view'],
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    //not needed since there is a signup
    /*    public function actionCreate()
        {
            $model = new User();
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }*/
    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		
		
		
        $model = $this->findModel($id);
        //check if it is the own user
        if (Yii::$app->user->isGuest || $model->id != Yii::$app->user->identity->id) {
            Yii::$app->getSession()->setFlash('error',"You can't modify another user's profile");
            return $this->goHome();
        }
        $original_name = $model->avatar;
        // Upload new avatar

        $model->avatar = UploadedFile::getInstance($model, 'avatar');
        if (is_object($model->avatar)) {
       
            if (!empty($original_name)) {
                $avatar_name = $original_name;
            }
            if (!$avatar_name=$model->upload($model->avatar)) {
                Yii::$app->getSession()->setFlash('error',"There was an error storing the image, please try again later.");
                return $this->render('update', [
                    'model' => $model,
                ]);
            } else {
                $original_name = $avatar_name;
            }
        }
        $model->scenario = 'changePass';
        // save changes
        if ($model->load(Yii::$app->request->post())) {
            $model->avatar = $original_name;
            // If any pass field is empty do not change password
            if (empty($model->old_password) || empty($model->new_password) || empty($model->repeat_password)) {
                $model->scenario = 'default';
            }
            if ($model->validate()) {
                $model->setPassword($model->new_password);
                if ($model->save(false)) {
                    Yii::$app->getSession()->setFlash('success',"Your profile was modified successfully.");
                    return $this->redirect(['view', 'id' => $model->id]);
                }else{
                    Yii::$app->getSession()->setFlash('error',"There was an error saving your data, please try again later.");
                }
            } else{
                Yii::$app->getSession()->setFlash('error',"Please correct the mistakes and try again.");
            }
        }
        $model->scenario = 'default';
        return $this->render('update', [
            'model' => $model,
        ]);	
	

    }
    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!Yii::$app->user->isGuest && $model->id === Yii::$app->user->identity->id) {
            $model->status = 0;
            if ($model->save()) {
                Yii::$app->user->logout();
                Yii::$app->getSession()->setFlash('success','Your profile was successfully deleted.');
                return $this->goHome();
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
        Yii::$app->getSession()->setFlash('error',"You can't delete another user's profile");
        return $this->goHome();
    }
    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}