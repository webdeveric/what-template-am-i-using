<?php

class WTAIU_Panel {
	
	protected $label;
	protected $id;

	protected $author;
	protected $author_url;
	protected $version;

	public function __construct( $label = '', $id = '' ){

		$this->author		= '';
		$this->author_url	= '';
		$this->version		= '';

		$this->label = $label;

		if( $id != '' ){
			$this->id = $id;
		} else {
			$this->id = $label != '' ? sanitize_title( 'panel-' . $label ) : uniqid('panel');
		}

		add_action('init', array( &$this, 'setup' ), 11 );

	}

	public function setup(){
		// do stuff here with actions
	}

	public function get_content(){
		// This is the content that you want to show in the panel.
		return '';
	}

	public function get_label(){
		return $this->label;
	}

	public function get_id(){
		return $this->id;
	}

	public function info(){
		return array(
			'author'		=> $this->author,
			'author_url'	=> $this->author_url,
			'version'		=> $this->version
		);
	}

	public function render(){
		echo $this->get_content();
	}

	public function __toString(){
		return $this->get_content();
	}

}