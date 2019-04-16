<?php

use yii\db\Migration;

/**
 * Class m190204_134959_create_service_tariff.
 */
class m190204_134959_create_service_tariff extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%service_tariff}}', [
          'id' => $this->primaryKey(),
          'key' => $this->integer(),
          'price_shift' => $this->money(),
          'price_overnight' => $this->money(),
          'show_price_overnight' => $this->boolean(),
          'room_category_name' => $this->string(),
          'room_category_name_short' => $this->string(),
          'turn_duration' => $this->string(),
          'overnight_start' => $this->string(),
          'overnight_finish' => $this->string(),
          'long_turn_start' => $this->string(),
          'long_turn_finish' => $this->string(),
          'show_overnight_start_finish' => $this->boolean(),
          'show_long_turn_start_finish' => $this->boolean(),
      ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%service_tariff}}');
    }
}
