<?php
/**
 *
 * Better Ranks. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, https://github.com/riofriz
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vcc\betterranks\controller;

use phpbb\json_response;
use phpbb\user;

/**
 * Better Ranks UCP controller.
 */
class ucp_banners_controller
{
    /** @var \phpbb\config\config */
    protected $config;

    /** @var \phpbb\controller\helper */
    protected $helper;

    /** @var \phpbb\template\template */
    protected $template;

    /** @var \phpbb\language\language */
    protected $language;

    /** @var \phpbb\auth\auth */
    protected $auth;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /** @var \phpbb\user */
    protected $user;

    /** @var \phpbb\request\request	*/
    protected $request;

    /** @var string Custom form action */
    protected $u_action;


    /**
     * Constructor
     *
     * @param \phpbb\config\config		$config		Config object
     * @param \phpbb\controller\helper	$helper		Controller helper object
     * @param \phpbb\template\template	$template	Template object
     * @param \phpbb\language\language	$language	Language object
     * @param \phpbb\auth\auth          $auth
     * @param \phpbb\db\driver\driver_interface	$db Database,
     * @param \phpbb\user               $user User
     * @param \phpbb\request\request 	$request phpBB request
     */
    public function __construct(
        \phpbb\config\config $config,
        \phpbb\controller\helper $helper,
        \phpbb\template\template $template,
        \phpbb\language\language $language,
        \phpbb\auth\auth $auth,
        \phpbb\db\driver\driver_interface $db,
        \phpbb\user $user,
        \phpbb\request\request $request
    ){
        $this->config	= $config;
        $this->helper	= $helper;
        $this->template	= $template;
        $this->language	= $language;
        $this->auth = $auth;
        $this->db   = $db;
        $this->user = $user;
        $this->request = $request;
    }

	public function edit_banners()
	{
		$user_id = (int) $this->user->data['user_id'];
		var_dump('yeah!');
	}
}
