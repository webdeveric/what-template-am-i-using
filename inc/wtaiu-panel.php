<?php

abstract class WTAIU_Panel
{
    const VERSION = '0.0.0';

    protected $label;
    protected $id;
    protected $author;
    protected $author_url;
    protected $default_open_state;

    public function __construct($label = '', $id = '')
    {
        $this->author             = '';
        $this->author_url         = '';
        $this->label              = $label;
        $this->default_open_state = 'open';

        if ($id != '') {
            $this->id = $id;
        } else {
            $this->id = $label != '' ? sanitize_title('panel-' . $label) : uniqid('panel');
        }

        add_action('init', array( &$this, 'setup' ), 11);
    }

    /**
     * Activation tasks go here.
     *
     * @throws Exception
     * @return void
     */
    public function activate()
    {
    }

    /**
     * Deactivation tasks go here.
     *
     * @return void
     */
    public function deactivate()
    {
    }

    public function can_show()
    {
        return true;
    }

    public function get_default_open_state()
    {
        return $this->default_open_state;
    }

    /**
     * Initialization tasks go here.
     *
     * @return void
     */
    public function setup()
    {
    }

    public function get_label()
    {
        return $this->label;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function info()
    {
        return array(
            'author'     => $this->author,
            'author_url' => $this->author_url,
            'version'    => $this->version
        );
    }

    public function render()
    {
        echo $this->get_content();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->get_content();
    }

    /**
     * Get the contents of the Panel.
     *
     * @return string
     */
    abstract public function get_content();

    public function get_help()
    {
        return '';
    }
}

abstract class WTAIU_Debug_Panel extends WTAIU_Panel
{
    public function can_show()
    {
        return defined('WP_DEBUG') && constant('WP_DEBUG') == true;
    }
}
