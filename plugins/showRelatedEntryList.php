<?php
/**
 * Loggix_Plugin - Show Related Entry List
 *
 * @copyright Copyright (C) UP!
 * @author    hijiri
 * @link      http://tkns.homelinux.net/
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @since     2010.05.25
 * @version   10.5.28
 */

$this->plugin->addFilter('permalink-view', 'showRelatedEntryList');

function showRelatedEntryList($text)
{
    global $pathToIndex, $app, $item;

    // SETTING BEGIN
    // Entry Count
    $entryCount    = 3;
    // <h4> text </h4>
    $relatedText   = '関連記事';
    //$relatedText   = 'Related Entries';
    // No related
    $noRelatedText = '&lt; No Related Entries &gt;';
    // Target string
    $tagetStr      = '<!-- Begin #comments -->';
    // SETTING END

    if(!ereg('/downloads/', $app->getRequestURI())) {
        // Get Log ID List
        $tagIdList = $app->getTagIdArray();
        foreach ($tagIdList as $row) {
            $sql = 'SELECT '
                 . ' log_id '
                 . 'FROM ' 
                 . LOG_TAG_MAP_TABLE . ' '
                 . 'WHERE '
                 . "tag_id = '" . $row . "'"
                 . 'AND '
                 . "log_id != '" . $item['id'] ."'";

            if ($res = $app->db->query($sql)) {
                $list = $res->fetchAll();

                foreach ($list as $row) {
                    $logIdList[] = $row['log_id'];
                }
            }
        }

        if (count($logIdList)) {
            // Get related entry list
            $logIdList = array_count_values($logIdList);
            foreach ($logIdList as $key => $value) {
                $sql = 'SELECT '
                     . ' title '
                     . 'FROM ' 
                     . LOG_TABLE . ' '
                     . 'WHERE '
                     . "id = '" . $key . "'";

                $res = $app->db->query($sql);
                $relatedEntryList[] = array(
                                      'log_id' => $key,
                                      'count'  => $value,
                                      'title'  => $res->fetchColumn()
                                      );

                if ($entryCount <= count($relatedEntryList)) { break; }
            }
            uasort($relatedEntryList, 'compare');

            // Markup
            $listHtml = '<ul id="related-entry-list">' . "\n";
            foreach ($relatedEntryList as $row) {
                $listHtml .= '<li><a href="' . $pathToIndex .'/index.php?id=' . $row['log_id'] . '">' . $row['title'] . '</a></li>' . "\n";
            }
            $listHtml .= '</ul>' . "\n";

        } else {
            $listHtml = '<p class="passive">' . $noRelatedText . '</p>' ."\n";
        }

        // Contents
        $content = '
<div id="related-entries">
<h4>' . $relatedText . '</h4>
' . $listHtml . 
'</div>
';

        // Replace
        $text = mb_ereg_replace($tagetStr, $content, $text);
    }
    return $text;
}

function compare($a, $b)
{
    if ($a['count'] == $b['count']) {
        if ( $a['log_id'] == $b['log_id'] ) {
            return 0;
        }
        return ($a['log_id'] > $b['log_id']) ? -1 : 1;
    }
    return  ($a['count'] > $b['count']) ? -1 : 1;
}
