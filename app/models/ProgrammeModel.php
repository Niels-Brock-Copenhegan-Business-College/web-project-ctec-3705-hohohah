<?php
declare(strict_types=1);

namespace App\models;

use App\config\Database;
use PDO;

class ProgrammeModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAllPublished(string $search = '', string $level = ''): array
    {
        $sql = '
            SELECT p.*, s.name AS leader_name
            FROM programmes p
            LEFT JOIN staff s ON s.id = p.programme_leader_id
            WHERE p.published = 1
        ';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (p.title LIKE :search OR p.description LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        if ($level !== '') {
            $sql .= ' AND p.level = :level';
            $params[':level'] = $level;
        }

        // Sorted alphabetically by programme level and title
        $sql .= ' ORDER BY p.level ASC, p.title ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare('
            SELECT p.*, s.name AS leader_name, s.email AS leader_email, s.bio AS leader_bio, s.photo AS leader_photo
            FROM programmes p
            LEFT JOIN staff s ON s.id = p.programme_leader_id
            WHERE p.id = :id AND p.published = 1
        ');

        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function getByIdAdmin(int $id): array|false
    {
        $stmt = $this->db->prepare('
            SELECT p.*, s.name AS leader_name
            FROM programmes p
            LEFT JOIN staff s ON s.id = p.programme_leader_id
            WHERE p.id = :id
        ');

        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function getAll(): array
    {
        $stmt = $this->db->prepare('
            SELECT p.*, s.name AS leader_name
            FROM programmes p
            LEFT JOIN staff s ON s.id = p.programme_leader_id
            ORDER BY p.level, p.title
        ');

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO programmes (title, level, description, duration_years, published, programme_leader_id)
            VALUES (:title, :level, :description, :duration_years, :published, :programme_leader_id)
        ');

        $stmt->execute([
            ':title'               => $data['title'],
            ':level'               => $data['level'],
            ':description'         => $data['description'],
            ':duration_years'      => (int)$data['duration_years'],
            ':published'           => isset($data['published']) ? 1 : 0,
            ':programme_leader_id' => $data['programme_leader_id'] ?: null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare('
            UPDATE programmes
            SET title = :title, level = :level, description = :description,
                duration_years = :duration_years, published = :published,
                programme_leader_id = :programme_leader_id
            WHERE id = :id
        ');

        $stmt->execute([
            ':id'                  => $id,
            ':title'               => $data['title'],
            ':level'               => $data['level'],
            ':description'         => $data['description'],
            ':duration_years'      => (int)$data['duration_years'],
            ':published'           => isset($data['published']) ? 1 : 0,
            ':programme_leader_id' => $data['programme_leader_id'] ?: null,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM programmes WHERE id = :id');

        $stmt->execute([':id' => $id]);
    }

    public function togglePublished(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE programmes SET published = 1 - published WHERE id = :id');

        $stmt->execute([':id' => $id]);
    }
}
