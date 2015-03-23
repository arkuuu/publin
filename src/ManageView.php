<?php


namespace publin\src;

class ManageView extends View {

	private $model;


	public function __construct(ManageModel $model, array $errors) {

		parent::__construct('manage', $errors);
		$this->model = $model;
	}


	/**
	 * Shows page title.
	 *
	 * @return    string
	 */
	public function showPageTitle() {

		return 'Manage';
	}


	public function showPermissions() {

		$roles = $this->model->getRoles();
		$permissions = $this->model->getPermissions();

		$string = '<form action="./?p=manage" method="post">';
		$string .= '<table><tr><th>Permission</th>';
		/* @var $role Role */
		foreach ($roles as $role) {
			$string .= '<th>'.$role->getName().'</th>';
		}
		$string .= '</tr>';

		foreach ($permissions as $permission) {
			$string .= '<tr><td>'.$permission['name'].'</td>';
			foreach ($roles as $role) {
				if ($role->hasPermission($permission['id'])) {
					$string .= '<td class="green"><input type="checkbox" name="permissions['.$role->getId().']['.$permission['id'].']" checked></td>';
				}
				else {
					$string .= '<td class="red"><input type="checkbox" name="permissions['.$role->getId().']['.$permission['id'].']"></td>';
				}
			}
			$string .= '</tr>';
		}
		$string .= '</table>
<input type="hidden" name="action" value="updatePermissions">
<input type="submit" value="Submit changes">
<input type="reset" value="Reset changes">
</form>';

		return $string;
	}


	public function showRoles() {

		$roles = $this->model->getRoles();
		$string = '<ul>';

		/** @var Role $role */
		foreach ($roles as $role) {
			$string .= '<li>
						<form action="#" method="post" accept-charset="utf-8">
						'.$this->html($role->getName()).'
						<input type="hidden" name="role_id" value="'.$this->html($role->getId()).'"/>
						<input type="hidden" name="action" value="deleteRole"/>
						<input type="submit" value="x"/>
						</form>
						</li>';
		}

		return $string.'</ul>';
	}


	public function showUsers() {

		$string = '<table>
							<tr>
								<th>Name</th>
								<th>Email</th>
								<th>Registration</th>
								<th>Active</th>
								<th>Last login</th>
								<th>Assigned Roles</th>
							</tr>';
		/* @var $user User */
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
			$string .= '<form action="./?p=manage" method="post"><select name="role_id">
							<option selected disabled>Select role...</option>';

			/* @var $role Role */
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
}
