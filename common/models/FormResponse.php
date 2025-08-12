<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "form_response".
 *
 * @property int $id
 * @property int $form_id
 * @property string $response_json
 * @property string|null $submitted_at
 *
 * @property Form $form
 */
class FormResponse extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'form_response';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['form_id', 'response_json'], 'required'],
            [['form_id'], 'integer'],
            [['response_json', 'submitted_at'], 'safe'],
            [['form_id'], 'exist', 'skipOnError' => true, 'targetClass' => Form::class, 'targetAttribute' => ['form_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'form_id' => 'Form ID',
            'response_json' => 'Response Json',
            'submitted_at' => 'Submitted At',
        ];
    }

    /**
     * Gets query for [[Form]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForm()
    {
        return $this->hasOne(Form::class, ['id' => 'form_id']);
    }

}
