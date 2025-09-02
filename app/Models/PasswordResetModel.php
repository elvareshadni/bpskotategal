<?php

namespace App\Models;

use CodeIgniter\Model;

class PasswordResetModel extends Model
{
    protected $table         = 'password_resets';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['email', 'token_hash', 'expires_at', 'used_at', 'created_at'];
    protected $useTimestamps = false;

    public function createToken(string $email, string $tokenPlain, int $ttlMinutes = 30): bool
    {
        // Nonaktifkan token lama aktif agar hanya satu yang valid
        $this->where('email', $email)->where('used_at', null)->set(['used_at' => date('Y-m-d H:i:s')])->update();

        return $this->insert([
            'email'      => $email,
            'token_hash' => password_hash($tokenPlain, PASSWORD_DEFAULT),
            'expires_at' => date('Y-m-d H:i:s', time() + $ttlMinutes * 60),
        ], false);
    }

    public function validateToken(string $email, string $tokenPlain): ?array
    {
        $row = $this->where('email', $email)
            ->where('used_at', null)
            ->orderBy('id', 'DESC')
            ->first();

        if (!$row) return null;
        if (strtotime($row['expires_at']) < time()) return null;
        if (!password_verify($tokenPlain, $row['token_hash'])) return null;

        return $row;
    }

    public function markUsed(int $id): bool
    {
        return $this->update($id, ['used_at' => date('Y-m-d H:i:s')]);
    }

    public function purgeExpired(): int
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'))->delete();
    }
}
