<?php
/**
 * User: Callum Carmicheal
 * Date: 18/10/2016
 * Time: 20:27
 */

namespace App\Helpers;


use Bican\Roles\Models\Permission;
use Bican\Roles\Models\Role;

class RolePermissions {
	
	/**
	 * @param Role $role
	 * @param bool $SortAtoZ
	 * @return RolePermissions[]
	 */
	public static function GetAllPermissions(Role $role, bool $SortAtoZ = true, $SortByName = true) {
		/** @var RolePermissions $res */
		$res 		= [];
		/** @var string $pslugs */
		$cache 		= array(); 
		
		foreach ($role->getPermissions() as $perm) {
			array_push($cache, $perm->id);
			
			$obj = new RolePermissions($role, $perm, true);
			array_push($res, $obj);
		}
		
		foreach (Permission::all() as $perm) {
			if (in_array($perm->id, $cache)) 
				continue;
			
			$obj = new RolePermissions($role, $perm, false);
			array_push($res, $obj);
		}
		
		if($SortAtoZ) {
			if($SortByName) {
				usort ($res, function ($a, $b) {
					return strcmp ($a->permission->name, $b->permission->name);
				});
			} else {
				usort ($res, function ($a, $b) {
					return strcmp ($a->permission->slug, $b->permission->slug);
				});
			}
		}
		
		return $res;
	}
	
	// ------------------------------------
	
	
	/**
	 * @var Role $role
	 */
	public $role;
	
	/**
	 * @var Permission $permission
	 */
	public $permission;
	
	/**
	 * @var string $slug
	 */
	public $slug;
	
	private $_isEnabled;
	
	/**
	 * RolePerm constructor.
	 * @param $role Role
	 * @param $permission Permission
	 * @param $enabled bool
	 */
	public function __construct ($role, $permission, $enabled) {
		$this->role = $role;
		$this->permission = $permission;
		$this->slug = $permission->slug;
		
		$this->_isEnabled = $enabled;
	}
	
	/**
	 * @param  bool $useCacheVar If true the local variable ($_isEnabled) will be returned
	 *                           instead of a real time value retrieved from the database
	 * @return bool If the role contains this permission
	 */
	public function isEnabled($useCacheVar = false) {
		if($useCacheVar)
			return $this->_isEnabled;
		
		// Check if the current permission is in the list of
		// role permissions
		$res = in_array($this->slug, $this->role->getPermissionsSLugs());
		
		$this->_isEnabled = $res;
		return $res;
	}
	
	
	public function setState($state) {
		try {
			if ($state)
				 $this->role->attachPermission($this->permission);
			else $this->role->detachPermission($this->permission);
			
			$this->_isEnabled = $state;
		} catch (\Exception $ex) {
			// Role does not contain permission/already have it etc...
			// Or who knows... T_T
		}
	}
	
	
	public function Enable() {
		$this->setState(true);
	}
	
	public function Disable() {
		$this->setState(false);
	}
	
}