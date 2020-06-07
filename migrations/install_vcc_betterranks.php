<?php
/**
 *
 * Better Ranks. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, riofriz, https://github.com/riofriz
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vcc\betterranks\migrations;

class install_vcc_betterranks extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return $this->db_tools->sql_column_exists($this->table_prefix . 'user_group', 'show_banner');
    }

    public static function depends_on()
    {
        return array('\phpbb\db\migration\data\v320\v320');
    }

    /**
     * @return array
     */
    public function update_schema()
    {
        return array(
            'add_columns'	=> array(
                $this->table_prefix . 'user_group' => array(
                    'show_banner' => array('UINT', 1),
                    'order' => array('UINT', 1),
                ),
            ),
        );
    }

    /**
     * @return array
     */
    public function revert_schema()
    {
        return array(
            'drop_columns' => array(
                $this->table_prefix . 'user_group' => array(
                    'show_banner',
                    'order',
                ),
            ),
        );
    }
}
