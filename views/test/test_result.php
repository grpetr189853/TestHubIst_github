<?php

/* @var $totalScore integer */
/* @var $studentTest \app\models\StudentTest */
/* @var $message string */

use yii\helpers\Url; ?>
<div class="test-result-info">
    <h2><?= $message ?></h2>
    <p>Ваш балл: <?= $totalScore ?></p>
    <p>Проходной балл: <?= $studentTest->test->minimum_score ?></p>
    <p>Попыток осталось: <?= $studentTest->attempts ?></p>
    <?php if($studentTest->attempts >= 1):?>
        <a class="start-test-button" type="button" href="<?= Url::to(['/test/init', 'id'=>$studentTest->test_id]) ?>">Пройти тест еще раз</a>
        <a class="my-tests-button" type="button" href="<?= Url::to(['/test/index']) ?>">Перейти к тестам</a>
    <?php endif;?>
</div>
