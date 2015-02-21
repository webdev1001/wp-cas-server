<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'WPCASResponse' ) ) {

	class WPCASResponse {

		/**
		 * CAS XML Namespace URI
		 */
		const CAS_NS = 'http://www.yale.edu/tp/cas';

		/**
		 * XML response document.
		 * @var DOMDocument
		 */
		protected $document;

		/**
		 * XML response node.
		 * @var DOMNode
		 */
		protected $response;

		/**
		 * Response constructor.
		 */
		public function __construct() {
			$this->document = new DOMDocument( '1.0', get_bloginfo( 'charset' ) );
		}

		/**
		 * Response mutator.
		 * @param DOMNode $response Response DOM node.
		 */
		public function setResponse( DOMNode $response ) {
			$this->response = $response;
		}

		/**
		 * Create response element.
		 * @param  string  $element Unqualified element tag name.
		 * @param  string  $value   Optional element value.
		 * @return DOMNode          XML element.
		 */
		public function createElement( $element, $value = null ) {
			return $this->document->createElementNS( static::CAS_NS, "cas:$element", $value );
		}

		/**
		 * Wrap a CAS 2.0 XML response and output it as a string.
		 *
		 * This method attempts to set a `Content-Type: text/xml` HTTP response header.
		 *
		 * @param  DOMNode $response XML response contents for a CAS 2.0 request.
		 *
		 * @return string            CAS 2.0 server response as an XML string.
		 *
		 * @uses get_bloginfo()
		 */
		public function prepare() {
			$root = $this->createElement( 'serviceResponse' );
			$root->appendChild( $this->response );

			// Removing all child nodes from response document:

			while ($this->document->firstChild) {
				$this->document->removeChild( $this->document->firstChild );
			}

			$this->document->appendChild( $root );

			return $this->document->saveXML();
		}

		/**
		 * Set error response.
		 *
		 * @param WP_Error $error Response error.
		 * @param string   $tag   Response XML tag (defaults to `authenticationFailure`).
		 */
		public function setError( WP_Error $error, $tag = 'authenticationFailure' ) {
			/**
			 * Fires if the CAS server has to return an XML error.
			 *
			 * @param WP_Error $error WordPress error to return as XML.
			 */
			do_action( 'cas_server_error', $error );

			$message   = __( 'Unknown error', 'wp-cas-server' );
			$code      = WPCASException::ERROR_INTERNAL_ERROR;

			if ( isset( $error ) ) {
				$code        = $error->get_error_code();
				$message     = $error->get_error_message( $code );
			}

			$response = $this->createElement( $tag, $message );

			$response->setAttribute( 'code', $code );

			$this->setResponse( $response );
		}

	}

}