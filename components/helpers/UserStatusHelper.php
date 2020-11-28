<?php

namespace app\components\helpers;

use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class UserStatusHelper 
{
    public static function statusList(): array
    {
        return [
            User::STATUS_DELETED => 'Удален',
            User::STATUS_INACTIVE => 'Неактивен',
            User::STATUS_ACTIVE => 'Активен',
        ];
    }
    public static function statusName($status): string
    {
        return ArrayHelper::getValue(self::statusList(), $status);
    }
        public static function statusLabel($status): string
    {
        switch ($status) {
            case User::STATUS_DELETED:
                $class = 'label label-danger';
                break;
            case User::STATUS_ACTIVE:
                $class = 'label label-success';
                break;
            case User::STATUS_INACTIVE:
                $class = 'label label-default';
                break;            
            default:
                $class = 'label label-default';
        }

        return Html::tag('span', ArrayHelper::getValue(self::statusList(), $status), [
            'class' => $class,
        ]);
    }
}
