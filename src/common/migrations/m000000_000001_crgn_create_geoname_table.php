<?php

use yii\db\Migration;

/**
 * Handles the creation of table `geonames`.
 */
class m000000_000001_crgn_create_geoname_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('geoname', [
            'id' => $this->primaryKey(),
            'lat' => $this->double(),
            'lng' => $this->double(),
            'geonameId' => $this->integer()->notNull()->unsigned()->unique(),
            'name' => $this->string()->notNull(),
            'properties' => $this->string(1024),
        ], 'CHARACTER SET utf8 ENGINE=InnoDb');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('geoname');
    }
}
