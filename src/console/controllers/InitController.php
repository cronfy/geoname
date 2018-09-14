<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 22.08.18
 * Time: 17:09
 */

namespace cronfy\geoname\console\controllers;

use cronfy\geoname\BaseModule;
use cronfy\geoname\common\misc\Admin1CodesCsvRepository;
use cronfy\geoname\common\misc\AlternateNamesCsvRepository;
use cronfy\geoname\common\misc\CachedRemoteFile;
use cronfy\geoname\common\misc\CachedZipFile;
use cronfy\geoname\common\misc\CountryGeonamesCsvRepository;
use cronfy\geoname\common\misc\HierarchyCsvRepository;
use cronfy\geoname\common\models\sqlite\Admin1Code;
use cronfy\geoname\common\models\sqlite\AlternateName;
use cronfy\geoname\common\models\sqlite\Geoname;
use cronfy\geoname\common\models\sqlite\Hierarchy;
use yii\console\Controller;

/**
 * @property BaseModule $module
 */
class InitController extends Controller
{

    public function actionCountryGeonames($country, $force = null) {
        $service = $this->module->getGeonamesService();
        $countryCode = $service->getCountryCodeByAlias($country);

        $dataCacheDir = $this->module->cacheDir;

        $cachedRemoteFile = new CachedRemoteFile();
        $cachedRemoteFile->srcUrl = "http://download.geonames.org/export/dump/{$countryCode}.zip";
        $cachedRemoteFile->destFilePath = $dataCacheDir . '/' . $countryCode . '.zip';

        $cachedZipFile = new CachedZipFile();
        $cachedZipFile->sourceZip = $cachedRemoteFile;
        $cachedZipFile->archiveItemFileName = "{$countryCode}.txt";
        $cachedZipFile->destFilePath = $dataCacheDir . '/' . $countryCode . '.txt';

        $csvRepository = new CountryGeonamesCsvRepository();
        $csvRepository->sourceFile = $cachedZipFile;

        $db = $this->module->getSqliteDatabase();

        if (Geoname::find()->andWhere(['country_code' => $countryCode])->one($db)) {
            if ($force !== 'force') {
                throw new \Exception("Data already exist. To remote it run me with 'force' second argiment");
            }

            $this->stdout("Removing old data...\n");
            $command = $db->createCommand(); $params = [];
            $command->delete(Geoname::tableName(), ['country_code' => $countryCode], $params);
            $command->execute();
        }


        $this->stdout("Loading data ");
        $count = 0;
        $items = [];
        foreach ($csvRepository->iterate() as $item) {
            $items[] = $item;

            $count++;
            // max is 500
            // otherwise we get error (is this a sqlite bug?):
            // General error: 1 too many terms in compound SELECT
            if ($count >= 500) {
                $command = $db->createCommand();
                $command->batchInsert(Geoname::tableName(), array_keys($item), $items);
                $command->execute();

                $count = 0;
                $items = [];
                echo ".";
            }

        }

        if ($items) {
            $command = $db->createCommand();
            $command->batchInsert(Geoname::tableName(), array_keys($item), $items);
            $command->execute();
        }

        $this->stdout("\nDone\n");
    }

    public function actionAlternateNames($country, $force = null) {
        $service = $this->module->getGeonamesService();
        $countryCode = $service->getCountryCodeByAlias($country);

        $dataCacheDir = $this->module->cacheDir;

        $cachedRemoteFile = new CachedRemoteFile();
        $cachedRemoteFile->srcUrl = "http://download.geonames.org/export/dump/alternatenames/" . $countryCode . ".zip";
        $cachedRemoteFile->destFilePath = $dataCacheDir . '/' . $countryCode . '-alternateNames.zip';

        $cachedZipFile = new CachedZipFile();
        $cachedZipFile->sourceZip = $cachedRemoteFile;
        $cachedZipFile->archiveItemFileName = "{$countryCode}.txt";
        $cachedZipFile->destFilePath = $dataCacheDir . '/' . $countryCode . '-alternateNames.txt';

        $csvRepository = new AlternateNamesCsvRepository();
        $csvRepository->sourceFile = $cachedZipFile;

        $db = $this->module->getSqliteDatabase();

        if (AlternateName::find()->andWhere(['countryCode' => $countryCode])->exists($db)) {
            if ($force !== 'force') {
                throw new \Exception("Data already exist. To remote it run me with 'force' second argiment");
            }

            $this->stdout("Removing old data...\n");
            $command = $db->createCommand(); $params = [];
            $command->delete(AlternateName::tableName(), ['countryCode' => $countryCode], $params);
            $command->execute();
        }

        $this->stdout("Loading data ");
        $count = 0;
        $items = [];
        foreach ($csvRepository->iterate() as $item) {
            $item['countryCode'] = $countryCode;

            $items[] = $item;

            $count++;
            // max is 500
            // otherwise we get error (is this an sqlite bug/feature?):
            // General error: 1 too many terms in compound SELECT
            if ($count >= 500) {
                $command = $db->createCommand();
                $command->batchInsert(AlternateName::tableName(), array_keys($item), $items);
                $command->execute();

                $count = 0;
                $items = [];
                echo ".";
            }

        }

        if ($items) {
            $command = $db->createCommand();
            $command->batchInsert(AlternateName::tableName(), array_keys($item), $items);
            $command->execute();
        }

        $this->stdout("\nDone\n");
    }

    public function actionHierarchy($force = null) {
        $dataCacheDir = $this->module->cacheDir;

        $cachedRemoteFile = new CachedRemoteFile();
        $cachedRemoteFile->srcUrl = 'http://download.geonames.org/export/dump/hierarchy.zip';
        $cachedRemoteFile->destFilePath = $dataCacheDir . '/hierarchy.zip';

        $cachedZipFile = new CachedZipFile();
        $cachedZipFile->sourceZip = $cachedRemoteFile;
        $cachedZipFile->archiveItemFileName = "hierarchy.txt";
        $cachedZipFile->destFilePath = $dataCacheDir . '/hierarchy.txt';

        $csvRepository = new HierarchyCsvRepository();
        $csvRepository->sourceFile = $cachedZipFile;

        $db = $this->module->getSqliteDatabase();

        if (Hierarchy::find()->exists($db)) {
            if ($force !== 'force') {
                throw new \Exception("Data already exist. To remote it run me with 'force' second argiment");
            }

            $this->stdout("Removing old data...\n");
            $command = $db->createCommand(); $params = [];
            $command->delete(Hierarchy::tableName(), '', $params);
            $command->execute();
        }

        $this->stdout("Loading data ");
        $count = 0;
        $items = [];
        foreach ($csvRepository->iterate() as $item) {
            $items[] = $item;

            $count++;
            // max is 500
            // otherwise we get error (is this a sqlite bug?):
            // General error: 1 too many terms in compound SELECT
            if ($count >= 500) {
                $command = $db->createCommand();
                $command->batchInsert(Hierarchy::tableName(), array_keys($item), $items);
                $command->execute();

                $count = 0;
                $items = [];
                echo ".";
            }

        }

        if ($items) {
            $command = $db->createCommand();
            $command->batchInsert(Hierarchy::tableName(), array_keys($item), $items);
            $command->execute();
        }

        $this->stdout("\nDone\n");
    }

    public function actionAdmin1Codes($force = null) {
        $dataCacheDir = $this->module->cacheDir;

        $cachedRemoteFile = new CachedRemoteFile();
        $cachedRemoteFile->srcUrl = 'http://download.geonames.org/export/dump/admin1CodesASCII.txt';
        $cachedRemoteFile->destFilePath = $dataCacheDir . '/admin1CodesASCII.txt';

        $csvRepository = new Admin1CodesCsvRepository();
        $csvRepository->sourceFile = $cachedRemoteFile;

        $db = $this->module->getSqliteDatabase();

        if (Admin1Code::find()->exists($db)) {
            if ($force !== 'force') {
                throw new \Exception("Data already exist. To remote it run me with 'force' second argiment");
            }

            $this->stdout("Removing old data...\n");
            $command = $db->createCommand(); $params = [];
            $command->delete(Admin1Code::tableName(), '', $params);
            $command->execute();
        }

        $this->stdout("Loading data ");
        $count = 0;
        $items = [];
        foreach ($csvRepository->iterate() as $item) {
            $items[] = $item;

            $count++;
            // max is 500
            // otherwise we get error (is this a sqlite bug?):
            // General error: 1 too many terms in compound SELECT
            if ($count >= 500) {
                $command = $db->createCommand();
                $command->batchInsert(Admin1Code::tableName(), array_keys($item), $items);
                $command->execute();

                $count = 0;
                $items = [];
                echo ".";
            }
        }

        if ($items) {
            $command = $db->createCommand();
            $command->batchInsert(Admin1Code::tableName(), array_keys($item), $items);
            $command->execute();
        }

        $this->stdout("\nDone\n");
    }
}