<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 17.08.18
 * Time: 19:31
 */

namespace cronfy\geoname\common\misc;

use cronfy\geoname\BaseModule;
use cronfy\geoname\common\models\GeonameDTO;
use cronfy\geoname\common\models\sqlite\AlternateName;
use cronfy\geoname\common\models\sqlite\Geoname;
use cronfy\geoname\common\models\sqlite\Hierarchy;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class GeonameService
{
    /** @var BaseModule */
    public $module;

    public $dataCacheDir;

    protected $_countryRepositories = [];

    protected $_countryGeonamesSqliteRepository;
    public function getCountryGeonamesSqliteRepository() {
        if (!$this->_countryGeonamesSqliteRepository) {
            $countryRepository = new CountryGeonamesSqliteRepository();
            $countryRepository->db = $this->module->getSqliteDatabase();

            $this->_countryGeonamesSqliteRepository = $countryRepository;
        }

        return $this->_countryGeonamesSqliteRepository;
    }

    protected $_alternateNamesRepository;
    public function getAlternateNamesRepository() {
        if (!$this->_alternateNamesRepository) {
            $repository = new AlternateNamesSqliteRepository();
            $repository->db = $this->module->getSqliteDatabase();

            $this->_alternateNamesRepository = $repository;
        }

        return $this->_alternateNamesRepository;
    }

    protected $_hierarchyRepository;
    public function getHierarchyRepository() {
        if (!$this->_hierarchyRepository) {
            $repository = new HierarchySqliteRepository();
            $repository->db = $this->module->getSqliteDatabase();

            $this->_hierarchyRepository = $repository;
        }

        return $this->_hierarchyRepository;
    }

    protected $_admin1CodesRepository;
    public function getAdmin1CodesRepository() {
        if (!$this->_admin1CodesRepository) {
            $repository = new Admin1CodesSqliteRepository();
            $repository->db = $this->module->getSqliteDatabase();

            $this->_admin1CodesRepository = $repository;
        }

        return $this->_admin1CodesRepository;
    }


    public function normalizeCountryCode($countryCode) {
        return strtoupper($countryCode);
    }

    public function getCountryCodeByAlias($countryAlias) {
        switch (strtolower($countryAlias)) {
            case 'russia':
                $countryCode = 'RU';
                break;
            case 'austria':
                $countryCode = 'AT';
                break;
            default:
                if (preg_match('/^[a-zA-Z]{2}$/', $countryAlias)) {
                    $countryCode = strtoupper($countryAlias);
                    break;
                }

                throw new \Exception("Unknown alias $countryAlias");
        }

        return $this->normalizeCountryCode($countryCode);
    }

    /**
     * @param $languageAlternateNameItems AlternateName[]
     * @return AlternateName
     */
    public function filterOfficialLanguageName($languageAlternateNameItems) {
        // В geonames есть ошибки, так как редактируются они с помощью community в wiki-style.
        // Например, для обычного названия города Сургут может быть проставлено, что он isHistoric
        // и isColloquial. Это ошибка.
        // Данный алгоритм выбирает ОДНО имя, которое с большей вероятностью будет официальным и наиболее
        // часто употребимым в отношении данного населенного пункта.

        // Сначала посмотрим, есть ли у нас выбор
        if (count($languageAlternateNameItems) === 1) {
            return array_pop($languageAlternateNameItems); // we have no choice
        }

        // Выбор есть, тогда будем искать среди  isPreferredName'ов
        $preferreds = [];
        foreach ($languageAlternateNameItems as $languageAlternateNameItem) {
            if ($languageAlternateNameItem->isPreferredName) {
                $preferreds[] = $languageAlternateNameItem;
            }
        }
        if ($preferreds) {
            if (count($preferreds) === 1) {
                return array_pop($preferreds); // we have no choice
            }

            // Preferred несколько, будем искать среди них имена без пометок вида
            // historic, colloquial, short,
            // так как имена с этими пометками считаем вторичными.

            $noMarksPreferreds = [];
            foreach ($preferreds as $preferred) {
                if ($preferred->isHistoric) {
                    continue;
                }

                if ($preferred->isColloquial) {
                    continue;
                }

                if ($preferred->isShortName) {
                    continue;
                }

                $noMarksPreferreds[] = $preferred;
            }

            if (count($noMarksPreferreds) === 1) {
                return array_pop($noMarksPreferreds); // we have no choice
            }

            $geonameid = $preferreds[0]->geonameid;

            $exception = new MultipleAlternateNamesException("$geonameid: multiple preferred names, failed to find best, need more research and exact cases");
            $exception->names = $preferreds;
            throw $exception;
        }

        // Preferred нет, будем искать среди имеющихся имен без пометок вида historic, colloquial, short,
        // так как имена с этими пометками считаем вторичными.
        $noMarks = [];
        foreach ($languageAlternateNameItems as $languageAlternateNameItem) {
            if ($languageAlternateNameItem->isHistoric) {
                continue;
            }

            if ($languageAlternateNameItem->isColloquial) {
                continue;
            }

            if ($languageAlternateNameItem->isShortName) {
                continue;
            }

            $noMarks[] = $languageAlternateNameItem;
        }

        if (!$noMarks) {
            if ($languageAlternateNameItems) {
                $exception = new NoAcceptableAlternateNamesException("No preferreds, no unmarked names - not implemented: need more research and exact cases");
                $exception->names = $languageAlternateNameItems;
                throw $exception;
            }

            throw new Exception("No alternate names found");
        }

        if (count($noMarks) === 1) {
            return array_pop($noMarks);
        }

        $exception = new MultipleAlternateNamesException("No preferreds, more than 1 unmarked names - not implemented: need more research and exact cases");
        $exception->names = $noMarks;
        throw $exception;
    }

    /**
     * @param $geoname Geoname
     * @return Geoname|null
     */
    public function getRegionByGeoname($geoname) {
        $admin1CodesRepository = $this->getAdmin1CodesRepository();
        $countryGeonamesRepository = $this->getCountryGeonamesSqliteRepository();
        $hierarchyRepository = $this->getHierarchyRepository();

        if (!$admin1Code = $admin1CodesRepository->getByGeoname($geoname)) {
            return null;
        }
        $admGeoname = $countryGeonamesRepository->getByGeonameId($admin1Code->geonameid);
        $admPath = $hierarchyRepository->getAdmPathToGeoname($admGeoname);
        $admPath = array_reverse($admPath);

        $regionGeoname = null;
        foreach ($admPath as $hierarchy) {
            /** @var Hierarchy $hierarchy */
            $regionGeonameId = $hierarchy['childId'];
            $adm = $countryGeonamesRepository->getByGeonameId($regionGeonameId);
            if ($adm->feature_code === 'ADM1') {
                $regionGeoname = $adm;
                break;
            }
        }

        return $regionGeoname;
    }

    /**
     * @param $geoname Geoname
     * @return AlternateName
     */
    public function getOfficialNameByGeoname($geoname, $isolanguage) {
        $alternateNamesRepository = $this->getAlternateNamesRepository();

        $alternateNames = $alternateNamesRepository
            ->getFindQuery()
            ->language($isolanguage)
            ->andWhere(['not', ['alternateNameId' => $alternateNamesRepository->getIdsOfKnownErrors()]])
            ->andWhere(['geonameid' => $geoname->geonameid])
            ->all()
        ;

        if (!$alternateNames) {
            // Нет названий в alternate names => не можем определить язык названия.
            // И возможно это не совсем корректный населенный пункт, например, Петродворец
            // Пропускаем.
            return null;
        }

        $alternateNamesByLanguage = ArrayHelper::index($alternateNames, null, 'isolanguage');

        try {
            $officialName = $this->filterOfficialLanguageName($alternateNamesByLanguage[$isolanguage]);
            return $officialName;
        } catch (\Exception $e) {
            do {
                if (is_a($e, MultipleAlternateNamesException::class)) {
                    break;
                }
                if (is_a($e, NoAcceptableAlternateNamesException::class)) {
                    break;
                }

                throw $e;
            } while (false);

            if ($e->names) {
                // Несколько имен. В этом случае все что мы можем сделать - это взять
                // первое попавшееся, за неимением экспертизы и из-за слишком большого
                // количества таких случаев.
                $officialName = array_pop($e->names);
                return $officialName;
            }

            throw $e;
        }
    }

    /**
     * @param $geoname Geoname
     * @return GeonameDTO
     */
    public function getGeonameDTOFromGeoname($geoname) {
        $dto = new GeonameDTO();
        foreach (array_keys(get_object_vars($dto)) as $key) {
            $dto->$key = $geoname->$key;
        }
        return $dto;
    }





    private function getAlternateNamesByGeonameIdCacheKey($countryCode, $geonameId) {
        return 'lvendor.cronfy.geoname.service.altnames.geoname.' . $countryCode . '.' . $geonameId;
    }

    protected function getCachedAlternateNamesByGeonameId($countryCode, $geonameId) {
        $cacheKey = $this->getAlternateNamesByGeonameIdCacheKey($countryCode, $geonameId);
        return Yii::$app->cache->get($cacheKey);
    }

    protected function setCachedAlternateNamesByGeonameId($countryCode, $geonameId, $value) {
        $cacheKey = $this->getAlternateNamesByGeonameIdCacheKey($countryCode, $geonameId);
        return Yii::$app->cache->set($cacheKey, $value, 60 * 60 * 24 * 30);
    }

    public function getAlternateNamesByGeonameIds($countryCode, $geonameIds, $filters = []) {
        $result = [];

        $alternateNames = [];
        foreach ($geonameIds as $index => $geonameId) {
            $cached = $this->getCachedAlternateNamesByGeonameId($countryCode, $geonameId);
            if ($cached !== false) {
                $alternateNames = array_merge($alternateNames, $cached);
                unset($geonameIds[$index]);
            }
        }

        if ($geonameIds) {
            $forCache = [];
            foreach ($this->iterateCountryAlternateNamesItems($countryCode) as $alternateNamesItem) {
                if (!in_array($alternateNamesItem->geonameid, $geonameIds)) {
                    continue;
                }

                $forCache[$alternateNamesItem->geonameid][] = $alternateNamesItem;
            }

            foreach ($geonameIds as $geonameId) {
                // может, и нет alternateNames для этого geonameid, отсутствие тоже надо закешировать
                $items = @$forCache[$geonameId] ?: [];
                $this->setCachedAlternateNamesByGeonameId($countryCode, $geonameId, $items);
                $alternateNames = array_merge($alternateNames, $items);
            }
        }

        foreach ($alternateNames as $alternateNamesItem) {
            foreach ($filters as $filter) {
                if (!$filter($alternateNamesItem)) {
                    continue 2;
                }
            }

            $result[$alternateNamesItem->geonameid][] = $alternateNamesItem;
        }

        return $result;
    }
}