<?php
namespace backend\controllers;
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
                'only' => ['create','index','update','view'],
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
    public function actionCreate()
    {
		try{
		$model = new User();
        $model->scenario = 'create';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->setPassword($model->new_password);
            $model->generateAuthKey();
			$model->avatar=UploadedFile::getInstance($model, 'avatar');
            if ($model->upload($model->avatar)){
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            Yii::$app->getSession()->setFlash('error',"Please correct the mistakes and try again later.");
            return $this->render('create', [
                'model' => $model,
            ]);
        }

		return true;
		
		
        $model = new User();
        $model->scenario = 'create';
        if ($model->load(Yii::$app->request->post())&& !$model->validate())
		{
			Yii::$app->getSession()->setFlash('error',"Please correct the mistakes and try again later.");
            return $this->render('create', [
                'model' => $model,
            ]);
		}else{
			

			
            $model->setPassword($model->new_password);
            $model->generateAuthKey();
			
            if ($model->save(false)){	
				$model->avatar = UploadedFile::getInstance($model, 'avatar');
				
				if (!$model->upload($model->avatar)) {
					// file isn't uploaded successfully
					Yii::$app->getSession()->setFlash('error',"Please correct the mistakes and try again later.");
	
					return $this->redirect(['view', 'id' => $model->id]);
				}
			}
        }
		}catch(Exception $e){
			$this->addError($attribute,'Error In uploading file!' );
			return false;
		}
    }
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
        $original_name = $model->avatar;
        // Upload new avatar
        $model->avatar = UploadedFile::getInstance($model, 'avatar');
        if (is_object($model->avatar)) {
            $avatar_name = uniqid() . "." . $model->avatar->extension;
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

        // validate deletion and on failure process any exception 
        // e.g. display an error message 
        if ($model->delete()) {
            if (!$model->deleteImage()) {
                Yii::$app->session->setFlash('error', 'Error deleting image');
            }
        }
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
    /**
     * Activates an existing User model.
     * If activation is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionActivate($id)
    {
        $model = $this->findModel($id);
        $model->status = 10;
        if ($model->save()) {
            Yii::$app->getSession()->setFlash('success','Your profile was successfully activated.');
            return $this->redirect(['index']);
        }
        return $this->redirect(['index']);
    }
}