<?php
  /**
	 *
   * Site URL.
	 *
	 */

// sub-directory, or null if cms installed in root directory
	  $config_URL_DIR = '';

// fully resolved URL including any sub-directory.
	  $config_URL = 'http://' . $_SERVER['HTTP_HOST'] . $config_URL_DIR;
