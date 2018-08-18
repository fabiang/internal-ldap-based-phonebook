<?php
/**
 * This file is the application level ExtendCakeTestCase class.
 *
 * CakeExtendTest: Extended test tools for CakePHP.
 * @copyright Copyright 2016, Andrey Klimov.
 * @package app.Test
 */

App::uses('ExtendCakeTestCase', 'CakeExtendTest.Test');
require_once App::pluginPath('CakeTheme') . 'Test' . DS . 'Config' . DS . 'const.php';

/**
 * Application level CakeTestCase class
 *
 */
class AppCakeTestCase extends ExtendCakeTestCase
{

    /**
     * Setup the test case, backup the static object values so they can be restored.
     * Specifically backs up the contents of Configure and paths in App if they have
     * not already been backed up.
     *
     * Actions:
     * - Write test configuration.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $path = __DIR__ . DS;
        $this->applyTestConfig($path);
        Configure::write('Routing.prefixes', ['admin']);
        Router::reload();
    }

    /**
     * teardown any static object changes and restore them.
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
    }
}
