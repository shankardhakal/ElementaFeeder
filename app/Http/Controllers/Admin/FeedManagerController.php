<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feed;
use App\Models\Network;

class FeedManagerController extends Controller
{
    /**
     * Show the custom, multi-tabbed management page for a single feed.
     */
    public function manage(int $id)
    {
        $feed = Feed::with(['productType', 'network', 'websites', 'transformationRules'])->findOrFail($id);

        $breadcrumbs = [
            'Admin' => backpack_url('dashboard'),
            'Networks' => backpack_url('network'),
        ];

        if ($feed->network) {
             $breadcrumbs[$feed->network->name] = backpack_url('network/' . $feed->network_id . '/show');
             $breadcrumbs['Feeds'] = backpack_url('network/' . $feed->network_id . '/show');
        } else {
            $breadcrumbs['Feeds'] = backpack_url('feed'); // Fallback if no network
        }
       
        $breadcrumbs[$feed->name] = false;
        $breadcrumbs['Manage'] = false;


        return view('admin.feeds.manage', [ // This points to the view the command created
            'title' => 'Manage Feed: ' . $feed->name,
            'breadcrumbs' => $breadcrumbs,
            'feed' => $feed,
        ]);
    }
}