<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
# Deprecated Functions
#
# These functions below have been deprecated. That means they have been replaced by new functions and will be removed from
# future versions. 
#
#####################################################

//Builds the query and returns either the fetched data or the query string
function Query( $query, $fetchAll = false, $returnQueryString = false, $returnExecute = false, $count = false, $returnLastKey = false, $unbuffered = false )
{
	$sql = '';
	$prefix = ( ( isset( $query['PARAMS']['NO_PREFIX'] ) && $query['PARAMS']['NO_PREFIX'] ) ? '' : DB_PREFIX );

	if ( isset( $query['SELECT'] ) )
	{
		$sql = 'SELECT ' . $query['SELECT'];
		
		if ( isset( $query['FROM'] ) )
			$sql .= ' FROM ' . $prefix . $query['FROM'];
		
		if ( isset( $query['JOINS'] ) )
		{
			foreach ( $query['JOINS'] as $join )
			{
				$sql .= ' ' . key( $join ) . ' ' . $prefix . current( $join ) . ' ON ' . $join['ON'];
				
				unset( $join );
			}
		}

		if ( isset( $query['WHERE'] ) && !empty( $query['WHERE'] ) )
			$sql .= ' WHERE ' . $query['WHERE'];
		
		if ( isset( $query['GROUP'] ) && !empty( $query['GROUP'] ) )
			$sql .= ' GROUP BY ' . $query['GROUP'];
		
		if ( isset( $query['HAVING'] ) && !empty( $query['HAVING'] ) )
			$sql .= ' HAVING ' . $query['HAVING'];
		
		if ( isset( $query['ORDER'] ) && !empty( $query['ORDER'] ) )
			$sql .= ' ORDER BY ' . $query['ORDER'];
		
		if ( isset( $query['LIMIT'] ) && !empty( $query['LIMIT'] ) )
			$sql .= ' LIMIT ' . $query['LIMIT'];
	}
	
	elseif ( isset( $query['INSERT'] ) )
	{
		$sql = 'INSERT INTO ' . $prefix . $query['INTO'];

		if ( !empty($query['INSERT'] ) )
			$sql .= ' ('.$query['INSERT'].')';

		if ( is_array( $query['VALUES'] ) )
			$sql .= ' VALUES(' . implode( '),(', $query['VALUES'] ) . ')';
		
		else
			$sql .= ' VALUES(' . $query['VALUES'] . ')';
	}
	
	elseif ( isset( $query['UPDATE'] ) )
	{
		$query['UPDATE'] = $prefix . $query['UPDATE'];

		$sql = 'UPDATE ' . $query['UPDATE'] . ' SET ' . $query['SET'];

		if ( !empty( $query['WHERE'] ) )
			$sql .= ' WHERE ' . $query['WHERE'];
	}
	
	elseif ( isset( $query['DELETE'] ) )
	{
		$sql = 'DELETE FROM ' . $prefix . $query['DELETE'];

		if ( !empty( $query['WHERE'] ) )
			$sql .= ' WHERE ' . $query['WHERE'];
	}
	
	elseif ( isset( $query['REPLACE'] ) )
	{
		$sql = 'REPLACE INTO ' . $prefix . $query['INTO'];

		if ( !empty( $query['REPLACE'] ) )
			$sql .= ' (' . $query['REPLACE'] . ')';

		$sql .= ' VALUES(' . $query['VALUES'] . ')';
	}

	if ( $returnQueryString  )
		return $sql;

	$db = dbLoad();

	try
	{
		if ( $unbuffered )
			$db->setAttribute( PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false );

		$sth = $db->prepare( $sql );
		
		if ( isset( $query['BINDS'] ) && !empty( $query['BINDS'] ) && is_array( $query['BINDS'] ) )
		{
			foreach ( $query['BINDS'] as $bind )
			{
				if ( isset( $bind['FLAG'] ) && !empty( $bind['FLAG'] ) )
					$sth->bindValue( $bind['PARAM'], $bind['VAR'], ( ( $bind['FLAG'] == 'STR' ) ? PDO::PARAM_STR : PDO::PARAM_INT ) );
				
				else
					$sth->bindValue( $bind['PARAM'], $bind['VAR'] );
				
				unset( $bind );
			}
		}
		
		$sth->execute();
	}
	catch(PDOException $e) 
	{
		echo 'Error: ' . $e->getMessage();
		return false;
	}
	
	if ( $count )
		return $sth->fetchColumn();
	
	if ( $returnLastKey && isset( $query['INSERT'] ) )
		return $db->lastInsertId();
	
	if ( $returnExecute )
		return ( $sth ? true : false );
	
	else
	{
		$return = $fetchAll ? $sth->fetchAll(PDO::FETCH_ASSOC) : $sth->fetch(PDO::FETCH_ASSOC);
		
		if ( $unbuffered )
			$sth->closeCursor();
		
		return ( $return );
	}
}