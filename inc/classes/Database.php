<?php defined('TOKICMS') or die('Hacking attempt...');

class Database extends \PDO
{
	private $pdo;
	private $qry = '';
	private $bind;
	private $unionSql;
	private $having;
	private $tableName;
	private $key;
	private $where;
	private $grouped;
    private $group_id;
	private $isUpdate = false;
	public  $dbConnected = false;
	public $reference = [
        'NOW()'
    ];
	
	public function __construct(){}
	
	public function from( $tableName, $custom = null, $bind = null )
    {		
		$this->bind = $bind;
		
        if( $custom )
		{
            $this->qry = $custom;
        }
		
		else
		{
            $this->tableName  = DB_PREFIX . $tableName;
			
			if ( empty( $this->key ) )
			{
				$this->qry 		 = 'SELECT * ';
			}
			
			$this->qry 		 .= ' FROM `' . $this->tableName . '`';
        }

        return $this;
    }
	
	public function select( $key = '*' )
	{
		$this->qry 		 = 'SELECT ' . $key;
		$this->key 		 = $key;
		
		return $this;
	}
	
	public function generateQuery()
    {
		$this->set_where('where');
		
		if ( !empty( $this->bind ) )
		{
			$binds = array();
			
			$query = $this->prepare($this->qry);
			
			foreach ( $this->bind as $id => $bind )
			{
				//$query->bindParam( $bind, $id );
				$query->bindValue( $bind, $id );
			}
			
			$query->execute();
		}
		
		else
		{
			$query = $this->query($this->qry);
		}

        return $query;
    }
	
	public function total()
    {
		$this->db();
		
		try {
			$query = $this->generateQuery();
			$result = $query->fetch(parent::FETCH_ASSOC);
			return $result['total'];
		} catch (PDOException $e) {
            $this->show_error($e);
        }
    }
	
	public function single()
    {
		$this->db();
		
        try {
            $query = $this->generateQuery();
            $result = $query->fetch(parent::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            $this->show_error($e);
        }
    }
	
	public function all()
    {
		$this->db();
		
        try {
            $query = $this->generateQuery();
            $result = $query->fetchAll(parent::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            $this->show_error($e);
        }
    }
	
	public function set($data, $value = null, $returnLast = false )
    {
		$this->db();

        try {
            if ( !is_null ( $value ) && !is_array( $value ) )
			{
                if( str_contains( $value, ' + ' ) || str_contains( $value, ' - ' ) )
				{
                    $this->qry .= ' SET ' . $data . ' = ' . $value;
                    $binds = null;
                }
				
				else
				{
                    $this->qry .= ' SET ' . $data . ' = :' . $data . '';
                    
					$binds = [
                        ':' . $data => $value
                    ];
                }

            }
			
			else if ( is_array( $data ) )
			{
				$dat = array();
				
				$this->qry .= ' SET';
				
				$count = count( $data );
				$i = 0;

				foreach( $data as $id => $val )
				{
					if( is_array( $val ) )
					{
						$this->qry .= ' ' . $id . ' = ' . $val['0'];
						$dat[':' . $id] = $val['1'];
					}
					
					else
					{
						$this->qry .= ' ' . $id . ' = :' . $id;

						$dat[':' . $id] = $val;
					}
					
					$i++;

					if ( $i < $count )
					{
						$this->qry .= ',';
					}
				}
				
				$binds = $dat;
                
            }

			$this->set_where();

            $query = $this->prepare( $this->qry );
            $result = $query->execute( $binds );
			
			//Get the return value but don't return it yet
			$return = ( ( $returnLast && !$this->isUpdate ) ? $this->lastInsertId() : $result );
			
			//We have to set this back as "false" if it's "true"
			if ( $this->isUpdate )
			{
				$this->isUpdate = false;
			}
			
			return $return;
			
        } catch (PDOException $e) {
            $this->showError($e);
        }
    }
	
	public function decrease( $where, $num = '1' )
    {
		$this->db();
        
		try {
            $this->qry .= ' SET ' . $where . ' = ( CASE WHEN ' . $where . ' < 1 THEN 0 ELSE (' . $where . ' - ' . $num . ') end )';
			$this->set_where('where');
            $this->set_where('having');
            $query = $this->prepare( $this->qry );
            return $query->execute();
        } catch (PDOException $e) {
            $this->showError($e);
        }
    }
	
	public function increase( $where, $num = '1' )
    {
		$this->db();
        
		try {
            $this->qry .= ' SET ' . $where . ' = ' . $where . ' + ' . $num;
			$this->set_where('where');
            $this->set_where('having');
            $query = $this->prepare( $this->qry );
            return $query->execute();
        } catch (PDOException $e) {
            $this->showError($e);
        }
    }
	
	public function run()
    {
		$this->db();
        try {
            $this->set_where('where');
            $this->set_where('having');
            return $this->query($this->qry);
        } catch (PDOException $e) {
            $this->showError($e);
        }
    }
	
	public function first()
    {
		$this->db();
		
        try {
			$query = $this->generateQuery();
			return $query->fetch( parent::FETCH_ASSOC );
        } catch (PDOException $e) {
            //
        }
    }
	
	public function where($column, $value = '', $custom = false, $mark = '=', $logical = 'AND' )
    {
        $this->where[] = [
            'column' 	=> $column,
            'value' 	=> $value,
            'mark' 		=> $mark,
			'custom' 	=> $custom,
            'logical' 	=> $logical,
            'grouped' 	=> $this->grouped,
            'group_id' 	=> $this->group_id
        ];
        return $this;
    }
	
	private function set_where( $conditionType = 'where' )
    {
        if (
        (is_array($this->{$conditionType}) && count($this->{$conditionType}) > 0)
        ) {
            $whereClause = ' ' . ($conditionType == 'having' ? 'HAVING' : 'WHERE') . ' ';
            $arrs = $this->{$conditionType};
            if (is_array($arrs)) {
                foreach ($arrs as $key => $item) {
                    if (
                        $item['grouped'] === true &&
                        (
                            (
                                (isset($arrs[$key - 1]) && $arrs[$key - 1]['grouped'] !== true) ||
                                (isset($arrs[$key - 1]) && $arrs[$key - 1]['group_id'] != $item['group_id'])
                            ) ||
                            (
                                (isset($arrs[$key - 1]) && $arrs[$key - 1]['grouped'] !== true) ||
                                (!isset($arrs[$key - 1]))
                            )
                        )
                    ) {
                        $whereClause .= (isset($arrs[$key - 1]) && $arrs[$key - 1]['grouped'] == true ? ' ' . $item['logical'] : null) . ' (';
                    }
                    switch ($item['mark']) {
                        case 'LIKE':
                            $where = $item['column'] . ' LIKE "%' . $item['value'] . '%"';
                            break;
                        case 'NOT LIKE':
                            $where = $item['column'] . ' NOT LIKE "%' . $item['value'] . '%"';
                            break;
                        case 'BETWEEN':
                            $where = $item['column'] . ' BETWEEN "' . $item['value'][0] . '" AND "' . $item['value'][1] . '"';
                            break;
                        case 'NOT BETWEEN':
                            $where = $item['column'] . ' NOT BETWEEN "' . $item['value'][0] . '" AND "' . $item['value'][1] . '"';
                            break;
                        case 'FIND_IN_SET':
                            $where = 'FIND_IN_SET(' . $item['column'] . ', ' . $item['value'] . ')';
                            break;
                        case 'FIND_IN_SET_REVERSE':
                            $where = 'FIND_IN_SET(' . $item['value'] . ', ' . $item['column'] . ')';
                            break;
                        case 'IN':
                            $where = $item['column'] . ' IN(' . (is_array($item['value']) ? implode(', ', $item['value']) : $item['value']) . ')';
                            break;
                        case 'NOT IN':
                            $where = $item['column'] . ' NOT IN(' . (is_array($item['value']) ? implode(', ', $item['value']) : $item['value']) . ')';
                            break;
                        case 'SOUNDEX':
                            $where = 'SOUNDEX(' . $item['column'] . ') LIKE CONCAT(\'%\', TRIM(TRAILING \'0\' FROM SOUNDEX(\'' . $item['value'] . '\')), \'%\')';
                            break;
                        default:
                            $where = $item['column'] . ' ' . $item['mark'] . ' ' . (preg_grep('/' . trim($item['value']) . '/i', $this->reference) ? $item['value'] : ( $item['custom'] ? $item['value'] : '"' . $item['value'] . '"') );
                            break;
                    }
                    if ($key == 0) {
                        if (
                            $item['grouped'] == false &&
                            isset($arrs[$key + 1]['grouped']) == true
                        ) {
                            $whereClause .= $where . ' ' . $item['logical'];
                        } else {
                            $whereClause .= $where;
                        }
                    } else {
                        $whereClause .= ' ' . $item['logical'] . ' ' . $where;
                    }
                    if (
                        $item['grouped'] === true &&
                        (
                            (
                                (isset($arrs[$key + 1]) && $arrs[$key + 1]['grouped'] !== true) ||
                                ($item['grouped'] === true && !isset($arrs[$key + 1]))
                            )
                            ||
                            (
                                (isset($arrs[$key + 1]) && $arrs[$key + 1]['group_id'] != $item['group_id']) ||
                                ($item['grouped'] === true && !isset($arrs[$key + 1]))
                            )
                        )
                    ) {
                        $whereClause .= ' )';
                    }
                }
            }
            $whereClause = rtrim($whereClause, '||');
            $whereClause = rtrim($whereClause, '&&');
            $whereClause = preg_replace('/\(\s+(\|\||&&)/', '(', $whereClause);
            $whereClause = preg_replace('/(\|\||&&)\s+\)/', ')', $whereClause);
            $this->qry .= $whereClause;
            $this->unionSql .= $whereClause;
            $this->{$conditionType} = null;
        }
    }
	
	public function lastId()
    {
        return $this->lastInsertId();
    }
	
	public function insert( $tableName )
    {
        $this->qry = 'INSERT INTO ' . DB_PREFIX . $tableName;
        return $this;
    }
	
	public function optimize( $tableName )
    {
		$this->db();
		
		try {
			$query = $this->prepare( 'OPTIMIZE TABLE ' . DB_PREFIX . $tableName );
            return $query->execute();
		} catch (PDOException $e) {
			//
        }
    }
	
	public function update( $tableName )
    {
        $this->qry = 'UPDATE ' . DB_PREFIX . $tableName;
		$this->isUpdate = true;
        return $this;
    }
	
    public function delete( $tableName )
    {
        $this->qry = 'DELETE FROM ' . DB_PREFIX . $tableName;
        return $this;
    }	

	public function table_exists( $table_name )
	{
		$this->db();
		
		try {
			$sh = $this->pdo->prepare('SELECT 1 FROM `' . DB_PREFIX . $table_name . '` LIMIT 1');
			return $sh->execute();
		} catch (PDOException $e) {
			//
        }
	}
	
	private function db_connect()
    {		
		try {
			if ( !defined('SERVER') || !defined('DATABASE') || !defined( 'DBUSERNAME' ) )
			{
                die();
            }
			
			parent::__construct('mysql:host=' . SERVER . ';dbname=' . DATABASE, DBUSERNAME, DBPASSWORD);
            $this->query('SET CHARACTER SET utf8mb4');
            $this->query('SET NAMES utf8mb4');
            $this->query('SET sql_mode=""');
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			
			$this->dbConnected = true;
			
		} catch (PDOException $e) {
			die("Could not establish a connection to DB.");
		}
    }
	
	//Check if the DB is already connected otherwise set a new connection
	private function db()
	{
		//Check if we have an active DB connenction
		if ( !$this->dbConnected )
		{
			$this->db_connect();
		}
	}
	
	private function show_error(PDOException $error)
    {
        echo $error->getMessage();
    }
	
	public function close_connection()
	{
		# Set the PDO object to null to close the connection
	 	# http://www.php.net/manual/en/pdo.connections.php
	 	$this->pdo = null;
	}
	
	private function showError(PDOException $error)
    {
        $this->errorTemplate($error->getMessage());
    }
    private function errorTemplate($errorMsg, $title = null)
    {
        ?>
        <?php
    }
}