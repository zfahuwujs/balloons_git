<?php

/*
fetch-test branch
  +--------------------------------------------------------------------------
  |   CubeCart v3.0.15
  |   ========================================
  |   by Alistair Brookbanks
  |	CubeCart is a Trade Mark of Devellion Limited
  |   Copyright Devellion Limited 2005 - 2006. All rights reserved.
  |   Devellion Limited,
  |   22 Thomas Heskin Court,
  |   Station Road,
  |   Bishops Stortford,
  |   HERTFORDSHIRE.
  |   CM23 3EE
  |   UNITED KINGDOM
  |   http://www.devellion.com
  |	UK Private Limited Company No. 5323904
  |   ========================================
  |   Web: http://www.cubecart.com
  |   Date: Thursday, 4th January 2007
  |   Email: sales (at) cubecart (dot) com
  |	License Type: CubeCart is NOT Open Source Software and Limitations Apply
  |   Licence Info: http://www.cubecart.com/site/faq/license.php
  +--------------------------------------------------------------------------
  |	index.php
  |   ========================================
  |	Main pages of the store
  +--------------------------------------------------------------------------
 */

//sshtest
include_once("includes/ini.inc.php");


// INCLUDE CORE VARIABLES & FUNCTIONS
include_once("includes/global.inc.php");

// check if installed
if ($glob['installed'] == 0) {

    header("location: install/index.php");
    exit;
} elseif ((file_exists($glob['rootDir'] . "/install/index.php") || file_exists($glob['rootDir'] . "/upgrade.php") && $glob['installed'] == 1)) {

    echo "<strong>WARNING</strong> - Your store will not function until the install directory and/or upgrade.php is deleted from the server.";
    exit;
}

// initiate db class
include_once("classes/db.inc.php");
$db = new db();
include_once("includes/functions.inc.php");
$config = fetchDbConfig("config");

include_once("includes/sessionStart.inc.php");

/* <rf> search engine friendly url mod */
include_once("includes/sef_urls.inc.php");
$sefroot = sef_script_name();
if ($config['sef'] == 0 && preg_match('#' . $glob['rootRel'] . $sefroot . '#i', $_SERVER['PHP_SELF'])) {
    // if this script got called by the shop script and we aren't using sef urls then redirect to index.php	
    Header("Location: " . $glob['rootRel'] . "index.php");
}
/* <rf> end of mod */

include_once("includes/sslSwitch.inc.php");

// get session data
include_once("includes/session.inc.php");

// get exchange rates etc
include_once("includes/currencyVars.inc.php");

$lang_folder = "";

if (empty($ccUserData[0]['lang'])) {
    $lang_folder = $config['defaultLang'];
} else {
    $lang_folder = $ccUserData[0]['lang'];
}
include_once("language/" . $lang_folder . "/lang.inc.php");

// require template class
include_once("classes/xtpl.php");

$body = new XTemplate("skins/" . $config['skinDir'] . "/styleTemplates/global/index.tpl");

if (isset($_GET['searchStr'])) {
    $body->assign("SEARCHSTR", treatGet($_GET['searchStr']));
} else {
    $body->assign("SEARCHSTR", "");
}

$body->assign("CURRENCY_VER", $currencyVer);
$body->assign("VAL_ISO", $charsetIso);
$body->assign("VAL_SKIN", $config['skinDir']);
$body->assign('DOMAIN_URI', $glob['storeURL']);
$body->assign('CUR_URL', currentPage());

if ($ccUserData[0]['tax'] == 1) {
    $body->assign('VAT_SEL_NO', 'selected="selected"');
} else {
    $body->assign('VAT_SEL_NO', '');
}
if ($ccUserData[0]['tax'] == 2) {
    $body->assign('VAT_SEL_YES', 'selected="selected"');
} else {
    $body->assign('VAT_SEL_YES', '');
}


// START  MAIN CONTENT
if (isset($_GET['act'])) {

    switch (treatGet($_GET['act'])) {

        case "viewDoc":
            include("includes/content/viewDoc.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;
        case "viewNews":
            include("includes/content/viewNews.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;
        case "contact":
            include("includes/content/contact.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;
        case "viewCat":
            include("includes/content/viewCat.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            //var_dump($page_content);
            break;
        case "viewVideo":
            include("includes/content/viewVideo.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;
        case "thankyou":
            include("includes/content/thankyou.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;
        /* 	brand mod php page include	 */
        case "viewBrand":
            include("includes/content/viewBrand.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;
        case "search":
            include("includes/content/search.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;
        /* 	brand mod php page include	 */
        case "viewProd":
            include("includes/content/viewProd.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;
        case "unsubscribe":
            include("includes/content/unsubscribe.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;
        case "taf":
            include("includes/content/tellafriend.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;
        case "login":
            include("includes/content/login.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;
        case "logout":
            include("includes/content/logout.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;
        case "forgotPass":
            include("includes/content/forgotPass.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;
        case "account":
            include("includes/content/account.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;
        case "profile":
            include("includes/content/profile.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;
        case "changePass":
            include("includes/content/changePass.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;
        case "newsletter":
            include("includes/content/newsletter.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;
        case "dnExpire":
            include("includes/content/dnExpire.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;

        case "review":
            include("includes/content/review.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;

        case "gallery":
            include("includes/content/gallery.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;

        case "quote":
            include("includes/content/quote.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;

        default:
            include("includes/content/index.inc.php");
            $body->assign("PAGE_CONTENT", $page_content);
            break;
    }
} elseif ($_SERVER['REQUEST_URI'] == "/gallery.html") {
    include("includes/content/gallery.inc.php");
    $body->assign("PAGE_CONTENT", $page_content);
} else {
    include("includes/content/index.inc.php");
    $body->assign("PAGE_CONTENT", $page_content);
}
// END MAIN CONTENT
// START META DATA
if (isset($meta)) {
    /* <rf> search engine friendly url mod */
    $body->assign("META_TITLE", str_replace("&#39;", "'", html_entity_decode(stripslashes(sefMetaTitle()))));
    $body->assign("META_DESC", strip_tags(sefMetaDesc()));
    $body->assign("META_KEYWORDS", strip_tags(sefMetaKeywords()));
    /* <rf> end mod */
} else {
    $body->assign("META_TITLE", str_replace("&#39;", "'", html_entity_decode(htmlspecialchars(stripslashes($config['siteTitle'])) . c())));
    $body->assign("META_DESC", strip_tags($config['metaDescription']));
    $body->assign("META_KEYWORDS", strip_tags($config['metaKeyWords']));
}

// START CONTENT BOXES
include("includes/boxes/searchForm.inc.php");
$body->assign("SEARCH_FORM", $box_content);

include("includes/boxes/session.inc.php");
$body->assign("SESSION", $box_content);

include("includes/boxes/categories.inc.php");
$body->assign("CATEGORIES", $box_content);

include("includes/boxes/randomProd.inc.php");
$body->assign("RANDOM_PROD", $box_content);

include("includes/boxes/info.inc.php");
$body->assign("INFORMATION", $box_content);

include("includes/boxes/language.inc.php");
$body->assign("LANGUAGE", $box_content);

include("includes/boxes/currency.inc.php");
$body->assign("CURRENCY", $box_content);

include("includes/boxes/shoppingCart.inc.php");
$body->assign("SHOPPING_CART", $box_content);

include("includes/boxes/prouctModuleSide.inc.php");
$body->assign("PRODUCTS_MODULE_SIDE", $box_content);

include("includes/boxes/saleItems.inc.php");
$body->assign("SALE_ITEMS", $box_content);

include("includes/boxes/mailList.inc.php");
$body->assign("MAIL_LIST", $box_content);

include("includes/boxes/siteDocs.inc.php");
$body->assign("SITE_DOCS", $box_content);

include("includes/boxes/siteDocsLeft.inc.php");
$body->assign("SITE_DOCS_LEFT", $box_content);

include("includes/boxes/fontResize.inc.php");
$body->assign("FONT_RESIZE", $box_content);

/* include("includes/boxes/recentProducts.inc.php");
  $body->assign("RECENT_PRODS",$box_content); */

/* 	begin brand box include	 */
include("includes/boxes/brands.inc.php");
$body->assign("BRANDS", $box_content);

include("includes/boxes/topNav.inc.php");
$body->assign("TOP_NAV", $box_content);

include("includes/boxes/partFinder.inc.php");
$body->assign("PART_FINDER", $box_content);

include("includes/boxes/footer.inc.php");
$body->assign("FOOTER", $box_content);


/* 	end brand box include	 */
// END CONTENT BOXES
if (preg_match('/MSIE 7/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/MSIE 6/i', $_SERVER['HTTP_USER_AGENT'])) {
    $body->parse("body.disclaimer");
}
if (!empty($config['analytics_uid'])) {
    $body->assign("GOOGLE_ANALYTICS_UID", $config['analytics_uid']);
    $body->parse("body.google_analytics");
}

if (isset($_COOKIE['recentProds'])) {
    $body->parse("body.recent_prods");
}

if (isset($menuJs)) {
    $body->assign("MENUJS", $menuJs);
}

if ($config['facebook'] != '') {
    $body->assign("FACEBOOK", '<a href = "' . $config['facebook'] . '" target="_blank"><img src = "/skins/FixedSize/styleImages/backgrounds/facebook.png" /></a>');
} else {
    $body->assign("FACEBOOK", '');
}
if ($config['twitter'] != '') {
    $body->assign("TWITTER", '<a href = "' . $config['twitter'] . '" target="_blank"><img src = "/skins/FixedSize/styleImages/backgrounds/twitter.png" /></a>');
} else {
    $body->assign("TWITTER", '');
}
if ($config['linkedin'] != '') {
    $body->assign("LINKEDIN", '<a href = "' . $config['linkedin'] . '" target="_blank"><img src = "/skins/FixedSize/styleImages/backgrounds/linkedin.png" /></a>');
} else {
    $body->assign("LINKEDIN", '');
}
if ($config['youtube'] != '') {
    $body->assign("YOUTUBE", '<a href = "' . $config['youtube'] . '" target="_blank"><img src = "/skins/FixedSize/styleImages/backgrounds/youtube.png" /></a>');
} else {
    $body->assign("YOUTUBE", '');
}
$body->assign("COMPANY_NAME", $config['storeName']);
$body->assign("COMPANY_ADDRESS", nl2br($config['storeAddress']));
$body->assign("PHONE", $config['masterPhone']);


if ($config['left_col_no'] > 0) {

    for ($c = 1; $c < $config['left_col_no'] + 1; $c++) {

        if (!empty($config['lci' . $c])) {
            if (!empty($config['lcl' . $c])) {
                $body->assign("LCI", '<a href = "' . $config['lcl' . $c] . '"><img src = "' . $config['lci' . $c] . '" alt = "' . $config['storeName'] . '" /></a>');
            } else {
                $body->assign("LCI", '<img src = "' . $config['lci' . $c] . '" alt = "' . $config['storeName'] . '" />');
            }
        } else {
            $body->assign("LCI", '');
        }

        $body->parse("body.left_col_img");
    }
}

if (evoHideBol(104) == false) {
    $body->parse("body.font_resize_js");
}


// parse and spit out final document
$body->parse("body");
$body->out("body");
?>
