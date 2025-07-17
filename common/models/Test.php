<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "test".
 *
 * @property int $idtest
 * @property string|null $value
 */
class Test extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'test';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value'], 'default', 'value' => null],
            [['value'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idtest' => 'Idtest',
            'value' => 'Value',
        ];
    }

}
