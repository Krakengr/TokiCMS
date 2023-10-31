<?php defined('TOKICMS') or die('Hacking attempt...');

//
// Comments Functions
//

#####################################################
#
# Import Comments function
#
#####################################################
function ExportComments()
{
	global $Admin;
	
	$disqusName = $Admin->CurrentLang()['settings']['disqus_shortname'];
	
	if ( empty( $disqusName ) )
	{
		return null;
	}
	
	$xml = new DOMDocument( '1.0', get_bloginfo( 'charset' ) );

        $rss = $xml->createElement( 'rss' );
        $rss->setAttribute( 'version', '2.0' );
        $rss->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:excerpt',
            'http://wordpress.org/export/1.0/excerpt/'
        );
        $rss->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:content',
            'http://purl.org/rss/1.0/modules/content/'
        );
        $rss->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:dsq',
            'https://disqus.com/'
        );
        $rss->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:wfw',
            'http://wellformedweb.org/CommentAPI/'
        );
        $rss->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:dc',
            'http://purl.org/dc/elements/1.1/'
        );
        $rss->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:wp',
            'http://wordpress.org/export/1.0/'
        );

        $channel = $xml->createElement( 'channel' );
        $channel->appendChild( $xml->createElement( 'title', get_bloginfo_rss( 'name' ) ) );
        $channel->appendChild( $xml->createElement( 'link', get_bloginfo_rss( 'url' ) ) );
        $channel->appendChild(
            $xml->createElement(
                'pubDate',
                mysql2date( 'D, d M Y H:i:s +0000', get_lastpostmodified( 'GMT' ), false )
            )
        );
        $channel->appendChild(
            $xml->createElement(
                'generator',
                'WordPress ' . get_bloginfo_rss( 'version' ) . '; Disqus ' . $this->version
            )
        );

        // Generate the item (the post).
        $item = $xml->createElement( 'item' );
        $item->appendChild(
            $xml->createElement( 'title', apply_filters( 'the_title_rss', $post->post_title ) )
        );
        $item->appendChild(
            $xml->createElement(
                'link',
                esc_url( apply_filters( 'the_permalink_rss', get_permalink( $post->ID ) ) )
            )
        );
        $item->appendChild(
            $xml->createElement(
                'pubDate',
                mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true, $post ), false )
            )
        );

        $author_name_cdata = $xml->createCDATASection( $post_author->display_name );
        $author_name_element = $xml->createElement( 'dc:creator' );
        $author_name_element->appendChild( $author_name_cdata );
        $item->appendChild( $author_name_element );

        $guid = $xml->createElement( 'guid', $post->guid );
        $guid->setAttribute( 'isPermalink', 'false' );
        $item->appendChild( $guid );

        $post_content_cdata = $xml->createCDATASection( apply_filters( 'the_content_export', $post->post_content ) );
        $post_content_element = $xml->createElement( 'content:encoded' );
        $post_content_element->appendChild( $post_content_cdata );
        $item->appendChild( $post_content_element );

        $identifier_cdata = $xml->createCDATASection( $post->ID . ' ' . $post->guid );
        $identifier_element = $xml->createElement( 'dsq:thread_identifier' );
        $identifier_element->appendChild( $identifier_cdata );
        $item->appendChild( $identifier_element );

        $item->appendChild(
            $xml->createElement(
                'wp:post_id',
                $post->ID
            )
        );

        $item->appendChild(
            $xml->createElement(
                'wp:post_date_gmt',
                $post->post_date_gmt
            )
        );

        $item->appendChild(
            $xml->createElement(
                'wp:comment_status',
                $post->comment_status
            )
        );

        foreach ( $comments as $c ) {

            $wpcomment = $xml->createElement( 'wp:comment' );

            $wpcomment->appendChild(
                $xml->createElement(
                    'wp:comment_id',
                    $c->comment_ID
                )
            );

            $comment_author_name_cdata = $xml->createCDATASection( $c->comment_author );
            $comment_author_name_element = $xml->createElement( 'wp:comment_author' );
            $comment_author_name_element->appendChild( $comment_author_name_cdata );
            $wpcomment->appendChild( $comment_author_name_element );

            $wpcomment->appendChild(
                $xml->createElement(
                    'wp:comment_author_email',
                    $c->comment_author_email
                )
            );

            $wpcomment->appendChild(
                $xml->createElement(
                    'wp:comment_author_url',
                    $c->comment_author_url
                )
            );

            $wpcomment->appendChild(
                $xml->createElement(
                    'wp:comment_author_IP',
                    $c->comment_author_IP
                )
            );

            $wpcomment->appendChild(
                $xml->createElement(
                    'wp:comment_date',
                    $c->comment_date
                )
            );

            $wpcomment->appendChild(
                $xml->createElement(
                    'wp:comment_date_gmt',
                    $c->comment_date_gmt
                )
            );

            $comment_content_cdata = $xml->createCDATASection( $c->comment_content );
            $comment_content_element = $xml->createElement( 'wp:comment_content' );
            $comment_content_element->appendChild( $comment_content_cdata );
            $wpcomment->appendChild( $comment_content_element );

            $wpcomment->appendChild(
                $xml->createElement(
                    'wp:comment_approved',
                    $c->comment_approved
                )
            );

            $wpcomment->appendChild(
                $xml->createElement(
                    'wp:comment_type',
                    $c->comment_type
                )
            );

            $wpcomment->appendChild(
                $xml->createElement(
                    'wp:comment_parent',
                    $c->comment_parent
                )
            );

            $item->appendChild( $wpcomment );
        }

        // Append the post item to the channel.
        $channel->appendChild( $item );

        // Append the root channel to the RSS element.
        $rss->appendChild( $channel );

        // Finally append the root RSS element to the XML document.
        $xml->appendChild( $rss );

        $wxr = $xml->saveXML();
}

#####################################################
#
# Sync Comments function
#
#####################################################
function SyncComments()
{
	global $Admin;
	
	$disqusName = $Admin->CurrentLang()['settings']['disqus_shortname'];
	
	$settings 	= $Admin->Settings()::Get()['api_keys'];
	
	$keys 		= Json( $settings );
	
	if ( empty( $disqusName ) || empty( $keys['disqus']['public_key'] ) || empty( $keys['disqus']['secret_key'] ) )
	{
		return null;
	}
	
	$public = $keys['disqus']['public_key'];
	$secret = $keys['disqus']['secret_key'];
	
	require_once( TOOLS_ROOT . 'disqusapi' . DS . 'disqusapi.php' );
	
	$disqus 	= new DisqusAPI( $secret );
	$threads 	= $disqus->forums->listThreads( array( 'forum' => $disqusName ) );
	
	if ( empty( $threads ) )
	{
		return null;
	}
	
	$tmp = $comments = array();
	
	foreach( $threads as $t )
	{
		if ( empty( $t->posts ) )
		{
			continue;
		}
		
		$tmp[$t->id] = array(
			'threadId' 	=> $t->id,
			'postId' 	=> $t->identifiers['0']
		);
	}
	
	if ( empty( $tmp ) )
	{
		return null;
	}
	
	$list = $disqus->forums->listPosts( array( 'forum' => 'akismpo' ) );
	
	if ( empty( $list ) )
	{
		return null;
	}
		
	foreach( $list as $t )
	{	
		$threadId = $t->thread;
		
		if ( !isset( $tmp[$threadId] ) )
		{
			continue;
		}
		
		$comments[] = array(
			'postId' 	=> $tmp[$threadId]['postId'],
			'id'		=> $t->id,
			'isEdited'	=> $t->isEdited,
			'date'		=> strtotime( $t->createdAt ),
			'author'	=> $t->author->name,
			'url'		=> $t->author->url,
			'avatar'	=> $t->author->avatar->large->permalink,
			'post'		=> $t->raw_message,
			'status'	=> ( $t->isApproved ? 'approved' : ( $t->isSpam ? 'spam' : ( $t->isDeleted ? 'deleted' : 'pending' ) ) )
		);
	}
	
	if ( empty( $comments ) )
	{
		return null;
	}
	
	foreach ( $comments as $c )
	{
		//fisrt check if this post is valid
		$tmp = $Admin->db->from( null, "
		SELECT id_post, id_lang, id_blog, id_site
		FROM `" . DB_PREFIX . POSTS . "`
		WHERE (id_post = " . (int) $c['postId'] . ")"
		)->single();
		
		if ( !$tmp )
		{
			continue;
		}
		
		$tc = $Admin->db->from( null, "
		SELECT id
		FROM `" . DB_PREFIX . "comments`
		WHERE (ext_id = " . (int) $c['id'] . ")"
		)->single();
		
		if ( !$tc )
		{
			$dbarr = array(
				"id_post" 		=> $tmp['id_post'],
				"id_lang" 		=> $tmp['id_lang'],
				"id_site" 		=> $tmp['id_site'],
				"id_blog" 		=> $tmp['id_blog'],
				"name" 			=> $c['author'],
				"url" 			=> $c['url'],
				"comment" 		=> $c['post'],
				"status" 		=> $c['status'],
				"added_time" 	=> $c['date'],
				"ext_id" 		=> $c['id']
			);
            
			$put = $Admin->db->insert( 'comments' )->set( $dbarr );
			
			if ( $put && ( $c['post'] == 'approved' ) )
			{
				$Admin->db->update( POSTS )->where( 'id_post', $tmp['id_post'] )->set( "num_comments", array( "num_comments", "1", "+" ) );
				
				if ( $tmp['id_blog'] > 0 )
				{
					$Admin->db->update( "blogs" )->where( 'id_blog', $tmp['id_blog'] )->set( "num_posts", array( "num_posts", "1", "+" ) );
				}
			}
		}
	}
	
	return true;
}
#####################################################
#
# Comment List function
#
#####################################################
function Comments( $param = null, $remove_zero = false, $echo = true )
{
	global $Post;

	if ( empty( $Post ) || !$Post->HasCommentsEnabled() )
		return;
	
	$UserGroup 		= UserGroup();
	$UserId 		= UserId();
	$extSys 		= CurrentLang()['data']['ext_comm_system'];
	
	//Default Args
	$args = array(
		'container'      		 	=> 'div',
		'container_class'		 	=> 'comments-area',
		'container_id'    		 	=> 'comments',
		'title_class'      			=> 'comments-title',
		'title_wrap'        		=> '',
		'title_id'        		 	=> '',
		'title_html_tag'        	=> 'h2',
		'title_single'        		=> __( 'one' ) . ' ' . __( 'comment' ) . ' ' . __( 'on' ) . ' &ldquo;<span>%1$s</span>&rdquo;',
		'title_zero'        		=> '0 ' . __( 'comments' ) . ' ' . __( 'on' ) . ' &ldquo;<span>%1$s</span>&rdquo;',
		'remove_zero_title'			=> $remove_zero,
		'title_plural'        		=> '%1$d ' . __( 'comments' ) . ' ' . __( 'on' ) . ' &ldquo;<span>%2$s</span>&rdquo;',
		'list_wrap'      		 	=> '<ol class="%1$s">%2$s</ol>',
		'list_class'      		 	=> 'comment-list',
		'item_wrap'      		 	=> '<li id="%1$s" class="%2$s">%3$s</li>',
		'item_class'   	  	 	 	=> 'comment %s thread-%s depth-1',
		'respond_wrap'      	 	=> '<div id="%s" class="%s">%s</div>',
		'before_respond'      	 	=> '',
		'after_respond'      	 	=> '',
		'respond_class'      		=> 'comment-respond',
		'respond_id'      		 	=> 'respond',
		'respond_title_wrap'      	=> '<h3 id="%s" class="%s">%s</h3>',
		'respond_title_class'      	=> 'comment-reply-title',
		'respond_title_id'      	=> 'reply-title',
		'item_content_wrap'      	=> '<article id="%s" class="%s">%s</article>',
		'item_content_class'      	=> 'comment-body',
		'comment_form_class'		=> 'comment-form',
		'comment_form_id'			=> 'commentform',
		'guest_notify_message'		=> '<p class="comment-notes"><span id="email-notes">' . __ ( 'your-email-address-will-not-be-published' ) . '</span> <span class="required-field-message">' . __( 'required-fields-are-marked' ) . ' <span class="required">*</span></span></p>',
		'comment_author_wrap'		=> '<p class="comment-form-author"><label for="author">' . __( 'name' ) . ' <span class="required">*</span></label> %s</p>',
		'comment_author_icon_wrap'	=> '',
		'comment_author_icon_class'	=> '',
		'comment_content_wrap'		=> '',
		'comment_title_wrap'		=> '',
		'comment_date_wrap'			=> '',
		'author_input_class'		=> '',
		'author_input_id'			=> '',
		'comment_email_wrap'		=> '<p class="comment-form-email"><label for="email">' . __( 'email' ) . ' <span class="required">*</span></label> %s</p>',
		'email_input_class'			=> '',
		'email_input_id'			=> '',
		'comment_url_wrap'			=> '<p class="comment-form-url"><label for="url">' . __( 'url' ) . '</label> %s</p>',
		'url_input_class'			=> '',
		'url_input_id'				=> '',
		'comment_message_wrap'		=> '<p class="comment-form-comment"><label for="comment">' . __( 'comment' ) . ' <span class="required">*</span></label> %s</p>',
		'comment_message_class'		=> '',
		'comment_message_id'		=> '',
		'form_button_wrap'			=> '<input name="submit" type="submit" id="submit" class="%s" value="' . __( 'post-comment' ) . '" />',
		'form_button_class'			=> 'submit',
		'before_comments'			=> '',
		'after_comments'			=> '',
		'before'					=> '',
		'after'						=> '',
		'comment_inner_wrap' 		=> '%1$s %2$s %3$s %4$s',
		'display_author_avatar'		=> true
	);
	
	if ( !empty( $param ) ) 
	{
		$args = array_merge( $args, $param );
	}
	
	$args = (object) $args;
	
	//If the disqus is enabled, load the comment form and stop here
	if ( !empty( $extSys ) && ( $extSys != 'none' ) )
	{
		echo CommentForm( $args, false );
		return;
	}
	
	$html = $comments = '';
	
	if ( !empty( $args->before ) )
		$html .= $args->before;
	
	if ( !empty( $args->container ) )
	{
		$html .= '<' . $args->container;
	
		$html .= ( !empty( $args->container_id ) ? ' id="' . $args->container_id . '"' : '' );

		$html .= ( !empty( $args->container_class ) ? ' class="' . $args->container_class . '"' : '' );
	
		$html .= '>';
	}
	
	if ( !empty( $args->title_html_tag ) && ( ( $Post->NumComments() > 0 ) || ( !$args->remove_zero_title && ( $Post->NumComments() == 0 ) && !empty( $args->title_zero ) ) ) )
	{
		$h = '<' . $args->title_html_tag;
		
		if ( !empty( $args->title_class ) )
		{
			$h .= ' class="' . $args->title_class . '"';
		}
		
		if ( !empty( $args->title_id ) )
		{
			$h .= ' id="' . $args->title_id . '"';
		}
		
		$h .= '>';
		
		if ( $Post->NumComments() > 0 )
		{
			$h .= sprintf( $args->title_plural, $Post->NumComments(), $Post->Title() );
		}
		
		elseif ( ( $Post->NumComments() == 0 ) && !empty( $args->title_zero ) )
		{
			$html .= sprintf( $args->title_zero, $Post->Title() );
		}
		
		elseif ( $Post->NumComments() == 1 )
		{
			$h .= sprintf( $args->title_single, $Post->Title() );
		}
		
		$h .= '</' . $args->title_html_tag . '>';
		
		if ( !empty( $args->title_wrap ) )
		{
			$html .= sprintf( $args->title_wrap, $h );
		}
		else
		{
			$html .= $h;
		}
		
	}
	
	if ( Settings::IsTrue( 'enable_reviews' ) && IsEnabledTo( 'reviews_allowed_in' ) && ( $Post->Rating() > 0 ) )
	{
		$html .= '
		<div class="pr30 pl30 text-center">
			<div class="avrg-rating">
				<p class="comments-title font90">' . __( 'user-reviews' ) . ': <span class="blackcolor font200 fontbold">' . $Post->Rating() . '</span> <span class="greycolor font90">' . __( 'out-of' ) . ' 5</span></p>
			</div>				
			<div class="clearfix"></div>
		</div>';
	}

	if ( !empty( $Post->Comments() ) )
	{
		if ( !IsAllowedTo( 'read-comments' ) )
		{
			$html .= '<p>' . __( 'restricted-read-comments-message' ) . '</p>';
		}
		else
		{
			$i = 1;
			
			foreach( $Post->Comments() as $commId => $comm )
			{
				$commIcon = $commContent = $commDate = $commTitle = '';
				
				if ( !empty( $comm['imageUrl'] ) && $args->display_author_avatar )
				{
					if ( !empty( $args->comment_author_icon_wrap ) )
					{
						$icon_class = $args->comment_author_icon_class . ( Settings::IsTrue( 'enable_lazyloader' ) ? ' lazyload' : '' );
							
						$icon_url	= $comm['imageUrl'];

						$commIcon = sprintf( $args->comment_author_icon_wrap, $icon_class, $icon_url );
					}
						
					else
					{
						$commIcon = '<img alt="" src="' . $comm['imageUrl'] . '" class="avatar avatar-60 photo' . ( Settings::IsTrue( 'enable_lazyloader' ) ? ' lazyload' : '' ) . '" height="60" width="60" />';
					}
				}
				
				$commTitle = '<strong>' . $comm['name'] . '</strong>';

				if ( !empty( $args->comment_title_wrap ) )
				{
					$commTitle = sprintf( $args->comment_title_wrap, $commTitle );
				}
				
				$commDate = '<a href="#comment-' . $comm['id'] . '"><time datetime="' . $comm['timeC'] . '">' . $comm['time'] . '</time></a>';
				
				if ( !empty( $args->comment_date_wrap ) )
				{
					$commDate = sprintf( $args->comment_date_wrap, $commDate );
				}
				
				if ( Settings::IsTrue( 'enable_reviews' ) && IsEnabledTo( 'reviews_allowed_in' ) )
				{
					$commContent .= '
					<div class="user-rating" title="Rated ' . $comm['rating'] . ' out of 5">';
							
					if ( $comm['rating'] > 0 )
					{
						for ( $i = 0; $i < 5; $i++ )
						{
							$commContent .= '<span class="userstar userstar' . $i . ' ' . ( $i < $comm['rating'] ? 'active' : '' )  . '">&#9733;</span>';
						}
					}
							
					$commContent .= '</div>';
				}
				
				if ( !empty( $args->comment_content_wrap ) )
				{
					$commContent = sprintf( $args->comment_content_wrap, $comm['comment'] );
				}
				
				else
				{
					$commContent .= $comm['comment'];
				}
				
				if ( Settings::IsTrue( 'enable_reviews' ) && IsEnabledTo( 'reviews_allowed_in' ) )
				{
					$commContent .= '
					<div class="flowhidden">';
					if ( !empty( $comm['reviewPos'] ) )
					{
						$commContent .= '
							<div class="text-one-half lineheight20 padd20 lightgreenbg mt15 font90 blackcolor"><span class="mb10 blockstyle fontbold">+ PROS: </span><span> <span class="blockstyle mb5">' . $comm['reviewPos'] . '</span></span></div>';
					}
						
					if ( !empty( $comm['reviewNeg'] ) )
					{
						$commContent .= '
						<div class="text-one-half lineheight20 lightredbg padd20 mt15 font90 blackcolor"><span class="mb10 blockstyle fontbold">- CONS:</span><span> <span class="blockstyle mb5">' . $comm['reviewNeg'] . '</span></span></div>';
					}
						
					$commContent .= '
					</div>';
				}

				$tc = ( ( !empty( $comm['userId'] ) && ( $Post->Author()->id == $comm['userId'] ) ) ? 'byuser bypostauthor ' : '' ) . ( ( $i % 2 ) ? 'odd alt' : 'even' );
					
				$class = sprintf( $args->item_class, $tc, ( ( $i % 2 ) ? 'thread-odd thread-alt' : 'thread-even' ) );
				
				$id = 'comment-' . $comm['id'];
				
				$itemId = 'div-comment-' . $comm['id'];
				
				$content = sprintf( $args->comment_inner_wrap, $commTitle, $commDate, $commIcon, $commContent );
				
				$item = sprintf( $args->item_content_wrap, $itemId, $args->item_content_class, $content );
				
				$comments .= sprintf( $args->item_wrap, $id, $class, $item );

				$i++;	
			}
		}
		
		if ( !empty( $args->list_wrap ) )
		{
			$html .= sprintf( $args->list_wrap, $args->list_class, $comments );
		}
		
		else
		{
			$html .= $comments;
		}
	}
	
	if ( !empty( $args->before_respond ) )
	{
		$html .= $args->before_respond;
	}
	
	$resp = sprintf( $args->respond_title_wrap, $args->respond_title_id, $args->respond_title_class, __( 'leave-a-reply' ) );
	
	$resp .= CommentForm( $args, false );
	
	$html .= sprintf( $args->respond_wrap, $args->respond_id, $args->respond_class, $resp );
	
	if ( !empty( $args->after_respond ) )
	{
		$html .= $args->after_respond;
	}
	
	if ( !empty( $args->container ) )
	{
		$html .= '</' . $args->container . '>';
	}

	if ( !$echo )
		return $html;
	
	else
		echo $html;
}

#####################################################
#
# Comment Form function
#
#####################################################
function CommentForm( $args, $echo = true )
{
	global $Post;

	$UserGroup 		= UserGroup();
	$UserId 		= UserId();
	$settings 		= Settings::PrivacySettings();
	$extSys 		= CurrentLang()['data']['ext_comm_system'];
	$extSysName 	= CurrentLang()['data']['ext_comm_shortname'];
	$commSettings	= Settings::Comments();
	$limit			= ( !empty( $commSettings['comments_limit'] ) ? $commSettings['comments_limit'] : 10 );
	
	$html = $temp = '';

	if ( !empty( $Post ) && $Post->CanComment() && IsAllowedTo( 'post-comments' ) && IsAllowedTo( 'read-comments' ) )
	{
		if ( !empty( $extSys ) && ( $extSys != 'none' ) )
		{
			if ( $extSys == 'disqus' )
			{
				//Source for disqus lazy loading code:
				//https://usefulangle.com/post/251/disqus-comments-improve-page-load-speed
				$temp .= '
				<!-- Disqus -->
				<div id="disqus_thread" style="min-height: 100px">
					<div id="disqus_thread_loader" style="display: block;margin-left: auto;margin-right: auto;width: 50%;"><img src="' . TOOLS_HTML . 'theme_files/assets/frontend/img/loading.gif" alt="loading" /></div>
				</div>
				<script>
					var disqus_config = function () {
						this.page.url = "' . $Post->Url() . '";
						this.page.identifier = "' . $Post->PostId() . '";
					};
					var disqus_observer = new IntersectionObserver(function(entries) {
						if(entries[0].isIntersecting) {
							(function() {
								var d = document, s = d.createElement("script");
								s.src = "https://' . $extSysName . '.disqus.com/embed.js";
								s.setAttribute("data-timestamp", +new Date());
								(d.head || d.body).appendChild(s);
							})();
							disqus_observer.disconnect();
						}
					}, { threshold: [0] });
				disqus_observer.observe(document.querySelector("#disqus_thread"));
				</script>
				<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
				<!-- //Disqus -->';
			}
			
			elseif ( $extSys == 'fb-comments' )
			{
				$temp .= '
				<!-- Facebook Comments -->
				<div id="fb-root"></div>
				<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v17.0&appId=' . $extSysName . '&autoLogAppEvents=1" nonce="OZw2FHbg"></script>
				<div class="fb-comments" data-order-by="reverse-time" data-href="' . $Post->Url() . '" data-width="100%" data-lazy="true" data-numposts="' . $limit . '"></div>
				<!-- //Facebook Comments -->';
			}
			
			elseif ( $extSys == 'intensedebate' )
			{
				$temp .= '
				<!-- IntenseDebate -->
				<script>
					var idcomments_acct = \'' . $extSysName . '\';
					var idcomments_post_id = \'' . $Post->PostId() . '\';
					var idcomments_post_url = \'' . $Post->Url() . '\';
				</script>
				<span id="IDCommentsPostTitle" style="display:none"></span>
				<script type=\'text/javascript\' src=\'https://www.intensedebate.com/js/genericCommentWrapperV2.js\' async></script>
				<!-- //IntenseDebate -->';
			}
		}
		
		else
		{
			$temp .= '
			<form action="' . SITE_URL . 'comment-post.php" method="post"' . ( !empty( $args->comment_form_class ) ? ' class="' . $args->comment_form_class . '"' : '' ) . ( !empty( $args->comment_form_id ) ? ' id="' . $args->comment_form_id . '"' : '' ) . ' novalidate>';
			
			if ( ( $UserId == 0 ) && !empty( $args->guest_notify_message ) )
			{
				$temp .= $args->guest_notify_message;
			}
			elseif ( !empty( $UserId ) )
			{
				$temp .= '<p class="logged-in-as">' . __( 'you-are-logged-in' ) . ' <a href="' . SITE_URL . 'logout/">' . __( 'logout' ) . '?</a>';
			}
			
			$temp .= CommentPostInput( $args, false );
		
			if ( $UserId == 0 )
			{
				$temp .= CommentNameInput ( $args, false );
				
				$temp .= CommentEmailInput( $args, false );
				
				$temp .= CommentUrlInput  ( $args, false );
			}
		
			if ( Settings::IsTrue( 'enable_honeypot' ) && ( !Settings::IsTrue( 'hide_captcha_logged_in_users' ) || ( Settings::IsTrue( 'hide_captcha_logged_in_users' ) && ( empty( $UserId ) || ( $UserId == 0 ) ) ) ) && 
				( ( Settings::Get()['show_captcha_in_forms'] == 'everywhere' ) || ( Settings::Get()['show_captcha_in_forms'] == 'comment-form' ) )
			)
			{
				$temp .= '<p class="ohhney"><label for="url">' . __( 'name' ) . '</label>
					<input class="ohhney" autocomplete="off" type="text" id="name" name="name" placeholder="Your name here"></p>';
				
				$temp .= '<p class="ohhney"><label for="url">' . __( 'email' ) . '</label>
					<input class="ohhney" autocomplete="off" type="email" id="email" name="email" placeholder="Your e-mail here"></p>';
			}

			if ( Settings::IsTrue( 'enable_reviews' ) && IsEnabledTo( 'reviews_allowed_in' ) )
			{
				$groups = Json( Settings::Get()['allow_reviews_group'] );

				if ( ( $UserGroup == 1 ) || ( !empty( $groups ) && in_array( $UserGroup, $groups ) ) )
				{
					$temp .= '
					<div class="star-ratings start-ratings-main clearfix">
						<div class="stars stars-main">
							<p>' . __( 'your-rating' ) . '</p>
							<select id="rating-stars" name="rating" autocomplete="off">
								<option value=""></option>
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
							</select>
						</div>
					 </div>
			 
					<div class="flowhidden">
						<div class="comment-form-comment text-one-half"><textarea id="pos_comment" name="pos_comment" rows="4" placeholder="PROS:"></textarea></div>
						<div class="comment-form-comment text-one-half"><textarea id="neg_comment" name="neg_comment" rows="4" placeholder="CONS:"></textarea></div>
					</div>';
				}
			}
		
			if ( !empty( $settings ) && isset( $settings['require_users_agree_terms_of_service'] ) && isset( $settings['show_required_terms_in'] ) && $settings['require_users_agree_terms_of_service'] )
			{
				if ( ( $settings['show_required_terms_in'] == 'everywhere' ) || ( $settings['show_required_terms_in'] == 'comment-form' ) )
				{
					$pages = Settings::LegalPages();
		
					$code = CurrentLang()['lang']['code'];
		
					if ( !empty( $pages ) && isset( $pages['terms'] ) && !empty( $pages['terms'] ) 
						&& isset( $pages['terms'][$code] ) && !empty( $pages['terms'][$code] )
					)
					{
						$url = $pages['terms'][$code]['url'];
						$title = $pages['terms'][$code]['title'];
		
						$c = '
							<input class="form-check-input" type="checkbox" value="1" name="terms-of-service" id="TermsOfServiceAgreement" required="required">
							<label class="form-check-label" for="TermsOfServiceAgreement">' . sprintf( __( 'i-accept-the-terms-of-service' ), $url ) . '</label>';

						$temp .= sprintf( $args->item_wrap, ( $args->form_generic_class ? $args->form_generic_class : $args->form_checkbox_class ), $c );
					}
					
					//Add this to avoid any errors when adding a comment
					else
					{
						$c = '
							<input class="form-check-input" type="checkbox" value="1" name="terms-of-service" id="TermsOfServiceAgreement" required="required">
							<label class="form-check-label" for="TermsOfServiceAgreement">' . sprintf( __( 'i-accept-the-terms-of-service' ), '#' ) . '</label>';

						$temp .= sprintf( $args->item_wrap, ( $args->form_generic_class ? $args->form_generic_class : $args->form_checkbox_class ), $c );
					}
				}
			}
			
			if ( ( Settings::Get()['enable_recaptcha'] != 'false' ) && 
				( ( Settings::Get()['show_captcha_in_forms'] == 'everywhere' ) || ( Settings::Get()['show_captcha_in_forms'] == 'comment-form' ) ) && ( !Settings::IsTrue( 'hide_captcha_logged_in_users' ) || ( Settings::IsTrue( 'hide_captcha_logged_in_users' ) && ( empty( $UserId ) || ( $UserId == 0 ) ) ) ) && !empty( Settings::Get()[ 'recaptcha_site_key'] )
			)
			{
				if ( Settings::Get()['enable_recaptcha'] == 'google-recaptcha-v2' )
				{
					$temp .= '
						<div class="g-recaptcha" data-sitekey="' . Settings::Get()['recaptcha_site_key'] . '"></div>';
				}
				else
				{
					$temp .= '
					<input type="hidden" name="recaptcha_response" id="recaptchaResponse">';
				}
			}
		
			$temp .= '
				<input type="hidden" name="postID" value="' . $Post->PostId() . '" id="postID" />
				<input type="hidden" name="parentID" id="parentID" value="0" />
				<input type="hidden" name="_token" value="' . csrf::token() . '" />';
				
			if ( is_numeric( $UserId ) && ( $UserId > 0 ) )
			{
				$temp .= '
					<input type="hidden" name="userID" id="userID" value="' . $UserId . '" />';
			}
		
			//Add the button
			if ( !empty( $args->form_button_class ) )
			{
				$temp .= sprintf( $args->form_button_wrap, $args->form_button_class );
			}
			
			else
			{
				$temp .= $args->form_button_wrap;
			}

			$temp .= '
			</form>';
		}
	}
	else
	{
		if ( $Post && !$Post->CanComment() )
		{
			$temp .= '<p>' . __( 'comments-are-closed' ) . '</p>';
		}
		
		elseif ( $UserId == 0 )
		{
			if ( Settings::IsTrue( 'enable_registration', 'site' ) && !Settings::IsTrue( 'disable_user_login' ) )
			{
				$temp .= '
					<p>' . sprintf( __( 'restricted-comment' ), SITE_URL . 'login/', SITE_URL . 'register/' ) . '</p>';
			}
			
			elseif ( !Settings::IsTrue( 'enable_registration', 'site' ) && !Settings::IsTrue( 'disable_user_login' ) )
			{
				$temp .= '
					<p>' . sprintf( __( 'restricted-comment-no-register' ), SITE_URL . 'login/' ) . '</p>';
			}
		}
		else
		{
			$temp .= '
			<p>' . __( 'restricted-add-comment-message' ) . '</p>';
		}
	}
	
	if ( !empty ( $args->form_wrap ) )
	{
		$html = sprintf( $args->form_wrap, $temp );
	}
	
	else
	{
		$html = $temp;
	}

	if ( $echo )
		echo $html;
	
	else
		return $html;
}

#####################################################
#
# Comment Name Input Form function
#
#####################################################
function CommentNameInput( $args, $echo = true )
{	
	$temp = '<input id="author" name="author"' . ( !empty( $args->author_input_class ) ? ' class="' . $args->author_input_class . '"' : '' ) . ( !empty( $args->author_input_id ) ? ' id="' . $args->author_input_id . '"' : '' ) . ' type="text" value="" size="30" maxlength="245" required="required" />';
		
	if ( !empty( $args->comment_author_wrap ) )
	{
		$html = sprintf( $args->comment_author_wrap, $temp );
	}
	else
	{
		$html = $temp;
	}
	
	if ( !$echo )
		return $html;
	
	else
		echo $html;
}

#####################################################
#
# Comment Email Input Form function
#
#####################################################
function CommentEmailInput( $args, $echo = true )
{	
	$temp = '<input id="email" name="aemail"' . ( !empty( $args->email_input_class ) ? ' class="' . $args->email_input_class . '"' : '' ) . ( !empty( $args->email_input_id ) ? ' id="' . $args->email_input_id . '"' : '' ) . ' type="email" value="" size="30" maxlength="100" required="required" />';
		
	if ( !empty( $args->comment_email_wrap ) )
	{
		$html = sprintf( $args->comment_email_wrap, $temp );
	}
	else
	{
		$html = $temp;
	}
	
	if ( !$echo )
		return $html;
	
	else
		echo $html;
}

#####################################################
#
# Comment Url Input Form function
#
#####################################################
function CommentUrlInput( $args, $echo = true )
{
	$temp = '<input id="email" name="url"' . ( !empty( $args->url_input_class ) ? ' class="' . $args->url_input_class . '"' : '' ) . ( !empty( $args->url_input_id ) ? ' id="' . $args->url_input_id . '"' : '' ) . ' type="email" value="" size="30" maxlength="100" required="required" />';
		
	if ( !empty( $args->comment_url_wrap ) )
	{
		$html = sprintf( $args->comment_url_wrap, $temp );
	}
	else
	{
		$html = $temp;
	}
	
	if ( !$echo )
		return $html;
	
	else
		echo $html;
}

#####################################################
#
# Comment Post Input Form function
#
#####################################################
function CommentPostInput( $args, $echo = true )
{
	$temp = '<textarea name="comment"' . ( !empty( $args->comment_message_class ) ? ' class="' . $args->comment_message_class . '"' : '' ) . ( !empty( $args->comment_message_id ) ? ' id="' . $args->comment_message_id . '"' : '' ) . ' cols="45" rows="8" maxlength="65525" required="required"></textarea>';
	
	if ( !empty( $args->comment_message_wrap ) )
	{
		$html = sprintf( $args->comment_message_wrap, $temp );
	}
	else
	{
		$html = $temp;
	}
	
	if ( !$echo )
		return $html;
	
	else
		echo $html;
}

#####################################################
#
# Comment Security Form function
#
#####################################################
function SecurityForm( $arg = '<div class="%s">%s</div>', $form = 'comment-form', $echo = true )
{
	$html = '';
	
	$UserId = UserId();
	
	if ( Settings::IsTrue( 'enable_honeypot' ) && 
			( ( Settings::Get()['show_captcha_in_forms'] == 'everywhere' ) || ( Settings::Get()['show_captcha_in_forms'] == $form ) )
	)
	{
		$c = '<label for="name">' . __( 'name' ) . '</label>
			<input class="ohhney" autocomplete="off" type="text" id="name" name="name" placeholder="Your name here">';
			
		$html .= sprintf( $arg, 'form-submit ohhney', $c );
			
		$c = '<label for="email">' . __( 'email' ) . '</label>
				<input class="ohhney" autocomplete="off" type="email" id="email" name="email" placeholder="Your e-mail here">';
			
		$html .= sprintf( $arg, 'form-submit ohhney', $c );
	}
	
	if ( ( Settings::Get()['enable_recaptcha'] != 'false' ) && 
			( ( Settings::Get()['show_captcha_in_forms'] == 'everywhere' ) || ( Settings::Get()['show_captcha_in_forms'] == $form ) ) && ( !Settings::IsTrue( 'hide_captcha_logged_in_users' ) || ( Settings::IsTrue( 'hide_captcha_logged_in_users' ) && ( empty( $UserId ) || ( $UserId == 0 ) ) ) ) && !empty( Settings::Get()[ 'recaptcha_site_key'] )
	)
	{
			if ( Settings::Get()['enable_recaptcha'] == 'google-recaptcha-v2' )
			{
				$html .= '
					<div class="g-recaptcha" data-sitekey="' . Settings::Get()['recaptcha_site_key'] . '"></div>';
			}
			else
			{
				$html .= '
				<input type="hidden" name="recaptcha_response" id="recaptchaResponse">';
			}
	}
	
	$html .= '<input type="hidden" name="_token" value="' . csrf::token() . '" />';
	
	if ( $echo )
		echo $html;
	
	else
		return $html;
}

#####################################################
#
# Comment Review Blocks function
#
#####################################################
function CommentReviewBlocks( $pos, $neg, $echo = true )
{
	$html = '';
	
	if ( Settings::IsTrue( 'enable_reviews' ) && IsEnabledTo( 'reviews_allowed_in' ) )
	{
		$html .= '
		<div class="flowhidden">';
		
		if ( !empty( $pos ) )
		{
			$html .= '
			<div class="text-one-half lineheight20 padd20 lightgreenbg mt15 font90 blackcolor"><span class="mb10 blockstyle fontbold">+ PROS: </span><span> <span class="blockstyle mb5">' . $pos . '</span></span></div>';
		}

		if ( !empty( $neg ) )
		{
			$html .= '
			<div class="text-one-half lineheight20 lightredbg padd20 mt15 font90 blackcolor"><span class="mb10 blockstyle fontbold">- CONS:</span><span> <span class="blockstyle mb5">' . $neg . '</span></span></div>';
		}

		$html .= '
		</div>';
	}
	
	if ( $echo )
		echo $html;
	
	else
		return $html;
}

#####################################################
#
# Comment Review Field function
#
#####################################################
function CommentReviewField( $rating, $echo = true )
{
	$html = '';
	
	if ( Settings::IsTrue( 'enable_reviews' ) && IsEnabledTo( 'reviews_allowed_in' ) )
	{
		$html .= '
		<div class="user-rating" title="Rated ' . $rating . ' out of 5">';

		if ( $rating > 0 )
		{
			for ( $i = 0; $i < 5; $i++ )
			{
				$html .= '<span class="userstar userstar' . $i . ' ' . ( $i < $rating ? 'active' : '' )  . '">&#9733;</span>';
			}
		}

		$html .= '</div>';
	}
	
	if ( $echo )
		echo $html;
	
	else
		return $html;
}

#####################################################
#
# Comment Review Button function
#
#####################################################
function CommentReviewButton( $echo = true )
{
	$UserGroup = UserGroup();
	$UserId = UserId();
	
	$html = '';
	
	if ( Settings::IsTrue( 'enable_reviews' ) && IsEnabledTo( 'reviews_allowed_in' ) )
	{
		$groups = Json( Settings::Get()['allow_reviews_group'] );

		if ( ( $UserGroup == 1 ) || ( !empty( $groups ) && in_array( $UserGroup, $groups ) ) )
		{
			$html .= '
				<div class="star-ratings start-ratings-main clearfix">
					<div class="stars stars-main">
						<p>' . __( 'your-rating' ) . '</p>
						<select id="rating-stars" name="rating" autocomplete="off">
							<option value=""></option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</div>
				 </div>
		 
				<div class="flowhidden">
					<div class="comment-form-comment text-one-half"><textarea id="pos_comment" name="pos_comment" rows="4" placeholder="PROS:"></textarea></div>
					<div class="comment-form-comment text-one-half"><textarea id="neg_comment" name="neg_comment" rows="4" placeholder="CONS:"></textarea></div>
				</div>';
		}
	}
	
	if ( $echo )
		echo $html;
	
	else
		return $html;
}