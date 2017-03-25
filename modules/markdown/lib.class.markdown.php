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

include(WB_PATH.'/include/shyzik93/sql_tools.php')
 
class Markdown {

   function __construct($db, $processor_classes) {
        $this->db = $db;
        $this->tbl_markdown = "`".TABLE_PREFIX."mod_shyzik93_markdown`";
        $this->module_name = "shyzik93_markdown";
        $this->module_dir = "/modules/{$this->module_name}";
        
        foreach ($processor_classes as $name => $value) {
            $this->$name = $value;
        }
    }
    
    public function install() {
    	$this->db->query("DROP TABLE IF EXISTS {$this->tbl_markdown}");
	    $sql= "CREATE TABLE IF NOT EXISTS {$this->tbl_markdown} ( "
		. ' `section_id` INT NOT NULL DEFAULT \'0\','
		. ' `page_id` INT NOT NULL DEFAULT \'0\','
		. ' `processor` VARCHAR(15) NOT NULL DEFAULT \'markdown\','
		. ' `text` LONGTEXT NOT NULL DEFAULT \'\','
		. ' `is_active` INT NOT NULL DEFAULT 1,'
		. ' PRIMARY KEY ( `section_id` ) '
		. ' ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';
    	$r = $this->db->query($sql);
        if (!$r) $this->db->get_error();
        return true;
	
    }
    
    public function add($page_id, $section_id) {
        $sql = "INSERT INTO {$this->tbl_markdown}
                SET `page_id`=$page_id, `section_id`=$section_id";
        $r = $this->db->query($sql);
        if (!$r) $this->db->get_error();
        return true;
    }
    
    public function delete($section_id) {
        $r = $this->db->query("DELETE FROM {$this->tbl_markdown} WHERE section_id = '$section_id'");
        if (!$r) $this->db->get_error();
        return true;
    }
    
    public function update($section_id, $processor, $text, $is_active) {
        $processor = $this->db->escapeString($processor);
        $text = $this->db->escapeString($text);
        $is_active = $this->db->escapeString($is_active);
        $sql = "UPDATE {$this->tbl_markdown} SET `processor`='$processor', `text`='$text', `is_active`='$is_active' WHERE `section_id`='$section_id'";
        if (!$this->db->query($sql)) return $this->db->get_error();
        return true;
    }
    
    function api_get_markdown($section_id) {
        $sql = "SELECT * FROM {$this->tbl_markdown} WHERE `section_id`=".(int)$section_id;
        $r = $this->db->query($sql);
        if ($this->db->is_error()) {echo $this->db->get_error(); exit();}
        $r = $r->fetchRow();
        return $r;
    }

    function convert_text($text, $processor) {
        if ($processor == 'raw') {
            return $text;
        } else if ($processor == 'markdown') {
            return $this->clsParsedown->text($text);
        }
        return 'Неверный обработчик разметки';
    }

}

?>