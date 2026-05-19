<?php
declare(strict_types=1);

namespace App\models;

use App\config\Database;
use PDO;

class AdminModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByUsername(string $username): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM admin_users WHERE username = :username');
        $stmt->execute([':username' => $username]);
        return $stmt->fetch();
    }

    public function getAllStaff(): array
    {
        $stmt = $this->db->prepare('SELECT * FROM staff ORDER BY name');
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
