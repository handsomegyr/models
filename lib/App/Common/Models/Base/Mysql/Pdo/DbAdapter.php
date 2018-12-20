<?php
namespace App\Common\Models\Base\Mysql\Pdo;

use App\Common\Models\Base\Mysql\Base;

class DbAdapter
{
    // @object, The PDO object
    private $pdo;
    
    // @object, PDO statement object
    private $statement;
    
    // @array, The database settings
    private $descriptor;
    
    // @bool , Connected to the database
    private $bConnected = false;
    
    // @array, The parameters of the SQL query
    private $parameters;

    private $transnum = 0;

    /**
     * Constructor for App\Common\Models\Base\Mysql\Pdo\DbAdapter
     *
     * @param array $descriptor            
     */
    public function __construct(array $descriptor)
    {
        $this->descriptor = $descriptor;
        $this->connect($descriptor);
        $this->parameters = array();
    }

    /**
     * This method is automatically called in \Phalcon\Db\Adapter\Pdo constructor.
     *
     * Call it when you need to restore a database connection.
     *
     * <code>
     * use App\Common\Models\Base\Mysql\Pdo\DbAdapter;
     *
     * // Make a connection
     * $connection = new Mysql(
     * [
     * "host" => "localhost",
     * "username" => "sigma",
     * "password" => "secret",
     * "dbname" => "blog",
     * "port" => 3306,
     * ]
     * );
     *
     * // Reconnect
     * $connection->connect();
     * </code>
     *
     * @param array $descriptor            
     * @return bool
     */
    public function connect(array $descriptor = null)
    {
        $this->descriptor = $descriptor;
        $dsn = 'mysql:dbname=' . $this->descriptor["dbname"] . ';host=' . $this->descriptor["host"] . ';charset=' . $this->descriptor['charset'];
        
        $this->pdo = new \PDO($dsn, $this->descriptor["username"], $this->descriptor["password"], array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ));
        
        // We can now log any exceptions on Fatal error.
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        // Disable emulation of prepared statements, use REAL prepared statements instead.
        $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        
        // $this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        
        // Connection succeeded, set the boolean to true.
        $this->bConnected = true;
    }

    /**
     * Sets the event manager
     *
     * @param \Phalcon\Events\ManagerInterface $eventsManager            
     */
    public function setEventsManager(\Phalcon\Events\ManagerInterface $eventsManager)
    {}

    /**
     * Returns the internal event manager
     *
     * @return \Phalcon\Events\ManagerInterface
     */
    public function getEventsManager()
    {}

    /**
     * Sends SQL statements to the database server returning the success state.
     * Use this method only when the SQL statement sent to the server is returning rows
     *
     * <code>
     * // Querying data
     * $resultset = $connection->query(
     * "SELECT FROM robots WHERE type = 'mechanical'"
     * );
     *
     * $resultset = $connection->query(
     * "SELECT FROM robots WHERE type = ?",
     * [
     * "mechanical",
     * ]
     * );
     * </code>
     *
     * @param string $sqlStatement            
     * @param mixed $bindParams            
     * @param mixed $bindTypes            
     * @return PDOStatement If the database server successfully prepares the statement,
     */
    public function query($sqlStatement, $bindParams = null, $bindTypes = null)
    {
        $query = trim(str_replace("\r", " ", $sqlStatement));
        $this->statement = $this->getPDOStatement($query, $bindParams);
        $this->statement->execute();
        return $this->statement;
    }

    /**
     * Sends SQL statements to the database server returning the success state.
     * Use this method only when the SQL statement sent to the server doesn't return any rows
     *
     * <code>
     * // Inserting data
     * $success = $connection->execute(
     * "INSERT INTO robots VALUES (1, 'Astro Boy')"
     * );
     *
     * $success = $connection->execute(
     * "INSERT INTO robots VALUES (?, ?)",
     * [
     * 1,
     * "Astro Boy",
     * ]
     * );
     * </code>
     *
     * @param string $sqlStatement            
     * @param mixed $bindParams            
     * @param mixed $bindTypes            
     * @return bool
     */
    public function execute($sqlStatement, $bindParams = null, $bindTypes = null)
    {
        $query = trim(str_replace("\r", " ", $sqlStatement));
        
        $this->statement = $this->getPDOStatement($query, $bindParams);
        
        // Execute SQL
        return $this->statement->execute();
    }

    /**
     * Returns the number of affected rows by the latest INSERT/UPDATE/DELETE executed in the database system
     *
     * <code>
     * $connection->execute(
     * "DELETE FROM robots"
     * );
     *
     * echo $connection->affectedRows(), " were deleted";
     * </code>
     *
     * @return int
     */
    public function affectedRows()
    {
        if ($this->statement) {
            return $this->statement->rowCount();
        }
    }

    /**
     * Closes the active connection returning success.
     * Phalcon automatically closes and destroys
     * active connections when the request ends
     *
     * @return bool
     */
    public function close()
    {
        $this->pdo = null;
        $this->bConnected = false;
    }

    /**
     * Returns the insert id for the auto_increment/serial column inserted in the latest executed SQL statement
     *
     * <code>
     * // Inserting a new robot
     * $success = $connection->insert(
     * "robots",
     * [
     * "Astro Boy",
     * 1952,
     * ],
     * [
     * "name",
     * "year",
     * ]
     * );
     *
     * // Getting the generated id
     * $id = $connection->lastInsertId();
     * </code>
     *
     * @param string $sequenceName            
     * @return int|bool
     */
    public function lastInsertId($sequenceName = null)
    {
        if ($this->pdo) {
            return $this->pdo->lastInsertId();
        } else {
            return false;
        }
    }

    /**
     * Starts a transaction in the connection
     *
     * @param bool $nesting            
     * @return bool
     */
    public function begin($nesting = true)
    {
        if ($this->pdo) {
            $this->transnum ++;
            if ($this->transnum == 1) {
                return $this->pdo->beginTransaction();
            } else {
                return true;
            }
        } else {
            throw new \Exception("pdo is empty");
            return false;
        }
    }

    /**
     * Rollbacks the active transaction in the connection
     *
     * @param bool $nesting            
     * @return bool
     */
    public function rollback($nesting = true)
    {
        if ($this->pdo) {
            $this->transnum --;
            if (empty($this->transnum)) {
                return $this->pdo->rollBack();
            } else {
                return false;
            }
        } else {
            throw new \Exception("pdo is empty");
            return false;
        }
    }

    /**
     * Commits the active transaction in the connection
     *
     * @param bool $nesting            
     * @return bool
     */
    public function commit($nesting = true)
    {
        if ($this->pdo) {
            $this->transnum --;
            if (empty($this->transnum)) {
                return $this->pdo->commit();
            } else {
                return true;
            }
        } else {
            throw new \Exception("pdo is empty");
            return false;
        }
    }

    /**
     * Every method which needs to execute a SQL query uses this method.
     *
     * @return PDOStatement If the database server successfully prepares the statement,
     */
    private function getPDOStatement($query, $parameters = "")
    {
        // Connect to database
        if (! $this->pdo) {
            $this->connect($this->descriptor);
        }
        
        // Prepare query
        $statement = $this->pdo->prepare($query);
        
        // Bind parameters
        if (! empty($parameters)) {
            foreach ($parameters as $param => $value) {
                if (is_int($value)) {
                    $type = \PDO::PARAM_INT;
                } elseif (is_bool($value)) {
                    $type = \PDO::PARAM_BOOL;
                } elseif (is_null($value)) {
                    $type = \PDO::PARAM_NULL;
                } else {
                    $type = \PDO::PARAM_STR;
                }
                
                // Add type when binding the values to the column
                $statement->bindValue($param, $value, $type);
            }
        }
        return $statement;
    }

    public function getDescriptor()
    {
        return $this->descriptor;
    }
}
