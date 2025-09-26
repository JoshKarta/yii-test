<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notification_trigger".
 *
 * @property int $id
 * @property string $route
 * @property string $notification_key
 * @property string|null $request_type
 * @property string|null $link_template
 *
 * @property Notification $notificationKey
 */
class NotificationTrigger extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const REQUEST_TYPE_ANY = 'ANY';
    const REQUEST_TYPE_AJAX = 'AJAX';
    const REQUEST_TYPE_NON_AJAX = 'NON_AJAX';

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
            [['link_template'], 'default', 'value' => null],
            [['request_type'], 'default', 'value' => 'ANY'],
            [['route', 'notification_key'], 'required'],
            [['request_type'], 'string'],
            [['route', 'notification_key', 'link_template'], 'string', 'max' => 255],
            ['request_type', 'in', 'range' => array_keys(self::optsRequestType())],
            [['route', 'notification_key', 'request_type'], 'unique', 'targetAttribute' => ['route', 'notification_key', 'request_type']],
            [['notification_key'], 'exist', 'skipOnError' => true, 'targetClass' => Notification::class, 'targetAttribute' => ['notification_key' => 'key']],
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
            'request_type' => 'Request Type',
            'link_template' => 'Link Template',
        ];
    }

    /**
     * Gets query for [[NotificationKey]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationKey()
    {
        return $this->hasOne(Notification::class, ['key' => 'notification_key']);
    }


    /**
     * column request_type ENUM value labels
     * @return string[]
     */
    public static function optsRequestType()
    {
        return [
            self::REQUEST_TYPE_ANY => 'ANY',
            self::REQUEST_TYPE_AJAX => 'AJAX',
            self::REQUEST_TYPE_NON_AJAX => 'NON_AJAX',
        ];
    }

    /**
     * @return string
     */
    public function displayRequestType()
    {
        return self::optsRequestType()[$this->request_type];
    }

    /**
     * @return bool
     */
    public function isRequestTypeAny()
    {
        return $this->request_type === self::REQUEST_TYPE_ANY;
    }

    public function setRequestTypeToAny()
    {
        $this->request_type = self::REQUEST_TYPE_ANY;
    }

    /**
     * @return bool
     */
    public function isRequestTypeAjax()
    {
        return $this->request_type === self::REQUEST_TYPE_AJAX;
    }

    public function setRequestTypeToAjax()
    {
        $this->request_type = self::REQUEST_TYPE_AJAX;
    }

    /**
     * @return bool
     */
    public function isRequestTypeNonajax()
    {
        return $this->request_type === self::REQUEST_TYPE_NON_AJAX;
    }

    public function setRequestTypeToNonajax()
    {
        $this->request_type = self::REQUEST_TYPE_NON_AJAX;
    }
}
