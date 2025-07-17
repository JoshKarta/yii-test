<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "mock_posts".
 *
 * @property int $id
 * @property int $source_id
 * @property string|null $author
 * @property string $title
 * @property string|null $slug
 * @property string $content
 * @property string $status
 * @property string|null $published_at
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $type
 */
class MockPosts extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mock_posts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author', 'slug', 'published_at', 'type', 'title', 'content'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'draft'],
            // [['title', 'content'], 'required'],
            [['content'], 'string'],
            [['published_at', 'created_at', 'updated_at'], 'safe'],
            [['author', 'title', 'slug', 'type'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 50],
            [['source_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author' => 'Author',
            'title' => 'Title',
            'slug' => 'Slug',
            'content' => 'Content',
            'status' => 'Status',
            'published_at' => 'Published At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'type' => 'Type',
        ];
    }

    // public function __toString(): string
    // {
    //     return $this->username ?? '';
    // }
}
