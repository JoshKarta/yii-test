<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "api".
 *
 * @property int $id
 * @property string $name
 * @property string $table_name
 * @property string|null $relations
 * @property int|null $is_active
 * @property string|null $token
 * @property int|null $rate_limit
 * @property int|null $rate_limit_remaining
 * @property string|null $rate_limit_reset_at
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Api extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'api';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['allowed_fields', 'relations', 'token', 'rate_limit_reset_at'], 'default', 'value' => null],
            [['is_active'], 'default', 'value' => 1],
            [['rate_limit_remaining', 'rate_limit'], 'default', 'value' => 100],
            [['name', 'table_name'], 'required'],
            // [['relations'], 'string'],
            [['is_active', 'rate_limit', 'rate_limit_remaining'], 'integer'],
            [['rate_limit_reset_at', 'created_at', 'updated_at', 'allowed_fields', 'relations'], 'safe'],
            [['name', 'table_name'], 'string', 'max' => 255],
            [['token'], 'string', 'max' => 512],
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
            'table_name' => 'Table Name',
            'allowed_fields' => 'Allowed Fields',
            'relations' => 'Relations',
            'is_active' => 'Is Active',
            'token' => 'Token',
            'rate_limit' => 'Rate Limit',
            'rate_limit_remaining' => 'Rate Limit Remaining',
            'rate_limit_reset_at' => 'Rate Limit Reset At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function beforeSave($insert)
    {
        if (!empty($this->name)) {
            $this->name = strtolower(str_replace(' ', '-', $this->name));
        }
        if (is_array($this->allowed_fields)) {
            $this->allowed_fields = json_encode($this->allowed_fields);
        }

        if (is_array($this->relations)) {
            $this->relations = json_encode($this->relations);
        }

        return parent::beforeSave($insert);
    }

    // public function afterFind()
    // {
    //     parent::afterFind();

    //     if (is_string($this->allowed_fields)) {
    //         $decoded = json_decode($this->allowed_fields, true);
    //         $this->allowed_fields = is_array($decoded) ? $decoded : [];
    //     }

    //     if (is_string($this->relations)) {
    //         $decoded = json_decode($this->relations, true);
    //         $this->relations = is_array($decoded) ? $decoded : [];
    //     }
    // }
    public function getAllowedFieldsArray()
    {
        if (is_string($this->allowed_fields)) {
            $decoded = json_decode($this->allowed_fields, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($this->allowed_fields)
            ? $this->allowed_fields
            : [];
    }
    public function getRelationsArray()
    {
        if (is_string($this->relations)) {
            $decoded = json_decode($this->relations, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($this->relations)
            ? $this->relations
            : [];
    }
}
