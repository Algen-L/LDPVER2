<?php
namespace App\Models;

class ReferenceRepository
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllLDTypes()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ld_types ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllModalities()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM modalities ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllClassifications()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM classifications ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
