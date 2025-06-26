{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('dashboard') }}">
        <i class="la la-tachometer nav-icon"></i> {{ trans('backpack::base.dashboard') }}
    </a>
</li>

<x-backpack::menu-item title="Product types" icon="la la-cogs" :link="backpack_url('product-type')" />
<x-backpack::menu-item title="Destination platforms" icon="la la-globe" :link="backpack_url('destination-platform')" />
<x-backpack::menu-item title="Networks" icon="la la-network-wired" :link="backpack_url('network')" />
<x-backpack::menu-item title="Feeds" icon="la la-rss" :link="backpack_url('feed')" />
<x-backpack::menu-item title="Websites" icon="la la-sitemap" :link="backpack_url('website')" />
<x-backpack::menu-item title="Transformation rules" icon="la la-exchange-alt" :link="backpack_url('transformation-rule')" />
<x-backpack::menu-item title="Manage" icon="la la-cogs" :link="backpack_url('manage')" />
