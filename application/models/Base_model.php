<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Base_model extends CI_Model {
	const TABLE = '';
	
	public function __construct() {
		parent::__construct();
	}

	protected function _get($op = NULL, $arr = FALSE, $table_name = NULL) {
		if (gettype($op) != 'array') {
			if (gettype($op) == 'integer' || gettype($op) == 'string') {
				$this->id = $op;
			}
			if (!empty($this->id)) {
				$que = $this->db
					->where('id', $this->id);

				if (empty($table_name)) {
					$this->db->get(self::TABLE);
				} else {
					$this->db->get($table_name);
				}

				$ret = $que->get($table_name)->row_array();
				foreach ($ret as $ix => $vl) {
					if ($ix == 'id')
						continue;
					$this->$ix = $vl;
				}
				return $this;
			}
			return FALSE;
		}

		if (!empty($op['select'])) {
			switch (gettype($op['select'])) {
				case 'array':
					$this->db->select(
						implode(',', $op['select'])
					);
					break;
				case 'integer':
				case 'string':
					$this->db->select($op['select']);
					break;
			}
		}

		if (!empty($op['where']) &&
			gettype($op['where']) == 'array') {
			foreach ($op['where'] as $kx => $vl) {
				if (isset($vl) && $vl !== '' && !empty($kx)) {
					switch (gettype($vl)) {
						case 'array':
							$this->db->where_in($kx, $vl);
							break;
						case 'integer':
						case 'string':
							$this->db->where($kx, $vl);
							break;
					}
				}
			}
		}
		
		if (!empty($op['custom_where']) &&
			gettype($op['custom_where'] == 'array')) {
			foreach ($op['custom_where'] as $vl) {
				if (isset($vl) && $vl !== '') {
					switch (gettype($vl)) {
						case 'string':
							$this->db->where($vl);
							break;
					}
				}
			}
		}

		if (!empty($op['where_not_in']) &&
			gettype($op['where_not_in']) == 'array') {
			foreach ($op['where_not_in'] as $kx => $vl) {
				if (isset($vl) && $vl !== '' && !empty($kx)) {
					switch (gettype($vl)) {
						case 'array':
							$this->db->where_not_in($kx, $vl);
							break;
						case 'integer':
						case 'string':
							$this->db->where_not_in($kx, $vl);
							break;
					}
				}
			}
		}

		//EX: where (column_x IS NULL or column_y NOT IN (1,2,3,4,5))
		if (!empty($op['where_or_not_in']) &&
			gettype($op['where_or_not_in']) == 'array') {
			foreach ($op['where_or_not_in'] as $kx => $vl) {
				$keys = array_keys($op['where_or']);
				$values = array_values($op['where_or']);
				if (isset($vl) && $vl !== '' && !empty($kx)) {
					switch (gettype($vl)) {
						case 'array':
							$this->db->group_start();
							$this->db->where($keys[0] . $values[0]);
							$this->db->or_group_start();
							$this->db->where_not_in($kx, $vl);
							$this->db->group_end();
							//$this->db->group_end();
							break;
						case 'integer':
						case 'string':
							$this->db->group_start();
							$this->db->where($keys[0], $values[0]);
							$this->db->or_group_start();
							$this->db->where_not_in($kx, $vl);
							$this->db->group_end();
							//$this->db->group_end();
							break;
					}
				}
			}
		}

		if (!empty($op['join']) && gettype($op['join']) == 'array') {
			foreach ($op['join'] as $jn) {
				$this->db->join($jn['table'], $jn['on'], $jn['type']);
			}
		}

		if (!empty($op['search']) && gettype($op['search']) == 'array' && !empty($op['limit'])) {
			$start = 1;
			
			foreach ($op['search'] as $kx => $vl) {
				if (isset($vl) && $vl !== '') {
					if ($start == 1) {
						$this->db->group_start();
					}
					switch (gettype($vl)) {
						case 'integer':
						case 'string':
							$this->db->or_where("{$kx} LIKE '%{$vl}%'");
							break;
					}

					if ($start == count($op['search'])) {
						$this->db->group_end();
					}
				}

				$start++;
			}
		}

		if (!empty($op['order_by'])) {
			$od = $op['order_by'];
			foreach ($od as $kx => $vl) {
				$this->db->order_by($kx, $vl);
			}
		}
		
		if (!empty($op['group_by'])) {
			$this->db->group_by($op['group_by']);
		}

		$tbl_name = !empty($table_name) ? $table_name : self::TABLE;

		if (!empty($op['as'])) {
			$result_count = $this->db->count_all_results($tbl_name . ' AS ' . $op['as'], FALSE);
		} else {
			$result_count = $this->db->count_all_results($tbl_name, FALSE);
		}

		if (!empty($op['limit'])) {
			$lm = $op['limit'];
			switch (gettype($lm)) {
				case 'array':
					$this->db->limit($lm[0], $lm[1]);   // limit, offset
					break;
				case 'integer':
				case 'string':
					$this->db->limit($lm);
					break;
			}
		}
		
		$que = $this->db->get();

		if ($arr) {
			$ret['results'] = $que->result('array');
		} else {
			$ret['results'] = $que->result(get_class($this));
		}

		$ret['count'] = $result_count;

		return $ret;
	}
}