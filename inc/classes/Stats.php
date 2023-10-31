<?php defined('TOKICMS') or die('Hacking attempt...');

class Stats
{
	private $data;
	private $comment;
	
	public function __construct( $data )
	{
		$this->data = $data;
		$this->FormatComment();
	}
}