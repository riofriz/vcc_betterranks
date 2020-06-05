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
class banner_info
{
    public function module()
    {
        return array(
            'filename'	=> '\vcc\betterranks\ucp\banner_module',
            'title'		=> 'UCP_FLAIR',
            'modes'		=> array(
                'main'	=> array(
                    'title'	=> 'UCP_FLAIR',
                    'auth'	=> 'vcc/betterranks',
                    'cat'	=> array('UCP_FLAIR'),
                ),
            ),
        );
    }
}
