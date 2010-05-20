<?php
/**
 * Loggix_Plugin - Show Gravatar Image alpha
 *
 * @copyright Copyright (C) UP!
 * @author    hijiri
 * @link      http://tkns.homelinux.net/
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @since     2010.05.20
 * @version   10.5.21
 */

$this->plugin->addFilter('recent-comments-text', 'exampleFunction', 1);

function exampleFunction($text)
{
//    $pattern     = '<a href="([\.?\./]+)index.php\?id=([0-9]{1,})#c([0-9]{1,})" class="(.+)" title="(.+)">';
//    $replacement = '<a href="\1/index.php?id=\2#c\3" class="\4" title="\5">';
//    $text = mb_ereg_replace($pattern, $replacement, $text, 'l');

    $pattern     = '/<a href="([\.?\.\/]+)index.php\?id=([0-9]{1,})#c([0-9]{1,})" class="(.+)" title="(.+)">/';
//    $replacement = '<a href="$1/index.php?id=$2#c$3" class="$4" title="$5">';
//    $text = preg_replace($pattern, $replacement, $text);
    $text = preg_replace_callback($pattern, 'callbackFunction', $text);

    return $text;
}

function callbackFunction($arg)
{
//    return   '<a href="' . $arg[1] . '/index.php?id=' . $arg[2] . '#c' . $arg[3]
//           . '" class="' . $arg[4] . '" title="' . $arg[5] . '">' . $arg[1] . $arg[3] . $arg[4];
    return   '<a href="' . $arg[1] . '/index.php?id=' . $arg[2] . '#c' . $arg[3]
           . '" class="' . $arg[4] . '" title="' . $arg[5] . '">' . showGravatarImage($arg[3], $arg[4]);

}

function showGravatarImage($commentID, $class = 'guest')
{
    global $app;

    // No object when call from 'recent-comments-text'.
    if (!is_object($app)) { $app = new Loggix_Application; }

    // Get E-Mail
    $sql = 'SELECT '
              .     ' user_mail '
              . 'FROM ' 
              .     COMMENT_TABLE . ' '
              . 'WHERE '
              .     "id = '" . $commentID . "'";

    $res = $app->db->query($sql);
    $userMail = $res->fetchColumn();

    $defaultUrl = (ereg('admin', $class)) ? $app->getRootUri() . 'theme/css/default/images/icon-admin.png'
                                          : $app->getRootUri() . 'theme/css/default/images/icon-guest.png';

    // Make URL. http://ja.gravatar.com/site/implement/url
    if ($userMail) {
        $size    = 32;
        $rating  = 'g';
        $alt     = 'His or Her gravatar';
        $gravatarUrl = 'http://www.gravatar.com/avatar/' . md5(strtolower($userMail))
                     . '?default='    . urlencode($defaultUrl)
                     . '&amp;size='   . $size
                     . '&amp;rating=' . $rating;

        $gravatarImg = '<img src="' . $gravatarUrl . '" width="' . $size .'" height="' . $size .'" alt="' . $alt .'" />';
    } else {
        $gravatarImg = '<img src="' . $defaultUrl . '" width="' . $size .'" height="' . $size .'" alt="' . $alt .'" />';
    }

    return $gravatarImg;
}

