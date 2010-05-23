<?php
/**
 * Loggix_Plugin - Show Gravatar Image
 *
 * @copyright Copyright (C) UP!
 * @author    hijiri
 * @link      http://tkns.homelinux.net/
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @since     2010.05.20
 * @version   10.5.21
 */

$this->plugin->addFilter('recent-comments-text', 'addGravatarImage');

function showGravatarImage($commentID, $class = 'guest')
{
    global $app;

    // SETTING BEGIN
    // Image size
    $size   = 32;
    // Gravatar rating
    $rating = 'g';
    // Images altanative text
    $alt    = 'His or Her gravatar';
    // SETTING END

    // No object when call from 'recent-comments-text'.
    if (!is_object($app)) { $app = new Loggix_Application; }

    // Get E-Mail
    $sql = 'SELECT '
         . ' user_mail '
         . 'FROM ' 
         . COMMENT_TABLE . ' '
         . 'WHERE '
         . "id = '" . $commentID . "'";

    $res = $app->db->query($sql);
    $userMail = $res->fetchColumn();

    // Default image
    $defaultUrl = (ereg('admin', $class)) ? $app->getRootUri() . 'theme/css/default/images/icon-admin.png'
                                          : $app->getRootUri() . 'theme/css/default/images/icon-guest.png';

    // Make Gravatar URL
    if ($userMail) {
        $imageUrl = 'http://www.gravatar.com/avatar/' . md5(strtolower($userMail))
                  . '?default='    . urlencode($defaultUrl)
                  . '&amp;size='   . $size
                  . '&amp;rating=' . $rating;
    } else {
        $imageUrl = $defaultUrl;
    }

    return '<img src="' . $imageUrl . '" width="' . $size .'" height="' . $size .'" alt="' . $alt .'" />';
}

function addGravatarImage($text)
{
    // Get ONE recent comment parameters
    $pattern     = '/<a href="([\.?\.\/]+)index.php\?id=([0-9]{1,})#c([0-9]{1,})" class="(.+)" title="(.+)">/';
    // Pass parameters to callbackGravatarImage
    $text = preg_replace_callback($pattern, 'callbackGravatarImage', $text);

    return $text;
}

function callbackGravatarImage($arg)
{
    return '<a href="' . $arg[1] . 'index.php?id=' . $arg[2] . '#c' . $arg[3] .
           '" class="' . $arg[4] . '" title="' . $arg[5] . '">' . showGravatarImage($arg[3], $arg[4]);
}

