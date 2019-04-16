<?php

use yii\db\Migration;

/**
 * Class m190204_133447_create_bar_article.
 */
class m190204_133447_create_bar_article extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%bar_article}}', [
            'id' => $this->primaryKey(),
            'bar_group_id' => $this->integer(),
            'key' => $this->integer(),
            'id_number' => $this->integer(),
            'name' => $this->string(),
            'description' => $this->text(),
            'picture_filename' => $this->string(),
            'price' => $this->money(),
        ]);

        $this->addForeignKey(
            '{{%fk_bar_article_bar_group}}',
            '{{%bar_article}}',
            'bar_group_id',
            '{{%bar_group}}',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%bar_article}}');
    }
}
