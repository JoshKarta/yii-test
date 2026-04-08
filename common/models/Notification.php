<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property int $id
 * @property string $title
 * @property string $message
 * @property string|null $type
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $created_by
 *
 * @property NotificationRole[] $notificationRoles
 * @property NotificationUser[] $notificationUsers
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
            [['type', 'created_by'], 'default', 'value' => null],
            [['title', 'message'], 'required'],
            [['message'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'message' => 'Message',
            'type' => 'Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
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
     * Gets query for [[NotificationUsers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationUsers()
    {
        return $this->hasMany(NotificationUser::class, ['notification_id' => 'id']);
    }

}
