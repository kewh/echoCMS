<?php
/**
 * controller class for get
 *
 *
 * @since 1.0.0
 * @author Keith Wheatley
 * @package echocms\get
 */
namespace echocms;

class get
{

    /**
     * Download file
	   *
	   */
    function download($fileName=null)
    {
        $filePath = CONFIG_DIR.'/content/downloads/'.$fileName;
        if (file_exists($filePath) && substr($filePath,-4) == '.pdf') {
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$fileName");
            header("Content-Type: application/zip");
            header("Content-Transfer-Encoding: binary");
            readfile($filePath);
            exit;
        }else{
            echo 'unauthorised access.';
        }
    }
}
