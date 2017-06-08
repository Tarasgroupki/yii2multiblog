<?php

use common\models\Post;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Post */
/* @var $form yii\widgets\ActiveForm */
/* @var $authors yii\db\ActiveRecord[] */
/* @var $category yii\db\ActiveRecord[] */
/* @var $tags yii\db\ActiveRecord[] */
?>

<div class="post-form">

    <?php $form = ActiveForm::begin(); ?>
	<div class="tabs_menu">
<?php foreach($langs as $key => $lang):?>
<a class="a_link" href="#tab<?=$lang['lang_id'];?>"><?=$lang['name']?></a>
<?php endforeach;?>
</div>
<?php foreach($langs as $key => $lang):?>
<?php if($lang['lang_id'] == 1){?>
<div class="tab" id = "tab<?=$lang['lang_id']?>">
    <?php $form = ActiveForm::begin(); ?>
   <?=$lang['name']?>
   <?= $form->field($model, "news_translate[".$key."][id]")->hiddenInput(['value' => $lang['id'] ])->label(false) ?>
   <?= $form->field($model, "news_translate[".$key."][lang_id]")->hiddenInput(['value' => "{$lang['lang_id']}" ])->label(false) ?>
	<?= $form->field($model, "news_translate[".$key."][title]")->textInput(['value' => $lang['title']]) ?>
    <?= $form->field($model, "news_translate[".$key."][content]")->textarea(['value' => $lang['content']]) ?>
</div>
<?php }?>
<?php if($lang['lang_id'] > 1) {?>
<div class="tab" id = "tab<?=$lang['lang_id']?>" style="display:none;">
<?=$lang['name']?>
<?= $form->field($model, "news_translate[".$key."][id]")->hiddenInput(['value' => $lang['id'] ])->label(false) ?>
   <?= $form->field($model, "news_translate[".$key."]['lang_id']")->hiddenInput(['value' => "{$lang['lang_id']}" ])->label(false) ?>
	<?= $form->field($model, "news_translate[".$key."][title]")->textInput(['value' => $lang['title']]) ?>
    <?= $form->field($model, "news_translate[".$key."][content]")->textarea(['value' => $lang['content']]) ?>
</div>
<?php }?>
<?php endforeach;?>  
    <?= $form->field($model, 'category_id')->dropDownList(
        ArrayHelper::map($category, 'id', 'title')
    ) ?>

    <?= $form->field($model, 'author_id')->dropDownList(
        ArrayHelper::map($authors, 'id', 'username')
    ) ?>

    <?= $form->field($model, 'anons')->textarea(['rows' => 6]) ?>

    <?//= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'publish_status')->dropDownList(
        [Post::STATUS_DRAFT => Yii::t('backend', 'Draft'), Post::STATUS_PUBLISH => Yii::t('backend', 'Published')]
    ) ?>

    <?= $form->field($model, 'tags')->checkboxList(
        ArrayHelper::map($tags, 'id', 'title')
    ) ?>

    <?= $form->field($model, 'publish_date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
