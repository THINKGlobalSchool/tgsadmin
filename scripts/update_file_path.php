<?php
/** Update filestore path for old document objects **/
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

$files = elgg_get_entities(array(
        'type' => 'object',
        'subtype' => 'file',
        'limit' => 0,
));

echo "<pre>";

foreach($files as $file) {
        $filename = $file->getFilename();

        $doc_prefix = 'documents/';
        $file_prefix = 'file/';

//      echo $filename . "<br />";

        if (strpos($filename, $doc_prefix) !== FALSE) {
                echo $file->guid . "<br />";
                echo $filename . "<br />";
        
                $actual_filename = elgg_substr($filename, elgg_strlen($doc_prefix));
                echo "---> " . $file_prefix . $actual_filename . "<br />";
        
                $file->setFilename($file_prefix.$actual_filename);
                echo "SET> " . $file->filename . "<br />";
        }
}

echo "</pre>";