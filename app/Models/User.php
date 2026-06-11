<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'jabatan',
        'divisi',
        'foto',
        'last_login'
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
        'last_login' => 'datetime',
    ];

    // ============ ROLE CONSTANTS ============
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_SUPERVISOR = 'supervisor';
    const ROLE_OPERATOR_CUTTING = 'operator_cutting';
    const ROLE_OPERATOR_CRIMPING = 'operator_crimping';
    const ROLE_OPERATOR_LINE = 'operator_line';

    // ============ ROLE CHECK METHODS ============
    
    /**
     * Check if user is Admin
     */
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is Manager
     */
    public function isManager()
    {
        return $this->role === self::ROLE_MANAGER;
    }

    /**
     * Check if user is Supervisor
     */
    public function isSupervisor()
    {
        return $this->role === self::ROLE_SUPERVISOR;
    }

    /**
     * Check if user is Operator (any type)
     */
    public function isOperator()
    {
        return in_array($this->role, [
            self::ROLE_OPERATOR_CUTTING,
            self::ROLE_OPERATOR_CRIMPING,
            self::ROLE_OPERATOR_LINE
        ]);
    }

    /**
     * Check if user is Operator Cutting
     */
    public function isOperatorCutting()
    {
        return $this->role === self::ROLE_OPERATOR_CUTTING;
    }

    /**
     * Check if user is Operator Crimping
     */
    public function isOperatorCrimping()
    {
        return $this->role === self::ROLE_OPERATOR_CRIMPING;
    }

    /**
     * Check if user is Operator Line
     */
    public function isOperatorLine()
    {
        return $this->role === self::ROLE_OPERATOR_LINE;
    }

    // ============ PERMISSION METHODS ============

    /**
     * Check if user can manage machines
     */
    public function canManageMesin()
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_MANAGER]);
    }

    /**
     * Check if user can manage other users
     */
    public function canManageUser()
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_MANAGER]);
    }

    /**
     * Check if user can export data to PDF/Excel
     */
    public function canExport()
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_MANAGER, self::ROLE_SUPERVISOR]);
    }

    /**
     * Check if user can create new data
     */
    public function canCreateData()
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_MANAGER,
            self::ROLE_OPERATOR_CUTTING,
            self::ROLE_OPERATOR_CRIMPING,
            self::ROLE_OPERATOR_LINE
        ]);
    }

    /**
     * Check if user can edit data
     */
    public function canEditData()
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_MANAGER]);
    }

    /**
     * Check if user can delete data
     */
    public function canDeleteData()
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_MANAGER]);
    }

    /**
     * Check if user can view all data (not just their own)
     */
    public function canViewAllData()
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_MANAGER, self::ROLE_SUPERVISOR]);
    }

    /**
     * Get the module access for operator
     */
    public function getOperatorModule()
    {
        return match($this->role) {
            self::ROLE_OPERATOR_CUTTING => 'cutting',
            self::ROLE_OPERATOR_CRIMPING => 'crimping',
            self::ROLE_OPERATOR_LINE => 'line',
            default => 'all'
        };
    }

    // ============ ACCESSORS & MUTATORS ============

    /**
     * Get role badge HTML
     */
    public function getRoleBadgeAttribute()
    {
        return match($this->role) {
            self::ROLE_ADMIN => '<span class="badge bg-danger"><i class="fas fa-crown me-1"></i> Admin</span>',
            self::ROLE_MANAGER => '<span class="badge bg-primary"><i class="fas fa-chart-line me-1"></i> Manager</span>',
            self::ROLE_SUPERVISOR => '<span class="badge bg-warning text-dark"><i class="fas fa-clipboard-list me-1"></i> Supervisor</span>',
            self::ROLE_OPERATOR_CUTTING => '<span class="badge bg-info"><i class="fas fa-cut me-1"></i> Operator Cutting</span>',
            self::ROLE_OPERATOR_CRIMPING => '<span class="badge bg-info"><i class="fas fa-microchip me-1"></i> Operator Crimping</span>',
            self::ROLE_OPERATOR_LINE => '<span class="badge bg-info"><i class="fas fa-industry me-1"></i> Operator Line</span>',
            default => '<span class="badge bg-secondary">' . e($this->role) . '</span>',
        };
    }

    /**
     * Get role name in Indonesian
     */
    public function getRoleNameAttribute()
    {
        return match($this->role) {
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_SUPERVISOR => 'Supervisor',
            self::ROLE_OPERATOR_CUTTING => 'Operator Cutting',
            self::ROLE_OPERATOR_CRIMPING => 'Operator Crimping',
            self::ROLE_OPERATOR_LINE => 'Operator Line',
            default => ucfirst(str_replace('_', ' ', $this->role)),
        };
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->foto && file_exists(storage_path('app/public/' . $this->foto))) {
            return asset('storage/' . $this->foto);
        }
        
        // Generate avatar from name
        $initials = strtoupper(substr($this->name, 0, 2));
        return "https://ui-avatars.com/api/?name={$initials}&background=0D8ABC&color=fff&size=100";
    }

    // ============ RELATIONSHIPS ============

    /**
     * Get all cutting production data by this user
     */
    public function produksiCuttings()
    {
        return $this->hasMany(ProduksiCutting::class);
    }

    /**
     * Get all crimping production data by this user
     */
    public function produksiCrimpings()
    {
        return $this->hasMany(ProduksiCrimping::class);
    }

    /**
     * Get all line production data by this user
     */
    public function produksiLines()
    {
        return $this->hasMany(ProduksiLine::class);
    }

    /**
     * Get all machines data by this user
     */
    public function mesins()
    {
        return $this->hasMany(Mesin::class);
    }

    // ============ SCOPES ============

    /**
     * Scope to get only admin users
     */
    public function scopeAdmin($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    /**
     * Scope to get only manager users
     */
    public function scopeManager($query)
    {
        return $query->where('role', self::ROLE_MANAGER);
    }

    /**
     * Scope to get only supervisor users
     */
    public function scopeSupervisor($query)
    {
        return $query->where('role', self::ROLE_SUPERVISOR);
    }

    /**
     * Scope to get only operator users
     */
    public function scopeOperator($query)
    {
        return $query->whereIn('role', [
            self::ROLE_OPERATOR_CUTTING,
            self::ROLE_OPERATOR_CRIMPING,
            self::ROLE_OPERATOR_LINE
        ]);
    }

    /**
     * Scope to exclude admin and manager
     */
    public function scopeRegular($query)
    {
        return $query->whereNotIn('role', [self::ROLE_ADMIN, self::ROLE_MANAGER]);
    }

    // ============ HELPER METHODS ============

    /**
     * Update last login timestamp
     */
    public function updateLastLogin()
    {
        $this->update(['last_login' => now()]);
    }

    /**
     * Check if user has specific role
     */
    public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        return $this->role === $role;
    }

    /**
     * Get all available roles
     */
    public static function getAvailableRoles()
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_SUPERVISOR => 'Supervisor',
            self::ROLE_OPERATOR_CUTTING => 'Operator Cutting',
            self::ROLE_OPERATOR_CRIMPING => 'Operator Crimping',
            self::ROLE_OPERATOR_LINE => 'Operator Line',
        ];
    }

    /**
     * Get roles for dropdown selection
     */
    public static function getRolesForSelect()
    {
        return [
            self::ROLE_ADMIN => '👑 Admin',
            self::ROLE_MANAGER => '📊 Manager',
            self::ROLE_SUPERVISOR => '👔 Supervisor',
            self::ROLE_OPERATOR_CUTTING => '✂ Operator Cutting',
            self::ROLE_OPERATOR_CRIMPING => '⚙ Operator Crimping',
            self::ROLE_OPERATOR_LINE => '🏭 Operator Line',
        ];
    }
}