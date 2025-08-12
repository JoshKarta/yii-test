<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "persoon".
 *
 * @property int $persoonid
 * @property string|null $regnr
 * @property string $naam
 * @property string $voornaam
 * @property string|null $idnr
 * @property string|null $verzekeringskaartnr
 * @property string $geboortedatum
 * @property int $created_by
 * @property int $updated_by
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property User $createdBy
 * @property User $updatedBy
 */
class Persoon extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'persoon';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['regnr', 'idnr', 'verzekeringskaartnr'], 'default', 'value' => null],
            [['naam', 'voornaam', 'geboortedatum', 'created_by', 'updated_by'], 'required'],
            [['geboortedatum', 'created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['regnr', 'naam', 'voornaam', 'idnr', 'verzekeringskaartnr'], 'string', 'max' => 255],
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
            'persoonid' => 'Persoonid',
            'regnr' => 'Regnr',
            'naam' => 'Naam',
            'voornaam' => 'Voornaam',
            'idnr' => 'Idnr',
            'verzekeringskaartnr' => 'Verzekeringskaartnr',
            'geboortedatum' => 'Geboortedatum',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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

}
