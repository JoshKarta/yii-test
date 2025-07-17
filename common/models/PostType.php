<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "post_type".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $layout_template
 */
class PostType extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'layout_template'], 'default', 'value' => null],
            [['layout_template'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'layout_template' => 'Layout Template',
        ];
    }

}
