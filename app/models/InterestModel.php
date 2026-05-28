<?php
declare(strict_types=1);

namespace App\models;

use App\config\Database;
use PDO;

class InterestModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function register(array $data): bool
    {
        $stmt = $this->db->prepare('
            INSERT INTO interest_registrations (first_name, last_name, email, phone, programme_id, message)
            VALUES (:first_name, :last_name, :email, :phone, :programme_id, :message)
        ');

        try {
            $stmt->execute([
                ':first_name'   => $data['first_name'],
                ':last_name'    => $data['last_name'],
                ':email'        => $data['email'],
                ':phone'        => $data['phone'] ?? null,
                ':programme_id' => (int)$data['programme_id'],
                ':message'      => $data['message'] ?? null,
            ]);

            return true;
        } catch (\PDOException $e) {

            // Duplicate entry (email + programme_id already registered)
            if ($e->getCode() === '23000') {
                return false;
            }

            throw $e;
        }
    }

    public function withdraw(int $programmeId, string $email): bool
    {
        $stmt = $this->db->prepare('
            DELETE FROM interest_registrations
            WHERE programme_id = :programme_id AND email = :email
        ');

        $stmt->execute([
            ':programme_id' => $programmeId,
            ':email' => $email
        ]);

        return $stmt->rowCount() > 0;
    }

    public function isRegistered(int $programmeId, string $email): bool
    {
        $stmt = $this->db->prepare('
            SELECT id FROM interest_registrations
            WHERE programme_id = :programme_id AND email = :email
        ');

        $stmt->execute([
            ':programme_id' => $programmeId,
            ':email' => $email
        ]);

        return $stmt->fetch() !== false;
    }

    public function getByProgramme(int $programmeId): array
    {
        $stmt = $this->db->prepare('
            SELECT ir.*, p.title AS programme_title
            FROM interest_registrations ir
            JOIN programmes p ON p.id = ir.programme_id
            WHERE ir.programme_id = :programme_id
            ORDER BY ir.registered_at DESC
        ');

        $stmt->execute([
            ':programme_id' => $programmeId
        ]);

        return $stmt->fetchAll();
    }

    public function getAll(): array
    {
        $stmt = $this->db->prepare('
            SELECT ir.*, p.title AS programme_title, p.level
            FROM interest_registrations ir
            JOIN programmes p ON p.id = ir.programme_id
            ORDER BY p.title, ir.last_name, ir.first_name
        ');

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Delete an interest registration by ID.
     */
    public function deleteById(int $id): bool
    {
        $stmt = $this->db->prepare('
            DELETE FROM interest_registrations
            WHERE id = :id
        ');

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }
}
