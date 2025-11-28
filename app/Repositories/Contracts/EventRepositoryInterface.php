<?php
// app/Repositories/Contracts/EventRepositoryInterface.php

namespace App\Repositories\Contracts;

interface EventRepositoryInterface
{
    public function all();
    public function find($id);
    public function findBySlug($slug);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getPublishedEvents();
    public function getFeaturedEvents($limit = 6);
}
