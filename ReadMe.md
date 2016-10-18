# LARAVEL 5.3
This is currently using the code from the dev branch in [RamanBican/Roles, you may find it here](https://github.com/romanbican/roles/pull/189)

# Helpers
App\Helpers\RolePermissions.php
- Added static function `GetAllPermissions(Role $role, bool $SortAtoZ = true, $SortByName = true)`
  this function will return `RolePermissions[]` within the `RolePermissions` object you have the following
  properties: 
  + `$role` (This is the role that the permissions are being checked against)
  + `$permission` (This is the permission currently being checked)
  + `$slug` (A slug for the permission)
  
Usage example (flipping roles, enabled = disable, disabled = enabled): 
```
// Get the role "admin" via the slug
$role = Role::getRoleFromSLug("admin");

// List off all the current perm slugs in the
// current role
echo "Slugs in the current role: ";
$perm = $role->getPermissionsSLugs();

foreach($perm as $p) 
	echo "$p <br>";
echo "<br><br>";

// Get all the permissions for the 
// current role
$rp = RolePermissions::GetAllPermissions($role);

// Loop through each of the perms
foreach($rp as $p) {
	echo $p->slug;
	echo "<br>";
	
	if ($p->isEnabled()) 
		 echo "Enabled";
	else echo "Disabled";
	
	// Now we want to flip the
	// enabled to disabled
	// and 
	// disabled to enabled
	// Set to !(ENABLED) | Opposite of Enabled
	$p->setState(!$p->isEnabled());
	
	echo "<br><br>";
}
```


# Changed files in vendors
Bican\Roles\Models\Role.php:
- Added static function `getRoleFromSLug($slug)` to get a role using a SLug.
- Added function `usersInRole()` to get all the users who are inside the role
- Added function `users()` to get a list of users [DOES NOT RETURN THE MODEL, use `usersInRole()` instead!]
- Added function `getPermissions()` to get all the permissions that are given to the role
- Added function `permissions()` to get a list of permissions [DOES NOT RETURN THE MODEL, use `getPermissions()` instead!]
- Added function `getPermissionsSLugs()` to get a string array of the permissions in the role

Bican\Roles\Traits\HasRoleAndPermission.php:
- Added function `attachPermissionFromSLug($slug)` to attach a permission to a role using a slug (Returns false/true on success)

Bican\Roles\Traits\RoleHasRelations.php:
- Added function `attachPermissionFromSLug($slug)` to attach a permission to a role using a slug (Returns false/true on success)