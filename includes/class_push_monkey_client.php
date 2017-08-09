<?php

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'class_push_monkey_debugger.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class_push_monkey_cache.php' );

/**
 * API Client
 */
class PushMonkeyClient {

	public $endpointURL;
	public $registerURL;

	/* Public */

	const PLAN_NAME_KEY = 'push_monkey_plan_name_output';

	/**
 	* Calls the sign in endpoint with either an Account Key
 	* or with an API Token + API Secret combo.
	*
	* Returns false on WP errors.
	* Returns an object with the returned JSON.
	* @param string $account_key
	* @param string $api_token
	* @param string $api_secret
	* @return mixed; false if not signed in. 
	*/
	public function sign_in( $account_key, $api_token, $api_secret ) {

		$sign_in_url = $this->endpointURL . '/v2/api/sign_in';
		$args = array( 'body' => array( 
			
			'account_key' => $account_key, 
			'api_token' => $api_token, 
			'api_secret' => $api_secret,
			'website_url' => site_url()
			) );
		$response = wp_remote_post( $sign_in_url, $args );
		if ( is_wp_error( $response ) ) {
			
			return ( object ) array( 'error' => $response->get_error_message() );
		} else {

			$body = wp_remote_retrieve_body( $response );
			$output = json_decode( $body );
			$this->d->debug(print_r($output, true));
			return $output;				
		}
		return false;
	}
	
	/**
	 * Get the stats for an Account Key.
	 * @param string $account_key 
	 * @return mixed; false if nothing found; array otherwise.
	 */
	public function get_stats( $account_key ) {

		$stats_api_url = $this->endpointURL . '/stats/api';
		$args = array( 'body' => array( 'account_key' => $account_key ) );
		$response = wp_remote_post( $stats_api_url, $args );
		if( is_wp_error( $response ) ) {

			$this->d->debug( $response->get_error_message() );
			return ( object ) array( 'error' => $response->get_error_message() );
		} else {

			$body = wp_remote_retrieve_body( $response );
			$output = json_decode( $body ); 
			return $output;
		}
		return false;
	}

	/**
	 * Get the Website Push ID for an Account Key.
	 * @param string $account_key 
	 * @return string; array with error info if an error occured.
	 */
	public function get_website_push_ID( $account_key ) {

		$url = $this->endpointURL . '/v2/api/website_push_id';
		$args = array( 'body' => array( 'account_key' => $account_key ) );

		$response = wp_remote_post( $url, $args );

		if( is_wp_error( $response ) ) {

			return ( object ) array( 'error' => $response->get_error_message() );
		} 
		$body = wp_remote_retrieve_body( $response );
		$output = json_decode( $body ); 
		return $output;
	}

	/**
	 * Sends a desktop push notification.
	 * @param string $account_key 
	 * @param string $title 
	 * @param string $body 
	 * @param string $url_args 
	 * @param boolean $custom 
	 */
	public function send_push_notification( $account_key, $title, $body, $url_args, $custom, $segments, $image = NULL ) {

		$url = $this->endpointURL . '/push_message';
		$args = array( 
			'account_key' => $account_key,
			'title' => $title,
			'body' => $body, 
			'url_args' => $url_args,
			'send_to_segments_string' => implode(",", $segments),
			'image' => $image
		);
		$this->d->debug( print_r( $args, true ) );
		if ( $custom ) {

			$args['custom'] = true;
		}
		$response = $this->post_with_file( $url, $args, $image );
		if( is_wp_error( $response ) ) {

			$this->d->debug('send_push_notification '.$response->get_error_message());
		} else {

			$this->d->debug( print_r( $response, true) );
		}
	}

	/**
	 * Get the plan name.
	 * @param string $account_key 
	 * @return string; array with error info otherwise.
	 */
	public function get_plan_name( $account_key ) {

		$output = $this->cache->get( self::PLAN_NAME_KEY );
		if ( $output ) {
			
			$this->d->debug('served from cache');
			return (object) $output;
		}

		$url = $this->endpointURL . '/v2/api/get_plan_name';
		$args = array( 'body' => array( 'account_key' => $account_key ) );

		$response = wp_remote_post( $url, $args );

		if( is_wp_error( $response ) ) {

			return ( object ) array( 'error' => $response->get_error_message() );
		} 
		$body = wp_remote_retrieve_body( $response );
		$output = json_decode( $body ); 
		$serialized_output = json_decode( $body, true );
		if ( isset( $output->error ) ) {
			
			$this->d->debug('get_plan_name: ' . $output->error);
			return $output->error;
		} else {

			$this->d->debug("not from cache");
			$this->cache->store( self::PLAN_NAME_KEY, $serialized_output );
			return $output;
		}
		return '';
	}

	/**
	 * Get all the segments
	 * @param string $account_key
	 * @return associative array of [id=>string]
	 */
	public function get_segments( $account_key ) {

		$segments_api_url = $this->endpointURL . '/push/v1/segments/' . $account_key;
		$response = wp_remote_post( $segments_api_url, array() );
		if( is_wp_error( $response ) ) {

			$this->d->debug( $response->get_error_message() );
			return ( object ) array( 'error' => $response->get_error_message() );
		} else {

			$body = wp_remote_retrieve_body( $response );
			$output = json_decode( $body, true ); 
			if ( isset( $output["segments"] ) ) {

				if ( count( $output["segments"] ) > 0 ) {

					if ( gettype($output["segments"][0]) == "array" ) {

						return $output["segments"];
					}
				}
			}
		}
		return array();		
	}

	/**
	 * Save a segments
	 * @param string $account_key
	 * @param string $name	 
	 * @return response or error
	 */
	public function save_segment( $account_key, $name ) {

		$url = $this->endpointURL . '/push/v1/segments/create/' . $account_key;
		$args = array( 'body' => array( 
			
			'name' => $name
			) );
		$response = wp_remote_post( $url, $args );
		if ( is_wp_error( $response ) ) {
			
			return ( object ) array( 'error' => $response->get_error_message() );
		} else {

			$body = wp_remote_retrieve_body( $response );
			$output = json_decode( $body );
			$this->d->debug(print_r($output, true));
			return $output;				
		}
		return false;
	}

	/**
	 * Delete a segments
	 * @param string $account_key
	 * @param string $id of segment	 
	 * @return response or error
	 */
	public function delete_segment( $account_key, $id ) {

		$url = $this->endpointURL . '/push/v1/segments/delete/' . $account_key;
		$args = array( 'body' => array( 
			
			'id' => $id
			) );
		$this->d->debug($url);
		$response = wp_remote_post( $url, $args );
		if ( is_wp_error( $response ) ) {
			
			return ( object ) array( 'error' => $response->get_error_message() );
		} else {

			$body = wp_remote_retrieve_body( $response );
			$output = json_decode( $body );
			$this->d->debug(print_r($output, true));
			return $output;				
		}
		return false;		
	}

	/**
	 * Retrieve the status of a welcome message
	 * @param string $account_key
	 * @return associative array of JSON response
	 */
	public function get_welcome_message_status( $account_key ) {

		$url = $this->endpointURL . '/v2/api/welcome_notification_status/' . $account_key;
		$response = wp_remote_post( $url, array() );
		if( is_wp_error( $response ) ) {

			$this->d->debug( $response->get_error_message() );
			return ( object ) array( 'error' => $response->get_error_message() );			
		}
		$body = wp_remote_retrieve_body( $response );
		$output = json_decode( $body, true ); 
		if ( empty( $output ) ) {

			return ( object ) array( 'error' => 'empty' );			
		}
		return $output;
	}

	/**
	 * Update the welcome message info
	 * @param string $account_key
	 * @param boolean $enabled
	 * @param string $message
	 * @return boolean. True if operation finished successfully.
	 */
	public function update_welcome_message( $account_key, $enabled, $message ) {

		$url = $this->endpointURL . '/v2/api/update_welcome_notification/' . $account_key;
		$args = array( 'body' => array( 
			
			'message' => $message
		) );		
		if ( $enabled ) {

			$args['body']['enabled'] = true;
		}
		$response = wp_remote_post( $url, $args );
		if( is_wp_error( $response ) ) {

			$this->d->debug( $response->get_error_message() );
			return ( object ) array( 'error' => $response->get_error_message() );			
		}
		$body = wp_remote_retrieve_body( $response );
		$output = json_decode( $body, true ); 
		if ( isset( $output["status"] ) ) {

			if ( $output['status'] == "ok" ) {

				return true;
			}
		}
		return false;
	}

	/* Private */

	function __construct( $endpoint_url ) {

		$this->endpointURL = $endpoint_url;
		$this->registerURL = $endpoint_url.'/v2/register';
		$this->d = new PushMonkeyDebugger();
		$this->cache = new PushMonkeyCache();
	}

	function post_with_file( $url, $data, $file_path ) {

		$boundary = wp_generate_password( 24 );
		$headers  = array(

			'content-type' => 'multipart/form-data; boundary=' . $boundary
		);
		$payload = '';
		// First, add the standard POST fields:
		foreach ( $data as $name => $value ) {

			$payload .= '--' . $boundary;
			$payload .= "\r\n";
			$payload .= 'Content-Disposition: form-data; name="' . $name . '"' . "\r\n\r\n";
			$payload .= $value;
			$payload .= "\r\n";
		}
		// Upload the file
		if ( $file_path ) {

			$payload .= '--' . $boundary;
			$payload .= "\r\n";
			$payload .= 'Content-Disposition: form-data; name="' . 'image' . '"; filename="' . basename( $file_path ) . '"' . "\r\n";
			//        $payload .= 'Content-Type: image/jpeg' . "\r\n";
			$payload .= "\r\n";
			$payload .= file_get_contents( $file_path );
			$payload .= "\r\n";
		}
		$payload .= '--' . $boundary . '--';
		$response = wp_remote_post( $url,
			array(
				'headers'    => $headers,
				'body'       => $payload,
			)
		);
		return $response;
	}
}
