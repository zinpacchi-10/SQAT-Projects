<?php
require_once __DIR__ . '/Database.php';

/**
 * Base Model
 * All Model classes extend this so they share a DB connection helper.
 * No HTML/view logic ever belongs in a Model.
 */
abstract class Model
{
    protected mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }
}
