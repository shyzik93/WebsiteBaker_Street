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

if (! defined('WB_PATH')) { die('Cannot access this file directly'); }

include_once(dirname(__FILE__)."/init.php");

$r = $clsMarkdown->add($page_id, $section_id);
if ($r !== true) $admin->print_error($r);