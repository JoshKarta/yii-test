<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "form".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $json
 * @property int|null $created_by
 * @property string|null $created_at
 *
 * @property User $createdBy
 * @property FormResponse[] $formResponses
 */
class Form extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'form';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'created_by'], 'default', 'value' => null],
            [['title', 'json'], 'required'],
            [['description'], 'string'],
            [['json', 'created_at'], 'safe'],
            [['created_by'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
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
            'description' => 'Description',
            'json' => 'Json',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
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
     * Gets query for [[FormResponses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFormResponses()
    {
        return $this->hasMany(FormResponse::class, ['form_id' => 'id']);
    }

}
