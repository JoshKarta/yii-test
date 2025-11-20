<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * This is the model class for table "config".
 *
 * @property int $id
 * @property int $category_id
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
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property ConfigCategory $category
 */
class Config extends ActiveRecord
{
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_JSON = 'json';
    const TYPE_ARRAY = 'array';

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
                'value' => new Expression('NOW()'),
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
            [['category_id', 'key'], 'required'],
            [['category_id', 'is_system', 'sort_order', 'created_by', 'updated_by'], 'integer'],
            [['value', 'description'], 'string'],
            [['key'], 'string', 'max' => 100],
            [['type'], 'string', 'max' => 10],
            [['type'], 'in', 'range' => array_keys(self::getTypes())],
            [['category_id', 'key'], 'unique', 'targetAttribute' => ['category_id', 'key']],
            [['sort_order'], 'default', 'value' => 0],
            [['is_system'], 'default', 'value' => 0],
            [['type'], 'default', 'value' => self::TYPE_STRING],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ConfigCategory::class, 'targetAttribute' => ['category_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category',
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
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(ConfigCategory::class, ['id' => 'category_id']);
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
}
