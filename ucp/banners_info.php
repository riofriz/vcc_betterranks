<?php
/**
 *
 * Better Ranks.
 *
 * @copyright (c) 2020, https://github.com/riofriz
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vcc\betterranks\ucp;

/**
 * Better Ranks UCP module info.
 */
class banners_info
{
    public function module()
    {
        return array(
            'filename'	=> '\vcc\betterranks\ucp\banners_module',
            'title'		=> 'UCP_BANNERS',
            'modes'		=> array(
                'main'	=> array(
                    'title'	=> 'UCP_BANNERS',
                    'auth'	=> 'ext_vcc/betterranks',
                    'cat'	=> array('UCP_PROFILE'),
                ),
            ),
        );
    }
}
