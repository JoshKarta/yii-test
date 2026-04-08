<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notification_user".
 *
 * @property int $id
 * @property int $notification_id
 * @property int $user_id
 * @property int|null $is_read
 * @property string|null $read_at
 *
 * @property Notification $notification
 * @property User $user
 */
class NotificationUser extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['read_at'], 'default', 'value' => null],
            [['is_read'], 'default', 'value' => 0],
            [['notification_id', 'user_id'], 'required'],
            [['notification_id', 'user_id', 'is_read'], 'integer'],
            [['read_at'], 'safe'],
            [['notification_id'], 'exist', 'skipOnError' => true, 'targetClass' => Notification::class, 'targetAttribute' => ['notification_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'notification_id' => 'Notification ID',
            'user_id' => 'User ID',
            'is_read' => 'Is Read',
            'read_at' => 'Read At',
        ];
    }

    /**
     * Gets query for [[Notification]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotification()
    {
        return $this->hasOne(Notification::class, ['id' => 'notification_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function markAsRead()
    {
        $this->is_read = 1;
        $this->read_at = date('Y-m-d H:i:s');
        return $this->save(false);
    }
}
