<?php
/* @var $this yii\web\View */
/* @var $student_answers array */
/* @var $correct_answers array */
/* @var $result_points array */
/* @var $test_name string */
?>

<table border="1" width="50%" cellpadding="5" class="table-statistic">
    <caption>Статистика по тесту <?= $test_name; ?></caption>
    <tr>
        <th>Пользователь</th>
        <th>Количество набранных баллов</th>
        <th>Вопрос</th>
        <th>Ответы студента</th>
        <th>Правильные ответы</th>
    </tr>
    <?php foreach ($student_answers as $key1 => $questions): ?>
        <?php if($key1 != false):?>
            <?php foreach ($questions as $key2 => $userAnswers): ?>
                <tr>
                    <?php if($key2 == array_keys($questions)[0]): ?>
                        <td rowspan="<?= count($questions); ?>"><?php echo $key1 ; ?></td>
                        <td rowspan="<?= count($questions); ?>"><?php echo $result_points[$key1] ; ?></td>
                    <?php endif; ?>
                    <td rowspan="<?php count($userAnswers); ?>"><?= $key2; ?></td>
                    <td>
                        <?php foreach ($userAnswers as $answer):?>
                            <?php echo $answer . '</br>' ?>
                        <?php endforeach;?>
                    </td>
                    <td>
                        <?php foreach ($correct_answers[$key1][$key2] as $correct_answer):?>
                            <?php echo $correct_answer . '</br>' ?>
                        <?php endforeach;?>

                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>

</table>

