<?php

namespace fortytwostudio\passwordprotection\migrations;

use Craft;
use craft\db\Migration;

/**
 * m260319_210711_create_database migration.
 */
class m260319_210711_create_database extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        // Navigation Items Table
        $this->archiveTableIfExists('{{%forty_password_content}}');
        $this->createTable('{{%forty_password_content}}', [
			'id' => $this->primaryKey(),
			'heading' => $this->text(),
			'content' => $this->text(),
			'dateCreated' => $this->dateTime()->notNull(),
			'dateUpdated' => $this->dateTime()->notNull(),
			'uid' => $this->uid(),
		]);

		$this->insert('{{%forty_password_content}}', [
			'heading' => 'Protected Page',
			'content' => "",
			'dateCreated' => new \yii\db\Expression('NOW()'),
			'dateUpdated' => new \yii\db\Expression('NOW()'),
			'uid' => \craft\helpers\StringHelper::UUID(),
		]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m260319_210711_create_database cannot be reverted.\n";
        return false;
    }
}
