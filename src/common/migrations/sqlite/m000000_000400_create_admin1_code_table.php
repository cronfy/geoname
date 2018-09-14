<?php

class m000000_000400_create_admin1_code_table extends \yii\db\Migration {

    public function up()
    {
        $this->createTable(
            'admin1_code',
            [
                'code' => $this->string(),
                'name' => $this->string(),
                'name_ascii' => $this->string(),
                'geonameid' => $this->integer(),
            ]
        );

        $this->createIndex('admin1_code|code', 'admin1_code', ['code']);
    }

    public function down()
    {
        $this->dropTable('admin1_code');
    }
}