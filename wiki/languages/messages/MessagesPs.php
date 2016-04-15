<?php
/** Pashto (پښتو)
 *
 * To improve a translation please visit https://translatewiki.net
 *
 * @ingroup Language
 * @file
 *
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 * @author Kaganer
 * @author Umherirrender
 */

$rtl = true;

$namespaceNames = array(
	NS_MEDIA            => 'رسنۍ',
	NS_SPECIAL          => 'ځانګړی',
	NS_TALK             => 'خبرې_اترې',
	NS_USER             => 'کارن',
	NS_USER_TALK        => 'د_کارن_خبرې_اترې',
	NS_PROJECT_TALK     => 'د_$1_خبرې_اترې',
	NS_FILE             => 'دوتنه',
	NS_FILE_TALK        => 'د_دوتنې_خبرې_اترې',
	NS_MEDIAWIKI        => 'ميډياويکي',
	NS_MEDIAWIKI_TALK   => 'د_ميډياويکي_خبرې_اترې',
	NS_TEMPLATE         => 'کينډۍ',
	NS_TEMPLATE_TALK    => 'د_کينډۍ_خبرې_اترې',
	NS_HELP             => 'لارښود',
	NS_HELP_TALK        => 'د_لارښود_خبرې_اترې',
	NS_CATEGORY         => 'وېشنيزه',
	NS_CATEGORY_TALK    => 'د_وېشنيزې_خبرې_اترې',
);

$namespaceAliases = array(
	'کارونکی' => NS_USER,
	'د_کارونکي_خبرې_اترې' => NS_USER_TALK,
	'انځور' => NS_FILE,
	'د_انځور_خبرې_اترې' => NS_FILE_TALK,
);

$specialPageAliases = array(
	'Allmessages'               => array( 'ټول-پيغامونه' ),
	'Allpages'                  => array( 'ټول_مخونه' ),
	'Ancientpages'              => array( 'لرغوني_مخونه' ),
	'Blankpage'                 => array( 'تش_مخ' ),
	'Block'                     => array( 'بنديز،_د_آی_پي_بنديز،_بنديز_لګېدلی_کارن_Block' ),
	'Booksources'               => array( 'د_کتاب_سرچينې' ),
	'Categories'                => array( 'وېشنيزې' ),
	'ChangePassword'            => array( 'پټنوم_بدلول،_پټنوم_بيا_پر_ځای_کول،_د_بيا_پر_ځای_کولو_پاسپورټ' ),
	'Contributions'             => array( 'ونډې' ),
	'CreateAccount'             => array( 'کارن-حساب_جوړول' ),
	'DeletedContributions'      => array( 'ړنګې_شوي_ونډې' ),
	'Export'                    => array( 'صادرول' ),
	'BlockList'                 => array( 'د_بنديزلړليک' ),
	'LinkSearch'                => array( 'د_تړنې_پلټنه' ),
	'Listfiles'                 => array( 'د_انځورونو_لړليک' ),
	'Listusers'                 => array( 'د_کارنانو_لړليک' ),
	'Log'                       => array( 'يادښتونه،_يادښت' ),
	'Lonelypages'               => array( 'يتيم_مخونه' ),
	'Longpages'                 => array( 'اوږده_مخونه' ),
	'Mycontributions'           => array( 'زماونډې' ),
	'Mypage'                    => array( 'زما_پاڼه' ),
	'Mytalk'                    => array( 'زما_خبرې_اترې' ),
	'Newimages'                 => array( 'نوي_انځورونه' ),
	'Newpages'                  => array( 'نوي_مخونه' ),
	'Preferences'               => array( 'غوره_توبونه' ),
	'Prefixindex'               => array( 'د_مختاړيو_ليکلړ' ),
	'Protectedpages'            => array( 'ژغورلي_مخونه' ),
	'Protectedtitles'           => array( 'ژغورلي_سرليکونه' ),
	'Randompage'                => array( 'ناټاکلی،_ناټاکلی_مخ' ),
	'Recentchanges'             => array( 'اوسني_بدلونونه' ),
	'Search'                    => array( 'پلټنه' ),
	'Shortpages'                => array( 'لنډ_مخونه' ),
	'Specialpages'              => array( 'ځانګړي_مخونه' ),
	'Statistics'                => array( 'شمار' ),
	'Unblock'                   => array( 'بنديز_لرې_کول' ),
	'Uncategorizedcategories'   => array( 'ناوېشلې_وېشنيزې' ),
	'Uncategorizedimages'       => array( 'ناوېشلي_انځورونه،_ناوېشلې_دوتنې' ),
	'Uncategorizedpages'        => array( 'ناوېشلي_مخونه' ),
	'Uncategorizedtemplates'    => array( 'ناوېشلې_کينډۍ' ),
	'Undelete'                  => array( 'ناړنګول' ),
	'Unusedcategories'          => array( 'ناکارېدلي_وېشنيزې' ),
	'Unusedimages'              => array( 'ناکارېدلې_دوتنې' ),
	'Unusedtemplates'           => array( 'ناکارېدلې_کينډۍ' ),
	'Unwatchedpages'            => array( 'ناکتلي_مخونه' ),
	'Upload'                    => array( 'پورته_کول' ),
	'Userlogin'                 => array( 'ننوتل' ),
	'Userlogout'                => array( 'وتل' ),
	'Version'                   => array( 'بڼه' ),
	'Wantedcategories'          => array( 'غوښتلې_وېشنيزې' ),
	'Wantedfiles'               => array( 'غوښتلې_دوتنې' ),
	'Wantedtemplates'           => array( 'غوښتلې_کينډۍ' ),
	'Watchlist'                 => array( 'کتنلړ' ),
);

$magicWords = array(
	'notoc'                     => array( '0', '__بی‌نيولک__', '__NOTOC__' ),
	'nogallery'                 => array( '0', '__بی‌نندارتونه__', '__NOGALLERY__' ),
	'forcetoc'                  => array( '0', '__نيوليکداره__', '__FORCETOC__' ),
	'toc'                       => array( '0', '__نيوليک__', '__TOC__' ),
	'noeditsection'             => array( '0', '__بی‌برخې__', '__NOEDITSECTION__' ),
	'currentmonth'              => array( '1', 'روانه_مياشت', 'CURRENTMONTH', 'CURRENTMONTH2' ),
	'currentmonthname'          => array( '1', 'دروانې_مياشت_نوم', 'CURRENTMONTHNAME' ),
	'currentmonthabbrev'        => array( '1', 'دروانې_مياشت_لنډون', 'CURRENTMONTHABBREV' ),
	'currentday'                => array( '1', 'نن', 'CURRENTDAY' ),
	'currentday2'               => array( '1', 'نن۲', 'CURRENTDAY2' ),
	'currentdayname'            => array( '1', 'دننۍورځې_نوم', 'CURRENTDAYNAME' ),
	'currentyear'               => array( '1', 'سږکال', 'CURRENTYEAR' ),
	'currenttime'               => array( '1', 'داوخت', 'CURRENTTIME' ),
	'currenthour'               => array( '1', 'دم_ګړۍ', 'CURRENTHOUR' ),
	'localmonth'                => array( '1', 'سيمه_يزه_مياشت', 'LOCALMONTH', 'LOCALMONTH2' ),
	'localmonthname'            => array( '1', 'دسيمه_يزې_مياشت_نوم', 'LOCALMONTHNAME' ),
	'localmonthabbrev'          => array( '1', 'دسيمه_يزې_مياشت_لنډون', 'LOCALMONTHABBREV' ),
	'localday'                  => array( '1', 'سيمه_يزه_ورځ', 'LOCALDAY' ),
	'localday2'                 => array( '1', 'سيمه_يزه_ورځ۲', 'LOCALDAY2' ),
	'localdayname'              => array( '1', 'دسيمه_يزې_ورځ_نوم', 'LOCALDAYNAME' ),
	'localyear'                 => array( '1', 'سيمه_يزکال', 'LOCALYEAR' ),
	'localtime'                 => array( '1', 'سيمه_يزوخت', 'LOCALTIME' ),
	'localhour'                 => array( '1', 'سيمه_يزه_ګړۍ', 'LOCALHOUR' ),
	'numberofpages'             => array( '1', 'دمخونوشمېر', 'NUMBEROFPAGES' ),
	'numberofarticles'          => array( '1', 'دليکنوشمېر', 'NUMBEROFARTICLES' ),
	'numberoffiles'             => array( '1', 'ددوتنوشمېر', 'NUMBEROFFILES' ),
	'numberofusers'             => array( '1', 'دکارونکوشمېر', 'NUMBEROFUSERS' ),
	'pagename'                  => array( '1', 'دمخ_نوم', 'PAGENAME' ),
	'pagenamee'                 => array( '1', 'دمخ_نښه', 'PAGENAMEE' ),
	'namespace'                 => array( '1', 'نوم_تشيال', 'NAMESPACE' ),
	'namespacee'                => array( '1', 'د_نوم_تشيال_نښه', 'NAMESPACEE' ),
	'talkspace'                 => array( '1', 'دخبرواترو_تشيال', 'TALKSPACE' ),
	'talkspacee'                => array( '1', 'دخبرواترو_تشيال_نښه', 'TALKSPACEE' ),
	'subjectspace'              => array( '1', 'دسکالوتشيال', 'دليکنې_تشيال', 'SUBJECTSPACE', 'ARTICLESPACE' ),
	'subjectspacee'             => array( '1', 'دسکالوتشيال_نښه', 'دليکنې_تشيال_نښه', 'SUBJECTSPACEE', 'ARTICLESPACEE' ),
	'fullpagename'              => array( '1', 'دمخ_بشپړنوم', 'FULLPAGENAME' ),
	'fullpagenamee'             => array( '1', 'دمخ_بشپړنوم_نښه', 'FULLPAGENAMEE' ),
	'msg'                       => array( '0', 'پیغام:', 'پ:', 'MSG:' ),
	'img_thumbnail'             => array( '1', 'بټنوک', 'thumbnail', 'thumb' ),
	'img_right'                 => array( '1', 'ښي', 'right' ),
	'img_left'                  => array( '1', 'کيڼ', 'left' ),
	'img_none'                  => array( '1', 'هېڅ', 'none' ),
	'img_center'                => array( '1', 'مېنځ،_center', 'center', 'centre' ),
	'sitename'                  => array( '1', 'دوېبځي_نوم', 'SITENAME' ),
	'server'                    => array( '0', 'پالنګر', 'SERVER' ),
	'servername'                => array( '0', 'دپالنګر_نوم', 'SERVERNAME' ),
	'grammar'                   => array( '0', 'ګرامر:', 'GRAMMAR:' ),
	'currentweek'               => array( '1', 'روانه_اوونۍ', 'CURRENTWEEK' ),
	'currentdow'                => array( '1', 'داوونۍورځ', 'CURRENTDOW' ),
	'localweek'                 => array( '1', 'سيمه_يزه_اوونۍ', 'LOCALWEEK' ),
	'plural'                    => array( '0', 'جمع:', 'PLURAL:' ),
	'language'                  => array( '0', '#ژبه:', '#LANGUAGE:' ),
	'special'                   => array( '0', 'ځانګړی', 'special' ),
	'hiddencat'                 => array( '1', '__پټه_وېشنيزه__', '__HIDDENCAT__' ),
	'pagesize'                  => array( '1', 'مخکچه', 'PAGESIZE' ),
	'index'                     => array( '1', '__ليکلړ__', '__INDEX__' ),
	'noindex'                   => array( '1', '__بې_ليکلړ__', '__NOINDEX__' ),
	'protectionlevel'           => array( '1', 'ژغورکچه', 'PROTECTIONLEVEL' ),
);

