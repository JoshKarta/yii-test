<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sync_data".
 *
 * @property int $change_id
 * @property string $table_name
 * @property int $pk
 * @property string $action
 * @property string $change_time
 */
class SyncData extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ACTION_INSERT = 'INSERT';
    const ACTION_UPDATE = 'UPDATE';
    const ACTION_DELETE = 'DELETE';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sync_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['table_name', 'pk', 'action'], 'required'],
            [['pk', 'synced'], 'integer'],
            [['action'], 'string'],
            [['change_time'], 'safe'],
            [['table_name'], 'string', 'max' => 50],
            ['action', 'in', 'range' => array_keys(self::optsAction())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'change_id' => 'Change ID',
            'table_name' => 'Table Name',
            'pk' => 'Pk',
            'synced' => 'Synced',
            'action' => 'Action',
            'change_time' => 'Change Time',
        ];
    }


    /**
     * column action ENUM value labels
     * @return string[]
     */
    public static function optsAction()
    {
        return [
            self::ACTION_INSERT => 'INSERT',
            self::ACTION_UPDATE => 'UPDATE',
            self::ACTION_DELETE => 'DELETE',
        ];
    }

    /**
     * @return string
     */
    public function displayAction()
    {
        return self::optsAction()[$this->action];
    }

    /**
     * @return bool
     */
    public function isActionInsert()
    {
        return $this->action === self::ACTION_INSERT;
    }

    public function setActionToInsert()
    {
        $this->action = self::ACTION_INSERT;
    }

    /**
     * @return bool
     */
    public function isActionUpdate()
    {
        return $this->action === self::ACTION_UPDATE;
    }

    public function setActionToUpdate()
    {
        $this->action = self::ACTION_UPDATE;
    }

    /**
     * @return bool
     */
    public function isActionDelete()
    {
        return $this->action === self::ACTION_DELETE;
    }

    public function setActionToDelete()
    {
        $this->action = self::ACTION_DELETE;
    }
}
