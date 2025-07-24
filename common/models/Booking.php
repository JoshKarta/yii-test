<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "booking".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $start_time
 * @property string $end_time
 * @property string|null $created_at
 */
class Booking extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'booking';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['title', 'start_time', 'date'], 'required'],
            [['start_time', 'end_time', 'date'], 'safe'],
            [['description'], 'string'],
            // [['start_time', 'end_time'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            // [['date'], 'date', 'format' => 'php:Y-m-d'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'date' => 'Booking Date',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'created_at' => 'Created At',
        ];
    }

    // public function beforeSave($insert)
    // {
    //     if (parent::beforeSave($insert)) {
    //         if ($this->date) {
    //             if (preg_match('/^\d{2}:\d{2}$/', $this->start_time)) {
    //                 $this->start_time = $this->date . ' ' . $this->start_time . ':00';
    //             }
    //             if (preg_match('/^\d{2}:\d{2}$/', $this->end_time)) {
    //                 $this->end_time = $this->date . ' ' . $this->end_time . ':00';
    //             }
    //         }
    //         return true;
    //     }
    //     return false;
    // }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && empty($this->color)) {
                $this->color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            }
            return true;
        }
        return false;
    }
}
