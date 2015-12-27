Vanilla Twitter Embed
=======================================

Features
-----------
- Embed tweets directly into discussion posts by pasting the tweet URL.
- Tweets are cached in database for faster performance.
- When plugin is disabled, cache table is dropped from database.
- Tested to not conflcit with CLEditor.
- Tested to use more generic twitter HTML links so that widgets are properly rendered.

Installation
-----------
1. Simply drop the VanillaTwitterEmbed directory into your vanilla forum's plugin directory and enable in dashboard.

2. Enable plugin: Settings -> Vanilla Twitter Embed.

Get Support and Make Contributions
-----------
- Fork a copy and make contributions: https://github.com/JamieChung/VanillaTwitterEmbed
- Follow me on twitter: http://twitter.com/jamiechung

ChangeLog
-----------

### 0.4 (Jul.23.2013)
- Fixed bug where regex that looks for twitter links was too specific. Now using a more generic regex look for extra html attributes.
### 0.3 (Dec.27.2011)
- Fixed bug where youtube, mentions and anchors were no longer parsing properly (thanks to @Kiwii on vf.org)
### 0.2 (Dec.18.2011)
- Twitter embed widget adapts to native local of Vanilla installation.
- When plugin is disabled, cache table is dropped from database.
### 0.1.1 (Dec.17.2011)
- Minor changes and bug fixes
### 0.1 (Dec.17.2011)
- Initial Public Release

==============================

Copyright 2013

Jamie Chung

me@jamiechung.me

http://www.jamiechung.me

