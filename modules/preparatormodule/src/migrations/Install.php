<?php
/**
 * preparator module for Craft CMS 3.x
 *
 * Excel reader and preparator
 *
 * @link      cstudios.sk
 * @copyright Copyright (c) 2019 @lostika
 */

namespace modules\preparatormodule\migrations;

use modules\preparatormodule\PreparatorModule;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * preparator Install Migration
 *
 * If your module needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your module folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    @lostika
 * @package   PreparatorModule
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the module
     *
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

        // preparatormodule_calculation_datas table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%preparatormodule_calculation_datas}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%preparatormodule_calculation_datas}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    // Custom columns in the table
                    'siteId' => $this->integer()->notNull(),
                    'identification' => $this->string(20)->notNull(),
                    'value' => $this->decimal(8)->notNull()->defaultValue(1),
                ]
            );
        }

    // preparatormodule_housetypes table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%preparatormodule_housetypes}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%preparatormodule_housetypes}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                // Custom columns in the table
                    'siteId' => $this->integer()->notNull(),
                    'some_field' => $this->string(255)->notNull()->defaultValue(''),
                ]
            );
        }

    // preparatormodule_attributes table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%preparatormodule_attributes}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%preparatormodule_attributes}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                // Custom columns in the table
                    'siteId' => $this->integer()->notNull(),
                    'some_field' => $this->string(255)->notNull()->defaultValue(''),
                ]
            );
        }

    // preparatormodule_variants table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%preparatormodule_variants}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%preparatormodule_variants}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                // Custom columns in the table
                    'siteId' => $this->integer()->notNull(),
                    'some_field' => $this->string(255)->notNull()->defaultValue(''),
                ]
            );
        }

    // preparatormodule_options table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%preparatormodule_options}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%preparatormodule_options}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                // Custom columns in the table
                    'siteId' => $this->integer()->notNull(),
                    'some_field' => $this->string(255)->notNull()->defaultValue(''),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {
    // preparatormodule_housetypes table
        $this->createIndex(
            $this->db->getIndexName(
                '{{%preparatormodule_housetypes}}',
                'some_field',
                true
            ),
            '{{%preparatormodule_housetypes}}',
            'some_field',
            true
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }

    // preparatormodule_attributes table
        $this->createIndex(
            $this->db->getIndexName(
                '{{%preparatormodule_attributes}}',
                'some_field',
                true
            ),
            '{{%preparatormodule_attributes}}',
            'some_field',
            true
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }

    // preparatormodule_variants table
        $this->createIndex(
            $this->db->getIndexName(
                '{{%preparatormodule_variants}}',
                'some_field',
                true
            ),
            '{{%preparatormodule_variants}}',
            'some_field',
            true
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }

    // preparatormodule_options table
        $this->createIndex(
            $this->db->getIndexName(
                '{{%preparatormodule_options}}',
                'some_field',
                true
            ),
            '{{%preparatormodule_options}}',
            'some_field',
            true
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }
    }

    /**
     * Creates the foreign keys needed for the Records used by the module
     *
     * @return void
     */
    protected function addForeignKeys()
    {
    // preparatormodule_housetypes table
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%preparatormodule_housetypes}}', 'siteId'),
            '{{%preparatormodule_housetypes}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

    // preparatormodule_attributes table
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%preparatormodule_attributes}}', 'siteId'),
            '{{%preparatormodule_attributes}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

    // preparatormodule_variants table
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%preparatormodule_variants}}', 'siteId'),
            '{{%preparatormodule_variants}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

    // preparatormodule_options table
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%preparatormodule_options}}', 'siteId'),
            '{{%preparatormodule_options}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * Populates the DB with the default data.
     *
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * Removes the tables needed for the Records used by the module
     *
     * @return void
     */
    protected function removeTables()
    {
    // preparatormodule_housetypes table
        $this->dropTableIfExists('{{%preparatormodule_housetypes}}');

    // preparatormodule_attributes table
        $this->dropTableIfExists('{{%preparatormodule_attributes}}');

    // preparatormodule_variants table
        $this->dropTableIfExists('{{%preparatormodule_variants}}');

    // preparatormodule_options table
        $this->dropTableIfExists('{{%preparatormodule_options}}');
    }
}
