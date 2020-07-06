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

use phpbb\config\config;
use phpbb\template\template;
use phpbb\language\language;
use phpbb\db\driver\driver_interface as db_interface;
use phpbb\user;
use phpbb\request\request_interface;

/**
 * Better Ranks UCP controller.
 */
class ucp_banners_controller
{
	/** @var config */
	protected $config;

	/** @var template */
	protected $template;

	/** @var language */
	protected $language;

	/** @var db_interface */
	protected $db;

	/** @var user */
	protected $user;

	/** @var request_interfacet	*/
	protected $request;

	/** @var string */
	protected $user_group_table;

	/** @var string */
	protected $users_table;

	/** @var string */
	protected $groups_table;

	/** @var string */
	protected $ranks_table;

		/** @var string Custom form action */
	protected $u_action;

	/**
	 * Constructor
	 *
	 * @param config				$config
	 * @param template				$template
	 * @param language				$language
	 * @param db_interface			$db Database,
	 * @param user					$user
	 * @param request_interface		$request
	 * @param string				$user_group_table
	 * @param string				$users_table
	 * @param string				$groups_table
	 * @param string				$ranks_table
	 */
	public function __construct(
		config $config,
		template $template,
		language $language,
		db_interface $db,
		user $user,
		request_interface $request,
		$user_group_table,
		$users_table,
		$groups_table,
		$ranks_table
	)
	{
		$this->config			= $config;
		$this->template			= $template;
		$this->language			= $language;
		$this->db				= $db;
		$this->user 			= $user;
		$this->request 			= $request;
		$this->user_group_table = $user_group_table;
		$this->users_table 		= $users_table;
		$this->groups_table 	= $groups_table;
		$this->ranks_table 		= $ranks_table;
	}

	public function edit_banners()
	{
		$this->template->assign_vars([
			'U_UCP_ACTION'	=> $this->u_action,
		]);

		// Create a form key for preventing CSRF attacks
		add_form_key('vcc_betterranks_ucp');

		$user_id = (int) $this->user->data['user_id'];

		$this->get_user_banners();
	}

	public function get_user_banners()
	{
		$query = 'SELECT *
			FROM ' . $this->user_group_table . ' ug
			LEFT JOIN '. $this->users_table . ' u
				ON u.user_id = ug.user_id
			LEFT JOIN ' . $this->groups_table . ' g
				ON ug.group_id = g.group_id
			LEFT JOIN ' . $this->ranks_table . ' r
				ON r.rank_id = g.group_rank
			WHERE g.group_rank != 0
			AND ug.user_id = ' . $this->user->data['user_id'] . '
			ORDER BY ug.order ASC';
		$result = $this->db->sql_query($query);
		$rows = $this->db->sql_fetchrowset($result);
		$counter = 0;

		foreach($rows as $row)
		{
			$this->template->assign_block_vars('available_banners', [
				'RANK_IMAGE' 		=> generate_board_url() . "/images/ranks/" . $row['rank_image'],
				'GROUP_NAME' 		=> $row['group_name'],
				'GROUP_ID'	 		=> $row['group_id'],
				'ORDER' 			=> $row['order'],
				'NO_DUPLICATED_ID' 	=> $counter,
				'ACTIVE' 			=> $row['show_banner']
			]);

			$counter++;
		}

		$this->db->sql_freeresult($result);

		$this->submit_banners();
	}

	public function submit_banners()
	{
		if ($this->request->is_set_post('submitgroup'))
		{
			// Test if the submitted form is valid
			if (!check_form_key('vcc_betterranks_ucp'))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			// If no errors, process the form data
			if (empty($errors))
			{
				// Option settings have been updated
				// Confirm this to the user and provide (automated) link back to previous page
				meta_refresh(3, $this->u_action);

				$groupID = $this->request->variable('groupid', '', true);
				$order = $this->request->variable('order', '', true);
				$active = $this->request->variable('active', '', true);

				$check_value = $active ? 1 : 0;

				$updateBanners = 'UPDATE phpbb_user_group
									SET `order` = "' . $order . '", show_banner = ' . $check_value . '
									WHERE group_id = ' . $groupID . '
									AND user_id = ' . $this->user->data['user_id'];
				$this->db->sql_query($updateBanners);

				$message = $this->language->lang('UCP_BETTERRANKS_SAVED') . '<br /><br />' . $this->language->lang('RETURN_UCP', '<a href="' . $this->u_action . '">', '</a>');
				trigger_error($message);
			}
		}
	}

	/**
	 * Set custom form action.
	 *
	 * @param string	$u_action	Custom form action
	 * @return void
	 */
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
