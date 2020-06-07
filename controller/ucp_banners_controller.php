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
        $this->template->assign_vars(array(
            'U_UCP_ACTION'	=> $this->u_action,
        ));

        // Create a form key for preventing CSRF attacks
        add_form_key('vcc_betterranks_ucp');

		$user_id = (int) $this->user->data['user_id'];

		$this->get_user_banners();
	}

	public function get_user_banners()
    {
        $query = 'SELECT phpbb_user_group.order, phpbb_user_group.show_banner, phpbb_user_group.group_id, phpbb_groups.group_name, phpbb_groups.group_colour, phpbb_users.user_rank, phpbb_groups.group_rank, phpbb_ranks.rank_image, phpbb_user_group.user_id FROM phpbb_user_group LEFT JOIN phpbb_users ON phpbb_users.user_id = phpbb_user_group.user_id LEFT JOIN phpbb_groups ON phpbb_user_group.group_id = phpbb_groups.group_id LEFT JOIN phpbb_ranks ON phpbb_ranks.rank_id = phpbb_groups.group_rank WHERE phpbb_groups.group_rank != 0 AND phpbb_user_group.user_id = '.$this->user->data['user_id'].' ORDER BY phpbb_user_group.order ASC';
        $result = $this->db->sql_query($query);
        $rows = $this->db->sql_fetchrowset($result);
        $counter = 0;

        foreach($rows as $row)
        {
            $this->template->assign_block_vars('available_banners', array(
                'RANK_IMAGE' => generate_board_url()."/images/ranks/".$row['rank_image'],
                'GROUP_NAME' => $row['group_name'],
                'GROUP_ID' => $row['group_id'],
                'ORDER' => $row['order'],
                'NO_DUPLICATED_ID' => $counter,
                'ACTIVE' => $row['show_banner']
            ));

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

                $updateBanners = 'UPDATE phpbb_user_group SET `order` = "'.$order.'", show_banner = '.$check_value.' WHERE group_id = '.$groupID.' AND user_id = '.$this->user->data['user_id'];
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
