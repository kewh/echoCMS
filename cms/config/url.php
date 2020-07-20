<?php
  /**
	 *
   * Site URL.
	 *
	 */

// if cms is installed in root directory keep as null value, otherwise enter sub-directory name
	  $config_URL_DIR = '';

// fully resolved URL including any sub-directory.
		$config_URL = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $config_URL_DIR;
