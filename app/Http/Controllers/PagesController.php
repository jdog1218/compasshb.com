<?php

namespace CompassHB\Www\Http\Controllers;

use Log;
use Cache;
use GuzzleHttp\Client;
use CompassHB\Www\Song;
use CompassHB\Www\Series;
use CompassHB\Www\Sermon;
use CompassHB\Www\Contracts\Photos;
use CompassHB\Www\Contracts\Video;

class PagesController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Controller for the homepage.
     *
     * @param Photos $photos
     * @param Video $videoClient
     * @param Events $event
     * @return view
     */
    public function home(Photos $photos, Video $videoClient)
    {
        // Scripture of the Day Logo
        $client = new Client();

        $body = $client->get('https://api.compasshb.com/wp-json/compasshb/v1/site_logo/8')->getBody();
        $scripture_logo = json_decode($body);
        $scripture_logo = $scripture_logo[0];

        // Featured Events
        $body = $client->get('https://api.compasshb.com/wp-json/wp/v2/tribe_events', [
            'query' => [
                '_embed' => true,
                'tribe_events_cat' => 10
            ]
        ])->getBody();

        $featuredevents = json_decode($body);
        $featuredevents = array_reverse($featuredevents);

        // get four sermons
        $body = $client->get('https://api.compasshb.com/wp-json/wp/v2/posts', [
            'query' => [
                '_embed' => true,
                'categories' => 1,
                'per_page' => 4
            ]
        ])->getBody();

        $sermons = json_decode($body);

        // get two videos
        $body = $client->get('https://api.compasshb.com/wp-json/wp/v2/posts', [
            'query' => [
                '_embed' => true,
                'categories' => 12,
                'per_page' => 4
            ]
        ])->getBody();

        $videos = json_decode($body);

        // get single passages
        $body = $client->get('https://api.compasshb.com/reading/wp-json/wp/v2/posts?embed', [
            'query' => [
                '_embed' => true
            ]
        ])->getBody();

        $passage = json_decode($body);
        $passage = $passage[0];

        /*
         * Instagram
         * @todo Move caching out of controller
         */
        $url = 'https://api.instagram.com/v1/users/1363574956/media/recent/?count=4&client_id='.
            env('INSTAGRAM_CLIENT_ID');
        try {
            $instagrams = Cache::remember('instagrams', '180', function () use ($url) {
                return json_decode(file_get_contents($url), true);
            });
        } catch (\Exception $e) {
            Log::warning('Connection refused to api.instagram.com');
            $instagrams['data'] = [];
        }

        /*Pages
         * Smugmug
         */
        $results = $photos->getPhotos(8);

        $broadcast = Cache::get('broadcast');

        return view('pages.index', compact(
            'broadcast',
            'sermons',
            'featuredevents',
            'videos',
            'passage',
            'scripture_logo'
        ))->with('images', $results)
            ->with('instagrams', $instagrams['data'])
            ->with('title', 'Compass HB - Huntington Beach');
    }

    /**
     * Populate the Photos page from Photo Client.
     *
     * @param Photos $photos
     * @return \Illuminate\View\View
     */
    public function photos(Photos $photos)
    {
        $results = $photos->getRecentPhotos();

        return view('pages.photos')
            ->with('title', 'Photography')
            ->with('photos', $results);
    }

    public function pray()
    {
        return view('dashboard.pray.index')
            ->with('title', 'Pray');
    }

    public function whoweare()
    {
                $client = new Client();

        $body = $client->get('https://api.compasshb.com/wp-json/wp/v2/pages?slug=who-we-are')->getBody();

        $content = json_decode($body);
        $content = $content[0]->content->rendered;

        return view('pages.whoweare', compact('content'))
            ->with('title', 'Who We Are');
    }

    public function eightdistinctives()
    {
            $client = new Client();

        $body = $client->get('https://api.compasshb.com/wp-json/wp/v2/pages?slug=8-distinctives')->getBody();

        $content = json_decode($body);
        $content = $content[0]->content->rendered;

        return view('pages.eightdistinctives', compact('content'))
            ->with('title', '8 Distinctives');
    }
    
    public function sotd()
    {
        $client = new Client();

        $body = $client->get('https://api.compasshb.com/wp-json/wp/v2/pages?slug=sotd')->getBody();

        $content = json_decode($body);
        $content = $content[0]->content->rendered;

        return view('pages.sotd', compact('content'))
            ->with('title', 'Scripture of the Day (SOTD)');
    }

    public function give()
    {
        return redirect('giving');
    }

    public function giving()
    {
           return redirect('https://pushpay.com/pay/compasshb/');
    }

    public function icecreamevangelism()
    {
        $client = new Client();

        $body = $client->get('https://api.compasshb.com/wp-json/wp/v2/pages?slug=ice-cream-evangelism')->getBody();

        $content = json_decode($body);
        $content = $content[0]->content->rendered;

        return view('pages.icecreamevangelism', compact('content'))
            ->with('title', 'Ice Cream Evangelism');
    }

    public function whatwebelieve()
    {
                $client = new Client();

        $body = $client->get('https://api.compasshb.com/wp-json/wp/v2/pages?slug=what-we-believe')->getBody();

        $content = json_decode($body);
        $content = $content[0]->content->rendered;

        return view('pages.whatwebelieve', compact('content'))
            ->with('title', 'What We Believe');
    }


    public function goodytwoshoes()
    {
        return view('pages.goodytwoshoes')
            ->with('title', 'Goody Two Shoes');
    }

    public function manifest()
    {
        return view('feeds.manifest');
    }

    public function bunnyrun()
    {
        return redirect('https://www.compasshb.com/events/bunny-run/');
    }

    public function greatawakening()
    {
        $client = new Client();
        $body = $client->get('https://api.compasshb.com/wp-json/wp/v2/pages', [
            'query' => ['slug' => 'greatawakening']
        ])->getBody();

        $page = json_decode($body);
        $page = $page[0];
        
        return view('dashboard.landing.show', compact('page'));
    }

    public function resurrectionweek()
    {
        return redirect('https://www.compasshb.com/events/22662635553/resurrection-week/');
    }

    public function sitemap(Video $video)
    {
        $blogs = [];
        $sermons = [];
        $passages = [];
        $series = [];
        $songs = [];
        $events = [];
        $fellowships = [];

        return response()
            ->view('pages.sitemap', compact('sermons', 'passages', 'series', 'songs', 'events', 'fellowships', 'blogs'))
            ->header('Content-Type', 'application/xml');
    }

    public function eventsindex()
    {

        $client = new Client();

         $body = $client->get('https://api.compasshb.com/wp-json/wp/v2/tribe_events', [
                'query' => [
                    '_embed' => true
                ]
            ])->getBody();

            $events = json_decode($body);
            $events = array_reverse($events);


            return view('dashboard.events.index', compact('events'));
    }

    public function eventsshow($event) {

        $client = new Client();
            $body = $client->get('https://api.compasshb.com/wp-json/wp/v2/tribe_events', [
                'query' => [
                    '_embed' => true,
                    'slug' => $event,

                ]
            ])->getBody();

            $events = json_decode($body);

            // Handle 404 if event does not exist in API
            if (empty($event))
            {
                abort(404);
            } else {
                $events = $events[0];
            }

            return view('dashboard.events.show', compact('events'));

        }
    

    public function search()
    {
        return redirect()->route('home');
    }

    public function admin()
    {
        return redirect('https://api.compasshb.com/wp-admin/');
    }


    /**
     * Clear the video cache when video management system sends a webhook callback.
     * @param $auth
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function clearvideothumbcache($auth)
    {
        if ($auth == env('EVENTBRITE_CALLBACK')) {
            $latestsermon = Sermon::where('ministryId', '=', null)->latest('published_at')->published()->get()->first();
            Cache::forget($latestsermon->video);
        }

        return redirect(
            'https://developers.facebook.com/tools/debug/og/object?q=https://www.compasshb.com/sermons/'.
            $latestsermon->alias
        );
    }
}
