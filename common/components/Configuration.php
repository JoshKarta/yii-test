<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\caching\Cache;
use common\models\Config;

class Configuration extends Component
{
    const CACHE_KEY = 'app_configurations';
    const CACHE_DURATION = 3600; // 1 hour

    private $_configurations = [];

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

        $this->_configurations = $cache->getOrSet(self::CACHE_KEY, function () {
            $configs = Config::find()
                ->select(['category', 'key', 'value', 'type'])
                ->asArray()
                ->all();

            $result = [];
            foreach ($configs as $config) {
                $result[$config['category']][$config['key']] = [
                    'value' => $config['value'],
                    'type' => $config['type']
                ];
            }
            return $result;
        }, self::CACHE_DURATION);
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
    public function set($key, $value, $category = 'general')
    {
        $config = Config::findOne(['category' => $category, 'key' => $key]);

        if (!$config) {
            $config = new Config();
            $config->category = $category;
            $config->key = $key;
            $config->type = $this->detectType($value);
        }

        $config->setTypedValue($value);

        if ($config->save()) {
            // Update cached value
            $this->_configurations[$category][$key] = [
                'value' => $config->value,
                'type' => $config->type
            ];

            // Update cache
            Yii::$app->cache->set(self::CACHE_KEY, $this->_configurations, self::CACHE_DURATION);

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
     * Clear configuration cache
     */
    public function clearCache()
    {
        Yii::$app->cache->delete(self::CACHE_KEY);
        $this->_configurations = [];
        $this->loadConfigurations();
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
