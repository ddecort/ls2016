<?php namespace wildfire\Snippet\Components;

use Request;
use Cms\Classes\ComponentBase;
use wildfire\Snippet\Models\InstagramPost;
use wildfire\Snippet\Models\Page as Pagemodel;

class SubscribeContext extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Contextual Mailchimp Subscribe', 
            'description' => 'Signup Mailchimp form based on current sport'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        list($section, $sport) = Pagemodel::findSectionAndSport(Request::path(), $this->page, true);

        if ($sport == 'lacrosse')
        {
            $this->page['formaction'] = '//lizardskins.us13.list-manage.com/subscribe/post?u=ef1d1a91bff0d4a656060d629&amp;id=354b24e663'; 
            $this->page['formkey'] = 'b_ef1d1a91bff0d4a656060d629_354b24e663';
        }
        else if ($sport == 'baseball' || $section == 'sports')
        {
            $this->page['formaction'] = '//lizardskins.us13.list-manage.com/subscribe/post?u=ef1d1a91bff0d4a656060d629&amp;id=8fb4df5708';
            $this->page['formkey'] = 'b_ef1d1a91bff0d4a656060d629_8fb4df5708';
        }
        else
        {
            //cycling
            $this->page['formaction'] = '//lizardskins.us2.list-manage.com/subscribe/post?u=ae08e61d3f110e1ac944c8573&amp;id=93b7c6df1c';
            $this->page['formkey'] = 'b_ae08e61d3f110e1ac944c8573_93b7c6df1c';
        }
    }
}
