<?php

namespace backend\controllers;

use common\models\Category;
use common\models\Tags;
use common\models\User;
use Yii;
use common\models\Post;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * CRUD операции модели "Посты".
 */
class PostController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['admin'],
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
     * Список постов.
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Post::find()->where(['lang_id'=>1]),
			'pks' => 'product_id' ,
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Просмотр поста.
     * @param string $id идентификатор поста
     * @return string
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Создание поста.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Post();
		$connection = Yii::$app->db;
        $langs = array(1 => array('lang_id'=>1,'name'=>'English'),2 => array('lang_id'=>2,'name'=>'Russian'),3 => array('lang_id'=>3,'name'=>'Ukrainian'));
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$max = $connection->createCommand('SELECT MAX(product_id) FROM `post`')->queryOne();
            $model->product_id = $max['MAX(product_id)']+1;
			$model->insertNews($max,$model->category_id,$model->author_id,$model->anons,$model->publish_status,$model->publish_date,$model->slug);
			//print_r($model);die;
			$id = $connection->createCommand('SELECT MIN(`id`) FROM `post` WHERE product_id ='.$model->product_id.'')->queryOne();
			//print_r($id);
			return $this->redirect(['view', 'id' => $id['MIN(`id`)']]);           
		} else {
            $model->author_id = Yii::$app->user->id;
            return $this->render('create', [
			    'langs' => $langs,
                'model' => $model,
                'category' => Category::find()->all(),
                'tags' => Tags::find()->all(),
                'authors' => User::find()->all()
            ]);
        }
    }

    /**
     * Редактирование поста.
     * @param string $id идентификатор редактируемого поста
     * @return string|Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $langs = array(1 => array('lang_id'=>1,'name'=>'English'),2 => array('lang_id'=>2,'name'=>'Russian'),3 => array('lang_id'=>3,'name'=>'Ukrainian'));
        $all_news = Post::find()->where(['product_id'=> $id])->IndexBy('lang_id')->all();
		for($i = 1;$i<count($langs)+1;$i++):
		$langs[$i]['id'] = $all_news[$i]['id'];
		$langs[$i]['title'] = $all_news[$i]['title'];
		$langs[$i]['content'] = $all_news[$i]['content'];
		endfor;
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->UpdateNews($slug);
			return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
			    'langs' => $langs,
                'model' => $model,
                'authors' => User::find()->all(),
                'tags' => Tags::find()->all(),
                'category' => Category::find()->all()
            ]);
        }
    }

    /**
     * Удаление поста.
     * @param string $id идентификатор удаляемого поста
     * @return Response
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Post::find()->where(['product_id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
