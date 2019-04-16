<?php

use yii\db\Migration;

/**
 * Class m190204_134602_create_bar_group.
 */
class m190204_133202_create_bar_group extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%bar_group}}', [
            'id' => $this->primaryKey(),
            'key' => $this->integer(),
            'id_number' => $this->integer(),
            'name' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%bar_group}}');
    }
}
