<?php
/**
 * @coversDefaultClass \Cassava\Plugin
 */
class WP_TestWPCASServerPlugin extends WP_UnitTestCase {

	private $plugin;

	/**
	 * Setup a test method for the CASServerPlugin class.
	 */
	function setUp () {
		parent::setUp();
		$this->plugin = $GLOBALS[ \Cassava\Plugin::SLUG ];
	}

	/**
	 * Finish a test method for the CASServerPlugin class.
	 */
	function tearDown () {
		parent::tearDown();
		unset( $this->plugin );
	}

	/**
	 * Test plugin constant and static attributes.
	 */
	function test_plugin_constants () {
		$slug = 'wp-cas-server';
		$this->assertEquals( $slug, \Cassava\Plugin::SLUG, "Plugin slug is $slug." );

		$file = 'wp-cas-server/wp-cas-server.php';
		$this->assertEquals( $file, \Cassava\Plugin::FILE, "Plugin file is $file." );
	}

	/**
	 * The plugin should be installed and activated.
	 * @covers \Cassava\Plugin::plugin_activated
	 */
	function test_plugin_activated () {
		$this->assertNotNull( $GLOBALS[ \Cassava\Plugin::SLUG ],
			'Plugin is instantiated.' );

		$this->assertTrue( is_plugin_active( \Cassava\Plugin::FILE ),
			'Plugin is activated.' );
	}

	/**
	 * Test plugin options.
	 * @covers \Cassava\Plugin::init
	 */
	function test_init () {
		global $wp;

		delete_option( \Cassava\Plugin::OPTIONS_KEY );

		$this->plugin->init();

		$this->assertNotEmpty( get_option( \Cassava\Plugin::OPTIONS_KEY ), 'Plugin sets default options on init.' );

		$this->assertTrue( in_array( 'cas_route', $wp->public_query_vars ), 'Plugin sets the cas_route endpoint.' );
	}

	/**
	 * Test plugin action callbacks.
	 * @group action
	 */
	function test_actions () {
		$actions = array(
			'plugins_loaded' => array(
				'priority'  => 10,
				'callback'  => array( $this->plugin, 'plugins_loaded' ),
				),
			'init' => array(
				'priority'  => 10,
				'callback'  => array( $this->plugin, 'init' ),
				),
			'template_redirect' => array(
				'priority'  => -100,
				'callback'  => array( $this->plugin, 'template_redirect' ),
				),
		);

		foreach ($actions as $tag => $action) {
			$this->assertEquals( $action['priority'], has_action( $tag, $action['callback'] ),
				"Plugin has a '$tag' action callback." );
			$this->assertTrue( is_callable( $action['callback'] ),
				"'$tag' action callback is callable." );
		}
	}

	/**
	 * Test plugin filter callbacks.
	 * @group filter
	 */
	function test_filters () {
		$filters = array(
			'allowed_redirect_hosts' => array(
				'priority'  => 10,
				'callback'  => array( $this->plugin, 'allowed_redirect_hosts' ),
				),
		);

		foreach ($filters as $tag => $filter) {
			$this->assertEquals( $filter['priority'], has_filter( $tag, $filter['callback'] ),
				"Plugin has a '$tag' filter callback." );
			$this->assertTrue( is_callable( $filter['callback'] ),
				"'$tag' filter callback is callable." );
		}
	}

	/**
	 * Test plugin settings getter.
	 * @covers \Cassava\Plugin::getOption
	 */
	function testGetOption () {
		$path = \Cassava\Plugin::getOption( 'endpoint_slug' );
		$this->assertEquals( \Cassava\Plugin::ENDPOINT_SLUG, $path, 'Obtain the path setting.' );

		$path = \Cassava\Plugin::getOption( 'endpoint_slug', 'default' );
		$this->assertEquals( \Cassava\Plugin::ENDPOINT_SLUG, $path, 'Ignores default when obtaining an existing setting.' );

		$unset = \Cassava\Plugin::getOption( 'unset', 'nothing' );
		$this->assertEquals( 'nothing', $unset, 'Obtain the default for a non-existing setting.' );
	}

	/**
	 * Test plugin settings setter.
	 *
	 * @covers \Cassava\Plugin::getOption
	 * @covers \Cassava\Plugin::setOption
	 */
	function testSetOption () {
		\Cassava\Plugin::setOption( 'zero', 0 );
		$this->assertSame( 0, \Cassava\Plugin::getOption( 'zero' ), 'Set 0 integer.' );

		\Cassava\Plugin::setOption( 'integer', 99 );
		$this->assertSame( 99, \Cassava\Plugin::getOption( 'integer' ), 'Set non-zero integer.' );

		\Cassava\Plugin::setOption( 'float', 99.99 );
		$this->assertSame( 99.99, \Cassava\Plugin::getOption( 'float' ), 'Set float.' );

		\Cassava\Plugin::setOption( 'string', 'test' );
		$this->assertSame( 'test', \Cassava\Plugin::getOption( 'string' ), 'Set string.' );

		\Cassava\Plugin::setOption( 'array', array( 1, 2, 3 ) );
		$this->assertSame( array( 1, 2, 3 ), \Cassava\Plugin::getOption( 'array' ), 'Set array.' );

		\Cassava\Plugin::setOption( 'object', (object) array( 1, 2, 3 ) );
		$this->assertEquals( (object) array( 1, 2, 3 ), \Cassava\Plugin::getOption( 'object' ), 'Set object.' );
	}

	/**
	 * Test allowed_redirect_hosts filter callback.
	 * @covers \Cassava\Plugin::allowed_redirect_hosts
	 */
	function testAllowedRedirectHosts () {

		$noSchemaAllowed = version_compare( phpversion(), '5.4.7', '>=' );

		\Cassava\Plugin::setOption( 'allowed_services', array(
			'http://test1/',
			'http://test2:8080/',
			'https://test3/',
			'http://test4/path/',
			'http://user@test5/',
			'//test6',
			) );

		$hosts = $this->plugin->allowed_redirect_hosts( array( 'test.local' ));

		$this->assertContains( 'test.local', $hosts,
			'test.local is retained in the allowed redirect hosts list.' );

		$this->assertContains( 'test1', $hosts,
			'test1 is added to the allowed redirect hosts list.' );

		$this->assertContains( 'test2', $hosts,
			'test2 is added to the allowed redirect hosts list.' );

		$this->assertContains( 'test3', $hosts,
			'test3 is added to the allowed redirect hosts list.' );

		$this->assertContains( 'test4', $hosts,
			'test4 is added to the allowed redirect hosts list.' );

		$this->assertContains( 'test5', $hosts,
			'test5 is added to the allowed redirect hosts list.' );

		if ($noSchemaAllowed) {
			$this->assertContains( 'test6', $hosts,
				'test6 is added to the allowed redirect hosts list.' );
		}

		$expected_count = $noSchemaAllowed ? 7 : 6;

		$this->assertCount( $expected_count, $hosts,
			'Allowed hosts are added to an existing list.');

		\Cassava\Plugin::setOption( 'allowed_services', false );

		$hosts = $this->plugin->allowed_redirect_hosts( array( 'test.local' ));

		$this->assertContains( 'test.local', $hosts,
			'test.local is retained in the allowed redirect hosts list.' );

		$this->assertNotContains( '', $hosts,
			'Empty setting does not add invalid hosts to the list.' );

		$this->assertCount( 1, $hosts,
			'Invalid hosts are not added to an existing list.');

		\Cassava\Plugin::setOption( 'allowed_services', 'http://test-string/' );

		$hosts = $this->plugin->allowed_redirect_hosts();

		$this->assertContains( 'test-string', $hosts,
			'String setting adds a single host to the list.' );

		$this->assertCount( 1, $hosts,
			'Single host is added.');
	}

	/**
	 * Test the rewrite rules set by the plugin.
	 *
	 * @todo Test rewrite rules.
	 * @todo Test that the endpoint_slug reverts to the default when empty.
	 */
	function test_rewrite_rules () {

		$path = \Cassava\Plugin::getOption( 'endpoint_slug' );

		$this->assertNotEmpty( $path, 'Plugin sets default URI path root.');

		$rule = '^' . $path . '/(.*)?';

		// TODO: Look for endpoints
		// - Force SSL option OFF --> OK
		// - Force SSL option ON and...
		//     - SSL ON           --> OK
		//     - SSL OFF          --> Error

		// Plugin forces default endpoint slug

		\Cassava\Plugin::setOption( 'endpoint_slug', '' );

		$this->markTestIncomplete();
	}

}

