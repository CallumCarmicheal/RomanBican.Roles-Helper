<?php

namespace Bican\Roles\Models;

use Bican\Roles\Traits\Slugable;
use Illuminate\Database\Eloquent\Model;
use Bican\Roles\Traits\RoleHasRelations;
use Bican\Roles\Contracts\RoleHasRelations as RoleHasRelationsContract;

class Role extends Model implements RoleHasRelationsContract {
    use Slugable, RoleHasRelations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'description', 'level'];

    /**
     * Create a new model instance.
     *
     * @param array $attributes
     * @return void
     */
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);

        if ($connection = config('roles.connection')) {
            $this->connection = $connection;
        }
    }
    
    /**
     * app/Support/helpers.php
     *
     * Get a permission by slug and attach it.
     *
     * @param string $slug
     *
     * @return Role|bool true when succeed
     */
    public static function getRoleFromSLug($slug) {
        $role = Role::where('slug', $slug)->first();
        if (! is_null($role)) 
            return $role;
        return false;
    }
	
	/**
	 * @return \App\User[]
	 */
	public function usersInRole() {
		return $this->users()->get()->all();
	}
	
	/**
	 * @return Permission[]
	 */
	public function getPermissions() {
		return $this->permissions()->get()->all();
	}
	
	public function getPermissionsSLugs() {
		$p = $this->getPermissions();
		$a = array();
		
		foreach ($p as $perm) 
			array_push($a, $perm->slug);
		return $a;
	}
	
    public function users() {
        return $this->belongsToMany('App\User', 'role_user', 'role_id', 'user_id');
    }
	
	public function permissions() {
		// $related, $table = null, $foreignKey = null, $otherKey = null, $relation = null
		return $this->belongsToMany('Bican\Roles\Models\Permission', 'permission_role', 'role_id', 'permission_id');
	}

}
