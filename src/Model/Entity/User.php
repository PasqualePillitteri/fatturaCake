<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property int|null $tenant_id
 * @property int|null $role_id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string|null $nome
 * @property string|null $cognome
 * @property string|null $telefono
 * @property string|null $avatar
 * @property string $role
 * @property bool $is_active
 * @property \Cake\I18n\DateTime|null $email_verified
 * @property \Cake\I18n\DateTime|null $last_login
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property \Cake\I18n\DateTime|null $deleted
 *
 * @property \App\Model\Entity\Tenant $tenant
 * @property \App\Model\Entity\Role $role_entity
 * @property \App\Model\Entity\LogAttivitum[] $log_attivita
 * @property-read string $full_name
 */
class User extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        // SECURITY: Protected fields - admin/system only
        'tenant_id' => false,      // Managed by TenantScopeBehavior
        'role_id' => false,        // Admin only - prevents privilege escalation
        'role' => false,           // Admin only - prevents privilege escalation
        'is_active' => false,      // Admin only
        'email_verified' => false, // System only - email verification flow
        'last_login' => false,     // System only - set on login
        'created' => false,        // Managed by TimestampBehavior
        'modified' => false,       // Managed by TimestampBehavior
        'deleted' => false,        // Managed by TrashBehavior

        // User-editable fields
        'username' => true,
        'email' => true,
        'password' => true,
        'nome' => true,
        'cognome' => true,
        'telefono' => true,
        'avatar' => true,

        // Associations - protected
        'tenant' => false,
        'role_entity' => false,
        'log_attivita' => false,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array<string>
     */
    protected array $_hidden = [
        'password',
    ];

    /**
     * Automatically hash password when setting.
     *
     * @param string|null $password The plain text password
     * @return string|null The hashed password
     */
    protected function _setPassword(?string $password): ?string
    {
        if ($password !== null && strlen($password) > 0) {
            return (new DefaultPasswordHasher())->hash($password);
        }

        return $password;
    }

    /**
     * Virtual field for full name.
     *
     * @return string
     */
    protected function _getFullName(): string
    {
        $parts = array_filter([
            $this->nome,
            $this->cognome,
        ]);

        return implode(' ', $parts) ?: $this->username;
    }
}
