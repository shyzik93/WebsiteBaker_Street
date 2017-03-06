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
if(!defined('WB_PATH')) {
	require_once(dirname(dirname(__FILE__)).'/framework/globalExceptionHandler.php');
	throw new IllegalFileException();
}
/* -------------------------------------------------------- */
include_once(dirname(__FILE__)."/init.php");

$markdown = $clsMarkdown->api_get_markdown($section_id);

if ($markdown['is_active'] == '1') echo $clsMarkdown->convert_text($markdown['text'], $markdown['processor']);

?>