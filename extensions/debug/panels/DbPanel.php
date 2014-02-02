<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\debug\panels;

use Yii;
use yii\debug\Panel;
use yii\log\Logger;
use yii\debug\models\search\Db;

/**
 * Debugger panel that collects and displays database queries performed.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DbPanel extends Panel
{
	/**
	 *
	 * @var integer total queries critical count
	 */
	public $criticalQueriesCount;
	/**
	 * @var array db queries info extracted to array as models, to use with data provider.
	 */
	private $_models;

	/**
	 * @var array current database request timings
	 */
	private $_timings;

	/**
	 * @inheritdoc
	 */
	public function getName()
	{
		return 'Database';
	}

	/**
	 * @inheritdoc
	 */
	public function getSummary()
	{
		$timings = $this->calculateTimings();
		$queryCount = count($timings);
		$queryTime = number_format($this->getTotalQueryTime($timings) * 1000) . ' ms';

		return Yii::$app->view->render('panels/db/summary', [
			'timings' => $this->calculateTimings(), 
			'panel' => $this,
			'queryCount' => $queryCount,
			'queryTime' => $queryTime,
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function getDetail()
	{
		$searchModel = new Db();
		$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams(), $this->getModels());

		return Yii::$app->view->render('panels/db/detail', [
			'panel' => $this,
			'dataProvider' => $dataProvider,
			'searchModel' => $searchModel,
		]);
	}

	/**
	 * Calculates given request profile timings.
	 *
	 * @return array timings [token, category, timestamp, traces, nesting level, elapsed time]
	 */
	protected function calculateTimings()
	{
		if ($this->_timings === null) {
			$this->_timings = Yii::$app->getLog()->calculateTimings($this->data['messages']);
		}
		return $this->_timings;
	}

	/**
	 * @inheritdoc
	 */
	public function save()
	{
		$target = $this->module->logTarget;
		$messages = $target->filterMessages($target->messages, Logger::LEVEL_PROFILE, ['yii\db\Command::query', 'yii\db\Command::execute']);
		return ['messages' => $messages];
	}

	/**
	 * Returns total query time.
	 *
	 * @param array $timings
	 * @return integer total time
	 */
	protected function getTotalQueryTime($timings)
	{
		$queryTime = 0;

		foreach ($timings as $timing) {
			$queryTime += $timing['duration'];
		}

		return $queryTime;
	}

	/**
	 * Returns an  array of models that represents logs of the current request.
	 * Can be used with data providers such as \yii\data\ArrayDataProvider.
	 * @return array models
	 */
	protected function getModels()
	{
		if ($this->_models === null) {
			$this->_models = [];
			$timings = $this->calculateTimings();

			foreach($timings as $seq => $dbTiming) {
				$this->_models[] = 	[
					'type' => $this->getQueryType($dbTiming['info']),
					'query' => $dbTiming['info'],
					'duration' => ($dbTiming['duration'] * 1000), // in milliseconds
					'trace' => $dbTiming['trace'],
					'timestamp' => ($dbTiming['timestamp'] * 1000), // in milliseconds
					'seq' => $seq,
				];
			}
		}
		return $this->_models;
	}

	/**
	 * Returns databse query type.
	 *
	 * @param string $timing timing procedure string
	 * @return string query type such as select, insert, delete, etc.
	 */
	protected function getQueryType($timing)
	{
		$timing = ltrim($timing);
		preg_match('/^([a-zA-z]*)/', $timing, $matches);
		return count($matches) ? $matches[0] : '';
	}

	/**
	 * Check if given queries count is critical according settings.
	 * @param queries count $count
	 * @return boolean
	 */
	public function isQueriesCountCritical($count)
	{
		return (($this->criticalQueriesCount !== null) && ($count > $this->criticalQueriesCount));
	}

}
