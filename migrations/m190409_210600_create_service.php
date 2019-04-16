<?php

use yii\db\Migration;

/**
 * Class m190409_210600_create_service.
 */
class m190409_210600_create_service extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%service}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%service}}');
    }
}
