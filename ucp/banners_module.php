<?php
/**
 *
 * Better Ranks. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020 riofriz, https://github.com/riofriz
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vcc\betterranks\ucp;

/**
 * Better Ranks UCP module.
 */
class banners_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	public function main($id, $mode)
	{
		global $phpbb_container;
		$controller = $phpbb_container->get('vcc.betterranks.controller.ucp.banners');

		$this->tpl_name = 'ucp_banners';
		$this->page_title = 'UCP_BANNERS';

		$controller->set_page_url($this->u_action);
		$controller->edit_banners();
	}
}
