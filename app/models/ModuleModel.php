<?php
declare(strict_types=1);

namespace App\models;

use App\config\Database;
use PDO;

class ModuleModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getByProgramme(int $programmeId): array
    {
        $stmt = $this->db->prepare('
            SELECT m.*, pm.year_of_study, s.name AS leader_name, s.email AS leader_email, s.bio AS leader_bio
            FROM modules m
            JOIN programme_modules pm ON pm.module_id = m.id
            LEFT JOIN staff s ON s.id = m.module_leader_id
            WHERE pm.programme_id = :programme_id
            ORDER BY pm.year_of_study ASC, m.title ASC
        ');

        $stmt->execute([':programme_id' => $programmeId]);

        $rows = $stmt->fetchAll();

        $grouped = [];

        // Group modules by year of study
        foreach ($rows as $row) {
            $grouped[$row['year_of_study']][] = $row;
        }

        ksort($grouped);

        return $grouped;
    }

    public function getAll(): array
    {
        $stmt = $this->db->prepare('
            SELECT m.*, s.name AS leader_name
            FROM modules m
            LEFT JOIN staff s ON s.id = m.module_leader_id
            ORDER BY m.title
        ');

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare('
            SELECT m.*, s.name AS leader_name
            FROM modules m
            LEFT JOIN staff s ON s.id = m.module_leader_id
            WHERE m.id = :id
        ');

        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function getProgrammesForModule(int $moduleId): array
    {
        $stmt = $this->db->prepare('
            SELECT p.id, p.title, p.level
            FROM programmes p
            JOIN programme_modules pm ON pm.programme_id = p.id
            WHERE pm.module_id = :module_id AND p.published = 1
            ORDER BY p.title
        ');

        $stmt->execute([':module_id' => $moduleId]);

        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO modules (title, description, credits, module_leader_id)
            VALUES (:title, :description, :credits, :module_leader_id)
        ');

        $stmt->execute([
            ':title'            => $data['title'],
            ':description'      => $data['description'],
            ':credits'          => (int)$data['credits'],
            ':module_leader_id' => $data['module_leader_id'] ?: null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare('
            UPDATE modules
            SET title = :title, description = :description, credits = :credits, module_leader_id = :module_leader_id
            WHERE id = :id
        ');

        $stmt->execute([
            ':id'               => $id,
            ':title'            => $data['title'],
            ':description'      => $data['description'],
            ':credits'          => (int)$data['credits'],
            ':module_leader_id' => $data['module_leader_id'] ?: null,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM modules WHERE id = :id');

        $stmt->execute([':id' => $id]);
    }

    public function assignToProgramme(int $programmeId, int $moduleId, int $year): void
    {
        $stmt = $this->db->prepare('
            INSERT IGNORE INTO programme_modules (programme_id, module_id, year_of_study)
            VALUES (:programme_id, :module_id, :year)
        ');

        $stmt->execute([
            ':programme_id' => $programmeId,
            ':module_id'    => $moduleId,
            ':year'         => $year
        ]);
    }

    public function removeFromProgramme(int $programmeId, int $moduleId): void
    {
        $stmt = $this->db->prepare('
            DELETE FROM programme_modules
            WHERE programme_id = :programme_id AND module_id = :module_id
        ');

        $stmt->execute([
            ':programme_id' => $programmeId,
            ':module_id'    => $moduleId
        ]);
    }
}
