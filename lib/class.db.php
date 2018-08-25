<?php

	/**********************************************************************
	*  Author: Justin Vincent (jv@vip.ie)
	*  Web...: http://justinvincent.com
	*  Name..: ezSQL
	*  Desc..: ezSQL Core module - database abstraction library to make
	*          it very easy to deal with databases. ezSQLcore can not be used by
	*          itself (it is designed for use by database specific modules).
	*
	*/

	/**********************************************************************
	*  ezSQL Constants
	*/

	defined('EZSQL_VERSION') or define('EZSQL_VERSION', '2.17');
	defined('OBJECT') or define('OBJECT', 'OBJECT');
	defined('ARRAY_A') or define('ARRAY_A', 'ARRAY_A');
	defined('ARRAY_N') or define('ARRAY_N', 'ARRAY_N');

	/**********************************************************************
	*  Core class containg common functions to manipulate query result
	*  sets once returned
	*/

	class ezSQLcore
	{

		var $trace            = false;  // same as $debug_all
		var $debug_all        = false;  // same as $trace
		var $debug_called     = false;
		var $vardump_called   = false;
		var $show_errors      = true;
		var $num_queries      = 0;
		var $conn_queries     = 0;
		var $last_query       = null;
		var $last_error       = null;
		var $col_info         = null;
		var $captured_errors  = array();
		var $cache_dir        = false;
		var $cache_queries    = false;
		var $cache_inserts    = false;
		var $use_disk_cache   = false;
		var $cache_timeout    = 24; // hours
		var $timers           = array();
		var $total_query_time = 0;
		var $db_connect_time  = 0;
		var $trace_log        = array();
		var $use_trace_log    = false;
		var $sql_log_file     = false;
		var $do_profile       = false;
		var $profile_times    = array();

		// == TJH == default now needed for echo of debug function
		var $debug_echo_is_on = true;

		/**********************************************************************
		*  Constructor
		*/

		function __construct()
		{
		}

		/**********************************************************************
		*  Get host and port from an "host:port" notation.
		*  Returns array of host and port. If port is omitted, returns $default
		*/

		function get_host_port( $host, $default = false )
		{
			$port = $default;
			if ( false !== strpos( $host, ':' ) ) {
				list( $host, $port ) = explode( ':', $host );
				$port = (int) $port;
			}
			return array( $host, $port );
		}

		/**********************************************************************
		*  Print SQL/DB error - over-ridden by specific DB class
		*/

		function register_error($err_str)
		{
			// Keep track of last error
			$this->last_error = $err_str;

			// Capture all errors to an error array no matter what happens
			$this->captured_errors[] = array
			(
				'error_str' => $err_str,
				'query'     => $this->last_query
			);
		}

		/**********************************************************************
		*  Turn error handling on or off..
		*/

		function show_errors()
		{
			$this->show_errors = true;
		}

		function hide_errors()
		{
			$this->show_errors = false;
		}

		/**********************************************************************
		*  Kill cached query results
		*/

		function flush()
		{
			// Get rid of these
			$this->last_result = null;
			$this->col_info = null;
			$this->last_query = null;
			$this->from_disk_cache = false;
		}

		/**********************************************************************
		*  Get one variable from the DB - see docs for more detail
		*/

		function get_var($query=null,$x=0,$y=0)
		{

			// Log how the function was called
			$this->func_call = "\$db->get_var(\"$query\",$x,$y)";

			// If there is a query then perform it if not then use cached results..
			if ( $query )
			{
				$this->query($query);
			}

			// Extract var out of cached results based x,y vals
			if ( $this->last_result[$y] )
			{
				$values = array_values(get_object_vars($this->last_result[$y]));
			}

			// If there is a value return it else return null
			return (isset($values[$x]) && $values[$x]!=='')?$values[$x]:null;
		}

		/**********************************************************************
		*  Get one row from the DB - see docs for more detail
		*/

		function get_row($query=null,$output=OBJECT,$y=0)
		{

			// Log how the function was called
			$this->func_call = "\$db->get_row(\"$query\",$output,$y)";

			// If there is a query then perform it if not then use cached results..
			if ( $query )
			{
				$this->query($query);
			}

			// If the output is an object then return object using the row offset..
			if ( $output == OBJECT )
			{
				return $this->last_result[$y]?$this->last_result[$y]:null;
			}
			// If the output is an associative array then return row as such..
			elseif ( $output == ARRAY_A )
			{
				return $this->last_result[$y]?get_object_vars($this->last_result[$y]):null;
			}
			// If the output is an numerical array then return row as such..
			elseif ( $output == ARRAY_N )
			{
				return $this->last_result[$y]?array_values(get_object_vars($this->last_result[$y])):null;
			}
			// If invalid output type was specified..
			else
			{
				$this->show_errors ? trigger_error(" \$db->get_row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N",E_USER_WARNING) : null;
			}

		}

		/**********************************************************************
		*  Function to get 1 column from the cached result set based in X index
		*  see docs for usage and info
		*/

		function get_col($query=null,$x=0)
		{

			$new_array = array();

			// If there is a query then perform it if not then use cached results..
			if ( $query )
			{
				$this->query($query);
			}

			// Extract the column values
			$j = count($this->last_result);
			for ( $i=0; $i < $j; $i++ )
			{
				$new_array[$i] = $this->get_var(null,$x,$i);
			}

			return $new_array;
		}


		/**********************************************************************
		*  Return the the query as a result set - see docs for more details
		*/

		function get_results($query=null, $output = OBJECT)
		{

			// Log how the function was called
			$this->func_call = "\$db->get_results(\"$query\", $output)";

			// If there is a query then perform it if not then use cached results..
			if ( $query )
			{
				$this->query($query);
			}

			// Send back array of objects. Each row is an object
			if ( $output == OBJECT )
			{
				return $this->last_result;
			}
			elseif ( $output == ARRAY_A || $output == ARRAY_N )
			{
				if ( $this->last_result )
				{
					$i=0;
					foreach( $this->last_result as $row )
					{

						$new_array[$i] = get_object_vars($row);

						if ( $output == ARRAY_N )
						{
							$new_array[$i] = array_values($new_array[$i]);
						}

						$i++;
					}

					return $new_array;
				}
				else
				{
					return array();
				}
			}
		}


		/**********************************************************************
		*  Function to get column meta data info pertaining to the last query
		* see docs for more info and usage
		*/

		function get_col_info($info_type="name",$col_offset=-1)
		{

			if ( $this->col_info )
			{
				if ( $col_offset == -1 )
				{
					$i=0;
					foreach($this->col_info as $col )
					{
						$new_array[$i] = $col->{$info_type};
						$i++;
					}
					return $new_array;
				}
				else
				{
					return $this->col_info[$col_offset]->{$info_type};
				}

			}

		}

		/**********************************************************************
		*  store_cache
		*/

		function store_cache($query,$is_insert)
		{

			// The would be cache file for this query
			$cache_file = $this->cache_dir.'/'.md5($query);

			// disk caching of queries
			if ( $this->use_disk_cache && ( $this->cache_queries && ! $is_insert ) || ( $this->cache_inserts && $is_insert ))
			{
				if ( ! is_dir($this->cache_dir) )
				{
					$this->register_error("Could not open cache dir: $this->cache_dir");
					$this->show_errors ? trigger_error("Could not open cache dir: $this->cache_dir",E_USER_WARNING) : null;
				}
				else
				{
					// Cache all result values
					$result_cache = array
					(
						'col_info' => $this->col_info,
						'last_result' => $this->last_result,
						'num_rows' => $this->num_rows,
						'return_value' => $this->num_rows,
					);
					file_put_contents($cache_file, serialize($result_cache));
					if( file_exists($cache_file . ".updating") )
						unlink($cache_file . ".updating");
				}
			}

		}

		/**********************************************************************
		*  get_cache
		*/

		function get_cache($query)
		{

			// The would be cache file for this query
			$cache_file = $this->cache_dir.'/'.md5($query);

			// Try to get previously cached version
			if ( $this->use_disk_cache && file_exists($cache_file) )
			{
				// Only use this cache file if less than 'cache_timeout' (hours)
				if ( (time() - filemtime($cache_file)) > ($this->cache_timeout*3600) &&
					!(file_exists($cache_file . ".updating") && (time() - filemtime($cache_file . ".updating") < 60)) )
				{
					touch($cache_file . ".updating"); // Show that we in the process of updating the cache
				}
				else
				{
					$result_cache = unserialize(file_get_contents($cache_file));

					$this->col_info = $result_cache['col_info'];
					$this->last_result = $result_cache['last_result'];
					$this->num_rows = $result_cache['num_rows'];

					$this->from_disk_cache = true;

					// If debug ALL queries
					$this->trace || $this->debug_all ? $this->debug() : null ;

					return $result_cache['return_value'];
				}
			}

		}

		/**********************************************************************
		*  Timer related functions
		*/

		function timer_get_cur()
		{
			list($usec, $sec) = explode(" ",microtime());
			return ((float)$usec + (float)$sec);
		}

		function timer_start($timer_name)
		{
			$this->timers[$timer_name] = $this->timer_get_cur();
		}

		function timer_elapsed($timer_name)
		{
			return round($this->timer_get_cur() - $this->timers[$timer_name],2);
		}

		function timer_update_global($timer_name)
		{
			if ( $this->do_profile )
			{
				$this->profile_times[] = array
				(
					'query' => $this->last_query,
					'time' => $this->timer_elapsed($timer_name)
				);
			}

			$this->total_query_time += $this->timer_elapsed($timer_name);
		}

		/**********************************************************************
		* Creates a SET nvp sql string from an associative array (and escapes all values)
		*
		*  Usage:
		*
		*     $db_data = array('login'=>'jv','email'=>'jv@vip.ie', 'user_id' => 1, 'created' => 'NOW()');
		*
		*     $db->query("INSERT INTO users SET ".$db->get_set($db_data));
		*
		*     ...OR...
		*
		*     $db->query("UPDATE users SET ".$db->get_set($db_data)." WHERE user_id = 1");
		*
		* Output:
		*
		*     login = 'jv', email = 'jv@vip.ie', user_id = 1, created = NOW()
		*/

		function get_set($params)
		{
			if( !is_array( $params ) )
			{
				$this->register_error( 'get_set() parameter invalid. Expected array in '.__FILE__.' on line '.__LINE__);
				return;
			}
			$sql = array();
			foreach ( $params as $field => $val )
			{
				if ( $val === 'true' || $val === true )
					$val = 1;
				if ( $val === 'false' || $val === false )
					$val = 0;

				switch( $val ){
					case 'NOW()' :
					case 'NULL' :
					  $sql[] = "$field = $val";
						break;
					default :
						$sql[] = "$field = '".$this->escape( $val )."'";
				}
			}

			return implode( ', ' , $sql );
		}

		/**
		 * Function for operating query count
		 *
		 * @param bool $all Set to false for function to return queries only during this connection
		 * @param bool $increase Set to true to increase query count (internal usage)
		 * @return int Returns query count base on $all
		 */
		function count ($all = true, $increase = false) {
			if ($increase) {
				$this->num_queries++;
				$this->conn_queries++;
			}

			return ($all) ? $this->num_queries : $this->conn_queries;
		}
	}
	/**********************************************************************
	*  Author: Justin Vincent (jv@jvmultimedia.com) / Silvio Wanka 
	*  Web...: http://twitter.com/justinvincent
	*  Name..: ezSQL_sqlite3
	*  Desc..: SQLite3 component (part of ezSQL databse abstraction library)
	*
	*/

	/**********************************************************************
	*  ezSQL error strings - SQLite
	*/

	global $ezsql_sqlite3_str;
	
	$ezsql_sqlite3_str = array
	(
		1 => 'Require $dbpath and $dbname to open an SQLite database'
	);

	/**********************************************************************
	*  ezSQL Database specific class - SQLite
	*/

	if ( ! class_exists ('SQLite3') ) die('<b>Fatal Error:</b> ezSQL_sqlite3 requires SQLite3 Lib to be compiled and or linked in to the PHP engine');
	if ( ! class_exists ('ezSQLcore') ) die('<b>Fatal Error:</b> ezSQL_sqlite3 requires ezSQLcore (ez_sql_core.php) to be included/loaded before it can be used');

	class ezSQL_sqlite3 extends ezSQLcore
	{

		var $rows_affected = false;

		/**********************************************************************
		*  Constructor - allow the user to perform a quick connect at the 
		*  same time as initialising the ezSQL_sqlite3 class
		*/

		function __construct($dbpath='', $dbname='')
		{
			// Turn on track errors 
			ini_set('track_errors',1);
			
			if ( $dbpath && $dbname )
			{
				$this->connect($dbpath, $dbname);
			}
		}

		/**********************************************************************
		*  Try to connect to SQLite database server
		*/

		function connect($dbpath='', $dbname='')
		{
			global $ezsql_sqlite3_str; $return_val = false;
			
			// Must have a user and a password
			if ( ! $dbpath || ! $dbname )
			{
				$this->register_error($ezsql_sqlite3_str[1].' in '.__FILE__.' on line '.__LINE__);
				$this->show_errors ? trigger_error($ezsql_sqlite3_str[1],E_USER_WARNING) : null;
			}
			// Try to establish the server database handle
			else if ( ! $this->dbh = @new SQLite3($dbpath.$dbname) )
			{
				$this->register_error($php_errormsg);
				$this->show_errors ? trigger_error($php_errormsg,E_USER_WARNING) : null;
			}
			else
			{
				$return_val = true;
				$this->conn_queries = 0;
			}

			return $return_val;			
		}

		/**********************************************************************
		*  In the case of SQLite quick_connect is not really needed
		*  because std. connect already does what quick connect does - 
		*  but for the sake of consistency it has been included
		*/

		function quick_connect($dbpath='', $dbname='')
		{
			return $this->connect($dbpath, $dbname);
		}

		/**********************************************************************
		*  No real equivalent of mySQL select in SQLite 
		*  once again, function included for the sake of consistency
		*/

		function select($dbpath='', $dbname='')
		{
			return $this->connect($dbpath, $dbname);
		}

		/**********************************************************************
		*  Format a SQLite string correctly for safe SQLite insert
		*  (no mater if magic quotes are on or not)
		*/

		function escape($str)
		{
			return $this->dbh->escapeString(stripslashes(preg_replace("/[\r\n]/",'',$str)));				
		}

		/**********************************************************************
		*  Return SQLite specific system date syntax 
		*  i.e. Oracle: SYSDATE Mysql: NOW()
		*/

		function sysdate()
		{
			return 'now';			
		}
                /*
                 * Inicia una transacción 
                 */
                 function beginTransaction()
                 {
                     $this->dbh->exec('BEGIN TRANSACTION;');
                 }
                 /*
                  * Revierte una transacción
                  */
                 function rollBack() 
                 {
                     $this->dbh->exec('ROLLBACK TRANSACTION;');
                 }
                 /*
                  * Consigna una transacción 
                  */
                 function commit()
                 {
                     $this->dbh->exec('COMMIT;');
                 }
                //Master Vitronic
                //http://php.net/manual/es/sqlite3.createfunction.php
                 function createFunction($function_name,$callback,$num_args = false) 
                 {
                        $this->dbh->createFunction($function_name, $callback, $num_args);
                 }
                 //http://php.net/manual/es/sqlite3.createaggregate.php
                 function createAggregate ( $function_name , $step_func , $finalize_func , $num_args = false) 
                 {
                        $this->dbh->createAggregate($function_name, $step_func, $finalize_func,$num_args);
                 }
                 /*http://php.net/manual/es/sqlite3.exec.php*/
                 function exec($query)
                 {
                    return $this->dbh->exec($query);
                 }     
                 /*http://php.net/manual/es/sqlite3.lastinsertrowid.php*/
                 function lastInsertRowID()
                 {
                    return $this->dbh->lastInsertRowID();
                 }
                 /*https://secure.php.net/manual/es/sqlite3.close.php*/
                 function close()
                 {
                     $this->dbh->close();
                 }                                  
		/**********************************************************************
		*  Perform SQLite query and try to detirmin result value
		*/

		// ==================================================================
		//	Basic Query	- see docs for more detail
	
		function query($query)
		{

			// For reg expressions
			$query = str_replace("/[\n\r]/",'',trim($query)); 

			// initialise return
			$return_val = 0;

			// Flush cached values..
			$this->flush();

			// Log how the function was called
			$this->func_call = "\$db->query(\"$query\")";

			// Keep track of the last query for debug..
			$this->last_query = $query;

			// Perform the query via std mysql_query function..
			$this->result = $this->dbh->query($query);
			$this->count(true, true);

			// If there is an error then take note of it..
			if (@$this->dbh->lastErrorCode())
			{
				$err_str = $this->dbh->lastErrorMsg();
				$this->register_error($err_str);
				$this->show_errors ? trigger_error($err_str,E_USER_WARNING) : null;
				return false;
			}
			
			// Query was an insert, delete, update, replace
			if ( preg_match("/^(insert|delete|update|replace)\s+/i",$query) )
			{
				$this->rows_affected = @$this->dbh->changes();
				
				// Take note of the insert_id
				if ( preg_match("/^(insert|replace)\s+/i",$query) )
				{
					$this->insert_id = @$this->dbh->lastInsertRowID();	
				}
				
				// Return number fo rows affected
				$return_val = $this->rows_affected;
	
			}
			// Query was an select
			else
			{
				
				// Take note of column info	
				$i=0;
				$this->col_info = array();
				while ($i < @$this->result->numColumns())
				{
					$this->col_info[$i] = new StdClass;
					$this->col_info[$i]->name       = $this->result->columnName($i);
					$this->col_info[$i]->type       = null;
					$this->col_info[$i]->max_length = null;
					$i++;
				}
				
				// Store Query Results
				$num_rows=0;
				while ($row =  @$this->result->fetchArray(SQLITE3_ASSOC))
				{
					// Store relults as an objects within main array
					$obj= (object) $row; //convert to object
					$this->last_result[$num_rows] = $obj;
					$num_rows++;
				}

				// Log number of rows the query returned
				$this->num_rows = $num_rows;
				
				// Return number of rows selected
				$return_val = $this->num_rows;
			
			}

			// If debug ALL queries
			$this->trace||$this->debug_all ? $this->debug() : null ;

			return $return_val;
		
		}

	}

