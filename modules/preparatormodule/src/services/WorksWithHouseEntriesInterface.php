<?php
namespace modules\preparatormodule\services;


interface WorksWithHouseEntriesInterface
{
    public function save();
    public function clearById(int $id) : int;
}