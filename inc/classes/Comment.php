<?php defined('TOKICMS') or die('Hacking attempt...');

class Comment
{
	private $data;
	private $comment;
	
	public function __construct( $data )
	{
		$this->data = $data;
		$this->FormatComment();
	}
	
	public function Email()
	{
		return $this->comment['email'];
	}
	
	public function HtmlUrl()
	{
		return $this->comment['htmlUrl'];
	}

	public function Name()
	{
		return $this->comment['name'];
	}
	
	public function Ip()
	{
		return $this->comment['ip'];
	}
	
	public function Comment()
	{
		return $this->comment['comment'];
	}
	
	public function Date( $type = null )
	{
		switch( $type )
		{
			case 'c':
				$date = $this->comment['cTime'];
			break;
			
			case 'raw':
				$date = $this->comment['dateRaw'];
			break;
			
			case 'r':
				$date = $this->comment['rTime'];
			break;
			
			case 'nice':
				$date = $this->comment['niceTime'];
			break;
			
			default:
				$date = $this->comment['date'];
		}
		
		return $date;
	}
		
	public function Url()
	{
		return $this->comment['url'];
	}

	private function FormatComment()
	{
		$this->comment = array(
					'name' => stripslashes( $this->data['name'] ),
					'email' => stripslashes( $this->data['email'] ),
					'comment' => stripslashes( $this->data['comment'] ),
					'dateRaw' => $this->data['added_time'],
					'date' => postDate ( $this->data['added_time'] ),
					'niceTime' => niceTime ( $this->data['added_time'] ),
					'rTime' => date ( 'r', $this->data['added_time'] ),
					'cTime' => postDate ( $this->data['added_time'], true ),
					'ip' => stripslashes( $this->data['ip'] ),
					'url' => stripslashes( $this->data['url'] ),
					'htmlUrl' => ( !empty( $this->data['url'] ) ? '<a href=\'' . $this->data['url'] . '\' rel=\'external nofollow\' class=\'url\'>' . stripslashes( $this->data['name'] ) . '</a>' : null )
				);
	}
}