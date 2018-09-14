<?php

use yii\db\Migration;

/**
 * Handles the creation of table `geonames`.
 */
class m000000_000003_crgn_add_type extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('geoname', 'type', $this->string());
    }
}
