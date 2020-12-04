<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\FontAwesomeAsset;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
FontAwesomeAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="container th-container">
    <div class="navbar navbar-default th-navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="<?= Yii::$app->request->hostInfo . Yii::$app->request->baseUrl?>/site/index"><?= Html::img(Yii::getAlias('@web').'/img/logo.png' ); ?></a>
            </div>

            <?php

                if (Yii::$app->user->isGuest) {
                    $menuItems[] = [
                        'label' => 'Зарегистрироваться',
                        'items' => [
                            ['label' => 'Администратор', 'url' => ['/site/signup-admin']],
                            '<li class="divider"></li>',
                            ['label' => 'Преподаватель', 'url' => ['/site/signup-teacher']],
                            '<li class="divider"></li>',
                            ['label' => 'Студент', 'url' => ['/site/signup-student']],
                        ],
                        'options' => ['class' => 'menu'],
                    ];
                    $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
                } else {
                    $menuItems[] = '<li class="menu">'
                        . Html::beginForm(['/site/logout'], 'post')
                        . Html::submitButton(
                            'Logout (' . Yii::$app->user->identity->username . ')',
                            ['class' => 'btn btn-link logout']
                        )
                        . Html::endForm()
                        . '</li>';
                }

                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav navbar-right'],
                    'items' => $menuItems,
                ]);
                ?>
            </div>
        <!--/.container-fluid-->
        </div>
    <!--/.navbar-->
    <div class="jumbotron th-jumbotron">
        <?php echo $content; ?>
    </div>

</div>



<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
