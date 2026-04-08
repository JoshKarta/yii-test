<?php

namespace common\models;

use common\components\NotificationService;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use Twig\Loader\ArrayLoader;
use Twig\Environment;

/**
 * This is the model class for table "posts".
 *
 * @property int $id
 * @property int $author_id
 * @property int|null $updated_by
 * @property string $title
 * @property string|null $slug
 * @property string $content
 * @property string $status
 * @property string|null $published_at
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $post_type_id
 *
 * @property User $author
 * @property PostType $postType
 * @property PostsSignature[] $postsSignatures
 * @property User $updatedBy
 */
class Posts extends \yii\db\ActiveRecord
{

    public $publish = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'posts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['updated_by', 'slug', 'published_at', 'post_type_id'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'draft'],
            [['title', 'content'], 'required'],
            [['author_id', 'updated_by', 'post_type_id'], 'integer'],
            [['content'], 'string'],
            [['published_at', 'created_at', 'updated_at'], 'safe'],
            [['title', 'slug'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 50],
            [['publish'], 'boolean'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['author_id' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['post_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => PostType::class, 'targetAttribute' => ['post_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'Author ID',
            'updated_by' => 'Updated By',
            'title' => 'Title',
            'slug' => 'Slug',
            'content' => 'Content',
            'status' => 'Status',
            'published_at' => 'Published At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'post_type_id' => 'Post Type ID',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    /**
     * Gets query for [[PostType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPostType()
    {
        return $this->hasOne(PostType::class, ['id' => 'post_type_id']);
    }

    /**
     * Gets query for [[PostsSignatures]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPostsSignatures()
    {
        return $this->hasMany(PostsSignature::class, ['post_id' => 'id']);
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

    public function behaviors()
    {
        return [
            [
                'class' => \raoul2000\workflow\base\SimpleWorkflowBehavior::className(),
                'defaultWorkflowId' => 'posts-workflow',
                'propagateErrorsToModel' => true,
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'author_id',
                // 'updatedByAttribute' => 'updated_by',
                'value' => function () {
                    return Yii::$app->user && !Yii::$app->user->isGuest ? Yii::$app->user->id : null;
                },
            ],
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    public function renderWithLayout()
    {
        if (!$this->postType || !$this->postType->layout_template) {
            return $this->title . "\n\n" . $this->content;
        }

        $loader = new ArrayLoader([
            'template' => $this->postType->layout_template,
        ]);

        $twig = new Environment($loader);

        return $twig->render('template', [
            'post' => $this,
            'postType' => $this->postType,
            'signatures' => $this->postsSignatures,
        ]);
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // If publish is true and published_at not yet set, set it to now
        if ($this->publish && empty($this->published_at)) {
            $this->published_at = date('Y-m-d H:i:s');
        }

        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Only trigger on NEW record
        if ($insert) {

            // Optional: only notify when published
            // if ($this->status !== 'published') {
            //     return;
            // }

            NotificationService::create(
                'New Post',
                'A new post "' . $this->title . '" has been published.',
                [Yii::$app->params['hoofdId'], Yii::$app->params['onderhoofdId']] // role_ids
            );
        }

        // ✏️ UPDATE
        // Only notify if important fields changed
        $importantFields = ['title', 'content', 'status'];

        $changed = array_intersect(array_keys($changedAttributes), $importantFields);
        $fieldsChanged = implode(', ', array_keys($changed));

        if (!empty($changed)) {

            NotificationService::create(
                'Post Updated',
                'Post "' . $this->title . '" updated. Fields: ' . $fieldsChanged,
                [Yii::$app->params['hoofdId'], Yii::$app->params['onderhoofdId']] // role_ids
            );
        }
    }
}
