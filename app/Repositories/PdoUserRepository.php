<?php

namespace App\Repositories;

use App\Interfaces\DatabaseConnectionInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use PDOException;
use RuntimeException;

class PdoUserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly DatabaseConnectionInterface $db) {}

    public function findById(int $id): ?User
    {
        try {
            $stmt = $this->db->getConnection()->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            
            return $row ? $this->hydrate($row) : null;
        } catch (PDOException $e) {
            error_log('Error finding user by id: ' . $e->getMessage());
            throw new RuntimeException('Failed to retrieve user data.');
        }
    }

    public function findByEmail(string $email): ?User
    {
        try {
            $stmt = $this->db->getConnection()->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $row = $stmt->fetch();
            
            return $row ? $this->hydrate($row) : null;
        } catch (PDOException $e) {
            error_log('Error finding user by email: ' . $e->getMessage());
            throw new RuntimeException('Failed to retrieve user data.');
        }
    }

    public function save(User $user): ?User
    {
        try {
            $stmt = $this->db->getConnection()->prepare(
                'INSERT INTO users (email, password_hash) VALUES (?, ?) RETURNING id, created_at'
            );
            $stmt->execute([$user->email, $user->password_hash]);
            $row = $stmt->fetch();
            
            if ($row) {
                return new User(
                    id: (int) $row['id'],
                    email: $user->email,
                    password_hash: $user->password_hash,
                    created_at: $row['created_at']
                );
            }
            return null;
        } catch (PDOException $e) {
            error_log('Error saving user: ' . $e->getMessage());
            throw new RuntimeException('Failed to save user data.');
        }
    }

    private function hydrate(array $row): User
    {
        return new User(
            id: (int) $row['id'],
            email: $row['email'],
            password_hash: $row['password_hash'],
            created_at: $row['created_at']
        );
    }
}