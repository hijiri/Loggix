<?php
/**
 * Loggix_Plugin - Optimize DB
 *
 * @copyright Copyright (C) UP!
 * @author    hijiri
 * @link      http://tkns.homelinux.net/
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @since     2010.06.13
 * @version   10.6.27
 */

// Ummm.....
//$this->plugin->addAction('after-new-entry-posted', 'optimizeDB');
$this->plugin->addFilter('h1', 'optimizeDB');

//function optimizeDB($referId)
function optimizeDB($text)
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

            // Garbage Collection (/admin/delete.php)
            $maxLifeTime = get_cfg_var("session.gc_maxlifetime");
            $expirationTime = time() - $maxLifeTime;
            $sql = 'DELETE FROM ' 
                 .     SESSION_TABLE . ' '
                 . 'WHERE '
                 .     "sess_date < '" . $expirationTime . "'";
            $bdb->query($sql);

            // Vacuum DB
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
