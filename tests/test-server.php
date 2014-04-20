<?php
/**
 * @package WPCASServerPlugin
 * @subpackage WPCASServerPlugin_Tests
 */

class WP_TestWPCASServer extends WP_UnitTestCase {

	private $server;
	private $routes;

	/**
	 * Setup a test method for the WPCASServer class.
	 */
	function setUp () {
		parent::setUp();
		$this->server = new WPCASServer;
		$this->routes = $this->server->routes();
	}

	/**
	 * Finish a test method for the CASServer class.
	 */
	function tearDown () {
		parent::tearDown();
		unset( $this->server );
	}

	function test_routes () {
		$routes = array(
			'login',
			'logout',
			'proxy',
			'proxyValidate',
			'serviceValidate',
			'validate',
			);

		$server_routes = $this->server->routes();

		foreach ($routes as $route) {
			$this->assertArrayHasKey( $route, $server_routes,
				"Route '$route' has a callback." );
			$this->assertTrue( is_callable( $server_routes[$route] ),
				"Method for route '$route' is callable." );
		}
	}

	function test_handleRequest () {
		$this->assertTrue( is_callable( array( $this->server, 'handleRequest' ) ), "'handleRequest' method is callable." );

		$this->markTestIncomplete();
	}

	function test_login () {

		$this->assertTrue( is_callable( array( $this->server, 'login' ) ), "'login' method is callable." );

		// $this->go_to();

		$this->markTestIncomplete();
	}

	function test_logout () {

		$this->assertTrue( is_callable( array( $this->server, 'logout' ) ), "'logout' method is callable." );

		$this->markTestIncomplete();
	}

	function test_validate () {

		$this->assertTrue( is_callable( array( $this->server, 'validate' ) ), "'validate' method is callable." );

		$this->markTestIncomplete();
	}

	function test_serviceValidate () {

		$this->assertTrue( is_callable( array( $this->server, 'serviceValidate' ) ), "'serviceValidate' method is callable." );

		$this->markTestIncomplete();
	}

	function test_proxy () {

		$this->assertTrue( is_callable( array( $this->server, 'proxy' ) ), "'proxy' method is callable." );

		$this->markTestIncomplete();
	}

	function test_proxyValidate () {

		$this->assertTrue( is_callable( array( $this->server, 'proxyValidate' ) ), "'proxyValidate' method is callable." );

		$this->markTestIncomplete();
	}

}

