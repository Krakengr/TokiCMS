<?php defined('TOKICMS') or die('Hacking attempt...');

class Post
{
	protected $data;

	public function __construct( $post )
	{
		$this->data = $post;
	}
	
	#####################################################
	#
	# Get the page's Parent Url function
	#
	#####################################################
	public function PageParentUrl()
	{
		return $this->data['pageParentUrl'];
	}
	
	#####################################################
	#
	# Get the page's Parent Id function
	#
	#####################################################
	public function PageParentId()
	{
		return $this->data['pageParentId'];
	}
	
	#####################################################
	#
	# Get the page's Parent Title function
	#
	#####################################################
	public function PageParentTitle()
	{
		return $this->data['pageParentTitle'];
	}
	
	#####################################################
	#
	# Get the page's Order function
	#
	#####################################################
	public function PageOrder()
	{
		return $this->data['pageOrder'];
	}
	
	#####################################################
	#
	# Get SubTitle function
	#
	#####################################################
	public function SubTitle()
	{
		if ( !empty( $this->data['postData'] ) && isset ( $this->data['postData']['subtitle'] ) )
			return html_entity_decode( $this->data['postData']['subtitle'] );
		
		return null;
	}
	
	#####################################################
	#
	# Get Number of comments function
	#
	#####################################################
	public function NumComments()
	{
		return $this->data['commentsCount'];
	}
	
	#####################################################
	#
	# Get the comments function
	#
	#####################################################
	public function Comments( $bool = false )
	{
		if ( $bool )
			return ( !empty( $this->data['comments'] ) ? true : false );
		
		return $this->data['comments'];
	}
	
	#####################################################
	#
	# Get hide on post info function
	#
	#####################################################
	public function HideOnHome()
	{
		return $this->data['hideOnHome'];
	}
		
	#####################################################
	#
	# Get the External Url function
	#
	#####################################################
	public function ExternalUrl()
	{
		return $this->data['externalUrl'];
	}
	
	#####################################################
	#
	# Get the External Url function
	#
	#####################################################
	public function ReadingTime()
	{
		return $this->data['readingTime'];
	}
	
	#####################################################
	#
	# Get the External ID function
	#
	#####################################################
	public function ExternalId()
	{
		return $this->data['extId'];
	}
	
	#####################################################
	#
	# Get the title of the post function
	#
	#####################################################
	public function Title( $encoded = false )
	{
		return $encoded ? $this->data['titleEncoded'] : $this->data['title'];
	}
	
	#####################################################
	#
	# Get the time of the last comment
	#
	#####################################################
	public function LastCommented( $nice = false )
	{
		return ( $nice ? $this->data['lastCommentedNice'] : $this->data['lastCommented'] );
	}
	
	#####################################################
	#
	# Get the full html of the post's blog function
	#
	#####################################################
	public function BlogHtml( $class = null )
	{
		if ( empty( $this->data['blog'] ) )
			return null;
		
		return '<a href="' . $this->data['blog']['url'] . '" ' . ( $class ? ' class="' . $class . '" ' : '' ) . 'title="' . htmlspecialchars( $this->data['blog']['name'] ) . '">' . $this->data['blog']['name'] . '</a>';
	}
	
	#####################################################
	#
	# Get the full html of the post's blog function
	#
	#####################################################
	public function AuthorHtml( $class = null )
	{
		if ( empty( $this->data['author'] ) )
			return null;
		
		if ( $class )
		{
			return '<a class="' . $class . '" href="' . $this->data['author']['url'] . '" title="' . htmlspecialchars( $this->data['author']['name'] ) . '">' . $this->data['author']['name'] . '</a>';
		}
		
		else
		{
			return $this->data['author']['html'];
		}
	}
	
	#####################################################
	#
	# Get the post's author array function
	#
	#####################################################
	public function Author()
	{
		if ( empty( $this->data['author'] ) )
			return null;
		
		return (object) $this->data['author'];
	}
	
	#####################################################
	#
	# Get the Author Bio function
	#
	#####################################################
	public function AuthorBio()
	{
		return ( !empty( $this->data['author']['bio'] ) ? $this->data['author']['bio'] : null );
	}
	
	#####################################################
	#
	# Get the Author Social Media function
	#
	#####################################################
	public function AuthorSocialMedia()
	{
		return $this->data['author']['social'];
	}
	
	#####################################################
	#
	# Get the Author default image function
	#
	#####################################################
	public function AuthorProfileImage()
	{
		return ( !empty( $this->data['author']['coverImg']['default'] ) ? $this->data['author']['coverImg']['default']['imageUrl'] : null );
	}
	
	#####################################################
	#
	# Get the Author default image function
	#
	#####################################################
	public function AuthorProfileSrcSet()
	{
		return ( !empty( $this->data['author']['coverSrcSet'] ) ? (object) $this->data['author']['coverSrcSet'] : null );
	}
	
	#####################################################
	#
	# Get the full html of the post's category function
	#
	#####################################################
	public function CategoryHtml( $class = null )
	{
		if ( empty( $this->data['category'] ) )
			return null;
		
		return '<a href="' . $this->data['category']['url'] . '" ' . ( !empty( $this->data['category']['color'] ) ? ' style="color: ' . $this->data['category']['color'] . '"' : '' ) . ( $class ? ' class="' . $class . '" ' : '' ) . 'title="' . htmlspecialchars( $this->data['category']['name'] ) . '">' . $this->data['category']['name'] . '</a>';
	}
	
	#####################################################
	#
	# Get the full html of the post's subcategory function
	#
	#####################################################
	public function SubCategoryHtml( $class = null )
	{
		if ( empty( $this->data['subcategory'] ) )
			return null;

		return '<a href="' . $this->data['subcategory']['url'] . '" ' . ( !empty( $this->data['subcategory']['color'] ) ? ' style="color: ' . $this->data['subcategory']['color'] . '"' : '' ) . ( $class ? ' class="' . $class . '" ' : '' ) . 'title="' . htmlspecialchars( $this->data['subcategory']['name'] ) . '">' . $this->data['subcategory']['name'] . '</a>';
	}
	
	#####################################################
	#
	# Get the post's category function
	#
	#####################################################
	public function Category( $bool = false )
	{
		if ( $bool )
			return ( !empty( $this->data['category'] ) ? true : false );
		
		if ( empty( $this->data['category'] ) )
			return null;
		
		return (object) $this->data['category'];
	}
	
	#####################################################
	#
	# Get the post's subcategory function
	#
	#####################################################
	public function SubCategory( $bool = false )
	{
		if ( $bool )
			return ( !empty( $this->data['subcategory'] ) ? true : false );
		
		if ( empty( $this->data['subcategory'] ) )
			return null;
		
		return (object) $this->data['subcategory'];
	}
	
	#####################################################
	#
	# Get the post's blog function
	#
	#####################################################
	public function Blog( $bool = false )
	{
		if ( $bool )
			return ( !empty( $this->data['blog'] ) ? true : false );
		
		if ( empty( $this->data['blog'] ) )
			return null;
		
		return (object) $this->data['blog'];
	}
	
	#####################################################
	#
	# Get the post's language function
	#
	#####################################################
	public function Language()
	{
		if ( empty( $this->data['language'] ) )
			return null;
		
		return (object) $this->data['language'];
	}
	
	#####################################################
	#
	# Get the post's site function
	#
	#####################################################
	public function Site()
	{
		if ( empty( $this->data['site'] ) )
			return null;
		
		return (object) $this->data['site'];
	}
	
	#####################################################
	#
	# Get the id of the post's parent function
	#
	#####################################################
	public function ParentId()
	{
		return ( isset( $this->data['parentId'] ) ? $this->data['parentId'] : null );
	}
	
	#####################################################
	#
	# Get the contact form auto status function
	#
	#####################################################
	public function HasContactForm()
	{
		return $this->hasContactForm;
	}
	
	#####################################################
	#
	# Get the id of the post function
	#
	#####################################################
	public function PostId()
	{
		return $this->data['id'];
	}

	#####################################################
	#
	# Get the details of the custom type function
	#
	#####################################################
	public function CustomTypes()
	{
		return $this->data['customTypes'];
	}
	
	#####################################################
	#
	# Get the variations of the post function
	#
	#####################################################
	public function Variations( $bool = false )
	{
		if ( $bool )
			return ( !empty( $this->data['variations']['variations'] ) ? true : false );
		
		if ( empty( $this->data['variations']['variations'] ) )
			return null;
		
		return $this->data['variations'];
	}
	
	#####################################################
	#
	# Get the type of the post function
	#
	#####################################################
	public function IsPage()
	{
		return $this->data['isPage'];
	}
	
	#####################################################
	#
	# Get the video of the post function
	#
	#####################################################
	public function Video()
	{
		return ( !empty( $this->data['video'] ) ? (object) $this->data['video'] : null );
	}
	
	#####################################################
	#
	# Check if this post has a video attached to it function
	#
	#####################################################
	public function HasVideo()
	{
		return ( !empty( $this->data['video'] ) ? true : false );
	}
	
	#####################################################
	#
	# Get the Subs of the post function
	#
	#####################################################
	public function Subs()
	{
		return ( !empty( $this->data['subs'] ) ? $this->data['subs'] : '0' );
	}
	
	#####################################################
	#
	# Get the Ratings of the post function
	#
	#####################################################
	public function Rating()
	{
		return ( !empty( $this->data['rating'] ) ? $this->data['rating']['numRating'] : '0' );
	}
	
	#####################################################
	#
	# Get the Top Posts function
	#
	#####################################################
	public function TopPosts( $num = null, $bool = false, $build = true )
	{
		if ( $bool )
			return ( ( isset( $this->data['topPosts'] ) && !empty( $this->data['topPosts'] ) ) ? true : false );
		
		if ( $num && is_numeric( $num ) && ( count( $this->data['topPosts'] ) > $num ) )
			$rel = array_slice( $this->data['topPosts'], 0, $num, true );
		else
			$rel = $this->data['topPosts'];

		return ( $build ? BuildPosts( $rel ) : $rel );
	}
	
	#####################################################
	#
	# Get the Extra Data of the post function
	#
	#####################################################
	public function ExtraData( $str = null, $bool = false )
	{
		if ( $bool )
			return ( ( isset( $this->data['xtraData'] ) && !empty( $this->data['xtraData'] ) ) ? true : false );

		return ( $str ? ( isset( $this->data['xtraData'][$str] ) ? $this->data['xtraData'][$str] : null ) : ( !empty( $this->data['xtraData'] ) ? $this->data['xtraData'] : null ) );
	}
	
	#####################################################
	#
	# Get the Attributes function
	#
	#####################################################
	public function Attributes( $bool = false )
	{
		if ( $bool )
			return ( ( isset( $this->data['attributes'] ) && !empty( $this->data['attributes'] ) ) ? true : false );

		return ( ( isset( $this->data['attributes'] ) && !empty( $this->data['attributes'] ) ) ? $this->data['attributes'] : null );
	}
	
	#####################################################
	#
	# Get the Prices function
	#
	#####################################################
	public function Prices( $bool = false )
	{
		if ( $bool )
			return ( ( isset( $this->data['pricesData'] ) && !empty( $this->data['pricesData'] ) ) ? true : false );

		return ( ( isset( $this->data['pricesData'] ) && !empty( $this->data['pricesData'] ) ) ? $this->data['pricesData'] : null );
	}
		
	#####################################################
	#
	# Get the Deals function
	#
	#####################################################
	public function Deals( $bool = false )
	{
		if ( $bool )
			return ( ( isset( $this->data['dealsData'] ) && !empty( $this->data['dealsData'] ) ) ? true : false );

		return ( ( isset( $this->data['dealsData'] ) && !empty( $this->data['dealsData'] ) ) ? $this->data['dealsData'] : null );
	}
	
	#####################################################
	#
	# Get the Previous Post function
	#
	#####################################################
	public function PreviousPost( $bool = false, $build = true )
	{
		if ( empty( $this->data['previous'] ) )
			return null;
		
		if ( $bool )
			return ( !empty( $this->data['previous'] ) ? true : false );
		
		$data = ( ( isset( $this->data['previous']['0'] ) && !empty( $this->data['previous']['0'] ) ) ? $this->data['previous']['0'] : $this->data['previous'] );
		
		return ( $build ? new Post( $data ) : $data );
	}
	
	#####################################################
	#
	# Get the Next Post function
	#
	#####################################################
	public function NextPost( $bool = false, $build = true )
	{
		if ( empty( $this->data['next'] ) )
			return null;
		
		if ( $bool )
			return ( !empty( $this->data['next'] ) ? true : false );

		$data = ( ( isset( $this->data['next']['0'] ) && !empty( $this->data['next']['0'] ) ) ? $this->data['next']['0'] : $this->data['next'] );
		
		return ( $build ? new Post( $data ) : $data );
	}
	
	#####################################################
	#
	# Get the Related Posts function
	#
	#####################################################
	public function RelatedPosts( $num = null, $bool = false, $build = true )
	{
		if ( $bool )
			return ( ( isset( $this->data['relatedPosts'] ) && !empty( $this->data['relatedPosts'] ) ) ? true : false );
		
		if ( $num && is_numeric( $num ) && ( count( $this->data['relatedPosts'] ) > $num ) )
			$rel = array_slice( $this->data['relatedPosts'], 0, $num, true );
		else
			$rel = $this->data['relatedPosts'];
		
		return ( $build ? BuildPosts( $rel ) : $rel );
	}
	
	#####################################################
	#
	# Get the tags of the post function
	#
	#####################################################
	public function Tags( $bool = false, $html = false )
	{
		if ( $bool )
			return ( !empty( $this->data['tags'] ) ? true : false );
		
		if ( $html )
		{
			return ( empty( $this->data['tagsHtml'] ) ? null : $this->data['tagsHtml'] );
		}
		
		return ( !empty( $this->data['tags'] ) ? $this->data['tags'] : null );
	}
	
	#####################################################
	#
	# Check if the post has cover image function
	#
	#####################################################
	public function HasCoverImage()
	{
		return ( !empty( $this->data['coverImage'] ) ? true : false );
	}
	
	#####################################################
	#
	# Get the cover array function
	#
	#####################################################
	public function Cover()
	{
		return $this->data['coverImage'];
	}

	#####################################################
	#
	# Get the cover image URL function
	#
	#####################################################
	public function CoverImage( $html = false, $size = 'default', $urlOnly = true )
	{
		if ( $html )
		{
			return ( empty( $this->data['coverImageHtml'] ) ? null : $this->data['coverImageHtml'] );
		}
		
		if ( empty( $this->data['coverImage'] ) )
			return null;
		
		$arr = null;
		
		if ( $size == 'default' )
		{
			$arr = ( $urlOnly ? $this->data['coverImage']['default']['imageUrl'] : $this->data['coverImage']['default'] );
		}
		
		elseif ( $size == 'last' )
		{			
			$arr = end( $this->data['coverImage'] );
			$arr = ( $urlOnly ? $arr['imageUrl'] : $arr );
		}
		
		elseif ( $size == 'first' )
		{
			unset( $this->data['coverImage']['default'] );
			
			$arr = array_slice( $this->data['coverImage'], 0, 1 );
			$arr = ( $urlOnly ? $arr['imageUrl'] : $arr );
		}
		
		elseif ( isset( $this->data['coverImage'][$size] ) )
		{
			$arr = ( $urlOnly ? $this->data['coverImage'][$size]['imageUrl'] : $this->data['coverImage'][$size] );
		}

		return ( ( $arr && !$urlOnly ) ? (object) $arr : ( ( $arr && $urlOnly ) ? $arr : null ) );
	}
	
	#####################################################
	#
	# Get the Cover code for AMP pages function
	#
	#####################################################
	public function CoverAmp()
	{
		return ( !empty( $this->data['ampCoverImage'] ) ? $this->data['ampCoverImage'] : null );
	}
	
	#####################################################
	#
	# Get the Content for AMP pages function
	#
	#####################################################
	public function ContentAmp()
	{
		return $this->data['ampContent'];
	}
	
	#####################################################
	#
	# Get Blocks Data function
	#
	#####################################################
	public function Blocks()
	{
		return $this->data['blocksData'];
	}
	
	#####################################################
	#
	# Get Content function
	#
	#####################################################
	public function Content( $amp = false )
	{
		return ( $amp ? $this->data['ampContent'] : $this->data['content'] );
	}
	
	#####################################################
	#
	# Set Content function
	#
	#####################################################
	public function SetContent( $content )
	{
		$this->data['content'] = $content;
	}
	
	#####################################################
	#
	# Get Header Code function
	#
	#####################################################
	public function GetHeaderCode()
	{
		return $this->headerCode;
	}
	
	#####################################################
	#
	# Get Has Amp Bool function
	#
	#####################################################
	public function HasAmp()
	{
		return ( isset( $this->data['hasAmp'] ) ? $this->data['hasAmp'] : $this->hasAmp );
	}
	
	#####################################################
	#
	# Get Row Post Data function
	#
	#####################################################
	public function GetRawData()
	{
		return $this->data;
	}
	
	#####################################################
	#
	# Get the post's published date function
	#
	#####################################################
	public function Added()
	{
		return (object) $this->data['added'];
	}
	
	#####################################################
	#
	# Get the post's published date function
	#
	#####################################################
	public function Updated()
	{
		if ( empty( $this->data['updated'] ) )
			return null;
		
		return (object) $this->data['updated'];
	}
	
	#####################################################
	#
	# Get the post's slug function
	#
	#####################################################
	public function Sef()
	{
		return $this->data['sef'];
	}
	
	#####################################################
	#
	# Get the edit post function
	#
	#####################################################
	public function CanEdit()
	{
		return $this->data['canEditPost'];
	}
	
	#####################################################
	#
	# Get the post's type function
	#
	#####################################################
	public function PostType()
	{
		return $this->data['postType'];
	}
	
	#####################################################
	#
	# Get the post's description function
	#
	#####################################################
	public function Description()
	{
		return $this->data['description'];
	}
	
	#####################################################
	#
	# Get the post's Url function
	#
	#####################################################
	public function Url( $encoded = false )
	{
		return ( $encoded ? $this->data['urlEncoded'] : $this->data['postUrl'] );
	}
	
	#####################################################
	#
	# Get the post's AMP Url function
	#
	#####################################################
	public function AmpUrl( $encoded = false )
	{
		return ( $encoded ? urlencode( $this->data['ampUrl'] ) : $this->data['ampUrl'] );
	}
	
	#####################################################
	#
	# Get the post's Translations function
	#
	#####################################################
	public function Translations()
	{
		return ( !empty( $this->data['trans'] ) ? $this->data['trans'] : null );
	}
	
	#####################################################
	#
	# Get the post's Raw Content function
	#
	#####################################################
	public function PostRaw()
	{
		return $this->data['postRaw'];
	}
	
	#####################################################
	#
	# Get the post's Published Status function
	#
	#####################################################
	public function IsPublished()
	{
		return ( ( $this->data['postStatus'] == 'published' ) ? true : false );
	}
	
	#####################################################
	#
	# Get the post's Status function
	#
	#####################################################
	public function Status()
	{
		return $this->data['postStatus'];
	}
	
	#####################################################
	#
	# Get the post's Cover src set function
	#
	#####################################################
	public function CoverSrc()
	{
		return ( !empty( $this->data['coverSrc'] ) ? (object) $this->data['coverSrc'] : null );
	}

	#####################################################
	#
	# Get the post's comment status function
	#
	#####################################################
	public function HasCommentsEnabled()
	{
		return ( isset( $this->data['commentsEnabled'] ) ? $this->data['commentsEnabled'] : false );
	}
	
	#####################################################
	#
	# Get the post's comment status function
	#
	#####################################################
	public function CommentsHidden()
	{
		return ( isset( $this->data['hideComments'] ) ? $this->data['hideComments'] : false );
	}
	
	#####################################################
	#
	# Get the post's comment ability function
	#
	#####################################################
	public function CanComment()
	{
		return ( isset( $this->data['canComment'] ) ? $this->data['canComment'] : false );
	}
	
	#####################################################
	#
	# Get the post's comment data function
	#
	#####################################################
	public function HasComments()
	{
		return ( $this->data['commentsCount'] > 0 );
	}
		
	#####################################################
	#
	# Get the post's Schemas function
	#
	#####################################################
	public function Schemas()
	{
		return $this->data['schemas'];
	}
	
	#####################################################
	#
	# Get the Groups Data function
	#
	#####################################################
	public function GroupData( $data = 'blog' )
	{
		if ( $data == 'blog' )
		{
			return $this->data['blogGroups'];
		}
		
		if ( $data == 'category' )
		{
			return $this->data['catGroups'];
		}
		
		if ( $data == 'subcategory' )
		{
			return $this->data['subCatGroups'];
		}
		
		return null;
	}
}