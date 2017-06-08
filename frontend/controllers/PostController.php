<?php

namespace frontend\controllers;

use common\models\Category;
use frontend\models\CommentForm;
use Yii;
use common\models\Post;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
//use jumper423\VK;
//use jumper423\behaviors\CallableBehavior;

/**
 * Контролеры "Постов".
 */
class PostController extends Controller
{
    public function behaviors()
    {
        return [
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
        $post = new Post();
        $category = new Category();
//$vk = Yii::$app->authClientCollection->getClient('vkontakte');
        $posts = $post->getPublishedPosts();
        $posts->setPagination([
            'pageSize' => Yii::$app->params['pageSize']
        ]);

        return $this->render('index', [
            'posts' => $posts,
            'categories' => $category->getCategories()
        ]);
    }

    /**
     * Просмотр поста.
     * @param string $id идентификатор поста
     * @return string
     */
    public function actionView($id)
    {
        $post = new Post();
		
        return $this->render('view', [
            'model' => $post->getPost($id),
            'commentForm' => new CommentForm(Url::to(['comment/add', 'id' => $id])),
        ]);
    }
}
