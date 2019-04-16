<?php

use yii\db\Migration;

/**
 * Class m190204_132003_create_app_version.
 */
class m190204_132003_create_app_version extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%app_version}}', [
            'id' => $this->primaryKey(),
            'version' => $this->string(),
            'filename' => $this->string(),
            'force_update' => $this->boolean(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%app_version}}');
    }
}
