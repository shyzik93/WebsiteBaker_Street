<?php

/**
 * @author: Polyakov Konstantin <shyzik93@mail.ru>
 * @licenece: Public Domain
 * @date: 2016-2017
 * 
 * 2017-02-07 - ��������� ������� glue_fields, check_insert, check_delete. ������� prepare2update, prepare2select �� ������������� 
 */

// �������, ������� ������ ���� � ������ ������, ��������������� �� database

/**
 * ������ "��������":
 *     [
 *         'type'=> 'string'
 *         'value'=> $strValue || null || $intValue
 *
 *         'type'=>'function'
 *         'value'=> $strSQLFunctionName
 *     ]
 * 
 * ������ "��� �������":
 *     [
 *         'name' => $strName
 *         'alias' => $strAliasName
 *     ]
 */

function process_value($value, $type_value='string') {
	if (gettype($value) == 'array') {
		$type_value = isset($value['type']) ? $value['type'] : 'string';
		$value = isset($value['value']) ? $value['value'] : null;
	}
	
	if ($type_value === 'string') {
		if (gettype($value) == 'string') return '"'.mysql_escape_string($value).'"';
		else if ($value === null) return 'NULL';
		else return '"'.$value.'"'; // numbers (float or integer)
	} else if ($type_value == 'function') {
		return $value;
	}
}

function process_key($key) {
	if (gettype($key) == 'number') return (string)$key;
	if (substr($key, 0, 1) != '`') $key = "`{$key}`";
	return $key;
}

function process_table($arrTable) {
	if (gettype($arrTable)=='string')  return $arrTable;

    $arrTable['name'] = process_key($arrTable['name']);
	
	if (isset($arrTable['alias'])) return $arrTable['name']." AS ".$arrTable['alias'];
	return $arrTable['name'];

}

function process_tables($arrTables) {
	if (gettype($arrTables) == 'string') return $arrTables;
	foreach($arrTables as $i => $arrTable) $arrTables[$i] = process_table($arrTable);
    return implode(',', $arrTables);
}

function process_where($where) {
	//if (gettype($where) == 'string') return "WHERE ".$where;
	//if (gettype($where) == 'integer') return "WHERE ".$where;
	return $where;

}

/** 
 * 
 * ��������� ����� ����� �������. ������� ����� ���� ��� ������, ��� � �����.
 * ������ ����������� � ��������� ��������� �������
 * 
 * @param arra $keys ['key1', 'key2', 'key3']
 */
function glue_keys($keys) {
	if (gettype($keys) == 'string') $keys = [$keys];
	foreach($keys as $i => $key) {
		$keys[$i] = process_key($key);
	}
	return implode(',', $keys);
}
/** 
 * @param arra $values ['value1', 'value2', 'value3']
 */
function glue_values($values) {
	if (gettype($values) == 'string') $values = [$values];
	foreach($values as $i => $value) {
		$values[$i] = process_value($value);
	}
	return implode(',', $values);
}

/** 
 * @param arra $fields ['key1'=>value1', 'key2'=>'value2', 'key3'=>'value3']
 */
function glue_fields($fields, $sep) {
    $_fields = array();
    foreach ($fields as $key => $value) {
    	$value = process_value($value);
    	$key = process_key($key);
        $_fields[] = $key.'='.$value;
    }
    return implode($sep, $_fields);
}

//$condition = ['name'=>'name1', 'value'=>['value'=>'value1', 'type'=>'function'], 'operator'=>'='];
//$condition = ['name'=>'name1', 'value'=>[['value'=>'value1', 'type'=>'function'],['value'=>'value1', 'type'=>'function']], 'operator'=>'in'];
function glue_condition($condition) {
	$key = "`{$condition['key']}`";
	$operator = $condition['operator'];
	if (in_array($condition['operator'], ['=', '!=', '>', '<'])) {
		$value = process_value($condition['value']);
	} else if (in_array($condition['operator'], ['in', 'not in'])) {
		$value = "(".glue_values($condition['value']).")";
	}
	
	return $key." ".$operator." ".$value;
}

/* ----------- ������������� �������: ���������� ������� ----------- */ 

function build_order($keys=null, $direction=null) {
	if ($keys === null) return '';

	if (!in_array($direction, ['ASC', 'DESC'])) $direction = '';

	return " ORDER BY ".glue_keys($keys)." $direction ";
}

function build_limit($offset=null, $count=null) {
	if ($offset === '') $offset = null;
	if ($count === '') $count = null;
	
	if ($offset === null && $count === null) return '';

	if ($offset === null) $offset = 0;
	if ($count === null) $count = 0;

	return " LIMIT $offset,$count ";
}

function build_update($table, $fields, $where=null) {
	$fields = glue_fields($fields, ',');
	$table = process_tables($table);
    $where = process_where($where);
	$sql = "UPDATE $table SET $fields ";
	if ($where !== null) $sql = $sql."WHERE $where";
	return $sql;
}

/**
 * @param string $table Name of table
 * @param mixed $keys Array of field names or raw string
 */
function build_select($table, $keys, $where=null) {
	if (gettype($keys) == 'array') $keys = glue_keys($keys);
	$table = process_tables($table);
    $where = process_where($where);
	$sql = "SELECT $keys FROM $table ";
	if ($where !== null) $sql = $sql."WHERE $where";
	return $sql;
    
}

function build_delete($table, $where=null) {
	$table = process_tables($table);
    $where = process_where($where);
	$sql = "DELETE FROM $table ";
	if ($where !== null) $sql = $sql."WHERE $where";
	return $sql;
}

function build_insert($table, $fields, $value_lines=false) {
	$table = process_tables($table);
    if ($value_lines) {
    	$keys = glue_keys($fields);
		$_value_lines = [];
    	foreach($value_lines as $values) {
        	$values = "(".glue_keys($values).")";
    	}
    	$value_lines = implode(',', $_value_lines);
    } else {
		$fields = prepare2insert($fields);
		$keys = $fields['keys'];
		$value_lines = "(".$fields['values'].")";
    }
	return "INSERT INTO $table ($keys) VALUES $value_lines";    
    
}

/* ----------- ������������� �������: ��������� ��������� ----------- */

function check_update($sql) {
	global $database;

	if ($database->query($sql)) return true;
	if ($database->is_error()) return "update_row() '$sql' :: ".$database->get_error();
	return false;
}
function check_insert($sql) { return check_update($sql); }
function check_delete($sql) { return check_update($sql); }

/**
 * 
 */
function check_select($sql) {
	global $database;

	$r = $database->query($sql);
	if ($database->is_error()) return "select_rows() '$sql' :: ".$database->get_error();
	if ($r->numRows() == 0) return null;
	return $r;
}

/* ----------- ������ �������: ������, ������ ������, ��������� ��������� ----------- */ 

function update_row($table, $fields, $where=null) {
	global $database;
	$sql = build_update($table, $fields, $where);
    return check_update($sql);
}

function delete_row($table, $where=null) {
	global $database;
	$sql = build_delete($table, $where);
    return check_update($sql);
}

function select_row($table, $keys, $where='') {
	global $database;
	$sql = build_select($table, $keys, $where);
    return check_select($sql);
}

?>