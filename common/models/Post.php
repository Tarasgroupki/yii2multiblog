<?php

namespace common\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\SluggableBehavior1;
use yii\behaviors\SluggableBehavior2;
use yii\behaviors\SluggableBehavior3;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Модель постов.
 *
 * @property string $id
 * @property string $title заголовок
 * @property string $anons анонс
 * @property string $content контент
 * @property string $category_id категория
 * @property string $author_id автор
 * @property string $publish_status статус публикации
 * @property string $publish_date дата публикации
 *
 * @property User $author
 * @property Category $category
 * @property Comment[] $comments
 */
class Post extends ActiveRecord
{
    /**
     * Статус поста: опубликованн.
     */
    const STATUS_PUBLISH = 'publish';
    /**
     * Статус поста: черновие.
     */
    const STATUS_DRAFT = 'draft';

    /**
     * Список тэгов, закреплённых за постом.
     * @var array
     */
	public $news_translate = array();
	public $slug1;
	public $slug2;
	public $slug3;
    protected $tags = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%post}}';
    }
	
	public function behaviors()
    {
        return [
           'slug' => [
                'class' => SluggableBehavior::className(),
                'attribute' => 'news_translate',
				//'transliterateOptions' => 'Russian-Latin/BGN; Any-Latin; Latin-ASCII; NFD; [:Nonspacing Mark:] Remove; NFC;'
            ],
			'slug1' => [
			  'class' => SluggableBehavior1::className(),
              'attribute' => 'news_translate',
			],
			'slug2' => [
			  'class' => SluggableBehavior2::className(),
              'attribute' => 'news_translate',
			],
			'slug3' => [
			  'class' => SluggableBehavior3::className(),
              'attribute' => 'news_translate',
			]
       ];
	}
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
		    [['news_translate'],'validatorRequiredWords'],
           // [['title'], 'required'],
            [['category_id', 'author_id'], 'integer'],
            [['anons', 'content', 'publish_status'], 'string'],
            [['publish_date', 'tags'], 'safe'],
            [['title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'title' => Yii::t('backend', 'Title'),
            'anons' => Yii::t('backend', 'Announce'),
            'content' => Yii::t('backend', 'Content'),
            'category' => Yii::t('backend', 'Category'),
            'tags' => Yii::t('backend', 'Tags'),
            'category_id' => Yii::t('backend', 'Category ID'),
            'author' => Yii::t('backend', 'Author'),
            'author_id' => Yii::t('backend', 'Author ID'),
            'publish_status' => Yii::t('backend', 'Publish status'),
            'publish_date' => Yii::t('backend', 'Publish date'),
        ];
    }
	    public function validatorRequiredWords()
{ 
    foreach ( $this->news_translate as $news ) {
        if(empty($news['title']) && empty($news['description'])) {
			$this->addError('news_translate', 'Не заповнені всі поля!');     
        }
    }
}
public function insertNews($max,$cat_id,$auth_id,$anons,$publish_status,$publish_date,$slug)
	{ 
			 $connection = Yii::$app->db; 	
			 foreach($this->news_translate as $key => $name){
				 $name['category_id'] = $cat_id;
				 $name['author_id'] = $auth_id;
				 $name['anons'] = $anons;
				 $name['publish_status'] = $publish_status;
				 $name['publish_date'] = $publish_date;
				 $name['product_id'] = $max['MAX(product_id)']+1;
				 //$name['slug'] = $name['title'];
				 if($key == 1){
					 $name['slug'] = $slug[0];
				 }
				 if($key == 2){
					 $name['slug'] = $this->slug1;
				 }
				 if($key == 3){
					 $name['slug'] = $this->slug2;
				 }
				// $name['id'] = Yii::$app->user->identity->id;
				 $names[] = $name; 
				//echo $name;
			 }
			// print_r($names);die;
			$connection->createCommand()->batchInsert(Post::tableName(),['id','lang_id','title','content','category_id','author_id','anons','publish_status','publish_date','product_id','slug']
			,$names)->execute();
	}

	public function UpdateNews($slug)
	{//echo $this->slug;
		$connection = Yii::$app->db;
		//print_r($this->news_translate);die;
		foreach($this->news_translate as $key => $name){
				 //echo $key;
				 $name['anons'] = $this->anons;
				 $name['category_id'] = $this->category_id;
				 $name['product_id'] = $this->product_id;
				 $name['auth_id'] = $this->author_id;
				 $name['publish_status'] = $this->publish_status;
				 $name['publish_date'] = $this->publish_date;
				 $name['slug'] = $slug;
				 if($key == 1){
					 $name['slug'] = $this->slug3;
				 }
				 if($key == 2){
					 $name['slug'] = $this->slug1;
				 }
				 if($key == 3){
					 $name['slug'] = $this->slug2;
				 }
				 $names[] = $name;
			 }
		$query = $connection->queryBuilder->batchInsert('post',['id','lang_id','title','content','anons','category_id','product_id','author_id','publish_status','publish_date','slug']
		,$names);
		$connection->createCommand($query . " ON DUPLICATE KEY UPDATE `lang_id` = VALUES(`lang_id`), `title` = VALUES(`title`), `content`= VALUES(`content`),`anons`= VALUES(`anons`),`category_id` = VALUES(`category_id`),`product_id`= VALUES(`product_id`),`author_id` = VALUES(`author_id`),`publish_status`= VALUES(`publish_status`),`publish_date`= VALUES(`publish_date`),`slug` = VALUES(`slug`)")->execute();
	}
    /**
     * @return ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'product_id']);
    }

    /**
     * Возвращает опубликованные комментарии
     * @return ActiveDataProvider
     */
    public function getPublishedComments()
    {
        return new ActiveDataProvider([
            'query' => $this->getComments()
                ->where(['publish_status' => Comment::STATUS_PUBLISH])
        ]);
    }

    /**
     * Устанавлиает тэги поста.
     * @param $tagsId
     */
    public function setTags($tagsId)
    {
        $this->tags = (array) $tagsId;
    }

    /**
     * Возвращает массив идентификаторов тэгов.
     */
    public function getTags()
    {
        return ArrayHelper::getColumn(
            $this->getTagPost()->all(), 'tag_id'
        );
    }

    /**
     * Возвращает тэги поста.
     * @return ActiveQuery
     */
    public function getTagPost()
    {
        return $this->hasMany(
            TagPost::className(), ['post_id' => 'product_id']
        );
    }

    /**
     * Возвращает опубликованные посты
     * @return ActiveDataProvider
     */
    public function getPublishedPosts()
    {
        return new ActiveDataProvider([
            'query' => Post::find()
                ->where(['publish_status' => self::STATUS_PUBLISH])
                ->orderBy(['publish_date' => SORT_DESC])
        ]);
    }

    /**
     * Возвращает модель поста.
     * @param int $id
     * @throws NotFoundHttpException в случае, когда пост не найден или не опубликован
     * @return Post
     */
    public function getPost($id)
    {
        if (
            ($model = Post::find()->where(['product_id'=>$id])) !== null &&
            $model->isPublished()
        ) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested post does not exist.');
        }
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        TagPost::deleteAll(['post_id' => $this->id]);

        if (is_array($this->tags) && !empty($this->tags)) {
            $values = [];
            foreach ($this->tags as $id) {
                $values[] = [$this->id, $id];
            }
			//echo '<pre>'.print_r($values).'</pre>';die;
            self::getDb()->createCommand()
                ->batchInsert(TagPost::tableName(), ['post_id', 'tag_id'], $values)->execute();
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Опубликован ли пост.
     * @return bool
     */
    protected function isPublished()
    {
        return $this->publish_status === self::STATUS_PUBLISH;
    }
}
