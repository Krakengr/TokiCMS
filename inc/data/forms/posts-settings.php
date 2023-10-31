<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Posts Settings Form
#
#####################################################
$L = $this->lang;
$settings = $this->adminSettings::Get();

$editor = Json( $settings['editor_data'] );
$drafts = Json( $settings['drafts_data'] );

include ( ARRAYS_ROOT . 'generic-arrays.php');

$contentAllow = array(
	'posts' => array( 'name' => 'posts', 'title'=> $L['posts'], 'disabled' => false, 'data' => array() ),
	'pages' => array( 'name' => 'pages', 'title'=> $L['pages'], 'disabled' => false, 'data' => array() )
);

$showData = array(
	'older-first' => array( 'name' => 'older-first', 'title'=> $L['older-first'], 'disabled' => false, 'data' => array() ),
	'newer-first' => array( 'name' => 'newer-first', 'title'=> $L['newer-first'], 'disabled' => false, 'data' => array() )
);

$comments = Json( $settings['comments_data'] );

//Create Pages Array
$q = "(p.id_site = " . $this->siteID . ") AND (p.id_lang = " . $this->langID . ") AND (p.id_blog = " . $this->blogID . ") AND (p.post_type = 'page') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL)";

$query = PostsDefaultQuery( $q, null, "p.title ASC" );

$q = $this->db->from( null, $query )->all();

$pagesData = array();

if ( $q )
{
	foreach ( $q as $page )
		$pagesData[$page['id_post']] = array( 'name' => $page['id_post'], 'title' => $page['title'], 'disabled' => false );
}

$form = array
(
	'posts-settings' => array
	(
		'title' => $L['frontpage-settings'],
		'data' => array(
		
			'front-posts' => array(
				'title' => null, 'hide' => ( !$this->siteIsSelfHosted ? true : false ), 'data' => array
				(
					'front'=>array('label'=>$L['homepage-displays'], 'name' => 'settings[front_page]', 'type'=>'select', 'value'=>$settings['front_page'], 'tip'=>null,
					'firstNull' => false, 'tip'=>$L['your-latest-posts-tip'], 'disabled' => ( $this->IsDefaultLang() ? false : true ), 'extraKeys' => array( 'name' => 'onchange', 'data' => 'check()'),
					'data' => array(
						'your-latest-posts' => array( 'name' => 'latest-posts', 'title'=>$L['your-latest-posts'], 'disabled' => false ),
						'a-static-page' => array( 'name' => 'static-page', 'title'=>$L['a-static-page-select-below'], 'disabled' => false ),
						'forum' => array( 'name' => 'forum', 'title'=>$L['the-forum'], 'disabled' => ( !$this->adminSettings::IsTrue( 'enable_multiblog', 'site' ) ? true : false ) ),
						'store' => array( 'name' => 'store', 'title'=>$L['the-store'], 'disabled' => ( !$this->adminSettings::IsTrue( 'enable_multiblog', 'site' ) ? true : false ) ),
					) ),
				)
			),
			
			'static-page' => array( 
				'title' => null, 'hide' => ( !$this->siteIsSelfHosted ? true : false ), 'data' => array
				(
					'static-page'=>array('label'=>$L['homepage'], 'name' => 'settings[front_static_page]', 'type'=>'select', 'value'=>$settings['front_static_page'], 'tip'=>$L['static-page-tip'], 'id' => 'static-page-div', 'dnone' => ( empty( $settings['front_static_page'] ) ? true : false ), 'firstNull' => true, 'disabled' => ( $this->IsDefaultLang() ? false : true ), 'data' => $pagesData ),
				)
			),

			'num-posts' => array( 
				'title' => null, 'data' => array
				(
					'num-posts'=>array('label'=>$L['items-per-page'], 'name' => 'settings[article_limit]', 'type'=>'num', 'value'=>( empty( $settings['article_limit'] ) ? 12 : (int) $settings['article_limit'] ), 'tip'=>$L['number-of-items-to-show-per-page'], 'min'=>'1', 'max'=>'30' )
				)
			),
		)
	),
	
	'comments-settings' => array
	(
		'title' => $L['comments-settings'],
		'hide' => ( !$this->siteIsSelfHosted ? true : false ),
		'data' => array(
		
			'generic-settings' => array(
				'title' => null, 'data' => array
				(
					'enable-comments'=>array('label'=>$L['enable-comments'], 'type'=>'checkbox', 'name' => 'settings[enable_comments]', 'value' => $settings['enable_comments'], 'tip'=>$L['enable-comments-tip'] ),

					'hide-comments'=>array('label'=>$L['hide-comments'], 'type'=>'checkbox', 'name' => 'settings[hide_comments]', 'value' => ( isset( $comments['hide_comments'] ) ? $comments['hide_comments'] : null ) , 'tip'=>$L['hide-comments-tip'] ),
					
					'email-on-comments'=>array('label'=>$L['email-when-someone-posts-comments'], 'type'=>'checkbox', 'name' => 'settings[mail_on_comments]', 'value' => $this->adminSettings::IsTrue( 'mail_on_comments' ), 'tip'=>null ),

					'enable-comments-in'=>array('label'=>$L['enable-comments-in'], 'name' => 'settings[allow][]', 'type'=>'select', 'value'=>( isset( $comments['allow'] ) ? $comments['allow'] : null ), 'tip'=>$L['enable-comments-in-tip'], 'firstNull' => false, 'data' => $contentAllow, 'id' => 'slcAmp', 'class' => 'form-control select2 form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ),
						'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>' ),
					
					'num-comments'=>array('label'=>$L['items-per-page'], 'name' => 'settings[comments_limit]', 'type'=>'num', 'value'=>( isset( $comments['comments_limit'] ) ? $comments['comments_limit'] : 0 ), 'tip'=>$L['number-of-comments-to-show-per-page'], 'min'=>'0', 'max'=>'20'),
					
					'automatically-close-comments'=>array('label'=>$L['automatically-close-post-comments'], 'name' => 'settings[auto_comments_close]', 'type'=>'num', 'value'=>( isset( $comments['auto_comments_close'] ) ? $comments['auto_comments_close'] : 0 ), 'tip'=>$L['automatically-close-post-comments-tip'], 'min'=>'0', 'max'=>'999'),
					
					'display-a-message'=>array('label'=>$L['redirect-to-page-with-message-after-comment'], 'name' => 'settings[redirect_with_message]', 'type'=>'checkbox', 'value'=>( isset( $comments['redirect_with_message'] ) ? $comments['redirect_with_message'] : false ), 'tip'=>$L['redirect-to-page-with-message-after-comment-tip'], 'min'=>'0', 'max'=>'999'),
					
					'comment-repost-timer'=>array('label'=>$L['comment-repost-timer'], 'name' => 'settings[comment_repost_timer]', 'type'=>'num', 'value'=>( isset( $settings['comment_repost_timer'] ) ? $settings['comment_repost_timer'] : 0 ), 'tip'=>$L['comment-repost-timer-tip'], 'min'=>'0', 'max'=>'999'),
					
					'sort-by'=>array('label'=>$L['sort-by'], 'type'=>'select', 'name' => 'settings[sort_by]', 'value'=>( isset( $comments['sort_by'] ) ? $comments['sort_by'] : null ), 'tip'=>$L['sort-by-tip'], 'firstNull' => false, 'data' => $showData ),
				)
			)
		)
	),
	
	'writing-settings' => array
	(
		'title' => $L['writing-settings'],
		'data' => array(
		
			'html-editor' => array( 
				'title' => null, 'data' => array
				(
					'html-editor'=>array('label'=>$L['html-editor'], 'name' => 'settings[html_editor]', 'type'=>'select', 'value'=>$settings['html_editor'], 'tip'=>$L['html-editor-tip'], 'firstNull' => false, 'disabled' => false, 'data' => $postEditorOptions ),
					
					'share-images'=>array('label'=>$L['share-images-between-sites'], 'type'=>'checkbox', 'name' => 'settings[share_images_sites]', 'value' => $settings['share_images_sites'], 'tip'=>$L['share-images-between-sites-tip'], 'disabled' => ( $this->isChildSite || ( !$this->isChildSite && !$this->adminSettings::IsTrue( 'enable_multisite', 'site' ) ) ? true : false ) ),
					
					'share-tags'=>array('label'=>$L['share-tags'], 'type'=>'checkbox', 'name' => 'settings[share_tags_langs]', 'value' => $settings['share_tags_langs'], 'tip'=>$L['share-tags-tip'], 'disabled' => ( !$this->MultiLang() ? true : false ) ),
				)
			),
			
			'editor-settings' => array( 
				'title' => null, 'data' => array
				(
					'toolbar'=>array( 'label'=>$L['toolbar'], 'type'=>'text', 'name' => 'editor[toolbar]', 'value' => ( isset( $editor['toolbar'] ) ? ( ( $editor['toolbar'] != 'disable' ) ? htmlentities( $editor['toolbar'] ) : '' ) : null ), 'disabled' => ( ( isset( $editor['toolbar'] ) && ( $editor['toolbar'] == 'disable' ) ) ? true : false ), 'tip'=>null ),
					
					'plugins'=>array('label'=>$L['plugins'], 'type'=>'text', 'name' => 'editor[plugins]', 'value' => ( isset( $editor['plugins'] ) ? ( ( $editor['plugins'] != 'disable' ) ? htmlentities( $editor['plugins'] ) : '' ) : null ), 'disabled' => ( ( isset( $editor['plugins'] ) && ( $editor['plugins'] == 'disable' ) ) ? true : false ), 'tip'=>null ),
					
					'tab-size'=>array('label'=>$L['tab-size'], 'name' => 'editor[tab-size]', 'type'=>'num', 'value'=>( isset( $editor['tab-size'] ) ? $editor['tab-size'] : '2' ), 'hide' => ( ( isset( $editor['tab-size'] ) && ( $editor['tab-size'] == 'disable' ) ) ? true : false ), 'tip'=>null, 'min'=>'1', 'max'=>'10'),
					
					'spell-checker'=>array('label'=>$L['spell-checker'], 'type'=>'checkbox', 'name' => 'editor[spell-checker]', 'value' => ( ( isset( $editor['spell-checker'] ) && is_bool( $editor['spell-checker'] ) ) ? $editor['spell-checker'] : false ), 'tip'=>null, 'disabled' => ( ( isset( $editor['spell-checker'] ) && ( $editor['spell-checker'] == 'disable' ) ) ? true : false ) )
				)
			)
		)
	),

	'drafts-settings' => array
	(
		'title' => $L['drafts-settings'],
		'data' => array(

			'auto-drafts' => array( 
				'title' => null, 'data' => array
				(
					'enable-auto-drafts'=>array('label'=>$L['enable-drafts-auto-save'], 'type'=>'checkbox', 'name' => 'drafts[enable_auto_drafts]', 'value' => ( isset( $drafts['enable_auto_drafts'] ) ? $drafts['enable_auto_drafts'] : false ), 'tip'=>null, 'disabled' => false ),
					
					'drafts'=>array('label'=>$L['drafts-auto-save'], 'name' => 'drafts[auto_save]', 'type'=>'num', 'value'=>( isset( $drafts['auto_save'] ) ? $drafts['auto_save'] : '60' ), 'tip'=>$L['drafts-auto-save-tip'], 'min'=>'30', 'max'=>'900')
				)
			)
		)
	)	
);