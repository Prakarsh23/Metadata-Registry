<?php
/*
 *  $Id: StatementCommon.php 17 2006-05-24 00:52:58Z jphipps $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://creole.phpdb.org>.
 */

/**
 * Class that contains common/shared functionality for Statements.
 * 
 * @author   Hans Lellelid <hans@xmpl.org>
 * @version  $Revision: 17 $
 * @package  creole.common
 */
abstract class StatementCommon {

    /**
     * The database connection.
     * @var Connection
     */ 
    protected $conn;
    
    /**
     * Temporarily hold a ResultSet object after an execute() query.
     * @var ResultSet
     */
    protected $resultSet;

    /**
     * Temporary hold the affected row count after an execute() query.
     * @var int
     */
    protected $updateCount;
    
    /**
     * Array of warning objects generated by methods performed on result set.
     * @var array SQLWarning[]
     */
    protected $warnings = array();
    
    /** 
     * The ResultSet class name.
     * @var string
     */
    protected $resultClass;
    
    /**
     * The prepared statement resource id.
     * @var resource
     */
    protected $stmt;
    
    /**
     * Max rows to retrieve from DB.
     * @var int
     */
    protected $limit = 0;
    
    /**
     * Offset at which to start processing DB rows.
     * "Skip X rows"
     * @var int
     */
    protected $offset = 0;
    
    /**
     * Create new statement instance.
     * 
     * @param Connection $conn Connection object
     */ 
    function __construct(Connection $conn) 
    {
        $this->conn = $conn;        
    }
    
    /**
     * Sets the maximum number of rows to return from db.
     * This will affect the SQL if the RDBMS supports native LIMIT; if not,
     * it will be emulated.  Limit only applies to queries (not update sql).
     * @param int $v Maximum number of rows or 0 for all rows.
     * @return void
     */
    public function setLimit($v)
    {
        $this->limit = (int) $v;
    }
    
    /**
     * Returns the maximum number of rows to return or 0 for all.
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }
    
    /**
     * Sets the start row.
     * This will affect the SQL if the RDBMS supports native OFFSET; if not,
     * it will be emulated. Offset only applies to queries (not update) and 
     * only is evaluated when LIMIT is set!
     * @param int $v
     * @return void
     */ 
    public function setOffset($v)
    {
        $this->offset = (int) $v;
    }
    
    /**
     * Returns the start row.
     * Offset only applies when Limit is set!
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }
    
    /**
     * Free resources associated with this statement.
     * Some drivers will need to implement this method to free
     * database result resources. 
     * 
     * @return void
     */
    public function close()
    {
        // do nothing here (subclasses will implement)
    }


    /**
     * Generic execute() function has to check to see whether SQL is an update or select query.
     *
     * If you already know whether it's a SELECT or an update (manipulating) SQL, then use
     * the appropriate method, as this one will incur overhead to check the SQL.
     *
     * @param string $sql
     * @param int $fetchmode Fetchmode (only applies to queries).
     *
     * @return bool True if it is a result set, false if not or if no more results (this is identical to JDBC return val).
     * @todo -cStatementCommon Update execute() to not use isSelect() method, but rather to determine type based on returned results.
     */
    public function execute($sql, $fetchmode = null)
    {
        
        if (!$this->isSelect($sql)) {                    
            $this->updateCount = $this->executeUpdate($sql);
            return false;
        } else {
            $this->resultSet = $this->executeQuery($sql, $fetchmode);
            if ($this->resultSet->getRecordCount() === 0) {
                return false;
            }
            return true;
        }
    }

    /**
     * Get result set.
     * This assumes that the last thing done was an executeQuery() or an execute()
     * with SELECT-type query.
     *
     * @return ResultSet (or null if none)
     */
    public function getResultSet()
    {
        return $this->resultSet;
    }

    /**
     * Get update count.
     *
     * @return int Number of records affected, or <code>null</code> if not applicable.
     */
    public function getUpdateCount()
    {
        return $this->updateCount;
    }
        
    /**
     * Returns whether the passed SQL is a SELECT statement.
     * 
     * Returns true if SQL starts with 'SELECT' but not 'SELECT INTO'.  This exists
     * to support the execute() function -- which could either execute an update or
     * a query.
     * 
     * Currently this function does not take into consideration comments, primarily
     * because there are a number of different comment options for different drivers:
     * <pre>
     *     -- SQL-defined comment, but not truly comment in Oracle
     *  # comment in mysql
     *  /* comment in mssql, others * /
     *  // comment sometimes?
     *  REM also comment ...
     * </pre>
     * 
     * If you're wondering why we can't just execute the query and look at the return results
     * to see whether it was an update or a select, the reason is that for update queries we
     * need to do stuff before we execute them -- like start transactions if auto-commit is off.
     * 
     * @param string $sql
     * @return boolean Whether statement is a SELECT SQL statement.
     * @see execute()
     */
    protected function isSelect($sql)
    {
        // is first word is SELECT, then return true, unless it's SELECT INTO ...
        // this doesn't, however, take comments into account ...
        $sql = trim($sql);
        return (stripos($sql, 'select') === 0 && stripos($sql, 'select into ') !== 0);
    }

    /**
     * Executes the SQL query in this PreparedStatement object and returns the resultset generated by the query.
     * 
     * @param string $sql This method may optionally be called with the SQL statement.
     * @param int $fetchmode The mode to use when fetching the results (e.g. ResultSet::FETCHMODE_NUM, ResultSet::FETCHMODE_ASSOC).
     * @return ResultSet
     * @throws SQLException If there is an error executing the specified query.
     * @todo -cStatementCommon Put native query execution logic in statement subclasses.
     */
    public function executeQuery($sql, $fetchmode = null)
    {
        $this->updateCount = null;
        if ($this->limit > 0 || $this->offset > 0) {
            $this->conn->applyLimit($sql, $this->offset, $this->limit);
        }
        $this->resultSet = $this->conn->executeQuery($sql, $fetchmode);
        return $this->resultSet;
    }

    /**
     * Executes the SQL INSERT, UPDATE, or DELETE statement in this PreparedStatement object.
     * 
     * @param string $sql This method may optionally be called with the SQL statement.
     * @return int Number of affected rows (or 0 for drivers that return nothing).
     * @throws SQLException if a database access error occurs.
     */
    public function executeUpdate($sql) 
    {
        if ($this->resultSet) $this->resultSet->close();
        $this->resultSet = null;
        $this->updateCount = $this->conn->executeUpdate($sql);
        return $this->updateCount;
    }
    
    /**
     * Gets next result set (if this behavior is supported by driver).
     * Some drivers (e.g. MSSQL) support returning multiple result sets -- e.g.
     * from stored procedures.
     *
     * This function also closes any current resultset.
     *
     * Default behavior is for this function to return false.  Driver-specific
     * implementations of this class can override this method if they actually
     * support multiple result sets.
     * 
     * @return boolean True if there is another result set, otherwise false.
     */
    public function getMoreResults()
    {
        if ($this->resultSet) $this->resultSet->close();        
        $this->resultSet = null;
        return false;        
    }
     
    /**
     * Gets the db Connection that created this statement.
     * @return Connection
     */
    public function getConnection()
    {
        return $this->conn;
    }
}
