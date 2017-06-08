<?php
use common\models\VKUser;
//echo 'sfjwpgiw';die;
    if (Yii::$app->getSession()->hasFlash('error')) {
        echo '<div class="alert alert-danger">'.Yii::$app->getSession()->getFlash('error').'</div>';
    }
?>
...
<p class="lead">Do you already have an account on one of these sites? Click the logo to log in with it here:</p>
<?php echo \nodge\eauth\Widget::widget(array('action' => '/')); ?>