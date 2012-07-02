<?php
/*************************************************************************
    staff.inc.php
    
    File included on every staff page...handles logins (security) and file path issues.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2010 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id$
**********************************************************************/
require_once('includes/configure.php');
if(basename($_SERVER['SCRIPT_NAME'])==basename(__FILE__)) die('Kwaheri rafiki!'); //Say hi to our friend..
//if(!file_exists('../../includes/ost/main.inc.php')) die('Fatal error..get tech support');
define('ROOT_PATH','../'); //Path to the root dir.
require_once(DIR_OST.'/main.inc.php');
if(!defined('INCLUDE_DIR')) die('Fatal error');

/*Some more include defines specific to staff only */
define('STAFFINC_DIR',INCLUDE_DIR.'staff/');
define('SCP_DIR',str_replace('//','/',dirname(__FILE__).'/'));

/* Define tag that included files can check */
define('OSTSCPINC',TRUE);
define('OSTSTAFFINC',TRUE);

/* Tables used by staff only */
define('KB_PREMADE_TABLE',TABLE_PREFIX.'kb_premade');


/* include what is needed on staff control panel */

require_once(INCLUDE_DIR.'class.staff.php');
require_once(INCLUDE_DIR.'class.nav.php');


?>
