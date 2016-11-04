<?php
namespace BasicInvoices\Option;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Adapter;

class Option
{
    /**
     * @var AdapterInterface
     */
    private $__adapter;
    
    /**
     * @var array
     */
    private $__vars = [];
    
    /**
     * The database table.
     * 
     * @var string
     */
    private $__table = 'options';
    
    /**
     * Constructor.
     * 
     * @param AdapterInterface $adapter
     * @param string $table
     */
    public function __construct(AdapterInterface $adapter, $table = 'options')
    {
        $this->__adapter = $adapter;
        $this->__table   = $table;
    }
    
    /**
     * Get an option value.
     * 
     * @param string $name
     * @param mixed $default
     * @return mixed|string
     */
    public function get($name, $default = null)
    {
        if (array_key_exists($name, $this->__vars)) {
            return $this->__vars[$name];
        }
        
        $sql    = new Sql($this->__adapter);
        $select = $sql->select($this->__table);
        $select->columns(['value']);
        $select->where([
            'name' => $name,
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        if (($result instanceof ResultInterface) && ($result->isQueryResult()) && ($result->getAffectedRows())) {
            if ($current = $result->current()) {
                if ($current['value'] === null) {
                    $this->__vars[$name] = null;
                } else {
                    $unserialized = @unserialize($current['value']);
                    if (($unserialized !== false) || (strcmp($current['value'], 'b:0') === 0)) {
                        $this->__vars[$name] = $unserialized;
                    } else {
                        $this->__vars[$name] = $current['value'];
                    }
                }
                return $this->__vars[$name];
            }
        }
        
        return $default;
    }
    
    /**
     * Check if an option is set.
     * 
     * @param string $name
     * @return boolean|unknown
     */
    public function has($name)
    {
        if (array_key_exists($name, $this->__vars)) {
            return true;
        }
        
        $sql    = new Sql($this->__adapter);
        $select = $sql->select($this->__table);
        $select->columns(['value']);
        $select->where([
            'name' => $name,
        ]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        if (($result instanceof ResultInterface) && ($result->isQueryResult()) && ($result->getAffectedRows())) {
            if ($current = $result->current()) {
                $unserialized = @unserialize($current['value']);
                if (($unserialized !== false) || (strcmp($current['value'], 'b:0') === 0)) {
                    $this->__vars[$name] = $unserialized;
                } else {
                    $this->__vars[$name] = $current['value'];
                }
                return true;
            }
        }
        
        return $false;
    }
    
    /**
     * Set the option value.
     * 
     * @param string $name
     * @param mixed $value
     * @return Option
     */
    public function set($name, $value)
    {
        $sql = new Sql($this->__adapter);
        
        if (is_string($value) || is_null($value)) {
            $serialized = $value;
        } elseif (is_numeric($value)) {
            $serialized = $value;
        } else {
            $serialized = serialize($value);
        }
        
        if ($this->has($name)) {
            $update = $sql->update($this->__table);
            $update->set([
                'value' => $serialized,
            ]);
            $update->where([
                'name'  => $name,
            ]);
            
            $statement = $sql->prepareStatementForSqlObject($update);
            $result    = $statement->execute();
        } else {
            $insert = $sql->insert($this->__table);
            $insert->values([
                'name'  => $name,
                'value' => $serialized,
            ]);
            
            $statement = $sql->prepareStatementForSqlObject($insert);
            $result    = $statement->execute();
        }
        
        if ($result instanceof ResultInterface) {
            if ($result->getAffectedRows()) {
                $this->__vars[$name] = $value;
                return $this;
            }
        }
        
        throw new \RuntimeException(sprintf('The option "%s" could not be saved', $name));
    }
}