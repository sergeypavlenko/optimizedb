<?php

/**
 * @file
 * Tests for optimizedb module.
 */

namespace Drupal\optimizedb\Tests;

use Drupal\simpletest\KernelTestBase;

/**
 * Test the module functions.
 *
 * @group optimizedb
 */
class OptimizedbTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array.
   */
  public static $modules = array('optimizedb');

  /**
   * A user with permission the settings module.
   *
   * @var object
   */
  protected $web_user;

  public function setUp() {
    parent::setUp();

    $this->installConfig(array('optimizedb'));

    $this->web_user = $this->drupalCreateUser(array('administer optimizedb settings'));
    $this->drupalLogin($this->web_user);
  }

  /**
   * Sizes tables.
   */
  public function testTablesList() {
    $config = $this->config('optimizedb.settings');

    $config
      ->set('optimizedb_tables_size', 0)
      ->save();

    // Function for output all database tables and update their total size.
    _optimizedb_tables_list();

    $this->assertNotEqual($config->get('optimizedb_tables_size'), 0);
  }

  /**
   * Testing module admin page buttons.
   */
  public function testButtonsExecutingCommands() {
    for ($i = 1; $i <= 10; $i++) {
      $this->createCacheFormItem($i);
    }

    for ($i = 1; $i <= 5; $i++) {
      $this->createCacheFormItem($i + 10, REQUEST_TIME + 3600);
    }

    $this->assertEqual($this->countCacheFormRows(), 15);

    $this->drupalPost('admin/config/development/optimizedb', array(), t('Clear cache_form table'));
    $this->assertEqual($this->countCacheFormRows(), 5);

    $this->drupalPost('admin/config/development/optimizedb', array(), t('Clear an entire table cache_form'));
    $this->assertEqual($this->countCacheFormRows(), 0);

    $list_tables = _optimizedb_tables_list();
    $count_tables = count($list_tables);

    $this->drupalPost('admin/config/development/optimizedb', array(), t('Optimize tables'));
    $this->assertText(t('Optimized @count tables.', array('@count' => $count_tables)));
  }

  /**
   * Test notify optimize in optimizedb_cron() function.
   */
  public function testCronNotifyOptimize() {
    $config = $this->config('optimizedb.settings');

    $config
      ->set('optimizedb_optimization_period', 1)
      ->set('optimizedb_last_optimization', REQUEST_TIME - ((3600 * 24) * 2))
      ->set('optimizedb_notify_optimize', FALSE)
      ->save();

    $this->cronRun();
    $this->assertTrue($config->get('optimizedb_notify_optimize', FALSE));
  }

  /**
   * Count rows in cache form table.
   */
  protected function countCacheFormRows() {
    return (int) db_select('cache_form')
      ->countQuery()
      ->execute()
      ->fetchField();
  }

  /**
   * Create new item in table "cache_form".
   *
   * @param int $cid
   *   (optional) Cache ID.
   * @param bool|int $cache_time
   *   (optional) Cache expire time.
   *
   * @return int
   *   ID new cache item.
   */
  protected function createCacheFormItem($cid = 1, $cache_time = FALSE) {
    if (!$cache_time) {
      $cache_time = REQUEST_TIME - 3600;
    }

    return db_insert('cache_form')->fields(array(
      'cid' => $cid,
      'expire' => $cache_time,
      'created' => $cache_time,
    ))->execute();
  }
}

/**
 * Test the optimizedb_hide_notification() function.
 */
class OptimizedbHideNotificationTestCase extends OptimizedbTest {

  public static function getInfo() {
    return array(
      'name' => 'Load page hide notification.',
      'description' => 'Test the show page hide notification.',
      'group' => 'Optimizedb',
    );
  }

  /**
   * Display notification of the need to perform optimization.
   */
  public function testHideNotification() {
    $config = $this->config('optimizedb.settings');

    $config
      ->set('optimizedb_notify_optimize', FALSE)
      ->save();

    $this->drupalGet('admin/config/development/optimizedb/hide');
    $this->assertText(t('Alerts are not available.'));

    $config
      ->set('optimizedb_notify_optimize', TRUE)
      ->save();

    $this->drupalGet('admin/config/development/optimizedb/hide');
    $this->assertNoText(t('Alerts are not available.'));

    $notify_optimize = $config->get('optimizedb_notify_optimize');
    $this->assertFalse($notify_optimize);
  }
}

/**
 * Testing the performance of operations on tables.
 *
 * @link admin/config/development/optimizedb/list_tables @endlink
 */
class OptimizedbListTablesOperationExecuteTestCase extends OptimizedbTest {

  public static function getInfo() {
    return array(
      'name' => 'Performing operations on tables.',
      'description' => 'Test the function sampling tables.',
      'group' => 'Optimizedb',
    );
  }

  /**
   * Performing operations on tables.
   */
  public function testListTablesOperationExecute() {
    $this->drupalPost('admin/config/development/optimizedb/list_tables', array(), t('Check tables'));
    $this->assertText(t('To execute, you must select at least one table from the list.'));

    // Output all database tables.
    $tables = _optimizedb_tables_list();
    $table_name = key($tables);

    $edit = array();
    // Selected first table in list.
    $edit['tables[' . $table_name . ']'] = $table_name;

    $this->drupalPost('admin/config/development/optimizedb/list_tables', $edit, t('Check tables'));
    $this->assertText(t('The operation completed successfully.'));
  }
}
