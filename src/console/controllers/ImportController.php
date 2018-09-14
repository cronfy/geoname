<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 01.10.17
 * Time: 22:01
 */

namespace cronfy\geoname\console\controllers;

use cronfy\geoname\BaseModule;
use cronfy\geoname\common\models\GeonameAlternateNameItem;
use Yii;
use yii\console\Controller;
use cronfy\geoname\common\models\Geoname;

/**
 * @property BaseModule $module
 */
class ImportController extends Controller
{

    protected $cacheTime = 60 * 60 * 24 * 30;

    protected function getGeonamesService() {
        return $this->module->getGeonamesService();
    }

    public function actionDestroy()
    {
        Geoname::deleteAll();
    }

    public function actionGeonames($country)
    {
        $this->iterateGeonamesCities($country);
    }

    protected function iterateBatch($baseIterable, $count) {
        $batch = [];
        foreach ($baseIterable as $index => $item) {
            $batch[$index] = $item;
            if (count($batch) >= $count) {
                yield $batch;
                $batch = [];
            }
        }

        if ($batch) {
            yield $batch;
        }

    }

    protected function iterateGeonamesCitiesWithOfficialNames($countryCode) {
        $service = $this->getGeonamesService();

        $countryRepository = $service->getCountryGeonamesSqliteRepository();

        $query = $countryRepository->getFindQuery()
            ->country($countryCode)
            ->populatedLocations()
            ->population(15000)
        ;

        foreach ($query->batch(100) as $batch) {
            foreach ($batch as $geoname) {
                /** @var \cronfy\geoname\common\models\sqlite\Geoname $geoname */
                if (!$officialName = $service->getOfficialNameByGeoname($geoname, 'ru')) {
                    // не удалось определить официальное название пункта - пропускаем
                    continue;
                }

                yield [
                    'geonameItem' => $geoname,
                    'officialNames' => [
                        'ru' => $officialName
                    ]
                ];

            }
        }
    }

    protected function iterateGeonamesCities($countryCode)
    {
        foreach ($this->iterateGeonamesCitiesWithOfficialNames($countryCode) as $data) {
            /** @var \cronfy\geoname\common\models\sqlite\Geoname $geoname */

            $geoname = $data['geonameItem'];
            $officialNames = $data['officialNames'];
            /** @var GeonameAlternateNameItem $ruName */
            $ruName = $officialNames['ru'];


            //
            // prepare importedGeoname
            //
            $regionGeoname = $this->getGeonamesService()->getRegionByGeoname($geoname);

            if (!$regionGeoname) {
                // не удается определить регион. Встретилось у 2411585 (GI - Гибралтар),
                // у которого admin1_code == 00, и в admin1CodesASCII.txt такого нет.
                // Пока пропускаем.
                continue;
            }

            $existing = Geoname::findOne(['geonameId' => $geoname->geonameid]);

            if (!$existing) {
                $importedGeoname = new Geoname();
                $importedGeoname->geonameId = $geoname->geonameid;
                $importedGeoname->type = 'city';
            } else {
                $importedGeoname = $existing;
            }

            $importedGeoname->name = $ruName->alternate_name;
            $importedGeoname->lat = $geoname->latitude;
            $importedGeoname->lng = $geoname->longitude;
            $importedGeoname->data['population'] = $geoname->population;

            $geonameDTO = $this->getGeonamesService()->getGeonameDTOFromGeoname($geoname);
            $geonameDTO->alternatenames = null;
            $importedGeoname->data['geonameDTO'] = $geonameDTO;

            $importedGeoname->data['regionGeonameId'] = $regionGeoname->geonameid;







            //
            // prepare importedGeonameRegion
            //

            if (!$regionGeoname) {
                throw new \Exception("Failed to get region for {$geoname->geonameid}");
            }

            $existing = Geoname::findOne(['geonameId' => $regionGeoname->geonameid]);
            if (!$existing) {
                $importedGeonameRegion = new Geoname();
                $importedGeonameRegion->geonameId = $regionGeoname->geonameid;
                $importedGeonameRegion->type = 'region';
            } else {
                $importedGeonameRegion = $existing;
            }

            $importedGeonameRegion->lat = $regionGeoname->latitude;
            $importedGeonameRegion->lng = $regionGeoname->longitude;
            if ($officialName = $this->getGeonamesService()->getOfficialNameByGeoname($regionGeoname, 'ru')) {
                $importedGeonameRegion->name = $officialName->alternate_name;
            } else {
                $importedGeonameRegion->name = $regionGeoname->name;
            }

            $geonameDTO = $this->getGeonamesService()->getGeonameDTOFromGeoname($regionGeoname);
            $geonameDTO->alternatenames = null;
            $importedGeonameRegion->data['geonameDTO'] = $geonameDTO;

            //
            // Save
            //

            $transaction = Yii::$app->db->beginTransaction();

            try {
                if ($importedGeonameRegion->isNewRecord || $importedGeonameRegion->dirtyAttributes) {
                    echo $importedGeonameRegion->isNewRecord ? '*' : 'U';
                    $importedGeonameRegion->ensureSave();
                }
                if ($importedGeoname->isNewRecord || $importedGeoname->dirtyAttributes) {
                    echo $importedGeoname->isNewRecord ? '+' : 'u';
                    $importedGeoname->ensureSave();
                } else {
                    echo ".";
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
//                E(
//                    [
//                        $importedGeoname->errors,
//                        $importedGeoname->attributes,
//                        $importedGeonameRegion->errors,
//                        $importedGeonameRegion->attributes,
//                    ]
//                );
                throw $e;
            }

        }

        echo "\nFinished.\n";
    }
}
