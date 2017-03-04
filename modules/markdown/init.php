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
// You need this file
// http://parsedown.org/
// https://github.com/erusev/parsedown
include_once(dirname(__FILE__).'/../../include/parsedown-master/Parsedown.php');
// https://michelf.ca/projects/php-markdown/
include_once(dirname(__FILE__)."/lib.class.markdown.php");

$clsMarkdown = new Markdown($database, [
    'clsParsedown'=> new Parsedown(),
]);

?>