<?php
/**
 * Comparison module for Craft CMS 3.x
 *
 * ZZV Comparison Module
 *
 * @link      asmith.com
 * @copyright Copyright (c) 2018 Austin Smith
 */

namespace modules\comparisonmodule\migrations;

use modules\comparisonmodule\ComparisonModule;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * Comparison Install Migration
 *
 * If your module needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your module folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    Austin Smith
 * @package   ComparisonModule
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

    // comparisonmodule_comparisonmodulerecord table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%comparisonmodule_comparisonmodulerecord}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%comparisonmodule_comparisonmodulerecord}}',
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
    // comparisonmodule_comparisonmodulerecord table
        $this->createIndex(
            $this->db->getIndexName(
                '{{%comparisonmodule_comparisonmodulerecord}}',
                'some_field',
                true
            ),
            '{{%comparisonmodule_comparisonmodulerecord}}',
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
    // comparisonmodule_comparisonmodulerecord table
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%comparisonmodule_comparisonmodulerecord}}', 'siteId'),
            '{{%comparisonmodule_comparisonmodulerecord}}',
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
    // comparisonmodule_comparisonmodulerecord table
        $this->dropTableIfExists('{{%comparisonmodule_comparisonmodulerecord}}');
    }
}
