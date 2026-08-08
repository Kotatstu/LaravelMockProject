<?php

namespace App\Repositories;

abstract class BaseRepository
{
    abstract protected function model() : string;

    //Get all
    public function all(array $relations = [])
    {
        return $this->model()::with($relations)->get();
    }

    //Get by id
    public function find(int $id, array $relations = [])
    {
        return $this->model()::with($relations)->findOrFail($id);
    }

    //Create new record
    public function create(array $data, array $relations = [])
    {
        $instant = $this->model()::create($data);
        
        return $relations ? $instant->load($relations) : $instant;
    }

    //Update by id
    public function update(mixed $instant, array $data, array $relations = [])
    {
        $instant->update($data);
        return $relations ? $instant->load($relations) : $instant;
    }

    public function delete(mixed $instant) : void
    {
        $instant->delete();
    }
}