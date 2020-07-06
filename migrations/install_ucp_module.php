<?php
/**
 *
 * Cash Features. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vcc\betterranks\migrations;

class install_ucp_module extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		$sql = 'SELECT module_id
			FROM ' . $this->table_prefix . "modules
			WHERE module_class = 'ucp'
				AND module_basename = '\vcc\betterranks\ucp\banners_module'";
		$result = $this->db->sql_query($sql);
		$module_id = $this->db->sql_fetchfield('module_id');
		$this->db->sql_freeresult($result);

		return $module_id !== false;
	}

	public static function depends_on()
	{
		return [
			'\vcc\betterranks\migrations\install_vcc_betterranks'
		];
	}

	/**
	 * @return array
	 */
	public function update_data()
	{
		return [
			['module.add', [
				'ucp',
				'UCP_PROFILE',
				[
					'module_basename'	=> '\vcc\betterranks\ucp\banners_module',
					'module_auth'		=> 'ext_vcc/betterranks',
					'modes'				=> ['main'],
				],
			]],
		];
	}
}
