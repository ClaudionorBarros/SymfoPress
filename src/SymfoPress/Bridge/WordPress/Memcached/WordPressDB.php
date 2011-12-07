<?php

/*
 * This file is part of the SymfoPress framework.
 * 
 * (c) Carl Alexander <carlalexander@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfoPress\Bridge\WordPress\Memcached;

/**
 * Extension of the WordPress Database class to put all results in memcached.
 * 
 * @author Carl Alexander <carlalexander@gmail.com>
 */
class WordPressDB extends \wpdb
{
    /**
     * Expiration set to 3600 seconds. 1 hour.
     */
    const DEFAULT_EXPIRE = 3600;
    /**
     * Cache all table versions to minimize memcache hits
     *
     * @var array
     */
    protected $tableVersions = array();
    /**
     * Keeps the last query result in memory.
     *
     * @var array
     */
    protected $lastQueryResult;
    /**
     * A Memcached Manager intance
     *
     * @var Manager
     */
    protected $memcached;

    
    public function __construct($dbuser, $dbpassword, $dbname, $dbhost, Manager $memcached)
    {
        parent::__construct($dbuser, $dbpassword, $dbname, $dbhost);
        
        $this->memcached = $memcached;
    }
    
    /**
     * New WordPress query implementation. Checks memcached before querying the DB.
     *
     * @param string $query
     * @return stdClass
     */
    public function query($query)
    {
       // if($query == 'SELECT COUNT(group_id) FROM wp_sfgroups');
      //  die($this->_isUpdate($query));

        if (strtoupper($query) == 'SELECT FOUND_ROWS()') {
            return $this->last_result = $this->lastQueryResult['total'];
        } else if ($this->_isFetch($query)) {
            $this->_doFetch($query);
        } else if ($this->_isUpdate($query)) {
            $this->_doUpdate($query);
        } else {
            parent::query($query);
        }

        return true;
    }

    /**
     * Make all cached data for this specfic table invalid.
     *
     * @param string $table
     */
    public function invalidateTableCache($table)
    {
        $this->_updateTableTimestamp($table);
    }

    /**
     * Perform a database fetch
     *
     * @param string $query
     */
    protected function _doFetch($query)
    {
        $cache = $this->memcached->get($query);
        $versions = $this->_getTableVersions($query);

        if (!$cache || !$this->_isValid($cache, $versions)) {
            $cache = $this->_doQuery($query, $versions);
        }

        if ($cache['data'] instanceof Nothing) {
            $this->last_result = array();
        } else {
            $this->last_result = $cache['data'];
        }

        $this->lastQueryResult = $cache;
    }

    /**
     * Do a table updating operation. INSERT, DELETE or UPDATE.
     *
     * @param string $query
     */
    protected function _doUpdate($query)
    {
        $tables = $this->_getTables($query);
        $time = microtime();

        if (!empty($tables)) {
            foreach ($tables as $table) {
                $this->_updateTableTimestamp($table, $time);
            }
        }

        parent::query($query);
    }

    /**
     * This performs the actual query to the database and updates memcached
     *
     * @param string $query
     * @param array $versions
     * @return stdClass
     */
    protected function _doQuery($query, $versions)
    {
        $result = array();
        $result['versions'] = $versions;

        parent::query($query);

        if (!empty($this->last_result)) {
            $result['data'] = $this->last_result;
        } else {
            $result['data'] = new Nothing();
        }

        if ($this->_isWpQuery($query)) {
            parent::query('SELECT FOUND_ROWS()');
            $result['total'] = $this->last_result;
            $this->last_result = $result['data'];
        }

        $this->memcached->set($query, $result, self::DEFAULT_EXPIRE);

        return $result;
    }

    /**
     * Given a specfic query. Gets all the tables involved in the given query.
     *
     * @param string $query
     * @return array
     */
    protected function _getTables($query)
    {
        $tables = array();
        preg_match_all('/(wp_[\w]*)[`\s]?/i', $query, $tables);

        return $tables[1];
    }

    /**
     * Gets the table versions for all tables involved in the query.
     *
     * @param string $query
     * @return array
     */
    protected function _getTableVersions($query)
    {
        $versions = array();
        $tables = $this->_getTables($query);
        $time = microtime();

        foreach ($tables as $table) {
            if (!isset($this->tableVersions[$table])) {
                $this->tableVersions[$table] = $this->memcached->get($table);
            }

            if (!$this->tableVersions[$table]) {
                $this->_updateTableTimestamp($table, $time);
            }

            $versions[$table] = $this->tableVersions[$table];
        }

        return $versions;
    }

    protected function _updateTableTimestamp($table, $time = '')
    {
        if (empty($time)) {
            $time = microtime();
        }

        $this->memcached->set($table, $time);
        $this->tableVersions[$table] = $time;
    }

    /**
     * Determines if the query is trying to fetch data.
     *
     * @param string $query
     * @return bool
     */
    protected function _isFetch($query)
    {
        return stripos($query, 'select') !== false;
    }

    /**
     * Determines if the query is trying to update data
     *
     * @param string $query
     * @return bool
     */
    protected function _isUpdate($query)
    {
        if (stripos($query, 'insert') !== false) {
            return true;
        } else if (stripos($query, 'update') !== false) {
            return true;
        } else if (stripos($query, 'delete') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Determines if the query is being sent by the WP_Query object.
     *
     * @param string $query
     * @return bool
     */
    protected function _isWpQuery($query)
    {
        return stripos($query, 'SQL_CALC_FOUND_ROWS') !== false;
    }

    /**
     * Validates the cache data
     *
     * @param array $cache
     * @param array $versions
     * @return bool
     */
    protected function _isValid($cache, $versions)
    {
        if (!isset($cache['versions']) || !isset($cache['data'])) {
            return false;
        }

        $diff = array_diff($cache['versions'], $versions);
        if (!empty($diff)) {
            return false;
        }

        return true;
    }
}