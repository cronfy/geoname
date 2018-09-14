<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 23.10.17
 * Time: 18:34
 */

namespace cronfy\geoname;

use cronfy\geoname\common\misc\GeonameRepository;
use cronfy\geoname\common\misc\GeonameService;
use cronfy\geoname\common\models\Geoname;
use Yii;
use yii\db\Connection;

class BaseModule extends \yii\base\Module
{

    public $geonamesLogin;
    public $cacheDir;

    public function init()
    {
        $this->cacheDir = Yii::getAlias($this->cacheDir);
        parent::init();
    }

    public function getControllerPath()
    {
        // Yii определяет путь к контроллеру через алиас по controllerNamespace.
        // Алиас создавать не хочется, так как это лишняя сущность, которая может
        // конфликтовать с другими алиасами (мы - модуль и не знаем, какие алиасы
        // уже используются в приложении). Поэтому определим путь к контроллерам
        // своим способом.
        $rc = new \ReflectionClass(get_class($this));
        return dirname($rc->getFileName()) . '/controllers';
    }

    public static function findGeonames($name)
    {
        return Geoname::find()->where(['name' => static::yeYoOptions($name)])->all();
    }

    /**
     * Возвращает все возможные варианты замены е/ё в слове.
     * Например, для 'Ещё' вернет ['Еще', 'Ещё', 'Ёще', 'Ёщё']
     * @param $str
     * @return array
     */
    public static function yeYoOptions($str)
    {
        $variants = [''];
        for ($i = 0; $i < mb_strlen($str); $i++) {
            $char = mb_substr($str, $i, 1);
            if (in_array($char, ['е', "Е", "ё", "Ё"])) {
                $newVariants = $variants;
                foreach ($newVariants as &$variant) {
                    $variant .= 'е';
                }
                unset($variant);
                foreach ($variants as &$variant) {
                    $variant .= 'ё';
                }
                unset($variant);
                $variants = array_merge($variants, $newVariants);
            } else {
                foreach ($variants as &$variant) {
                    $variant .= $char;
                }
                unset($variant);
            }
        }

        return($variants);
    }

    protected $_db;
    public function getSqliteDatabase() {
        if (!$this->_db) {
            $db = new Connection();
            $sqlitePath = Yii::getAlias($this->cacheDir . '/geonames.sqlite');
            $sqliteDir = dirname($sqlitePath);
            if (!is_dir($sqliteDir)) {
                mkdir($sqliteDir, 0755, true);
            }
            $db->dsn = 'sqlite:' . $sqlitePath;
            $this->_db = $db;

            \cronfy\geoname\common\models\sqlite\Geoname::$db = $db;
            \cronfy\geoname\common\models\sqlite\AlternateName::$db = $db;
            \cronfy\geoname\common\models\sqlite\Admin1Code::$db = $db;
            \cronfy\geoname\common\models\sqlite\Hierarchy::$db = $db;
        }

        return $this->_db;
    }

    protected $_geonamesService;
    public function getGeonamesService() {
        if (!$this->_geonamesService) {
            $service = new GeonameService();
            $service->module = $this;
            $this->_geonamesService = $service;
        }

        return $this->_geonamesService;
    }

    protected $_geonameRepository;
    public function getGeonameRepository() {
        if (!$this->_geonameRepository) {
            $this->_geonameRepository = new GeonameRepository();
        }

        return $this->_geonameRepository;
    }
}
