<?php defined('TOKICMS') or die('Hacking attempt...');

require_once ( CLASSES_ROOT . 'Bot.php' );

class Embed
{
	private $enableAutoEmbedder;
	private $showOriginalLink;
	private $scripts = array();
	private $isAmp;
	private $after;
	private $before;
	private $ampWidth;
	private $ampHeight;
	private $width;
	private $height;
	private $autoPlay = 0;
	private $maxEmbeds;
	private $providers;
	private $currentEmbed = 0;
	private $isComment;
	
	public function __construct( $isAmp = false, $isComment = false )
	{
		$this->isAmp 		= $isAmp;
		$this->isComment 	= $isComment;

		$this->enableAutoEmbedder = Settings::IsTrue( 'enable_media_embedder' );
				
		$this->settings = ( $this->enableAutoEmbedder ? Json( Settings::Get()['embedder_data'] ) : array() );
		
		$this->width = ( ( isset( $this->settings['default_video_player_width'] ) && ( $this->settings['default_video_player_width'] > 0 ) )
							? $this->settings['default_video_player_width'] : '100%' );
		
		$this->height = ( ( isset( $this->settings['default_video_player_height'] ) && ( $this->settings['default_video_player_height'] > 0 ) ) 
							? $this->settings['default_video_player_height'] : 400 );
		
		$this->ampWidth = ( ( isset( $this->settings['default_video_player_width_amp'] ) && ( $this->settings['default_video_player_width_amp'] > 0 ) ) 
								? $this->settings['default_video_player_width_amp'] : 600 );
		
		$this->ampHeight = ( ( isset( $this->settings['default_video_player_height_amp'] ) && ( $this->settings['default_video_player_height_amp'] > 0 ) ) 
								? $this->settings['default_video_player_height_amp'] : 400 );
		
		$this->maxEmbeds = ( isset( $this->settings['maximum_number_of_embeds'] ) ? $this->settings['maximum_number_of_embeds'] : 0 );
		
		$this->showOriginalLink = ( isset( $this->settings['show_original_link'] ) ? IsTrue( $this->settings['show_original_link'] ) : false );

		$this->Providers();
	}
	
	/**
     * Adds any needed embed code to avoid duplicate scripts in the content
	 * @access private
    */
	private function BuildCode()
	{
		$string = '';
		
		if ( !empty( $this->scripts ) )
		{
			$string .= PHP_EOL;
			
			foreach ( $this->scripts as $k => $r )
			{
				$string .= $r . PHP_EOL;
			}
		}
		
		return $string;
	}
	
	/**
     * Creates the providers array based on the settings
	 * @access private
    */
	private function Providers()
	{
		if ( empty( $this->settings['sources'] ) )
			return;
		
		if ( $this->isAmp && !empty( $this->settings['disable_embeding_in_mobile'] ) )
			return;
		
		if ( $this->isComment && !empty( $this->settings['disable_embedding_in_comments'] ) )
			return;

		$this->providers = array(
			
			//Text URLs
			'custom-links' => array( 
				'regex' => '(?<!"|>)(?:<p>)?((http|https)\:\/\/(www\.)?([a-zA-Z0-9\-_.]+))(\/)?(?:<\/p>)?(?<!"|<)',
				'enabled' => ( !empty( $this->settings['enable_auto_embed_text_links'] ) ? true : false ),
				'function' => function ( $matches ) 
				{
					if ( $this->CheckMaxEmbeds() )
						$embed = $matches['0'];
					else
					{
						$url = $matches['1'];
						
						$bot 		= new Bot;
						$bot->url 	= $url;
						
						$bot->process();
						
						$el = $bot->getElementsByTagName();
						
						if ( !empty( $el ) )
						{
							$title = ( isset( $el['og:title'] ) ? $el['og:title'] : ( isset( $el['twitter:title'] ) ? $el['twitter:title'] : '' ) );
							
							$description = ( isset( $el['og:description'] ) ? $el['og:description'] : ( isset( $el['twitter:description'] ) ? $el['twitter:description'] : $el['description'] ) );
							
							$image = ( isset( $el['og:image'] ) ? $el['og:image'] : ( isset( $el['twitter:image'] ) ? $el['twitter:image'] : '' ) );
							
							$siteName = ( isset( $el['og:site_name'] ) ? $el['og:site_name'] : ( isset( $el['twitter:site'] ) ? $el['twitter:site'] : '' ) );
							
							$embed = TextLinkEmbed( $title, $description, $url, '_blank', $image, $siteName );
						}
						
						else
						{
							$embed = $matches['0'];
						}

						$this->currentEmbed++;
					}

					return $embed;
				}
			),

			//Facebook
			'facebook-videos' => array( 
							'regex' => '(?<!"|>)(?:<p>)?https?:\/\/(?:www\.)?facebook\.com\/(([a-zA-Z0-9\-_]+)\/videos\/|watch\/live\/\?v=)([a-zA-Z0-9\-_]+)(?:\/)?(?:<\/p>)?(?<!"|<)',
							'enabled' => ( in_array( 'fb-videos', $this->settings['sources'] ) ? true : false ),
							'function' => function ( $matches ) 
							{
								if ( $this->CheckMaxEmbeds() )
									$embed = $matches['0'];
								else
								{
									$args = array(
										'source' 	=> 'facebook-videos',
										'url'  	 	=>  urlencode( strip_tags( $matches['0'] ) ),
										'orUrl'		=> $matches['0']
									);
									
									$embed = IFrame( $args, $this->isAmp, $this->autoPlay );
									
									$this->currentEmbed++;
								}
								
								return $embed;
							}
			),
			
			//Facebook posts
			'facebook-posts' => array( 
							'regex' => '(?<!"|>)(?:<p>)?https?:\/\/(?:www\.)?facebook\.com\/([a-zA-Z0-9\-_]+)\/posts\/([a-zA-Z0-9\-_]+)(?:\/)?(?:<\/p>)?(?<!"|<)',
							'enabled' => ( in_array( 'fb-posts', $this->settings['sources'] ) ? true : false ),
							'function' => function ( $matches ) 
							{
								if ( $this->CheckMaxEmbeds() )
									$embed = $matches['0'];
								else
								{
									$args = array(
										'source' 	=> 'facebook-posts',
										'url'  	 	=>  urlencode( strip_tags( $matches['0'] ) ),
										'orUrl'		=> $matches['0']
									);
									
									$embed = IFrame( $args, $this->isAmp, $this->autoPlay );

									$this->currentEmbed++;
								}
								
								return $embed;
							}
			),

			//Youtube
			'youtube' => array(
							'regex' => '(?<!"|>)(?:<p>)?https?:\/\/(www\.)?youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)((&|&amp;)(t|start|end)=([0-9]+))?((&|&amp;)end=([0-9]+))?(?:<\/p>)?(?<!"|<)',
							'enabled' => ( in_array( 'youtube', $this->settings['sources'] ) ? true : false ),
							'function' => function ( $matches )
							{
								if ( $this->CheckMaxEmbeds() )
									$embed = $matches['0'];
								else
								{
									$start = ( ( isset( $matches['6'] ) && ( ( $matches['6'] == 't' ) || ( $matches['6'] == 'start' ) ) ) ? '?start=' . $matches['7'] : null );

									$end = ( ( isset( $matches['6'] ) && ( $matches['6'] == 'end' ) ) ? '?end=' . $matches['7'] : ( isset( $matches['9'] ) && is_numeric( $matches['9'] ) ? '&amp;end=' . $matches['9'] : null ) );
									
									$args = array(
										'source' 	=> 'youtube',
										'url'		=> 'https://www.youtube.com/embed/' . $matches['3'] . $start . $end . ( ( ( $start != '' ) || ( $end != '' ) ) ? '&amp;' : '?' ) . 'showinfo=1&amp;autoplay=' . $this->autoPlay . ';&amp;autohide=1&amp;rel=0&amp;wmode=opaque',
										'id' 		=>  $matches['3'],
										'start' 	=>  $start,
										'end' 		=>  $end,
										'orUrl'		=> 'https://www.youtube.com/watch?v=' . $matches['3']
									);
									
									$embed = IFrame( $args, $this->isAmp, $this->autoPlay );

									$this->currentEmbed++;
								}

								return $embed;
							}
			),
			
			//Twitter
			'twitter' => array(
							'regex' => '(?<!"|>)(?:<p>)?https?:\/\/(www\.)?twitter\.com\/([A-Za-z0-9_-]+)\/status\/(\d+)(?:\/)?(?:<\/p>)?(?<!"|<)',
							'enabled' => ( in_array( 'twitter', $this->settings['sources'] ) ? true : false ),
							'function' => function ( $matches )
							{
								if ( $this->CheckMaxEmbeds() )
									$embed = $matches['0'];
								else
								{
									$args = array(
										'source' 	=> 'twitter',
										'id'  	 	=>  $matches['3'],
										'orUrl'		=> $matches['0']
									);
										
									if ( $this->isAmp )
									{
										$embed = IFrame( $args, $this->isAmp, $this->autoPlay );

										$this->currentEmbed++;
									}
									
									else
									{
										$url = 'https://api.twitter.com/1/statuses/oembed.json?id=' . $matches['3'] . '&omit_script=true';
										
										$json = $this->_parse_json( $this->GetContent( $url ) );

										//let the user know that the link is dead
										if ( empty( $json ) || !isset( $json['html'] ) )
											$embed = '<p><del>https://twitter.com/' . $matches['1'] . '/status/' . $matches['3'] . '</del></p>';
										else
										{
											$embed = preg_replace('/<script.*><\/script>/', '', $json['html'] );
											$this->scripts['twitter'] = '<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>' . PHP_EOL;
											$this->currentEmbed++;
										}
									}
								}
								
								return $embed;
							}
			),
			
			//Veoh
			'veoh' => array(
							'regex' => '(?<!"|>)(?:<p>)?https?:\/\/(?:www\.)?veoh\.com\/watch\/([A-Za-z0-9_-]+)(\/)?(?:<\/p>)?(?<!"|<)',
							'enabled' => ( in_array( 'veoh', $this->settings['sources'] ) ? true : false ),
							'function' => function ( $matches )
							{
								$link = strip_tags( $matches['0'] );
								
								$url = $link;
								
								if ( $this->CheckMaxEmbeds() )
									$embed = $url;
								
								else
								{
									$args = array(
										'source' 	=> 'veoh',
										'id'  	 	=> $matches['1'],
										'orUrl'		=> $link
									);
									
									if ( $this->isAmp )
										{
											$embed = IFrame( $args, $this->isAmp, $this->autoPlay );
										}

										else
										{		
											$embed = IFrame( $args, $this->isAmp, $this->autoPlay );
										}
										
										$this->currentEmbed++;
								}
								
								return $embed;
							}
			),
			
			//Tik Tok
			'tik-tok' => array(
							'regex' => '(?<!"|>)(?:<p>)?https?:\/\/(?:www\.)?tiktok\.com\/(\@[A-Za-z0-9_-]+)\/video\/([0-9]+)(\/)?(?:<\/p>)?(?<!"|<)',
							'enabled' => ( in_array( 'tik-tok', $this->settings['sources'] ) ? true : false ),
							'function' => function ( $matches )
							{
								$link = strip_tags( $matches['0'] );
								
								$url = 'https://www.tiktok.com/oembed?url=' . urlencode( $link );
								
								if ( $this->CheckMaxEmbeds() )
									$embed = $url;
								
								else
								{
									$args = array(
										'source' 	=> 'tik-tok',
										'id'  	 	=> $matches['2'],
										'orUrl'		=> $link
									);
									
									$json = $this->_parse_json( $this->GetContent( $url ) );
									
									//let the visitor know that the link is dead
									if ( !$json || isset( $json['status_msg'] ) || !isset( $json['html'] ) )
										$embed = '<p><del>' . $url . '</del></p>';
									else
									{
										$string = $json['html'];
										$sourceImage = $json['thumbnail_url'];
										
										if ( $this->isAmp )
										{
											$embed = IFrame( $args, $this->isAmp, $this->autoPlay );
										}

										else
										{		
											$embed = IFrame( $args, $this->isAmp, $this->autoPlay );
											$this->scripts['tik-tok'] = '<script async src="https://www.tiktok.com/embed.js" charset="UTF-8"></script>' . PHP_EOL;
										}
										
										$this->currentEmbed++;
									}
								}
								
								return $embed;
							}
			),
			
			//Reddit
			'reddit' => array(
							'regex' => '(?<!"|>)(?:<p>)?https?:\/\/(?:www\.)?reddit\.com\/r\/([A-Za-z0-9_-]+)\/comments\/([A-Za-z0-9_-]+)\/([A-Za-z0-9_-]+)(\/)?(?:<\/p>)?(?<!"|<)',
							'enabled' => ( in_array( 'reddit', $this->settings['sources'] ) ? true : false ),
							'function' => function ( $matches )
							{
								$url = 'https://www.reddit.com/r/' . $matches['1'] . '/comments/' . $matches['2'] . '/' . $matches['3'];
								
								if ( $this->CheckMaxEmbeds() )
									$embed = $url;
								
								else
								{
									$json = $this->_parse_json( $this->GetContent( $url . '.json' ) );
									
									//let the visitor know that the link is dead
									if ( !$json )
										$embed = '<p><del>' . $url . '</del></p>';
									else
									{
										$string = $json['0']['data']['children']['0']['data'];
										$images = $string['preview']['images']['0'];
										$sourceImage = $images['source']['url'];
										
										$args = array(
											'source' 	=> 'reddit',
											'img' 		=> StripContent( $sourceImage ),
											'time' 		=> time(),
											'url'  	 	=> urlencode( $url ),
											'orUrl'  	=> 'https://www.reddit.com/r/' . $matches['1'] . '/'
										);
										
										if ( $this->isAmp )
										{
											$embed = IFrame( $args, $this->isAmp, $this->autoPlay );
										}
										
										else
										{		
											$embed = '<blockquote class="reddit-card" data-card-created="' . time() . '"><a href="' . $url . '">' . $string['title'] . '</a> from <a href="http://www.reddit.com/' . $string['subreddit_name_prefixed'] . '">' . $string['subreddit_name_prefixed'] . '</a></blockquote>';
											
											$this->scripts['reddit'] = '<script async src="//embed.redditmedia.com/widgets/platform.js" charset="UTF-8"></script>' . PHP_EOL;
										}
										
										$this->currentEmbed++;
									}
								}
								
								return $embed;
							}
			),
			
			//Instagram
			'instagram' => array(
							'regex' => '(?<!"|>)(?:<p>)?https?:\/\/(www\.)?instagram\.com\/p\/([A-Za-z0-9_-]+)(?:\/)?(?:<\/p>)?(?<!"|<)',
							'enabled' => ( in_array( 'instagram', $this->settings['sources'] ) ? true : false ),
							'function' => function ( $matches )
							{
								if ( $this->CheckMaxEmbeds() )
									$embed = $matches['0'];
								
								else
								{
									$url = 'https://api.instagram.com/oembed/?url=' . urlencode( 'https://www.instagram.com/p/' . $matches['2'] . '/' );

									$json = $this->_parse_json( $this->GetContent( $url  ) );
									
									//let the user know that the link is dead
									if ( empty( $json ) )
										$embed = '<p>' . sprintf( __( 'embed-not-available-error' ), 'https://www.instagram.com/p/' . $matches['2'] . '/', __( 'instagram' ) ) . '</p>';

									else
									{
										$args = array(
											'source' 	=> 'instagram',
											'id'  	 	=> $matches['2'],
											'orUrl'  	=> 'https://www.instagram.com/p/' . $matches['2'] . '/'
										);
										
										if ( $this->isAmp )
										{
											$embed = IFrame( $args, $this->isAmp, $this->autoPlay );
										}
											
										else
										{
											//Remove instagram's script to avoid multiple script calls. We will add a single one at the end of the content
											$embed = preg_replace('/<script.*><\/script>/', '', $json['html'] );
											$this->scripts['instagram'] = '<script async src="//www.instagram.com/embed.js" charset="UTF-8"></script>' . PHP_EOL;
										}
										
										$this->currentEmbed++;
									}
								}
								
								return $embed;
							}
			),
			
			//Vimeo
			'vimeo' => array(
							'regex' => '(?<!"|>)(?:<p>)?https?:\/\/(?:www\.)?(?:player\.)?vimeo\.com(?:\/video)?\/(\d+)(?:<\/p>)?(?<!"|<)',
							'enabled' => ( in_array( 'vimeo', $this->settings['sources'] ) ? true : false ),
							'function' => function ( $matches )
							{
								if ( $this->CheckMaxEmbeds() )
									$embed = $matches['0'];
								
								else
								{
									$args = array(
										'source' 	=> 'vimeo',
										'id'  	 	=> $matches['1'],
										'orUrl'  	=> $matches['0']
									);
									
									if ( $this->isAmp )
									{
										$embed = IFrame( $args, $this->isAmp, $this->autoPlay );
									}

									else
									{
										$embed = IFrame( $args, $this->isAmp, $this->autoPlay );
										
										$this->scripts['vimeo'] = '<script src="https://player.vimeo.com/api/player.js"></script>' . PHP_EOL;
									}
										
									$this->currentEmbed++;
								}
								
								return $embed;
							}
			), //Vimeo
			
			//Dailymotion
			'dailymotion' => array(
							'regex' => '(?<!"|>)(?:<p>)?https?:\/\/(?:www\.)?dailymotion\.com\/video\/([a-zA-Z0-9\-_]+)(?:\/)?(?:<\/p>)?(?<!"|<)',
							'enabled' => ( in_array( 'dailymotion', $this->settings['sources'] ) ? true : false ),
							'function' => function ( $matches )
							{
								if ( $this->CheckMaxEmbeds() )
									$embed = $matches['0'];
								
								else
								{
									$args = array(
										'source' 	=> 'dailymotion',
										'id'  	 	=> $matches['1'],
										'orUrl'  	=> $matches['0']
									);
									
									$embed = IFrame( $args, $this->isAmp, $this->autoPlay );

									$this->currentEmbed++;
								}
								
								return $embed;
							}
			), //Dailymotion
			
			//Youku
			'youku' => array(
							'regex' => '(?<!"|>)(?:<p>)?(?:https?:\/\/)?v\.youku\.com\/v_show\/id_([^_&]+).html(?:<\/p>)?(?<!"|<)',
							'enabled' => ( in_array( 'youku', $this->settings['sources'] ) ? true : false ),
							'function' => function ( $matches )
							{
								if ( $this->CheckMaxEmbeds() )
									$embed = $matches['0'];
								
								else
								{
									$args = array(
										'source' 	=> 'youku',
										'id'  	 	=> $matches['1'],
										'orUrl'  	=> $matches['0']
									);
									
									$embed = IFrame( $args, $this->isAmp, $this->autoPlay );

									$this->currentEmbed++;
								}
								
								return $embed;
							}
			), //Youku
			
			//Nytimes videos
			'nytimes-videos' => array(
							'regex' => '(?<!"|>)(?:<p>)?https?:\/\/(?:www\.)?nytimes\.com\/video\/([a-zA-Z0-9\-_]+)\/([0-9]+)\/.*\.html(?:<\/p>)?(?<!"|<)',
							'enabled' => ( in_array( 'nytimes', $this->settings['sources'] ) ? true : false ),
							'function' => function ( $matches )
							{
								if ( $this->CheckMaxEmbeds() )
									$embed = $matches['0'];
								
								else
								{
									$args = array(
										'source' 	=> 'nytimes-videos',
										'id'  	 	=> $matches['2'],
										'orUrl'  	=> $matches['0']
									);
									
									$embed = IFrame( $args, $this->isAmp, $this->autoPlay );
									
									$this->currentEmbed++;
								}
								
								return $embed;
							}
			), //Nytimes videos
			
			//Nytimes posts
			'nytimes-posts' => array(
							'regex' => '(?<!"|>)(?:<p>)?https?:\/\/(?:www\.)?nytimes\.com\/(live\/)?([0-9]+)\/.*\.html(?:<\/p>)?(?<!"|<)',
							'enabled' => ( in_array( 'nytimes', $this->settings['sources'] ) ? true : false ),
							'function' => function ( $matches )
							{
								if ( $this->CheckMaxEmbeds() )
									$embed = $matches['0'];
								
								else
								{
									$args = array(
										'source' 	=> 'nytimes-posts',
										'url='  	=> urlencode( strip_tags( $matches['0'] ) ),
										'orUrl'  	=> $matches['0']
									);
									
									$embed = IFrame( $args, $this->isAmp, $this->autoPlay );

									$this->currentEmbed++;
								}
								
								return $embed;
							}
			), //Nytimes posts

		);// Array End
	}
	
	/**
     * Passes on any unlinked URLs that are on their own line for potential embedding.
     * @param string $content The content to be searched.
     * @return string Potentially modified $content.
    */
    function parse( $content ) 
	{
		if ( $this->enableAutoEmbedder && !empty( $this->settings['sources'] ) )
		{
			foreach ( $this->providers as $key => $data )
			{
				if ( !$data['enabled'] )
					continue;
				
				if ( $this->CheckMaxEmbeds() )
					break;

				$content = preg_replace_callback
				(
					'#' . $data['regex'] . '#i',
					$data['function'],
					$content
				);
			}
		}
		
		//The functions below must be available even if we don't want any embeds
		$content .= $this->BuildCode();
		
		//Remove any paragraph tag
		//$content = str_replace( array( '<p>', '</p>' ), '', $content );

		return $content;
    }
	
	/**
     * Checks numbers of embeds
     * @access private
    */
	private function CheckMaxEmbeds()
	{
		return ( ( $this->maxEmbeds > 0 && ( $this->currentEmbed >= $this->maxEmbeds ) ) ? true : false );
	}

    /**
     * Parses a json response body.
     * @access private
    */
    private function _parse_json( $response_body ) 
	{
		if ($response_body === false) 
		{
			return false;
		}
		
		return json_decode( $response_body, true );
    }

    /**
     * Grabs the response from a remote URL.
     *
     * @param string $url The remote URL.
     * @return bool|string False on error, otherwise the response body.
    */
    private function GetContent( $url )
	{
        $handle = curl_init();
        curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 5 );
        curl_setopt( $handle, CURLOPT_TIMEOUT, 5 );
        curl_setopt( $handle, CURLOPT_URL, $url);
        curl_setopt( $handle, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $handle, CURLOPT_SSL_VERIFYHOST, false );
        curl_setopt( $handle, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $handle, CURLOPT_FOLLOWLOCATION, false );
        curl_setopt( $handle, CURLOPT_HEADER, false );
        curl_setopt( $handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
        $response = curl_exec( $handle );
        curl_close( $handle );
        return $response;
    }
}