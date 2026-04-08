<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notification_role".
 *
 * @property int $id
 * @property int $notification_id
 * @property int $role_id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Notification $notification
 * @property Roles $role
 */
class NotificationRole extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification_role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notification_id', 'role_id'], 'required'],
            [['notification_id', 'role_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['notification_id'], 'exist', 'skipOnError' => true, 'targetClass' => Notification::class, 'targetAttribute' => ['notification_id' => 'id']],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Roles::class, 'targetAttribute' => ['role_id' => 'id']],
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
            'role_id' => 'Role ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
     * Gets query for [[Role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Roles::class, ['id' => 'role_id']);
    }

}
