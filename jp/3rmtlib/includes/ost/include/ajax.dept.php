<?php
/*********************************************************************
    ajax.dept.php

    AJAX interface for knowledge base related...allowed methods.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2010 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id$
**********************************************************************/

if(!defined('OSTAJAXINC') || !defined('INCLUDE_DIR')) die('!');
	    
class DeptAjaxAPI{
    function getNg($params) {
      $deptid = $params['id'];
      $dept = new Dept($deptid);
      return json_encode(array('ng'=>$dept->getNg()));
	}
}
?>
