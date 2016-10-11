<?php namespace wildfire\Snippet\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Flash;
use Cache;
use wildfire\Snippet\Models\InstagramPost;

class Instagram extends Controller
{
    public $implement = ['Backend\Behaviors\ListController'];
    
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('wildfire.Snippet', 'main-menu-item', 'instagrams');
        trace_sql();

    }

    public function index_onImport()
    {
        $found = InstagramPost::refreshAll();
        if ($found == 0 )
        {
            Flash::warning('Nothing new to import');
        }
        else
        {
            Flash::success('Manually triggered refresh found '. $found.' new posts');
            return $this->listRefresh();
        }

    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $postId) {
                if ((!$post = InstagramPost::find($postId)))
                    continue;

                $post->delete();
            }

            Cache::flush();
            Flash::success('Successfully deleted those posts.');
        }

        return $this->listRefresh();
    }

    public function index_onBlacklist()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $postId) {
                if ((!$post = InstagramPost::find($postId)))
                    continue;

                $post->blacklisted = true;
                $post->save();
            }

            Cache::flush();
            Flash::success('Successfully added posts to blacklist.');
        }

        return $this->listRefresh();
    }

    public function index_onUnblacklist()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $postId) {
                if ((!$post = InstagramPost::find($postId)))
                    continue;

                $post->blacklisted = false;
                $post->save();
            }

            Cache::flush();
            Flash::success('Successfully removed selected posts from blacklist.');
        }

        return $this->listRefresh();
    }


}
