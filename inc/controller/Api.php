<?php defined('TOKICMS') or die('Hacking attempt...');

// No cache headers
header("Expires: Sat, 1 Jan 2000 01:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

class Api extends Controller {
	
	private $action;
	private $key;
	private $id;
	private $apiId;
	private $inputs;
	private $method;
	private $allowed;
	private $limit;
	
    public function process() 
	{		
		$this->setVariable( 'Lang', $this->lang );

		if ( !Settings::IsTrue( 'enable_api' ) )
		{
			Router::SetNotFound();

			$this->view();
			return;
		}
		
		$res = array();
		
		//Load the request method and its inputs
		$this->GetMethodInputs();

		//Check for the key
		if ( !isset( $this->inputs['key'] ) || empty( $this->inputs['key'] ) )
		{
			Log::Set( 'Bad API Request', 'Missing method inputs.', $this->inputs, 'system' );
			$this->Response( 401, 'Unauthorized', array( 'message' => 'Missing authentication key.' ) );
		}

		$token 			= Sanitize( $this->inputs['key'], false );
		$this->key 		= Sanitize( Router::GetVariable( 'key' ), false );
		$this->action 	= Sanitize( Router::GetVariable( 'slug' ), false );
		$this->id 		= ( ( !empty( $this->key ) && is_numeric( $this->key ) ) ? (int) $this->key : null );
		
		//Incude the arrays file
		require ( ARRAYS_ROOT . 'generic-arrays.php');
		
		//Check if this request exists
		if ( !isset( $apiPermissions[$this->action] ) )
		{
			Log::Set( 'Api Request Not Found', 'Invalid request: ' . $this->action, $this->inputs, 'system' );
			$this->Response( 404, 'Not Found', array( 'message' => 'Request doesn\'t exists.' ) );
		}
		
		$method = $apiPermissions[$this->action]['method'];
		
		if ( $method !== $this->method )
		{
			Log::Set( 'Bad API Request', 'Invalid request method: ' . $this->method, $this->inputs, 'system' );
			$this->Response( 400, 'Bad Request', array( 'message' => 'Wrong request method. Method required: ' . $method ) );
		}		

		$Api = $this->GetApi( $token );
		
		//Check if data exists
		if ( !$Api )
		{
			Log::Set( 'Unauthorized Api Request', 'Invalid token: ' . $token, $this->inputs, 'system' );
			$this->Response( 401, 'Unauthorized', array( 'message' => 'Invalid token.' ) );
		}
		
		//Check if has access to this request
		if ( !$this->IsAllowed() )
		{
			Log::Set( 'Permission Error', 'Invalid request: ' . $this->action, $this->inputs, 'system' );
			$this->Response( 401, 'Unauthorized', array( 'message' => 'Invalid request.' ) );
		}
		
		//The beginning of the day timestamp
		$start = strtotime("today 00:00");
		
		$end = strtotime("today 24:00");
		
		//Check if the last request was yesterday and reset the counter
		if ( $Api['last_time_viewed'] < $start )
		{
			$this->db->update( 'api_obj' )->where( 'id', $this->apiId )->set( "total_day_views", 0 );
		}
		
		//Let's check if there is any limit for this token
		elseif ( ( $Api['api_limit'] > 0 ) && ( $Api['total_day_views'] >= $Api['api_limit'] ) )
		{
			header('Retry-After: ' . gmdate('D, d M Y H:i:s', strtotime( 'today 24:00') ) );
			
			Log::Set( 'Too many requests', 'Too many requests for: ' . $token, $this->inputs, 'system' );
			$this->Response( 429, 'Too many requests', array( 'message' => 'Too many requests. Try again later.' ) );
		}
		
		//Default args
		$args = array(
			'postId' => $this->id
		);

		if ( !empty( $this->inputs['args'] ) ) 
		{
			$args = array_merge( $args, $this->inputs['args'] );
		}

		//Override some values to avoid cheating...
		$args['siteId'] 	= SITE_ID;
		$args['buildObj'] 	= false;
		$args['numItems']	= $Api['items_limit'];
		
		//Force caching
		$args['cacheData'] 	= true;
		$args['fromApi'] 	= true;
		
		//Check a few args
		$args['blogId'] 	= ( isset( $args['blogId'] ) ? (int) $args['blogId'] : 0 );
		$args['langId'] 	= ( isset( $args['langId'] ) ? (int) $args['langId'] : Settings::LangData()['lang']['id'] );
		$args['langCode'] 	= GetLangKey( $args['langId'] );
		$args['postStatus'] = 'published';

		switch ( $this->action )
		{
			case 'add-variations':
			
			//Check for any missing args
			if ( empty( $args['data'] ) )
			{
				$this->Response( 400, 'Bad Request', array( 'message' => 'Missing parameters.' ) );
			}
			
			$data = $Query->AddVariations( $args );
			
			if ( !$data )
			{
				$this->Response( 400, 'Bad Request', array( 'message' => 'The post couldn\'t be added. Please try again later' ) );
			}
			
			$ret = array(
				'status' => 'OK'
			);
	
			//Update the API and set the request info
			$this->UpdateApi();

			$this->Response( 201, 'Created', $ret );
			
			break;
			
			case 'add-post':
				
				$args['postType'] = 'post';
				
				//Check for any missing args
				if ( empty( $args['content'] ) )
				{
					$this->Response( 400, 'Bad Request', array( 'message' => 'Missing parameters.' ) );
				}
				
				if ( empty( $args['title'] ) )
				{
					$args['title'] 		= 'Empty Title';
					$args['postStatus'] = 'draft';
				}
		
				$data = $Query->AddPost( $args );
				
				if ( !$data )
				{
					$this->Response( 400, 'Bad Request', array( 'message' => 'The post couldn\'t be added. Please try again later' ) );
				}
				
				$ret = array(
					'status' => 'OK'
				);
	
				//Update the API and set the request info
				$this->UpdateApi();

				$this->Response( 201, 'Created', $ret );
			break;
			
			//Get a single post or posts
			case 'posts':

				if ( empty( $this->key ) )
				{
					$data = $Query->GetPosts( $args );
				}
				
				elseif ( is_numeric( $this->key ) )
				{
					$data = $Query->GetPost( $args );
				}

				else
				{
					$this->ErrorResp();
				}
				
				if ( empty( $data ) )
				{
					$this->Response( 404, 'Not Found', array( 'message' => 'Request doesn\'t exists.' ) );
				}

				$ret = array(
					'status' 	=> 'OK',
					'data'		=> $data
				);
				
				unset( $data );
				
				//Update the API and set the request info
				$this->UpdateApi();
				
				$this->Response( 200, 'OK', $ret );
			break;

			default:
				$this->ErrorResp();
		}
		
		exit;
	}
	
	//Check if an action is allowed
	private function IsAllowed()
	{
		if ( !empty( $this->allowed ) )
		{
			if ( !is_array( $this->allowed ) && ( $this->allowed === 'all' ) )
				return true;

			elseif ( in_array( $this->action, $this->allowed ) )
				return true;
		}

		return false;
	}
	
	private function ErrorResp()
	{
		$this->Response( 400, 'Bad Request', array( 'message' => 'Bad Request' ) );
	}
	
	//Get the response
	private function GetMethodInputs()
	{
		// METHODS
		// ------------------------------------------------------------
		// GET
		// POST
		// PUT
		// DELETE

		$this->method 	= $_SERVER['REQUEST_METHOD'];

		switch($this->method) {
			case "POST":
				$inputs = $_POST;
				$inputs = Json( $inputs );
				break;
				
			case "GET":
				//
				
			case "DELETE":
				$inputs = $_GET;
				break;
				
			case "PUT":
				$inputs = '';
				break;
				
			default:
				$inputs = json_encode(array());
				break;
		}

		// Try to get raw/json data
		if ( empty( $inputs ) )
		{
			$inputs = file_get_contents( 'php://input' );
			$inputs = Json( $inputs );
		}

		$this->inputs = $inputs;
	}
	
	//Update the Api
	private function UpdateApi()
	{
		$dbarr = array(
			"total_day_views" 	=> "total_day_views + 1",
			"total_num_views" 	=> "total_num_views + 1",
			"last_time_viewed" 	=> time(),
			"ip" 				=> GetRealIp()
		);
		
		//Update db
		$this->db->update( "api_obj" )->where( 'id', $this->apiId )->set( $dbarr );
	}
	
	//Get the Api
	private function GetApi( $key )
	{
		$q = $db->from( null, "
		SELECT id, is_primary, allow_data, api_limit, total_day_views, last_time_viewed, items_limit
		FROM `" . DB_PREFIX . "api_obj`
		WHERE (token = :key) AND (disabled = 0)",
		array( $key => ':key' )
		)->single();
		
		if ( !$q )
			return false;
		
		if ( !empty( $q['allow_data'] ) )
		{
			$this->allowed = ( ( $q['allow_data'] === 'all' ) ? 'all' : Json( $q['allow_data'] ) );
		}
		
		$this->apiId = $q['id'];
		$this->limit = $q['api_limit'];

		return $q;
	}
	
	//Return the response
	private function Response( $code = 200, $message = 'OK', $data = array() )
	{
		header( 'HTTP/1.1 ' . $code . ' ' . $message );
		header( 'Access-Control-Allow-Origin: *' );//TODO
		header( 'Access-Control-Allow-Methods: GET ');
		header( 'Content-Type: application/json' );
		
		echo json_encode( $data, JSON_UNESCAPED_UNICODE );
		
		exit;
	}
}