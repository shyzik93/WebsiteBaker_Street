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
?>

<form action="<?php echo WB_URL.$clsMarkdown->module_dir; ?>/save.php" method="post" name="<?php echo $clsMarkdown->module_dir.$section_id; ?>">
    <input type="hidden" name="action" value="settings">
    <input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
    <input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
    <?php echo $admin->getFTAN(); ?> 

    Активность: <input type="checkbox" name='is_active' <?php if($markdown['is_active'] == 1) echo "checked"; ?>>
    Обработчик:
    <select name="processor">
        <option value="raw" <?php if($markdown['processor'] == 'row') echo "selected"; ?>>Без обработки</option>
        <option value="markdown" <?php if($markdown['processor'] == 'markdown') echo "selected"; ?>>Markdown - класс Parsedown</option>
    </select>

    <br>

    <textarea name="text" rows="30" style='width:100%;font-size: 10pt;'><?=$markdown['text']?></textarea><br>
    
    <input type="submit" value="Сохранить">
</form>

<style>
</style>
