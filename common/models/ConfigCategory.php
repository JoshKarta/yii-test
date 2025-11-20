<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "config_category".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $sort_order
 * @property bool $is_system
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Config[] $configs
 */
class ConfigCategory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%config_category}}';
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['sort_order'], 'integer'],
            [['is_system'], 'boolean'],
            [['name'], 'string', 'max' => 50],
            [['name'], 'unique'],
            [['sort_order'], 'default', 'value' => 0],
            [['is_system'], 'default', 'value' => false],
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
            'description' => 'Description',
            'sort_order' => 'Sort Order',
            'is_system' => 'Is System',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Configs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConfigs()
    {
        return $this->hasMany(Config::class, ['category_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC]);
    }

    /**
     * Get categories for dropdown
     */
    public static function getCategories()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()
                ->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC])
                ->all(),
            'id',
            'name'
        );
    }

    /**
     * Get category names as array
     */
    public static function getCategoryNames()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()
                ->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC])
                ->all(),
            'name',
            'name'
        );
    }

    /**
     * Find category by name
     */
    public static function findByName($name)
    {
        return self::findOne(['name' => $name]);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->name;
    }
}
