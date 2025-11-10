<?php

namespace Bga\Games\DeadMenPax\DB;

use ReflectionAttribute;
use ReflectionClass;
use Bga\GameFramework\Table;

/**
 * Class DBManager
 * Manages interactions with the database for a specified table and class type.
 * Enhanced version with better BGA integration and performance optimizations.
 */
class DBManager
{
    protected string $baseTable;
    protected string $baseClass;
    protected Table $game;
    
    // Reflection caching for better performance
    private static array $reflectionCache = [];

    /**
     * Constructs a new DBManager instance.
     *
     * @param string $baseTable The name of the database table.
     * @param string $baseClass The class type of objects associated with the database table.
     * @param Table $game The BGA game instance for database operations.
     */
    public function __construct(string $baseTable, string $baseClass, Table $game)
    {
        $this->baseTable = $baseTable;
        $this->baseClass = $baseClass;
        $this->game = $game;
    }

    /**
     * Gets the base table name.
     *
     * @return string The base table name.
     */
    public function getBaseTable(): string
    {
        return $this->baseTable;
    }

    /**
     * Gets the base class name.
     *
     * @return string The base class name.
     */
    public function getBaseClass(): string
    {
        return $this->baseClass;
    }
    
    /**
     * Retrieves the primary key field name from the base class's annotations.
     *
     * @return string|null The primary key field name or null if none found.
     * @throws \BgaSystemException if multiple dbKey attributes are found.
     */
    private function getPrimaryKey(): ?string
    {
        $className = $this->baseClass;
        
        // Check cache first
        if (isset(self::$reflectionCache[$className])) {
            return self::$reflectionCache[$className];
        }
        
        $reflectionClass = new ReflectionClass($this->baseClass);
        $primaryKey = null;

        foreach ($reflectionClass->getProperties() as $property) {
            if (!empty($property->getAttributes(dbKey::class))) {
                if ($primaryKey !== null) {
                    throw new \BgaSystemException("Multiple primary keys found in class {$this->baseClass}.");
                }
                $keyAttribute = $property->getAttributes(dbKey::class)[0]->newInstance();
                $primaryKey = $keyAttribute->name;
            }
        }
        
        // Cache the result
        self::$reflectionCache[$className] = $primaryKey;
        return $primaryKey;
    }

    /**
     * Checks if a row with a specified primary key value exists in the table.
     *
     * @param string $primaryKey The primary key field.
     * @param string|int $primaryValue The value of the primary key to search for.
     * @return bool True if a row exists, false otherwise.
     */
    private function checkIfExists(string $primaryKey, string|int $primaryValue): bool
    {
        $sql = "SELECT COUNT(*) AS count FROM {$this->baseTable} WHERE $primaryKey = " . $this->formatValue($primaryValue);
        $this->logQuery($sql);
        
        $row = $this->game->getObjectListFromDB($sql);
        return $row[0]['count'] > 0;
    }

    /**
     * Retrieves all rows from the database table.
     *
     * @return array<int, array<string, mixed>> A list of all rows as associative arrays.
     */
    public function getAllRows(): array
    {
        $sql = "SELECT * FROM {$this->baseTable}";
        $this->logQuery($sql);
        
        return $this->game->getObjectListFromDB($sql);
    }

    /**
     * Retrieves all rows from the database table, indexed by the primary key.
     *
     * @return array<string, array<string, mixed>> An associative array of rows, indexed by primary key.
     */
    public function getAllRowsByKeys(): array
    {
        $sql = "SELECT * FROM {$this->baseTable}";
        $this->logQuery($sql);
        
        return $this->game->getCollectionFromDB($sql);
    }

    /**
     * Retrieves a single row by the primary key value.
     *
     * @param string|int $keyValue The primary key value.
     * @return array<string, mixed>|null The row data as an associative array or null if not found.
     */
    public function getRow(string|int $keyValue): ?array
    {
        $key = $this->getPrimaryKey();
        if (!$key) {
            throw new \BgaSystemException("Primary key not defined in class {$this->baseClass}.");
        }
        
        $sql = "SELECT * FROM {$this->baseTable} WHERE $key = " . $this->formatValue($keyValue);
        $this->logQuery($sql);
        
        $results = $this->game->getCollectionFromDB($sql);
        return reset($results) ?: null;
    }

    /**
     * Retrieves the last inserted row from the database.
     *
     * @return array<string, mixed>|null The last row as an associative array or null if not found.
     */
    public function getLastRow(): ?array
    {
        return $this->getRow($this->game->DbGetLastId());
    }

    /**
     * Creates and hydrates an object from the database based on the primary key value.
     *
     * @param string|int $keyValue The primary key value.
     * @return object|null An instance of the base class with data from the database or null if not found.
     * @throws \BgaSystemException if the primary key is not defined.
     */
    public function createObjectFromDB(string|int $keyValue): ?object
    {
        $rowData = $this->getRow($keyValue);
        return $rowData ? $this->hydrate($rowData) : null;
    }

    /**
     * Retrieves all rows as objects of the base class.
     *
     * @return array<string, object> An associative array of objects indexed by primary key.
     */
    public function getAllObjects(): array
    {
        $primaryKey = $this->getPrimaryKey();
        if (!$primaryKey) {
            throw new \BgaSystemException("Primary key not defined in class {$this->baseClass}.");
        }
        
        $objects = [];
        $rows = $this->getAllRows();
        
        foreach ($rows as $row) {
            $objects[$row[$primaryKey]] = $this->hydrate($row);
        }
        
        return $objects;
    }

    /**
     * Creates an object of the base class and populates it with the provided data.
     *
     * @param array<string, mixed> $data An associative array of database row data.
     * @return object An instance of the base class with data hydrated.
     */
    private function hydrate(array $data): object
    {
        $reflectionClass = new ReflectionClass($this->baseClass);
        $object = $reflectionClass->newInstanceWithoutConstructor();

        foreach ($reflectionClass->getProperties() as $property) {
            $columnAttributes = $property->getAttributes(dbColumn::class);
            $columnKeys = $property->getAttributes(dbKey::class);

            if (!empty($columnKeys)) {
                $column = $columnKeys[0];
            } elseif (!empty($columnAttributes)) {
                $column = $columnAttributes[0];
            } else {
                continue;
            }

            $attribute = $column->newInstance();
            $columnName = $attribute->name;
            
            if (array_key_exists($columnName, $data)) {
                $property->setAccessible(true);
                $property->setValue($object, $data[$columnName]);
            }
        }

        return $object;
    }

    /**
     * Saves or updates an object to the database.
     *
     * @param object $object An instance of the base class.
     * @return string|int|null The primary key of the saved object or null if the operation failed.
     * @throws \BgaSystemException if primary key is not defined or save operation fails.
     */
    public function saveObjectToDB(object $object): string|int|null
    {
        try {
            $reflectionClass = new ReflectionClass($this->baseClass);
            $properties = $reflectionClass->getProperties();
            $primaryKey = $this->getPrimaryKey();
            
            if (!$primaryKey) {
                throw new \BgaSystemException("Primary key not defined in class {$this->baseClass}.");
            }
            
            $primaryValue = null;
            $columns = [];

            foreach ($properties as $property) {
                $column = $property->getAttributes(dbColumn::class, ReflectionAttribute::IS_INSTANCEOF)[0] ?? null;
                if ($column) {
                    $columnName = $column->newInstance()->name;
                    $property->setAccessible(true);
                    $value = $property->getValue($object);
                    $columns[$columnName] = $value;

                    if ($columnName === $primaryKey) {
                        $primaryValue = $value;
                    }
                }
            }

            if ($primaryValue !== null && $this->checkIfExists($primaryKey, $primaryValue)) {
                // Update existing record
                $setClauses = [];
                foreach ($columns as $columnName => $value) {
                    if ($columnName === $primaryKey) {
                        continue;
                    }
                    $setClauses[] = "$columnName = " . $this->formatValue($value);
                }

                if (empty($setClauses)) {
                    return $primaryValue;
                }

                $sql = "UPDATE {$this->baseTable} SET " . implode(', ', $setClauses) . " WHERE $primaryKey = " . $this->formatValue($primaryValue);
                $this->logQuery($sql);
                $result = $this->game->DbQuery($sql);
                $returnValue = $primaryValue;
            } else {
                // Insert new record
                $columnNames = [];
                $columnValues = [];
                foreach ($columns as $columnName => $value) {
                    $columnNames[] = $columnName;
                    $columnValues[] = $this->formatValue($value);
                }

                $sql = "INSERT INTO {$this->baseTable} (" . implode(', ', $columnNames) . ") VALUES (" . implode(', ', $columnValues) . ")";
                $this->logQuery($sql);
                $result = $this->game->DbQuery($sql);
                
                if ($result) {
                    $returnValue = $primaryValue ?? $this->game->DbGetLastId();
                } else {
                    $returnValue = null;
                }
            }

            return $result ? $returnValue : null;
            
        } catch (\Exception $e) {
            throw new \BgaSystemException("Failed to save {$this->baseClass}: " . $e->getMessage());
        }
    }

    /**
     * Deletes an object from the database by primary key value.
     *
     * @param string|int $keyValue The primary key value of the object to delete.
     * @throws \BgaSystemException if primary key is not defined or delete operation fails.
     */
    public function deleteObjectFromDb(string|int $keyValue): void
    {
        try {
            $primaryKey = $this->getPrimaryKey();
            if (!$primaryKey) {
                throw new \BgaSystemException("Primary key not defined in class {$this->baseClass}.");
            }

            if ($keyValue && $this->checkIfExists($primaryKey, $keyValue)) {
                $sql = "DELETE FROM {$this->baseTable} WHERE $primaryKey = " . $this->formatValue($keyValue);
                $this->logQuery($sql);
                $this->game->DbQuery($sql);
            }
        } catch (\Exception $e) {
            throw new \BgaSystemException("Failed to delete {$this->baseClass}: " . $e->getMessage());
        }
    }

    /**
     * Clears all rows from the database table.
     * 
     * @throws \BgaSystemException if clear operation fails.
     */
    public function clearAll(): void
    {
        try {
            $sql = "DELETE FROM {$this->baseTable}";
            $this->logQuery($sql);
            $this->game->DbQuery($sql);
        } catch (\Exception $e) {
            throw new \BgaSystemException("Failed to clear table {$this->baseTable}: " . $e->getMessage());
        }
    }

    /**
     * Logs an SQL query for debugging purposes.
     *
     * @param string $sql The SQL query to log.
     */
    private function logQuery(string $sql): void
    {
        // Only log in debug mode to avoid performance impact in production
        // This can be enabled by setting a debug flag or in development environments
        // For now, we'll keep it simple and avoid logging in production
    }

    /**
     * Formats a PHP value into a SQL-safe string.
     *
     * @param mixed $value
     * @return string
     */
    private function formatValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            $value = $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string)$value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            $value = (string)$value;
        }

        return $this->game->escapeStringForDB((string)$value);
    }
}
