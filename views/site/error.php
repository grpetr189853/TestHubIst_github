<?php

use app\controllers\SiteController;
use yii\helpers\Html;


/* @var $this SiteController */
/* @var $error array */

$this->title=Yii::$app->name . ' - Error';
if($code == 403) {
	$messageHeader = 'Отказано в доступе';
} else {
	$messageHeader = "Ошибка {$code}";
}

?>
<h2><?= $messageHeader ?></h2>

<div class="error">
<?php echo Html::encode($message); ?>
</div>
<div class="contact-admin">
    Электронный адрес для связи с администрацией: <?= Html::a(Yii::$app->params['adminEmail'], array('site/contact')); ?>.
</div>