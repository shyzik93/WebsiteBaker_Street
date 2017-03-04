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

require('../../config.php');

$admin_header = false;
$update_when_modified = true;
require(WB_PATH.'/modules/admin.php');

$ret_url = ADMIN_URL.'/pages/modify.php?page_id='.$page_id;

if (!$admin->checkFTAN()) {
	$admin->print_header();
	$admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], $ret_url);
}
$admin->print_header();

include_once(dirname(__FILE__)."/init.php");

$action = isset($_POST['action']) ? $_POST['action'] : $_GET['action'];

if ($action == 'settings') {
    $processor = $_POST['processor'];
    $text = $_POST['text'];
    $is_active = $_POST['is_active'] == 'on' ? 1 : 0;

    $r = $clsMarkdown->update($section_id, $processor, $text, $is_active);
    if ($r !== true) $admin->print_error($errs, $ret_url);
    
    $admin->print_success("Успешно!", $ret_url);

} else {
    $admin->print_error("Не указано действие", $ret_url);
}

// Print admin footer
$admin->print_footer();
