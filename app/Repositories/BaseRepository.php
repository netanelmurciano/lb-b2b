<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10/11/2015
 * Time: 5:44 PM
 */

namespace App\Repositories;

/**
 * Class BaseRepository
 * @package App\Repositories
 */
abstract class BaseRepository
{

    /**
     * @var
     */
    public $model;

    /**
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = array('*'))
    {

        return $this->model->get($columns);
    }

    /**
     * @param        $column
     * @param string $direction
     *
     * @return mixed
     */
    public function orderBy($column, $direction = 'asc')
    {

        return $this->model->orderBy($column, $direction)->get();
    }

    /**
     * @param int   $perPage
     * @param array $columns
     *
     * @return mixed
     */
    public function paginate($perPage = 15, $columns = array('*'))
    {

        return $this->model->paginate($perPage, $columns);
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data)
    {

        return $this->model->create($data);
    }

    /**
     * @param array  $data
     * @param        $id
     * @param string $attribute
     *
     * @return mixed
     */
    public function update(array $data, $id, $attribute = "id")
    {

        $data = $this->dataCleaner($data);

        return $this->model->where($attribute, '=', $id)->update($data);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function delete($id)
    {

        return $this->model->destroy($id);
    }

    /**
     * @param       $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = array('*'))
    {

        return $this->model->find($id, $columns);
    }

    /**
     * @param $attribute
     * @param $value
     *
     * @param array $columns
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = array('*'))
    {

        return $this->model->where($attribute, '=', $value)->get($columns);
    }

    /**
     * @param $attribute
     * @param $value
     *
     * @return mixed
     */
    public function findByFirst($attribute, $value)
    {

        return $this->model->where($attribute, '=', $value)->first();
    }

    /**
     * @param        $attribute
     * @param        $value
     * @param        $column
     * @param string $direction
     *
     * @return mixed
     */
    public function findByAndOrderBy($attribute, $value, $column, $direction = 'asc')
    {

        return $this->model->where($attribute, '=', $value)->orderBy($column, $direction)->get();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function dataCleaner(array $data)
    {

        foreach ($data as $name => $value) {
            if (substr($name, 0, 1) === '_' || $name == '$$hashKey')
                unset($data[$name]);
        }

        return $data;
    }

    /**
     * @param $attribute
     * @param $value
     *
     * @param array $columns
     * @return mixed
     */
    public function except($attribute, $value, $columns = array('*'))
    {

        return $this->model->where($attribute, '!=', $value)->get($columns);
    }

    /**
     * @param array $columns
     * @return mixed
     *
     */
    public function published($columns = array('*'))
    {

        return $this->model->where('published_at', '!=', null)->get($columns);
    }

    /**
     * @return mixed
     */
    public function count()
    {

        return $this->model->count();

    }

    /**
     * @param string $column
     * @param string $direction
     * @param int    $pageNum
     * @param int    $itemsPerPage
     *
     * @return mixed
     */
    public function filterBy($column = 'name', $direction = 'asc', $pageNum = 0, $itemsPerPage = 20)
    {

        $pageNum      = (!is_int($pageNum)) ? intval($pageNum) : $pageNum;
        $itemsPerPage = (!is_int($itemsPerPage)) ? intval($itemsPerPage) : $itemsPerPage;
        $skip         = ($pageNum - 1) * $itemsPerPage;

        return $this->model->orderBy($column, $direction)->skip($skip)->take($itemsPerPage)->get();

    }

}
