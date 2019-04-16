<?php

use yii\db\Migration;

/**
 * Class m190204_132340_create_audio_message.
 */
class m190204_132340_create_audio_message extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%audio_message}}', [
            'id' => $this->primaryKey(),
            'key' => $this->integer(),
            'name' => $this->string(),
            'name_spanish' => $this->string(),
            'filename' => $this->string(),
            'kind' => $this->smallInteger(),
            'audio_output' => $this->smallInteger(),
            'delay' => $this->integer(),
            'manual' => $this->boolean(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%audio_message}}');
    }
}
