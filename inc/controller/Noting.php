<?php defined('TOKICMS') or die('Hacking attempt...');

class Noting extends Controller {
	public function process() {
		$AuthUser = $this->getVariable("AuthUser");
		$this->setVariable( 'Lang', $this->lang );
		$this->setVariable( 'Listings', null );
		$this->view();
	}
}
