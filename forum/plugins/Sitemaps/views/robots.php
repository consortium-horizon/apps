<?php if (!defined('APPLICATION')) exit;
echo 'Sitemap: '.Url('/sitemapindex.xml', TRUE)."\n";
?>

User-agent: *
Disallow: /entry/
Disallow: /search/