<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * @property int $id
 * @property string $category
 * @property string $key
 * @property string|null $value
 * @property string $type
 * @property string|null $description
 * @property int $is_system
 * @property int $sort_order
 * @property string $created_at
 * @property string $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class Config extends ActiveRecord
{
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_JSON = 'json';
    const TYPE_ARRAY = 'array';

    const CATEGORY_GENERAL = 'general';
    const CATEGORY_COMPANY = 'company';
    const CATEGORY_FRONTEND = 'frontend';
    const CATEGORY_BACKEND = 'backend';
    const CATEGORY_EMAIL = 'email';
    const CATEGORY_SYSTEM = 'system';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%config}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new \yii\db\Expression('NOW()'),
            ],
            BlameableBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category', 'key'], 'required'],
            [['value', 'description'], 'string'],
            [['is_system', 'sort_order', 'created_by', 'updated_by'], 'integer'],
            [['category'], 'string', 'max' => 50],
            [['key'], 'string', 'max' => 100],
            [['type'], 'string', 'max' => 10],
            [['type'], 'in', 'range' => array_keys(self::getTypes())],
            [['category', 'key'], 'unique', 'targetAttribute' => ['category', 'key']],
            [['sort_order'], 'default', 'value' => 0],
            [['is_system'], 'default', 'value' => 0],
            [['type'], 'default', 'value' => self::TYPE_STRING],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category' => 'Category',
            'key' => 'Key',
            'value' => 'Value',
            'type' => 'Type',
            'description' => 'Description',
            'is_system' => 'System Configuration',
            'sort_order' => 'Sort Order',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Get available types
     */
    public static function getTypes()
    {
        return [
            self::TYPE_STRING => 'String',
            self::TYPE_INTEGER => 'Integer',
            self::TYPE_BOOLEAN => 'Boolean',
            self::TYPE_JSON => 'JSON',
            self::TYPE_ARRAY => 'Array',
        ];
    }

    /**
     * Get available categories
     */
    public static function getCategories()
    {
        return [
            self::CATEGORY_GENERAL => 'General',
            self::CATEGORY_COMPANY => 'Company',
            self::CATEGORY_FRONTEND => 'Frontend',
            self::CATEGORY_BACKEND => 'Backend',
            self::CATEGORY_EMAIL => 'Email',
            self::CATEGORY_SYSTEM => 'System',
        ];
    }

    /**
     * Get typed value
     */
    public function getTypedValue()
    {
        if ($this->value === null) {
            return null;
        }

        switch ($this->type) {
            case self::TYPE_INTEGER:
                return (int) $this->value;
            case self::TYPE_BOOLEAN:
                return (bool) $this->value;
            case self::TYPE_JSON:
                return Json::decode($this->value);
            case self::TYPE_ARRAY:
                return explode(',', $this->value);
            default:
                return $this->value;
        }
    }

    /**
     * Set typed value
     */
    public function setTypedValue($value)
    {
        switch ($this->type) {
            case self::TYPE_INTEGER:
                $this->value = (string) (int) $value;
                break;
            case self::TYPE_BOOLEAN:
                $this->value = $value ? '1' : '0';
                break;
            case self::TYPE_JSON:
                $this->value = Json::encode($value);
                break;
            case self::TYPE_ARRAY:
                $this->value = is_array($value) ? implode(',', $value) : $value;
                break;
            default:
                $this->value = (string) $value;
        }
    }

    /**
     * Before save event
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Ensure system configs cannot be deleted through normal means
            if ($this->is_system && $this->getIsNewRecord()) {
                $this->is_system = 0; // Only allow system configs to be created via migrations
            }
            return true;
        }
        return false;
    }
}
