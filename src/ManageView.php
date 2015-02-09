<?php


namespace publin\src;

class ManageView extends View {

	private $model;


	public function __construct(ManageModel $model) {

		$this->model = $model;
		parent::__construct('manage');
	}


	/**
	 * Shows page title.
	 *
	 * @return    string
	 */
	public function showPageTitle() {

		return 'Manage DEV';
	}


	public function showRoles($mode = 'list') {

		$roles = $this->model->getRoles();
		$permissions = $this->model->getPermissions();

		/* List view */
		if ($mode == 'list') {
			$string = '<ul>';
			foreach ($roles as $role) {
				$role_permissions = $role->getPermissions();

				$string .= '<li>'.$role->getName();
				if (empty($role_permissions)) {
					$string .= ' <a href="./?p=manage&amp;m=rmr&amp;id='.$role->getId().'">(delete)</a>';
				}
				$string .= '<ul>';

				foreach ($role_permissions as $permission) {
					$string .= '<li>'.$permission['name'].'
 <a href="./?p=manage&amp;m=rmp&amp;id='.$permission['id'].'&amp;rid='.$role->getId().'">(remove)</a></li>';
				}
				$string .= '<li><form action="./?p=manage" method="post"><select name="permission_id">
							<option selected disabled>Select permission...</option>';

				foreach ($permissions as $permission) {
					if (!$role->hasPermission($permission['name'])) {
						$string .= '<option value="'.$permission['id'].'">'.$permission['name'].'</option>';
					}
				}

				$string .= '</select>
							<input type="hidden" name="role_id" value="'.$role->getId().'">
							<input type="submit" value="Add">
							</form></li></ul></li>';
			}
			$string .= '<li><form action="./?p=manage" method="post">
						<input name="role_name" type="text" placeholder="New role"/>
						<input type="submit" value="Create">
						</form></li></ul>';
		}

		/* Table view */
		else if ($mode == 'table') {
			$string = '<form action="./?p=manage" method="post">';
			$string .= '<table><tr><th>Permission</th>';
			foreach ($roles as $role) {
				$string .= '<th>'.$role->getName().'</th>';
			}
			$string .= '</tr>';

			foreach ($permissions as $permission) {
				$string .= '<tr><td>'.$permission['name'].'</td>';
				foreach ($roles as $role) {
					if ($role->hasPermission($permission['id'])) {
						$string .= '<td class="green"><input type="checkbox" name="role_perm['.$role->getId().']['.$permission['id'].']" checked></td>';
					}
					else {
						$string .= '<td class="red"><input type="checkbox" name="role_perm['.$role->getId().']['.$permission['id'].']"></td>';
					}
				}
				$string .= '</tr>';
			}
			$string .= '</table><input type="submit" value="Submit changes"><input type="reset" value="Reset changes"></form>';
		}

		else {
			$string = 'ERROR';
		}

		return $string;

	}


	public function showUsers($mode = 'table') {

		if ($mode == 'table') {
			$string = '<table>
							<tr>
								<th>Name</th>
								<th>Email</th>
								<th>Registration</th>
								<th>Active</th>
								<th>Last login</th>
								<th>Assigned Roles</th>
							</tr>';

			foreach ($this->model->getUsers() as $user) {
				$string .= '<tr>
								<td>'.$user->getName().'</td>
								<td>'.$user->getMail().'</td>
								<td>'.$user->getDateRegister('Y-m-d').'</td>
								<td>'.$user->isActive().'</td>
								<td>'.$user->getDateLastLogin('Y-m-d').'</td>
								<td>';
				foreach ($user->getRoles() as $role) {
					$string .= $role->getName().' <a href="./?p=manage&amp;m=rmur&amp;id='.$role->getId().'&amp;uid='.$user->getId().'">(remove)</a><br/>';
				}
				$string .= '<form action="./?p=manage#Users" method="post"><select name="user_role_id">
							<option selected disabled>Select role...</option>';

				foreach ($this->model->getRoles() as $role) {
					if (!$user->hasRole($role->getId())) {
						$string .= '<option value="'.$role->getId().'">'.$role->getName().'</option>';
					}
				}

				$string .= '</select>
							<input type="hidden" name="user_id" value="'.$user->getId().'">
							<input type="submit" value="Add">
							</form>';

				$string .= '</td></tr>';
			}

			$string .= '</table>';

			return $string;
		}
		else {
			return 'ERROR';
		}

	}


}
