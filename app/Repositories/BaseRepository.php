<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\AbstractPaginator as Paginator;

abstract class BaseRepository
{
	/**
	 * Model class for repo.
	 *
	 * @var string
	 */
	protected $modelClass;

	/**
	 * create a instance of the builder
	 * @return EloquentQueryBuilder|QueryBuilder
	 */
	protected function newQuery()
	{
		return app($this->modelClass)->newQuery();
	}

	/**
	 * Executes the repository query
	 * @param EloquentQueryBuilder|QueryBuilder $query
	 * @param int                               $take
	 * @param bool                              $paginate
	 * @return EloquentCollection|Paginator
	 */
	protected function doQuery($query = null, $take = 15, $paginate = true)
	{
		if (is_null($query)) $query = $this->newQuery();

		if ($paginate) return $query->paginate($take);
		
		if ($take > 0 || false !== $take) $query->take($take);
		
		return $query->get();
	}

	/**
	 * Returns all records.
	 * If $take is false then brings all records
	 * If $paginate is true returns Paginator instance.
	 *
	 * @param int  $take
	 * @param bool $paginate
	 *
	 * @return EloquentCollection|Paginator
	 */
	protected function getAll($take = 15, $paginate = true)
	{
		return $this->doQuery(null, $take, $paginate);
	}


	/**
	 * Retrieves a record by his id
	 * @param string  $id
	 * @param bool $fail
	 * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
	 * @return mixed
	 */
	public function find(String $id, bool $fail = true)
	{
		if ($fail) return $this->newQuery()->findOrFail($id);
		
		return $this->newQuery()->find($id);
	}

	/**
	 * Creates a new record
	 * @param string  $id
	 * @param bool $fail
	 * @return mixed
	 */
	public function create(Array $insert_data)
	{
		return $this->modelClass::create($insert_data);
	}

	/**
	 * saves a record
	 * @param Model $model
	 * @return Model $model
	 */
	public function save(Model $model)
	{
		$model->save();
		return $model;
	}

	/**
	 * Updates a record
	 * @param Model $model
	 * @param array $update_data
	 * @return bool
	 */
	public function update(Model $model ,array $update_data)
	{
		return $model->update($update_data);
	}

}