<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <div class="main-page-intro text-center">
        <?php if(Yii::$app->session->hasFlash('success')):?>
            <div class="alert alert-success">
                <?php echo Yii::$app->session->getFlash('success'); ?>
            </div>
        <?php endif; ?>
        <h1>TestHub</h1>
        <div class="th-info">
            <p>Сервис позволяет проверять знания студентов в автоматическом режиме с помощью тестов. Зарегистрируйтесь, чтобы принять участие.</p>
            <div class="main-register-pics">
                <a class="main-student-pic" href="<?= Yii::$app->urlManager->createUrl('site/signup-student')?>"><img title="Регистрация студента" src="<?= Yii::$app->request->baseUrl?>/img/student_pic.png"></a>
                <a class="main-teacher-pic" href="<?= Yii::$app->urlManager->createUrl('site/signup-teacher')?>"><img title="Регистрация преподавателя" src="<?= Yii::$app->request->baseUrl?>/img/teacher_pic.png"></a>
            </div>
        </div>
    </div>
</div>
