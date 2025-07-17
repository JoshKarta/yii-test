<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "menu_item".
 *
 * @property int $id
 * @property string $label
 * @property string|null $url
 * @property string|null $icon
 * @property string|null $icon_type
 * @property int|null $parent_id
 * @property string $location
 * @property int|null $sort_order
 * @property string|null $target
 * @property int|null $heading
 * @property int|null $visible
 * @property int|null $only_developers
 * @property string|null $visible_to_roles
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property MenuItem[] $menuItems
 * @property MenuItem $parent
 */
class MenuItem extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'menu_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url', 'icon', 'parent_id', 'target'], 'default', 'value' => null],
            [['location'], 'default', 'value' => 'backend'],
            [['sort_order'], 'default', 'value' => 0],
            [['icon_type'], 'default', 'value' => 'lucide'],
            [['visible'], 'default', 'value' => 1],
            [['only_developers', 'heading'], 'default', 'value' => 0],
            [['label'], 'required'],
            [['parent_id', 'sort_order', 'visible', 'only_developers', 'heading'], 'integer'],
            [['created_at', 'updated_at', 'visible_to_roles'], 'safe'],
            [['label', 'url'], 'string', 'max' => 255],
            [['icon'], 'string', 'max' => 100],
            [['icon_type', 'target'], 'string', 'max' => 20],
            [['location'], 'string', 'max' => 50],
            // [['visible_to_roles'], 'string'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => MenuItem::class, 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'label' => 'Label',
            'url' => 'Url',
            'icon' => 'Icon',
            'icon_type' => 'Icon Type',
            'parent_id' => 'Parent ID',
            'location' => 'Location',
            'sort_order' => 'Sort Order',
            'target' => 'Target',
            'heading' => 'Heading',
            'visible' => 'Visible',
            'only_developers' => 'Only For Developers',
            'visible_to_roles' => 'Visible For Roles',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[MenuItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMenuItems()
    {
        return $this->hasMany(MenuItem::class, ['parent_id' => 'id']);
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(MenuItem::class, ['id' => 'parent_id']);
    }

    /**
     * Gets child menu items
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(MenuItem::class, ['parent_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC]);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // If this is a new record and sort_order is not set
        if ($insert && empty($this->sort_order)) {
            // Get the highest sort_order value and add 1
            $maxOrder = self::find()->max('sort_order');
            $this->sort_order = $maxOrder ? $maxOrder + 1 : 1;
        }

        // Add "/" prefix to url if it's not empty and doesn't already have one
        if ($this->url && !str_starts_with($this->url, '/')) {
            $this->url = '/' . $this->url;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public static function find()
    {
        return parent::find()->orderBy(['sort_order' => SORT_ASC]);
    }

    public function getRoleList()
    {
        return $this->roles ? json_decode($this->roles, true) : [];
    }

    public function setRoleList(array $roles)
    {
        $this->roles = json_encode($roles);
    }

    // public function getVisibleToRoleNames(): string
    // {
    //     if (empty($this->visible_to_roles)) {
    //         return '(None)';
    //     }

    //     $roleIds = is_array($this->visible_to_roles)
    //         ? $this->visible_to_roles
    //         : json_decode($this->visible_to_roles, true);

    //     if (empty($roleIds)) {
    //         return '(None)';
    //     }

    //     $roles = Role::find()->select('name')->where(['id' => $roleIds])->column();

    //     return implode(', ', $roles);
    // }
}
