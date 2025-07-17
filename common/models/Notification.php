<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property int $id
 * @property string $key
 * @property string $title
 * @property string $message_template
 * @property int|null $enabled
 * @property int|null $send_email
 * @property string|null $created_at
 *
 * @property NotificationRole[] $notificationRoles
 * @property UserNotification[] $userNotifications
 */
class Notification extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enabled'], 'default', 'value' => 1],
            [['send_email'], 'default', 'value' => 0],
            [['key', 'title', 'message_template'], 'required'],
            [['message_template'], 'string'],
            [['enabled', 'send_email'], 'integer'],
            [['created_at'], 'safe'],
            [['key', 'title'], 'string', 'max' => 255],
            [['key'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'title' => 'Title',
            'message_template' => 'Message Template',
            'enabled' => 'Enabled',
            'send_email' => 'Send Email',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[NotificationRoles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationRoles()
    {
        return $this->hasMany(NotificationRole::class, ['notification_id' => 'id']);
    }

    /**
     * Gets query for [[UserNotifications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserNotifications()
    {
        return $this->hasMany(UserNotification::class, ['notification_id' => 'id']);
    }

}
