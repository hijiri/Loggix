<?php
/**
 * Loggix_Plugin - Vacuum DB
 *
 * @copyright Copyright (C) UP!
 * @author    hijiri
 * @link      http://tkns.homelinux.net/
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @since     2010.06.13
 * @version   10.6.15
 */

// after? before?
//$this->plugin->addAction('after-new-entry-posted', 'vacuumDB');
$this->plugin->addFilter('h1', 'vacuumDB', 1);

function vacuumDB($text)
{
    global $pathToIndex;

    // Day
    $limit = 10;

    $loggixDB = $pathToIndex . Loggix_Core::LOGGIX_SQLITE_3;
    $filename = $loggixDB . '.BAK';

    if (!file_exists($filename) || filemtime($filename)+86400*$limit <= time()) {
        if (copy($loggixDB, $filename)) {
            chmod($filename, 0666);
            $backupDB = 'sqlite:' . $filename;
            $bdb = new PDO($backupDB);
            $bdb->query('VACUUM');

            if(rename($loggixDB, $loggixDB . '.OLD')) {
                copy($filename, $loggixDB);
                chmod($loggixDB, 0666);
            }

            if (file_exists($loggixDB)) {
                unlink($loggixDB . '.OLD');
            }
        }
    }

    return $text;
}

