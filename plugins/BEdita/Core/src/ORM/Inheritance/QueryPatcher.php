<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\ORM\Inheritance;

use Cake\Database\ExpressionInterface;
use Cake\Database\Expression\FieldInterface;
use Cake\Database\Expression\IdentifierExpression;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\Table;

/**
 * QueryPatcher class.
 *
 * Patch the original Query instance using the Table inheritance
 *
 * @since 4.0.0
 */
class QueryPatcher
{
    /**
     * Table instance.
     *
     * @var \Cake\ORM\Table
     */
    protected $table = null;

    /**
     * Query instance.
     *
     * @var \Cake\ORM\Query
     */
    protected $query = null;

    /**
     * It keeps trace of inheritance to avoid repeated operations
     *
     * @var array
     */
    protected $inheritanceMap = [];

    /**
     * The alias checker used to extract fields name aliased with table alias
     *
     * @var array
     */
    protected $aliasChecker = [
        'string' => '',
        'length' => 0
    ];

    /**
     * Constructor.
     *
     * @param \Cake\ORM\Table $table The Table instance
     */
    public function __construct(Table $table)
    {
        if (!$table->hasBehavior('ClassTableInheritance')) {
            throw new \InvalidArgumentException(sprintf(
                'Table %s must use ClassTableInheritance behavior',
                $table->alias()
            ));
        }
        $this->table = $table;
        $this->aliasChecker['string'] = $table->alias() . '.';
        $this->aliasChecker['length'] = strlen($this->aliasChecker['string']);
    }

    /**
     * Return the complete table inheritance of `$this->table`.
     * Once obtained it returns its value without recalculate it.
     *
     * @return array
     */
    protected function inheritedTables()
    {
        if (array_key_exists('tables', $this->inheritanceMap)) {
            return $this->inheritanceMap['tables'];
        }

        $this->inheritanceMap['tables'] = $this->table->inheritedTables(true);

        return $this->inheritanceMap['tables'];
    }

    /**
     * Prepare the Query to patch
     *
     * @param \Cake\ORM\Query $query The Query to patch
     * @return $this
     */
    public function patch(Query $query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Execute all the patches and return the query patched
     *
     * @return \Cake\ORM\Query
     */
    public function all()
    {
        $this->contain();

        return $this->query
            ->traverseExpressions([$this, 'fixExpression'])
            ->traverse(
                [$this, 'fixClause'],
                ['select', 'group', 'distinct']
            );
    }

    /**
     * Arrange contain mapping tables to the right association
     *
     * * add inherited tables
     * * arrange tables in \Cake\ORM\Query::contain() to the right inherited table
     * * override contain data with that calculated
     *
     * @return $this
     */
    public function contain()
    {
        $inheritedTables = array_map(function (Table $table) {
            return $table->alias();
        }, $this->inheritedTables());

        $contain = $this->query->contain();
        $contain += array_fill_keys($inheritedTables, []);
        $result = [];

        foreach ($contain as $tableName => $tableContain) {
            $containString = $this->buildContainString($tableName);
            if ($containString === false) {
                $containString = $tableName;
            }
            $result[$containString] = $tableContain;
        }

        $this->query->contain($result, true);

        return $this;
    }

    /**
     * Given a table name return the right contain string
     *
     * If `$tableName` is a direct association to current table return it
     * else search if `$tableName` is associated to a inherited table
     *
     * Return false if `$tableName` is not found as association to any table
     *
     * @param string $tableName The starting table name
     * @return string|false
     */
    public function buildContainString($tableName)
    {
        if ($this->table->association($tableName)) {
            return $tableName;
        }

        foreach ($this->inheritedTables() as $inherited) {
            $containString = empty($containString) ? $inherited->alias() : $containString . '.' . $inherited->alias();
            if (!$inherited->association($tableName)) {
                continue;
            }

            return $containString . '.' . $tableName;
        }

        return false;
    }

    /**
     * Fix sql clause mapping inherited fields.
     *
     * Pay attention that this method just fixes clauses in the format of string or array of string.
     * Moreover at the end `\Cake\ORM\Query::$clause()` is called with overwrite `true` as second param
     * so assure that the clause method of `\Cake\ORM\Query` has the right signature.
     *
     * If you have to fix query expression you should use `self::fixExpression()` instead.
     *
     * @param array|\Cake\Database\ExpressionInterface|bool|string $clauseData The clause data
     * @param string $clause The sql clause
     * @return $this
     */
    public function fixClause($clauseData, $clause)
    {
        $clauseData = $clauseData ?: $this->query->clause($clause);
        if (empty($clauseData) || is_bool($clauseData) || $clauseData instanceof ExpressionInterface) {
            return $this;
        }

        if (!is_array($clauseData)) {
            $clauseData = [$clauseData];
        }

        foreach ($clauseData as $key => $data) {
            if (!is_string($data)) {
                continue;
            }

            $clauseData[$key] = $this->aliasField($data);
        }

        $this->query->{$clause}($clauseData, true);

        return $this;
    }

    /**
     * Fix query expressions mapping inherited fields
     *
     * @param \Cake\Database\ExpressionInterface $expression The expression to manipulate
     * @return $this
     */
    public function fixExpression(ExpressionInterface $expression)
    {
        if ($expression instanceof IdentifierExpression) {
            $identifier = $expression->getIdentifier();
            $expression->setIdentifier($this->aliasField($identifier));

            return $this;
        }

        if ($expression instanceof FieldInterface) {
            $field = $expression->getField();
            if (is_string($field)) {
                $expression->setField($this->aliasField($field));
            }

            return $this;
        }

        if ($expression instanceof QueryExpression) {
            $expression->iterateParts(function ($value, &$key) {
                if (!is_numeric($key)) {
                    $key = $this->aliasField($key);
                }

                if (is_string($value)) {
                    return $this->aliasField($value);
                }

                return $value;
            });
        }

        return $this;
    }

    /**
     * Given a `$field` return itself aliased as `TableAlias.column_name`
     *
     * If `$field` doesn't correspond to any inherited table columns
     * then return it without any change.
     *
     * @param string $field The field string
     * @return string
     */
    public function aliasField($field)
    {
        $field = $this->extractField($field);

        if (strpos($field, '.') !== false) {
            return $field;
        }

        if ($this->table->hasField($field)) {
            return $this->table->alias() . '.' . $field;
        }

        foreach ($this->inheritedTables() as $inherited) {
            if (!$inherited->hasField($field)) {
                continue;
            }

            return $inherited->alias() . '.' . $field;
        }

        return $field;
    }

    /**
     * Given a `$field` returns it without the `self::$table` alias
     *
     * @param string $field The field string
     * @return string
     */
    protected function extractField($field)
    {
        $aliasedWith = substr($field, 0, $this->aliasChecker['length']);
        if ($aliasedWith == $this->aliasChecker['string']) {
            $field = substr($field, $this->aliasChecker['length']);
        }

        return $field;
    }
}
