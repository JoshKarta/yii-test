<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notification_trigger".
 *
 * @property int $id
 * @property string $route
 * @property string $notification_key
 * @property string $model_class
 * @property string $model_id_param
 * @property string|null $fields
 * @property string|null $link_template
 * @property string|null $trigger_type
 */
class NotificationTrigger extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification_trigger';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fields', 'link_template'], 'default', 'value' => null],
            [['route', 'notification_key', 'model_class', 'model_id_param'], 'required'],
            [['fields'], 'safe'],
            [['route', 'notification_key', 'model_class', 'model_id_param', 'link_template', 'trigger_type'], 'string', 'max' => 255],
            [['route'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'route' => 'Route',
            'notification_key' => 'Notification Key',
            'model_class' => 'Model Class',
            'model_id_param' => 'Model Id Param',
            'fields' => 'Fields',
            'link_template' => 'Link Template',
            'trigger_type' => 'Trigger Type',
        ];
    }
}
