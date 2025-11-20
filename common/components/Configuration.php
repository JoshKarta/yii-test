<?php

namespace common\components;

use Yii;
use yii\base\Component;
use common\models\Config;
use common\models\ConfigCategory;

class Configuration extends Component
{
    const CACHE_KEY = 'app_configurations';
    const CACHE_DURATION = 3600; // 1 hour

    private $_configurations = [];
    private $_categories = [];

    /**
     * Initialize component and load configurations
     */
    public function init()
    {
        parent::init();
        $this->loadConfigurations();
    }

    /**
     * Load all configurations from database with caching
     */
    protected function loadConfigurations()
    {
        $cache = Yii::$app->cache;

        $data = $cache->getOrSet(self::CACHE_KEY, function () {
            $categories = ConfigCategory::find()
                ->with(['configs'])
                ->orderBy(['sort_order' => SORT_ASC])
                ->asArray()
                ->all();

            $result = [];
            foreach ($categories as $category) {
                $categoryName = $category['name'];
                $result[$categoryName] = [];

                foreach ($category['configs'] as $config) {
                    $result[$categoryName][$config['key']] = [
                        'value' => $config['value'],
                        'type' => $config['type']
                    ];
                }
            }

            return [
                'configurations' => $result,
                'categories' => array_column($categories, 'name', 'id')
            ];
        }, self::CACHE_DURATION);

        $this->_configurations = $data['configurations'];
        $this->_categories = $data['categories'];
    }

    /**
     * Get configuration value
     */
    public function get($key, $default = null, $category = 'general')
    {
        if (isset($this->_configurations[$category][$key])) {
            $config = $this->_configurations[$category][$key];
            return $this->convertValue($config['value'], $config['type']);
        }

        return $default;
    }

    /**
     * Set configuration value (and save to database)
     */
    public function set($key, $value, $categoryName = 'general')
    {
        // Find category by name
        $category = ConfigCategory::findOne(['name' => $categoryName]);
        if (!$category) {
            // Create category if it doesn't exist
            $category = new ConfigCategory();
            $category->name = $categoryName;
            $category->description = 'Automatically created category';
            if (!$category->save()) {
                return false;
            }
        }

        $config = Config::findOne(['category_id' => $category->id, 'key' => $key]);

        if (!$config) {
            $config = new Config();
            $config->category_id = $category->id;
            $config->key = $key;
            $config->type = $this->detectType($value);
        }

        $config->setTypedValue($value);

        if ($config->save()) {
            // Update cached value
            $this->_configurations[$categoryName][$key] = [
                'value' => $config->value,
                'type' => $config->type
            ];

            // Update cache
            $this->updateCache();

            return true;
        }

        return false;
    }

    /**
     * Check if configuration exists
     */
    public function has($key, $category = 'general')
    {
        return isset($this->_configurations[$category][$key]);
    }

    /**
     * Get all configurations for a category
     */
    public function getAll($category = 'general')
    {
        $result = [];

        if (isset($this->_configurations[$category])) {
            foreach ($this->_configurations[$category] as $key => $config) {
                $result[$key] = $this->convertValue($config['value'], $config['type']);
            }
        }

        return $result;
    }

    /**
     * Get all categories
     */
    public function getCategories()
    {
        return $this->_categories;
    }

    /**
     * Clear configuration cache
     */
    public function clearCache()
    {
        Yii::$app->cache->delete(self::CACHE_KEY);
        $this->_configurations = [];
        $this->_categories = [];
        $this->loadConfigurations();
    }

    /**
     * Update cache with current data
     */
    protected function updateCache()
    {
        $data = [
            'configurations' => $this->_configurations,
            'categories' => $this->_categories
        ];

        Yii::$app->cache->set(self::CACHE_KEY, $data, self::CACHE_DURATION);
    }

    /**
     * Convert string value to proper type
     */
    protected function convertValue($value, $type)
    {
        switch ($type) {
            case Config::TYPE_INTEGER:
                return (int) $value;
            case Config::TYPE_BOOLEAN:
                return (bool) $value;
            case Config::TYPE_JSON:
                return json_decode($value, true);
            case Config::TYPE_ARRAY:
                return explode(',', $value);
            default:
                return $value;
        }
    }

    /**
     * Detect value type
     */
    protected function detectType($value)
    {
        if (is_int($value)) {
            return Config::TYPE_INTEGER;
        } elseif (is_bool($value)) {
            return Config::TYPE_BOOLEAN;
        } elseif (is_array($value)) {
            return Config::TYPE_JSON;
        } else {
            return Config::TYPE_STRING;
        }
    }

    /**
     * Magic getter for category-based access
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_configurations)) {
            return new ConfigurationCategory($this, $name);
        }

        return parent::__get($name);
    }
}

/**
 * Helper class for category-based configuration access
 */
class ConfigurationCategory
{
    private $_component;
    private $_category;

    public function __construct(Configuration $component, $category)
    {
        $this->_component = $component;
        $this->_category = $category;
    }

    public function __get($name)
    {
        return $this->_component->get($name, null, $this->_category);
    }

    public function __isset($name)
    {
        return $this->_component->has($name, $this->_category);
    }
}
