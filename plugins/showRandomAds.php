<?php
/**
 * Loggix_Plugin - Show Random Ads
 *
 * @copyright Copyright (C) UP!
 * @author    hijiri
 * @link      http://tkns.homelinux.net/
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @since     2010.08.18
 * @version   10.8.18
 */

$this->plugin->addFilter('index-view', 'showRandomAds', 3);
$this->plugin->addFilter('downloads-index-view', 'showRandomAds', 3);
$this->plugin->addFilter('permalink-view', 'showRandomAds', 3);
$this->plugin->addFilter('entry-content', 'showRandomAds', 3);
$this->plugin->addFilter('navigation', 'showRandomAds', 3);
$this->plugin->addFilter('downloads-index-view', 'showRandomAds', 3);

function showRandomAds($text)
{
    global $pathToIndex;

    // SETTING BEGIN
    // Data file
    $dataFile  = $pathToIndex . '/data/showRandomAds.txt';
    // Target string
    $tagetStr  = '<!-- random -->';
    // SETTING END

    // Read
    $ads = file($dataFile);
    $i = rand(1, count($ads)) - 1;

    // Replace
    return mb_ereg_replace($tagetStr, $ads[$i], $text);
}

