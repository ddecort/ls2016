<?php namespace wildfire\Snippet\Models;

use Instagram\Instagram as InstagramApi;
use Metaphorceps\Instagram\Models\Settings;
use System\Models\File;
use Model;
use Cache;
use Session;

/**
 * Model
 */
class InstagramPost extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Validation
     */
    public $rules = [
    ];

    protected $guarded = [];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    protected $jsonable = ['data'];

    protected $dates = ['date_posted'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'wildfire_snippet_instagram';

    public $attachOne = [ 'image' => 'System\Models\File' ];

    public static function getInstagramAcctOptions()
    {
        return [
            'lizardskinsbaseball' => 'lizardskinsbaseball',
            'lizardskinslacrosse' => 'lizardskinslacrosse',
            'lizardskinscycling'  => 'lizardskinscycling'
        ];
    }

    public function scopeFilterAcct($query,$acct)
    {
        return $query->where('acct',$acct);
    }


    public static function refreshAll()
    {
        foreach (self::getInstagramAcctOptions() AS $user)
        {
            self::refreshOne($user);
        }
    }

    public static function refreshOne($user)
    {
        //return false;
        //set up API connection
        $settings = Settings::instance();
        $api = new InstagramApi();
        $api->setClientID($settings->client_id);
        $ret = false;
        if ($settings->access_token)
        {
            $api->setAccessToken($settings->access_token);
        }

        try {
            $results = $api->getUserByUsername($user)->getMedia( array( 'count' => 50 ));
        } catch( \Instagram\Core\ApiException $e ) {
            return false;
        }

        $idxd = [];
        foreach ($results AS $result)
        {
            $idxd[$result->getId()] = $result;
        }
        $latest_ids = array_keys($idxd);
        $old_ids = InstagramPost::whereIn('ig_id',$latest_ids)->lists('ig_id');
        $new_ids = array_diff($latest_ids, $old_ids);
        foreach ($new_ids AS $new_id)
        {
            $info = $idxd[$new_id];
            $igpost = new InstagramPost();
            $igpost->data = $info->getData();
            $igpost->type = $info->getType();
            $igpost->ig_id = $new_id;
            $igpost->acct = $info->getUser()->getUserName();
            $igpost->date_posted = (int) $info->created_time;
            $tgs = [];
            foreach ($info->getTags() AS $tag)
            {
                $tgs[] = $tag->getName();
            }
            $igpost->tags = implode(' ', $tgs);
            $igpost->link = $info->getLink();
            $igpost->save();

            //download image and attach locally
            $ch = curl_init();
            $source = $info->getStandardResImage()->url;
            curl_setopt($ch, CURLOPT_URL, $source);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec ($ch);
            curl_close ($ch);

            $name = explode('?', $source);
            $name = $name[0];
            $name = explode('.',$name);
            $name = array_pop($name);
            $destination = "/tmp/".$new_id.".".$name;
            $file = fopen($destination, "w+");
            fputs($file, $data);
            fclose($file);

            $file = new File;
            $file->data = $destination;
            $file->save();
            $igpost->image()->add($file);
            $igpost->save();
        }

    }

    public static function getPosts($account = null, $hashtag = null, $id = null)
    {
        if ($account) 
        {
            //find the latest posts
            Cache::remember('ig_user_'.$account, 30, function() use ($account){
                InstagramPost::refreshOne($account);
                return true;
            });
        }

        $key = $account.$hashtag.$id;
        $posts = Cache::remember('ig_'.$key, 30, function() use ($account,$hashtag,$id){
            $out = [];
            if ($id)
            {
                $posts = [ InstagramPost::find($id) ];
            }
            else if ($account)
            {
                $query = InstagramPost::where('acct',$account);
                if ($hashtag)
                {
                    $query->where('tags', 'like', '%'.strtolower(str_replace('#','',$hashtag)).'%');
                }

                $posts = $query->orderBy('date_posted','desc')->take(10)->get();
            }
            foreach ($posts AS $post)
            {
                if (!$post->blacklisted)
                {
                    $out[$post->id] = $post->toArray();
                }
            }

            return $out;
        });

        return $posts;
    }

    public static function pickUnusedPost($posts)
    {
        $used = Session::get('used_instagram_ids',[]);
        if (count($posts) > 0)
        {
            $avail_keys = array_keys($posts);
            $can_use_keys = array_diff($avail_keys,$used);

            if (count($can_use_keys) == 0)
            {
                $used = [];
                $can_use_keys = $avail_keys;
            }

            $rand = array_rand($can_use_keys);
            $post_id = $can_use_keys[$rand];

            $used[] = $post_id;
            Session::put('used_instagram_ids',$used);

            $post = $posts[$post_id];
            $post['data'] = json_decode($post['data']);
            return InstagramPost::make($post);
        }
    }
}
