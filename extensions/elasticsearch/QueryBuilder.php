<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\elasticsearch;

use yii\base\InvalidParamException;
use yii\base\NotSupportedException;
use yii\helpers\Json;

/**
 * QueryBuilder builds an elasticsearch query based on the specification given as a [[Query]] object.
 *
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class QueryBuilder extends \yii\base\Object
{
	/**
	 * @var Connection the database connection.
	 */
	public $db;

	/**
	 * Constructor.
	 * @param Connection $connection the database connection.
	 * @param array $config name-value pairs that will be used to initialize the object properties
	 */
	public function __construct($connection, $config = [])
	{
		$this->db = $connection;
		parent::__construct($config);
	}

	/**
	 * Generates query from a [[Query]] object.
	 * @param Query $query the [[Query]] object from which the query will be generated
	 * @return array the generated SQL statement (the first array element) and the corresponding
	 * parameters to be bound to the SQL statement (the second array element).
	 */
	public function build($query)
	{
		$parts = [];

		if ($query->fields !== null) {
			$parts['fields'] = (array) $query->fields;
		}
		if ($query->limit !== null && $query->limit >= 0) {
			$parts['size'] = $query->limit;
		}
		if ($query->offset > 0) {
			$parts['from'] = (int) $query->offset;
		}

		if (empty($parts['query'])) {
			$parts['query'] = ["match_all" => (object)[]];
		}

		$whereFilter = $this->buildCondition($query->where);
		if (is_string($query->filter)) {
			if (empty($whereFilter)) {
				$parts['filter'] = $query->filter;
			} else {
				$parts['filter'] = '{"and": [' . $query->filter . ', ' . Json::encode($whereFilter) . ']}';
			}
		} elseif ($query->filter !== null) {
			if (empty($whereFilter)) {
				$parts['filter'] = $query->filter;
			} else {
				$parts['filter'] = ['and' => [$query->filter, $whereFilter]];
			}
		} elseif (!empty($whereFilter)) {
			$parts['filter'] = $whereFilter;
		}

		$sort = $this->buildOrderBy($query->orderBy);
		if (!empty($sort)) {
			$parts['sort'] = $sort;
		}

		if (!empty($query->facets)) {
			$parts['facets'] = $query->facets;
		}

		$options = [];
		if ($query->timeout !== null) {
			$options['timeout'] = $query->timeout;
		}

		return [
			'queryParts' => $parts,
			'index' => $query->index,
			'type' => $query->type,
			'options' => $options,
		];
	}

	/**
	 * adds order by condition to the query
	 */
	public function buildOrderBy($columns)
	{
		if (empty($columns)) {
			return [];
		}
		$orders = [];
		foreach ($columns as $name => $direction) {
			if (is_string($direction)) {
				$column = $direction;
				$direction = SORT_ASC;
			} else {
				$column = $name;
			}
			if ($column == ActiveRecord::PRIMARY_KEY_NAME) {
				$column = '_uid';
			}

			// allow elasticsearch extended syntax as described in http://www.elasticsearch.org/guide/reference/api/search/sort/
			if (is_array($direction)) {
				$orders[] = [$column => $direction];
			} else {
				$orders[] = [$column => ($direction === SORT_DESC ? 'desc' : 'asc')];
			}
		}
		return $orders;
	}

	/**
	 * Parses the condition specification and generates the corresponding SQL expression.
	 * @param string|array $condition the condition specification. Please refer to [[Query::where()]]
	 * on how to specify a condition.
	 * @param array $params the binding parameters to be populated
	 * @return string the generated SQL expression
	 * @throws \yii\db\Exception if the condition is in bad format
	 */
	public function buildCondition($condition)
	{
		static $builders = array(
			'and' => 'buildAndCondition',
			'or' => 'buildAndCondition',
			'between' => 'buildBetweenCondition',
			'not between' => 'buildBetweenCondition',
			'in' => 'buildInCondition',
			'not in' => 'buildInCondition',
			'like' => 'buildLikeCondition',
			'not like' => 'buildLikeCondition',
			'or like' => 'buildLikeCondition',
			'or not like' => 'buildLikeCondition',
		);

		if (empty($condition)) {
			return [];
		}
		if (!is_array($condition)) {
			throw new NotSupportedException('String conditions in where() are not supported by elasticsearch.');
		}
		if (isset($condition[0])) { // operator format: operator, operand 1, operand 2, ...
			$operator = strtolower($condition[0]);
			if (isset($builders[$operator])) {
				$method = $builders[$operator];
				array_shift($condition);
				return $this->$method($operator, $condition);
			} else {
				throw new InvalidParamException('Found unknown operator in query: ' . $operator);
			}
		} else { // hash format: 'column1' => 'value1', 'column2' => 'value2', ...
			return $this->buildHashCondition($condition);
		}
	}

	private function buildHashCondition($condition)
	{
		$parts = [];
		foreach($condition as $attribute => $value) {
			if ($attribute == ActiveRecord::PRIMARY_KEY_NAME) {
				if ($value == null) { // there is no null pk
					$parts[] = ['script' => ['script' => '0==1']];
				} else {
					$parts[] = ['ids' => ['values' => is_array($value) ? $value : [$value]]];
				}
			} else {
				if (is_array($value)) { // IN condition
					$parts[] = ['in' => [$attribute => $value]];
				} else {
					if ($value === null) {
						$parts[] = ['missing' => ['field' => $attribute, 'existence' => true, 'null_value' => true]];
					} else {
						$parts[] = ['term' => [$attribute => $value]];
					}
				}
			}
		}
		return count($parts) === 1 ? $parts[0] : ['and' => $parts];
	}

	private function buildAndCondition($operator, $operands)
	{
		$parts = [];
		foreach ($operands as $operand) {
			if (is_array($operand)) {
				$operand = $this->buildCondition($operand);
			}
			if (!empty($operand)) {
				$parts[] = $operand;
			}
		}
		if (!empty($parts)) {
			return [$operator => $parts];
		} else {
			return [];
		}
	}

	private function buildBetweenCondition($operator, $operands)
	{
		if (!isset($operands[0], $operands[1], $operands[2])) {
			throw new InvalidParamException("Operator '$operator' requires three operands.");
		}

		list($column, $value1, $value2) = $operands;
		if ($column == ActiveRecord::PRIMARY_KEY_NAME) {
			throw new NotSupportedException('Between condition is not supported for primaryKey.');
		}
		$filter = ['range' => [$column => ['gte' => $value1, 'lte' => $value2]]];
		if ($operator == 'not between') {
			$filter = ['not' => $filter];
		}
		return $filter;
	}

	private function buildInCondition($operator, $operands)
	{
		if (!isset($operands[0], $operands[1])) {
			throw new InvalidParamException("Operator '$operator' requires two operands.");
		}

		list($column, $values) = $operands;

		$values = (array)$values;

		if (empty($values) || $column === []) {
			return $operator === 'in' ? ['script' => ['script' => '0==1']] : [];
		}

		if (count($column) > 1) {
			return $this->buildCompositeInCondition($operator, $column, $values, $params);
		} elseif (is_array($column)) {
			$column = reset($column);
		}
		$canBeNull = false;
		foreach ($values as $i => $value) {
			if (is_array($value)) {
				$values[$i] = $value = isset($value[$column]) ? $value[$column] : null;
			}
			if ($value === null) {
				$canBeNull = true;
				unset($values[$i]);
			}
		}
		if ($column == ActiveRecord::PRIMARY_KEY_NAME) {
			if (empty($values) && $canBeNull) { // there is no null pk
				$filter = ['script' => ['script' => '0==1']];
			} else {
				$filter = ['ids' => ['values' => array_values($values)]];
				if ($canBeNull) {
					$filter = ['or' => [$filter, ['missing' => ['field' => $column, 'existence' => true, 'null_value' => true]]]];
				}
			}
		} else {
			if (empty($values) && $canBeNull) {
				$filter = ['missing' => ['field' => $column, 'existence' => true, 'null_value' => true]];
			} else {
				$filter = ['in' => [$column => array_values($values)]];
				if ($canBeNull) {
					$filter = ['or' => [$filter, ['missing' => ['field' => $column, 'existence' => true, 'null_value' => true]]]];
				}
			}
		}
		if ($operator == 'not in') {
			$filter = ['not' => $filter];
		}
		return $filter;
	}

	protected function buildCompositeInCondition($operator, $columns, $values)
	{
		throw new NotSupportedException('composite in is not supported by elasticsearch.');
		$vss = array();
		foreach ($values as $value) {
			$vs = array();
			foreach ($columns as $column) {
				if (isset($value[$column])) {
					$phName = self::PARAM_PREFIX . count($params);
					$params[$phName] = $value[$column];
					$vs[] = $phName;
				} else {
					$vs[] = 'NULL';
				}
			}
			$vss[] = '(' . implode(', ', $vs) . ')';
		}
		foreach ($columns as $i => $column) {
			if (strpos($column, '(') === false) {
				$columns[$i] = $this->db->quoteColumnName($column);
			}
		}
		return '(' . implode(', ', $columns) . ") $operator (" . implode(', ', $vss) . ')';
	}

	private function buildLikeCondition($operator, $operands)
	{
		throw new NotSupportedException('like conditions is not supported by elasticsearch.');
		if (!isset($operands[0], $operands[1])) {
			throw new Exception("Operator '$operator' requires two operands.");
		}

		list($column, $values) = $operands;

		$values = (array)$values;

		if (empty($values)) {
			return $operator === 'LIKE' || $operator === 'OR LIKE' ? '0==1' : '';
		}

		if ($operator === 'LIKE' || $operator === 'NOT LIKE') {
			$andor = ' AND ';
		} else {
			$andor = ' OR ';
			$operator = $operator === 'OR LIKE' ? 'LIKE' : 'NOT LIKE';
		}

		if (strpos($column, '(') === false) {
			$column = $this->db->quoteColumnName($column);
		}

		$parts = array();
		foreach ($values as $value) {
			$phName = self::PARAM_PREFIX . count($params);
			$params[$phName] = $value;
			$parts[] = "$column $operator $phName";
		}

		return implode($andor, $parts);
	}
}
