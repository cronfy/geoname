<?php

use yii\db\Migration;

/**
 * Handles the creation of table `geonames`.
 */
class m000000_000002_crgn_properties_to_data extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->renameColumn('geoname', 'properties', 'data');
    }
}
