<?php
namespace frontend\controllers;

use common\models\VKUser;
use Yii;
use yii\web\Controller;
use yii\web\User;
use nodge\eauth\ServiceBase;

class VkController extends Controller
{
	
	 public function behaviors() {
            return array(
                'eauth' => array(
                    // required to disable csrf validation on OpenID requests
                    'class' => \nodge\eauth\openid\ControllerBehavior::className(),
                    'only' => array('login'),
                ),
            );
        }
		
		 public function actionLogin() {
			
        //$serviceName = Yii::$app->getRequest()->getQueryParam('vkontakte');
        $serviceName = 'vkontakte';
		//print_r($serviceName);
		if (isset($serviceName)) {
            /** @var $eauth \nodge\eauth\ServiceBase */
            $eauth = Yii::$app->get('eauth')->getIdentity($serviceName);
			//print_r($eauth);
            $eauth->setRedirectUrl(Yii::$app->getUser()->getReturnUrl());
            //echo Yii::$app->getUser()->getReturnUrl();
			$eauth->setCancelUrl(Yii::$app->getUrlManager()->createAbsoluteUrl('/'));
            //echo Yii::$app->getUrlManager()->createAbsoluteUrl('vk/login');//die;
 // if ($eauth->authenticate()) {
	//  echo 'gjg';
  //}
  //print_r($_SESSION);
 //echo $eauth->popup->width;
            try {//die;
			//print_r($eauth);
				//echo $eauth->authenticate();
                if ($eauth->authenticate()) {
//                  var_dump($eauth->getIsAuthenticated(), $eauth->getAttributes()); exit;
 echo 23;
                    $identity = VKUser::findByEAuth($eauth);
                    Yii::$app->getUser()->login($identity);
 
                    // special redirect with closing popup window
                    $eauth->redirect(['/']);
                }
                else {
					echo 'hdgts';
                    // close popup window and redirect to cancelUrl
                    $eauth->cancel();
                }
				echo 234;
            }
            catch (\nodge\eauth\ErrorException $e) {
                // save error to show it later
                Yii::$app->getSession()->setFlash('error', 'EAuthException: '.$e->getMessage());
 //echo 'Trurth';
// die;
//                 close popup window and redirect to cancelUrl
         //    $eauth->cancel();
                $eauth->redirect($eauth->getCancelUrl());
            }
        }
 
        // default authorization code through login/password ..
    }
	
}