<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		auiog413 <libo@chukong-inc.com>
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter MY_Model Class
 *
 * @package		CodeIgniter
 * @subpackage          Libraries
 * @category            Libraries
 * @author		auiog413 <libo@chukong-inc.com>
 * @link		http://codeigniter.com/user_guide/libraries/config.html
 */
class CIL_Model extends CI_Model {

        protected $_mdl_table = null;
    
	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct($table_name = null)
	{
                parent::__construct();
                if ($table_name !== null)
                {
                        $this->_mdl_table = trim($table_name);
                }
		log_message('debug', "MY_Model Class Initialized with table_name:$table_name");
	}

        /**
         * Get data from table by conditions and selection
         * 
         * @param array $conditions conditions to use
         *              $conditions['limit']    = 1;
         *              $conditions['limit']    = array(2, 1);
         *              $conditions['order_by'] = 'field1 desc, field2 asc';
         *              $conditions['order_by'] = array('field1' => 'desc', 'field2' => 'asc');
         *              $conditions['join']     = array('table', 'on condition'[, 'left|right|outer|inner|left outer|rightouter']);
         *              $conditions['where']    = array($key => $value[, ...]);
         *              $conditions['or_where'] = array($key => $value);
         *              $conditions['where_in'] = 
         *              $conditions['or_where_in']= 
         *              $conditions['where_not_in']= 
         *              $conditions['or_where_not_in']= 
         *              $conditions['like']     = 
         *              $conditions['or_like']  = 
         *              $conditions['not_like'] = 
         *              $conditions['or_not_like']  = 
         *              $conditions['having']   = array($key => $value[, ...])
         *              $conditions['or_having']= array($key => $value[, ...])
         * 
         * @param array $select fields to fetch
         *              $select['fields']   = 'field1,field2';
         *              $select['fields']   = array('field', 'max|min|avg|sum'[, 'alias']);
         *              $select['distinct'] = true/false; false as default
         *              $select['gourp_by'] = array('field1'[,'field2', ...])
         * @return array
         */
        public function get($conditions = array(), $select = array())
        {
                if ( ! empty($conditions)) {
                        $this->build_condition($conditions);
                }
                
                if ( ! empty($select))
                {
                        if (isset($select['fields']))
                        {
                                if (is_string($select['fields']))
                                {
                                        $this->db->select($select['fields']);
                                }
                                elseif (is_array($select['fields']))
                                {
                                        if (isset($select['fields'][2]))
                                        {
                                                $select_alias = $select['fields'][2];
                                        }
                                        else
                                        {
                                                $select_alias = $select['fields'][1] . '_' . $select['fields'][0];
                                        }
                                        
                                        switch ($select['fields'][1])
                                        {
                                                case 'max':
                                                    $this->db->select_max($select['fields'][0], $select_alias);
                                                    break;
                                                
                                                case 'min':
                                                    $this->db->select_min($select['fields'][0], $select_alias);
                                                    break;
                                                
                                                case 'avg':
                                                    $this->db->select_avg($select['fields'][0], $select_alias);
                                                    break;
                                                
                                                case 'sum':
                                                    $this->db->select_sum($select['fields'][0], $select_alias);
                                                    break;
                                        }
                                }
                        }
                        
                        if (isset($select['distinct']))
                        {
                                $this->db->distinct();
                        }
                        
                        if (isset($select['group_by']) && ! empty($select['group_by']))
                        {
                                $this->db->group_by($select['group_by']);
                        }
                }
                
		$infos = $this->db->get($this->_mdl_table)->result_array();

		if(empty($infos)){
                        return null;
                }else{
                        return $infos;
                }
        }
        
        public function add($insert_data = array())
        {
                if ( ! empty($insert_data))
                {
                        $now = time();
                        if ( ! isset($insert_data['created_time']))
                        {
                                $insert_data['created_time'] = $now;
                        }
                        if ( ! isset($insert_data['updated_time']))
                        {
                                $insert_data['updated_time'] = $now;
                        }
                        
                        $this->db->insert($this->_mdl_table, $insert_data);
                        $id = $this->db->insert_id();
                        if ($id)
                        {
                                return $id;
                        }
                        else
                        {
                                return FALSE;
                        }
                }
                
                return FALSE;
        }
        
        public function update($data = array(), $conditions = array())
        {
                if ( ! empty($conditions))
                {
                        $this->build_condition($conditions);
                }
        }
        
        public function del($conditions = array())
        {
                if ( ! empty($conditions))
                {
                        $this->build_condition($conditions);
                }
        }
        
        public function count($conditions = array())
        {
                if ( ! empty($conditions))
                {
                        $this->build_condition($conditions);
                }
                
                $this->db->from($this->_mdl_table);
                return $this->db->count_all_results();
        }
        
        protected function build_condition($conditions = array())
        {
                if (isset($conditions['limit']))
                {
                        if (is_numeric($conditions['limit']))
                        {
                                $this->db->limit($conditions['limit']);
                        }
                        elseif (is_array($conditions['limit']))
                        {
                                if (is_numeric($conditions['limit'][1]))
                                {
                                        $this->db->limit($conditions['limit'][1], $conditions['limit'][0]);
                                }
                                elseif (is_numeric($conditions['limit'][0]))
                                {
                                        $this->db->limit($conditions['limit'][0]);
                                }
                        }
                }
                
                if (isset($conditions['order_by']))
                {
                        if (is_string($conditions['order_by']))
                        {
                                $this->db->order_by($conditions['order_by']);
                        }
                        elseif (is_array($conditions['order_by']))
                        {
                                if (is_array($conditions['order_by'][0]))
                                {
                                        foreach($conditions['order_by'] as $key => $value)
                                        {
                                                if (is_string($value[0]) 
                                                    && in_array( strtolower($value[1]), array('desc', 'asc')))
                                                {
                                                        $this->db->order_by($value[0], $value[1]);
                                                }
                                        }
                                }
                                elseif (is_string($conditions['order_by'][0]) 
                                        && in_array( strtolower($conditions['order_by'][1]), array('desc', 'asc')))
                                {
                                        $this->db->order_by($conditions['order_by'][0], $conditions['order_by'][1]);
                                }
                                
                        }
                }
                
                if (isset($conditions['join']))
                {
                        if (is_array($conditions['join'][0]))
                        {
                                foreach ($conditions['join'] as $key => $join_arr)
                                {
                                        $join_arr = $conditions['join'];
                                        $haystack = array('left', 'right', 'outer', 'inner', 'left outer', 'right outer');
                                        if (isset($join_arr[2]) && in_array($join_arr[2], $haystack))
                                        {
                                                $this->db->join($join_arr[0], $join_arr[1], $join_arr[2]);
                                        }
                                        else
                                        {
                                                $this->db->join($join_arr[0], $join_arr[1]);
                                        }
                                }
                        }
                        elseif (is_string($conditions['join'][0]))
                        {
                                $join_arr = $conditions['join'];
                                $haystack = array('left', 'right', 'outer', 'inner', 'left outer', 'right outer');
                                if (isset($join_arr[2]) && in_array($join_arr[2], $haystack))
                                {
                                        $this->db->join($join_arr[0], $join_arr[1], $join_arr[2]);
                                }
                                else
                                {
                                        $this->db->join($join_arr[0], $join_arr[1]);
                                }
                        }
                }
                
                if (isset($conditions['where']))
                {
                        $this->db->where($conditions['where']);
                }
                
                if (isset($conditions['or_where']))
                {
                        $this->db->or_where($conditions['or_where']);
                }
                
                if (isset($conditions['where_in']))
                {
                        $this->db->where_in($conditions['where_in'][0], $conditions['where_in'][1]);
                }
                
                if (isset($conditions['or_where_in']))
                {
                        $this->db->or_where_in($conditions['or_where_in'][0], $conditions['or_where_in'][1]);
                }
                
                if (isset($conditions['where_not_in']))
                {
                        $this->db->where_not_in($conditions['where_not_in'][0], $conditions['where_not_in'][1]);
                }
                
                if (isset($conditions['or_where_not_in']))
                {
                        $this->db->or_where_not_in($conditions['or_where_not_in'][0], $conditions['or_where_not_in'][1]);
                }
                
                if (isset($conditions['like']))
                {
                        if (is_array($conditions['like'][0]))
                        {
                                foreach ($conditions['like'] as $key => $like_arr)
                                {
                                        $haystack = array('before', 'after', 'both', 'none');
                                        if (isset($like_arr[2]) && in_array($like_arr[2], $haystack))
                                        {
                                                $this->db->like($like_arr[0], $like_arr[1], $like_arr[2]);
                                        }
                                        else
                                        {
                                                $this->db->like($like_arr[0], $like_arr[1]);
                                        }
                                }
                        }
                        elseif (is_string($conditions['like'][0]))
                        {
                                $like_arr = $conditions['like'];
                                $haystack = array('before', 'after', 'both', 'none');
                                if (isset($like_arr[2]) && in_array($like_arr[2], $haystack))
                                {
                                        $this->db->like($like_arr[0], $like_arr[1], $like_arr[2]);
                                }
                                else
                                {
                                        $this->db->like($like_arr[0], $like_arr[1]);
                                }
                        }
                }
                
                if (isset($conditions['or_like']))
                {
                        if (is_array($conditions['or_like'][0]))
                        {
                                foreach ($conditions['or_like'] as $key => $like_arr)
                                {
                                        $haystack = array('before', 'after', 'both', 'none');
                                        if (isset($like_arr[2]) && in_array($like_arr[2], $haystack))
                                        {
                                                $this->db->or_like($like_arr[0], $like_arr[1], $like_arr[2]);
                                        }
                                        else
                                        {
                                                $this->db->or_like($like_arr[0], $like_arr[1]);
                                        }
                                }
                        }
                        elseif (is_string($conditions['or_like'][0]))
                        {
                                $like_arr = $conditions['or_like'];
                                $haystack = array('before', 'after', 'both', 'none');
                                if (isset($like_arr[2]) && in_array($like_arr[2], $haystack))
                                {
                                        $this->db->or_like($like_arr[0], $like_arr[1], $like_arr[2]);
                                }
                                else
                                {
                                        $this->db->or_like($like_arr[0], $like_arr[1]);
                                }
                        }
                }
                
                if (isset($conditions['not_like']))
                {
                        if (is_array($conditions['not_like'][0]))
                        {
                                foreach ($conditions['not_like'] as $key => $like_arr)
                                {
                                        $haystack = array('before', 'after', 'both', 'none');
                                        if (isset($like_arr[2]) && in_array($like_arr[2], $haystack))
                                        {
                                                $this->db->not_like($like_arr[0], $like_arr[1], $like_arr[2]);
                                        }
                                        else
                                        {
                                                $this->db->not_like($like_arr[0], $like_arr[1]);
                                        }
                                }
                        }
                        elseif (is_string($conditions['not_like'][0]))
                        {
                                $like_arr = $conditions['not_like'];
                                $haystack = array('before', 'after', 'both', 'none');
                                if (isset($like_arr[2]) && in_array($like_arr[2], $haystack))
                                {
                                        $this->db->not_like($like_arr[0], $like_arr[1], $like_arr[2]);
                                }
                                else
                                {
                                        $this->db->not_like($like_arr[0], $like_arr[1]);
                                }
                        }
                }
                
                if (isset($conditions['or_not_like']))
                {
                        if (is_array($conditions['or_not_like'][0]))
                        {
                                foreach ($conditions['or_not_like'] as $key => $like_arr)
                                {
                                        $haystack = array('before', 'after', 'both', 'none');
                                        if (isset($like_arr[2]) && in_array($like_arr[2], $haystack))
                                        {
                                                $this->db->or_not_like($like_arr[0], $like_arr[1], $like_arr[2]);
                                        }
                                        else
                                        {
                                                $this->db->or_not_like($like_arr[0], $like_arr[1]);
                                        }
                                }
                        }
                        elseif (is_string($conditions['or_not_like'][0]))
                        {
                                $like_arr = $conditions['or_not_like'];
                                $haystack = array('before', 'after', 'both', 'none');
                                if (isset($like_arr[2]) && in_array($like_arr[2], $haystack))
                                {
                                        $this->db->or_not_like($like_arr[0], $like_arr[1], $like_arr[2]);
                                }
                                else
                                {
                                        $this->db->or_not_like($like_arr[0], $like_arr[1]);
                                }
                        }
                }
                
                if (isset($conditions['having']))
                {
                        
                }
                
                if (isset($conditions['or_having']))
                {
                        
                }
        }
}
// END Model Class

/* End of file CIL_Model.php */
/* Location: ./application/core/CIL_Model.php */