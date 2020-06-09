<?php

/**
 *
 * riofriz: Better Ranks (vcc)
 * phpBB3.2 Extension Package
 *
 */

namespace vcc\betterranks\event;

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\event\data;
use phpbb\path_helper;
use phpbb\template\template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event Listener
 */
class listener implements EventSubscriberInterface
{
    /** @var template */
    protected $template;

    /** @var config */
    protected $config;

    /** @var driver_interface */
    protected $db;

    /** @var path_helper */
    protected $path_helper;

    /** @var array */
    private $users_extra_rank_template_data;

    /**
     * Constructor
     *
     * @param template			$template
     * @param config			$config
     * @param driver_interface	$db
     * @param path_helper		$path_helper
     * @access public
     */
    public function __construct(
        template $template,
        config $config,
        driver_interface $db,
        path_helper $path_helper
    )
    {
        $this->template		= $template;
        $this->config		= $config;
        $this->db			= $db;
        $this->path_helper	= $path_helper;
    }

    /**
     * Helper method to return the rank template data for a single user
     *
     * @param int $user_id The ID of the user to fetch the rank template data
     * @param int $user_posts The user's number of posts
     * @return array
     */
    protected function get_extra_rank_template_data($user_id, $user_posts)
    {
        $template_data = $this->get_extra_ranks_template_data($user_id, 'profile');

        return $template_data[$user_id];
    }

    /**
     * @param data $event
     */
    public function viewtopic_fetch($event)
    {
        $user_posts = [];

        foreach ($event['rowset'] as $post_row)
        {
            $user_id = $post_row['user_id'];
            $user_posts[$user_id] = $user_id;
        }

        $this->users_extra_rank_template_data = $this->get_extra_ranks_template_data($user_posts, 'posts');
    }

    /**
     * @param data $event
     */
    public function viewprofile($event)
    {
        $user_id = $event['member']['user_id'];
        $user_posts = $event['member']['user_posts'];
        $extra_rank_template_data = $this->get_extra_rank_template_data($user_id,  $user_posts);
        $this->template->assign_vars((array)$extra_rank_template_data);
    }

    /**
     * @param data $event
     */
    public function viewpm($event)
    {
        $user_id = $event['user_info']['user_id'];
        $user_posts = $event['user_info']['user_posts'];
        $extra_rank_template_data = $this->get_extra_rank_template_data($user_id, $user_posts);
        if ($extra_rank_template_data == NULL) { $extra_rank_template_data = []; }
        $this->template->assign_vars($extra_rank_template_data);
    }

    /**
     * @param data $event
     */
    public function viewtopic_assign($event)
    {
        $poster_id = $event['poster_id'];
        $extra_rank_template_data = $this->users_extra_rank_template_data[$poster_id];
        $event['post_row'] = array_merge($event['post_row'], (array)$extra_rank_template_data);
    }

    public static function getSubscribedEvents()
    {
        return [
            'core.memberlist_view_profile'		=> 'viewprofile',
            'core.viewtopic_modify_post_data'	=> 'viewtopic_fetch',
            'core.viewtopic_modify_post_row'	=> 'viewtopic_assign',
            'core.ucp_pm_view_messsage'			=> 'viewpm',
        ];
    }

    /**
     * Generates the rank template data for mutiple users
     *
     * @param array $user_posts, mapping from user_id to user_posts
     * @return array mapping from user_id to the array of rank template data
     */
    protected function get_extra_ranks_template_data($user_posts, $location = '')
    {
        $user_special_ranks = $this->get_users_special_ranks($user_posts, $location);

        $rankString = [];
        $userGroups = [];

        foreach($user_special_ranks as $user) {
            $niddle = $user['user_id'];


            if ($user['user_rank'] !== $user['group_rank']) {
                if ($user['rank_image'] !== '') {
                    $rankString[$niddle] = $rankString[$niddle] . '<img data-rankid="'. $user['group_rank'] .'" class="special-rank" src="/images/ranks/' . $user['rank_image'] . '" />';
                }
            }

            $userGroups[$niddle] = $userGroups[$niddle] . '<a style="color: #'.$user["group_colour"].'" href="memberlist.php?mode=group&g='. $user['group_id'].'">' . $user['group_name'] . '</a>';


            $template_data[$niddle] = [
                'GROUP'  =>  $rankString[$niddle],
                'GROUP_LIST' => $userGroups[$niddle]
            ];
        }

        return $template_data;
    }

    /**
     * Grabs the rank_special flag for all passed user IDs
     *
     * @param array $user_ids
     * @return array mapping from user_id to rank_special
     */
    protected function get_users_special_ranks($user_ids, $location = '')
    {
        $actualUsers = '';

        if ($location === 'posts') {
            foreach ($user_ids as $id) {
                $actualUsers .= (!next($user_ids)) ? $id : $id . ',';
            }
        } else {
            $actualUsers = $user_ids;
        }

        $sql = "SELECT phpbb_user_group.group_id, phpbb_user_group.order, phpbb_user_group.show_banner, phpbb_groups.group_name, phpbb_groups.group_colour, phpbb_users.user_rank, phpbb_groups.group_rank, phpbb_ranks.rank_image, phpbb_user_group.user_id FROM phpbb_user_group LEFT JOIN phpbb_users ON phpbb_users.user_id = phpbb_user_group.user_id LEFT JOIN phpbb_groups ON phpbb_user_group.group_id = phpbb_groups.group_id LEFT JOIN phpbb_ranks ON phpbb_ranks.rank_id = phpbb_groups.group_rank WHERE phpbb_groups.group_rank != 0 AND phpbb_user_group.user_id IN($actualUsers) AND phpbb_user_group.show_banner != 0 ORDER BY phpbb_user_group.order ASC";
        $result = $this->db->sql_query($sql);
        $rows = $this->db->sql_fetchrowset($result);
        $this->db->sql_freeresult($result);


        return $rows;
    }
}