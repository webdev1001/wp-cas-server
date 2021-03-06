<?php
/**
 * @coversDefaultClass \Cassava\Exception\GeneralException
 */
class WP_TestGeneralException extends WP_UnitTestCase {

	/**
	 * Setup a test method for the WPCASServer class.
	 */
	function setUp () {
		parent::setUp();
	}

	/**
	 * Finish a test method for the CASServer class.
	 */
	function tearDown () {
		parent::tearDown();
	}

	/**
	 * @covers ::__construct
	 * @covers ::getCASCode
	 */
	function test_construct () {

		$exception = new \Cassava\Exception\GeneralException();

		$this->assertInstanceOf( 'Exception', $exception,
			'Exception is an instance of Exception.' );

		$this->assertInstanceOf( '\Cassava\Exception\GeneralException', $exception,
			'Exception is an instance of \Cassava\Exception\GeneralException.' );

		$this->assertEquals( '', $exception->getMessage(),
			'Exception has empty message.' );

		$this->assertEquals( \Cassava\Exception\GeneralException::ERROR_INTERNAL_ERROR, $exception->getCASCode(),
			'Exception has default INTERNAL_ERROR code.' );

		$message = 'Test message.';
		$code    = 'TEST_CODE';

		$exception = new \Cassava\Exception\GeneralException( $message, $code );

		$this->assertEquals( $message, $exception->getMessage(),
			"Exception has '$message' message." );

		$this->assertEquals( $code, $exception->getCASCode(),
			"Exception has '$code' code." );

	}

	/**
	 * @covers ::fromError
	 */
	function test_fromError () {

		$error = new WP_Error();

		$exception = \Cassava\Exception\GeneralException::fromError( $error );

		$this->assertEquals( $error->get_error_message(), $exception->getMessage(),
			"Exception generated from WP_Error has the same default message." );

		$this->assertEquals( $error->get_error_code(), $exception->getCASCode(),
			"Exception generated from WP_Error has the same default code." );

		$message = 'Test message.';
		$code    = 'TEST_CODE';

		$error = new WP_Error( $code, $message);

		$exception = \Cassava\Exception\GeneralException::fromError( $error );

		$this->assertEquals( $error->get_error_message(), $exception->getMessage(),
			"Exception generated from WP_Error has the same '$message' message." );

		$this->assertEquals( $error->get_error_code(), $exception->getCASCode(),
			"Exception generated from WP_Error has the same '$code' code." );

	}

	/**
	 * @covers ::getErrorInstance
	 */
	function test_getErrorInstance () {
		$exception = new \Cassava\Exception\GeneralException();

		$error = $exception->getErrorInstance();

		$this->assertInstanceOf( 'WP_Error', $error,
			'Error is an instance of WP_Error.' );

		$this->assertEquals( $exception->getMessage(), $error->get_error_message(),
			'Error has the same default message as exception.' );

		$this->assertEquals( $exception->getCASCode(), $error->get_error_code(),
			'Exception has default INTERNAL_ERROR code.' );

		$message = 'Test message.';
		$code    = 'TEST_CODE';

		$exception = new \Cassava\Exception\GeneralException( $message, $code );

		$error = $exception->getErrorInstance();

		$this->assertEquals( $exception->getMessage(), $error->get_error_message(),
			"Error generated from exception has the same '$message' message." );

		$this->assertEquals( $exception->getCASCode(), $error->get_error_code(),
			"Error generated from exception has the same '$code' code." );
	}

}
