<?php

class m000000_000300_create_hierarchy_table extends \yii\db\Migration {

    public function up()
    {
        $this->createTable(
            'hierarchy',
            [
                'parentId' => $this->integer()->notNull(),
                'childId' => $this->integer()->notNull(),
                // The type ADM stands for the admin hierarchy modeled by the admin1-4 codes.
                // The other entries are entered with the user interface. The relation
                // toponym-adm hierarchy is not included in the file, it can instead
                // be built from the admincodes of the toponym.
                'type' => $this->string(),
            ]
        );

        $this->createIndex('childId', 'hierarchy', ['childId']);
    }
}