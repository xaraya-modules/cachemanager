
## Overview

### What is it?

The CacheManager module provides an administrative interface to
control the settings of the xarCache system of Xaraya. It also provides
hooks that allow the cache system to know when changes have been made to
modules so that it can respond accordingly (event based cache
expiration/invalidation).

The output cache system is designed to reduce the amount of work a
server system has to do under heavy load conditions. It saves the output
used by Xaraya so that it does not need to go through the entire process
to reproduce the same output over and over again. At this point, only
page level output caching for anonymous users and block level output
caching for all users are available. In time, the output cache system
will also support module level output caching for all users, as well as
more efficient serving of cached pages for anonymous users. Page level
output caching for certain user groups is also planned to be available
to sites that can take advantage of this.

Please do not confuse output caching with Xaraya's variable or template
caching systems. Each are different. In time, they will likely use the
same caching infrastructure, but will still provide different caching
for different purposes. General data caching is also planned for
xarCache. For more on the different points of caching in Xaraya, please
refer to: <http://xaraya.com/~jsb/cachingPoints.html>

### How to use it?

The CacheManager module has several tools for managing the xarCache
system and your cached output files. The tools are organized under the
following menu links:

  - Flush Cache - allows you to flush (delete) the output cache of
    specific Cache Keys. Page output Cache Keys are define by module,
    type, and function. Block output Cache Keys are define by module,
    block instance id, and block group.
  - Page Caching - allows you to enable page caching for certain user
    groups, set up session-less page caching, and configure auto-cache
    settings. Session-less page caching allows cached pages to be served
    before loading xarCore to users who do not have a session
    (new/first-time visitors). Auto-cache allows you to configure your
    site to dynamically enable session-less page caching for pages that
    hit certain page view thresholds.
  - Block Caching - provides a single view and management point of the
    output caching settings of all block instances.
  - View Statistics - shows a summary with the cache hit ratio and size
    for the different cache types, and the details on how the output
    cache system is performing.
  - Modify Config - allows you to set the values for the output cache
    system. Mouse over the setting titles for more information.

### Included Blocks

The CacheManager Module has no blocks included at this time.

### Included Hooks

The CacheManager module provides create, update, delete and modify
config API hooks and a modify GUI hook (currently only used for adding
caching configuration administration to block instance modify pages). To
activate these hooks, go to Admin panel -\> Modules -\> Configure Hooks
and click on the cachemanager link. Select the modules you would like
to enable the CacheManager hook functionality for (articles,
categories, autolinks, blocks, html are likely candidates) and click the
Save Changes button. Once hooked to CacheManager, when you create,
update, or delete items of the module, or modify the configuration of a
module, the call to the CacheManager hooks will flush the appropriate
cache to insure that your changes will be instantly available on your
site (not masked by cached output files).

### Additional Information

Please note that page level output caching is not appropriate for sites
that require the serving of dynamic content to anonymous users. Example:
if each page contains data that changes every second and must be
presented to the anonymous viewer in the latest state, you do not want
to use page level output caching. However, if it is acceptable to
present the data with changes no older than 1 minute, you can enable
page level output caching and set the Cache Expiration Time to 60
seconds. If your site serves the same page 100 times per minute this
will measurably reduce the load on your systems to do so.

Page level output caching may also affect the expected behavior of
certain aspects of Xaraya as cached pages are served without loading the
modules. Example: the stats and opentracker modules will not accurately
reflect the total number of page views your site receives because they
are not invoked when serving cached pages. If your site is under heavy
load that needs to be reduced, you may want to consider doing your site
statistic tracking via the web server log files or a javascript based
solution like that implemented by Hitbox or WebTrends Live, anyway.
Either of these methods will accurately work with page level caching
enabled.

Block level output caching does not have the same dynamic content
limitations. It is purpose-built to allow Xaraya to fully load the core
and active module(s) with each page request. Block output caching can be
configure on a per block instance basis, so you can have some block
instances be fully dynamic (never cached), while other instances are
cached for varying lifetimes. The cache of each block instance can be
configured to be or not be shared between users (or groups of users) and
pages. This allows for very flexible output caching behaviors for sites
with mixed static and dynamic pages, but the tradeoff is a much higher
fixed cost (page generation time). Where page level output caching can
reduce a site's page generation time by 65 to 80% (YMMV depending on
site configuration), block level output caching general only reduced
page generation time by 20 to 30%.

Page level and block level output caching can be used in tandem to
reduce loads and improve page generation times on sites that serve both
anonymous and non-anonymous audience members, or on sites with varying
page and block level output caching requirements.

### TODO List

General caching related:

  - Support multisite configurations.
  - Provide data caching API.
  - Provide an output cache file browser, like the template cache file
    browser in SiteTools.
  - Refactor, refactor, refactor.

Page level output caching related:

  - Support itemtype as a 'name' between cacheKey and page when
    available.
  - Provide Scheduler, getfile/link integration for pre-population of
    output cache.
  - Analyze Slashdot and Google News use of 304 & ETag HTTP headers and
    rework our use of these headers based on findings.

Block/module level output caching related:

  - Add module output caching functions, reutilizing xarBlock\*Cached
    functions when appropriate.
  - Add xar::mod()->\*Cached() calls in xar::mod()->guiFunc().
  - Provide Admin GUI hooks to modules beyond Blocks for the
    configuration of per module output cache settings.

### Further Information

Further information on the cachemanager module can be found at

  - cachemanager Extension page at [Xaraya Extension and
    Releases](http://www.xaraya.com/index.php/release/1652.html "cachemanager Module - Xaraya Extension 1652").
    Click on Version History tab at the bottom to get the latest release
    information.
  - Related tutorials and documentation on cachemanager found at
    [Xaraya
    Documentation.](http://www.xaraya.com/index.php/keywords/cachemanager/ "Related documentation on cachemanager")

** cachemanager Overview**  
 Version 1.0.1  2006-04-12

