<?php
/**
 *
 * @category        module
 * @package         shyzik93_markdown
 * @author          Polyakov Konstantin
 * @copyright       2017, Polyakov Konstantin
 * @license         http://www.gnu.org/licenses/gpl.html
 *
 */
if(defined('WB_PATH') == false){
	die('<head><title>Access denied</title></head><body><h2 style="color:red;margin:3em auto;text-align:center;">Cannot access this file directly</h2></body></html>');
}
/* -------------------------------------------------------- */
include_once(dirname(__FILE__)."/init.php");

$r = $clsMarkdown->delete($section_id);
if ($r !== true) $admin->print_error($r);