<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Form;
use App\Models\RoleFormPermission;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'role_id',
        'entity_id',
        'organization_id',
        'branch_id',
        'status',
        'last_login_at',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the role that owns the user (legacy single role).
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the roles that belong to the user (Many-to-Many).
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * Get the entity that owns the user.
     */
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Get the OTP verifications for the user.
     */
    public function otpVerifications()
    {
        return $this->hasMany(OtpVerification::class);
    }

    /**
     * Get the organization that owns the user.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the branch that owns the user (legacy single branch).
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get all branches assigned to the user (many-to-many).
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'user_branch');
    }

    /**
     * Check if user is Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role && $this->role->slug === 'super-admin';
    }

    /**
     * Check if user is Branch User.
     */
    public function isBranchUser(): bool
    {
        return $this->role && $this->role->slug === 'branch-user';
    }

    /**
     * Check if user has access to a specific branch.
     */
    public function hasAccessToBranch(int $branchId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        return $this->branches()->where('branches.id', $branchId)->exists();
    }

    /**
     * Get the user who created this user.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        // If status column doesn't exist or is null, treat as active (backward compatibility)
        if (!isset($this->status) || $this->status === null) {
            return true;
        }
        return $this->status === 'active';
    }

    /**
     * Check if user is locked.
     */
    public function isLocked(): bool
    {
        return $this->status === 'locked';
    }

    /**
     * Update last login timestamp.
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $form, string $type = 'read'): bool
    {
        // Super Admin has all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Map legacy actions to new permission columns
        $typeMap = [
            'view' => 'read',
            'read' => 'read',
            'create' => 'write',
            'edit' => 'write',
            'write' => 'write',
            'delete' => 'delete',
            'destroy' => 'delete',
        ];

        $checkColumn = $typeMap[strtolower($type)] ?? 'read';

        // Collect role IDs from both the many-to-many and legacy single role.
        $this->loadMissing(['roles.permissions', 'role.permissions']);
        $roles = $this->roles;
        if ($this->role && !$roles->contains('id', $this->role->id)) {
            $roles = $roles->concat([$this->role]);
        }

        $roleIds = $roles->pluck('id')->unique()->filter()->values();
        if ($roleIds->isEmpty()) {
            return false;
        }

        // First, try RoleFormPermission (menus/forms system) using route_name or form code.
        $formKey = strtolower(trim($form));
        $codeCandidates = array_values(array_unique(array_filter([
            $formKey,
            $formKey . '_form',
            str_replace('-', '_', $formKey) . '_form',
        ])));

        $formIds = Form::query()
            ->whereIn('code', $codeCandidates)
            ->orWhere('route_name', 'like', $formKey . '.%')
            ->pluck('id');

        if ($formIds->isNotEmpty()) {
            $minType = RoleFormPermission::VIEW;
            if ($checkColumn === 'write') {
                $minType = RoleFormPermission::ADD_EDIT_UPDATE;
            } elseif ($checkColumn === 'delete') {
                $minType = RoleFormPermission::FULL_ACCESS;
            }

            $hasFormPermission = RoleFormPermission::query()
                ->whereIn('role_id', $roleIds)
                ->whereIn('form_id', $formIds)
                ->where('permission_type', '>=', $minType)
                ->exists();

            return $hasFormPermission;
        }

        // Fallback to legacy role_permission pivot (permissions table).
        foreach ($roles as $role) {
            $permission = $role->permissions->firstWhere('form_name', $form);
            if (!$permission) {
                continue;
            }

            $pivot = $permission->pivot;
            $hasPermission = false;

            switch ($checkColumn) {
                case 'read':
                    $hasPermission = ($pivot->read ?? false)
                        || ($pivot->write ?? false)
                        || ($pivot->delete ?? false);
                    break;
                case 'write':
                    $hasPermission = ($pivot->write ?? false)
                        || ($pivot->delete ?? false);
                    break;
                case 'delete':
                    $hasPermission = ($pivot->delete ?? false);
                    break;
                default:
                    $hasPermission = ($pivot->$checkColumn ?? false);
                    break;
            }

            if ($hasPermission) {
                return true;
            }
        }

        return false;
    }
}
