<?php

namespace common\components;

use Yii;

class EncryptionHelper
{
    public static function encrypt($data, $key)
    {
        return Yii::$app->security->encryptByPassword($data, $key);
    }

    public static function decrypt($data, $key)
    {
        return Yii::$app->security->decryptByPassword($data, $key);
    }
}
