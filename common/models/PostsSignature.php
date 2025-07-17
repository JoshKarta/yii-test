<?php

namespace common\models;

use common\components\SignatureHelper;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "posts_signature".
 *
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property string $signature_base64
 * @property string|null $created_at
 * @property string|null $signed_at
 * @property string|null $updated_at
 * @property int|null $version
 * @property string|null $ip_address
 * @property string|null $user_agent
 *
 * @property Posts $post
 * @property User $user
 */
class PostsSignature extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'posts_signature';
    }

    public function behaviors()
    {
        return [
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['signed_at', 'ip_address', 'user_agent'], 'default', 'value' => null],
            [['version'], 'default', 'value' => 1],
            [['post_id', 'user_id', 'signature_base64'], 'required'],
            [['post_id', 'user_id', 'version'], 'integer'],
            [['signature_base64'], 'string'],
            [['created_at', 'signed_at', 'updated_at'], 'safe'],
            [['ip_address'], 'string', 'max' => 45],
            [['user_agent'], 'string', 'max' => 255],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posts::class, 'targetAttribute' => ['post_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'post_id' => 'Post ID',
            'user_id' => 'User ID',
            'signature_base64' => 'Signature Base64',
            'created_at' => 'Created At',
            'signed_at' => 'Signed At',
            'updated_at' => 'Updated At',
            'version' => 'Version',
            'ip_address' => 'Ip Address',
            'user_agent' => 'User Agent',
        ];
    }

    /**
     * Gets query for [[Post]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Posts::class, ['id' => 'post_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getSignatureImage()
    {
        $userId = $this->user_id; // assuming the model has this field
        return SignatureHelper::decodeAndDecrypt($this->signature_base64, $userId);
    }
}
