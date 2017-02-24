<?php

namespace arkuuu\Publin;

/**
 * Class ManageView
 *
 * @package arkuuu\Publin
 */
class ManageView extends View
{

    private $model;


    /**
     * @param ManageModel $model
     * @param array       $errors
     */
    public function __construct(ManageModel $model, array $errors)
    {
        parent::__construct('manage', $errors);
        $this->model = $model;
    }


    /**
     * Shows page title.
     *
     * @return    string
     */
    public function showPageTitle()
    {
        return 'Manage';
    }


    /**
     * @return string
     */
    public function showPermissions()
    {
        $roles = $this->model->getRoles();
        $permissions = $this->model->getPermissions();

        $string = '<form action="#" method="post" accept-charset="utf-8">';
        $string .= '<table><tr><th>Permission</th>';
        /* @var $role Role */
        foreach ($roles as $role) {
            $string .= '<th>'.$this->html($role->getName()).'</th>';
        }
        $string .= '</tr>';

        foreach ($permissions as $permission) {
            /* @var $permission Permission */
            $string .= '<tr><td>'.$this->html($permission->getName()).'</td>';
            foreach ($roles as $role) {
                if ($role->hasPermission($permission->getId())) {
                    $class = 'green';
                    $checked = 'checked';
                } else {
                    $class = 'red';
                    $checked = '';
                }
                $string .= '<td class="'.$class.'">
    <input type="checkbox" name="permissions['.$this->html($role->getId()).']['.$this->html($permission->getId()).']" '.$checked.'>
</td>';
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


    /**
     * @return string
     */
    public function showRoles()
    {
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


    /**
     * @return string
     */
    public function showUsers()
    {
        $string = '<table>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registration</th>
                                <th>Last login</th>
                                <th>Assigned Roles</th>
                                <th>Actions</th>
                            </tr>';
        /* @var $user User */
        foreach ($this->model->getUsers() as $user) {
            $string .= '<tr>
                                <td>'.$this->html($user->getName()).'</td>
                                <td>'.$this->html($user->getMail()).'</td>
                                <td>'.$this->html($user->getDateRegister('Y-m-d')).'</td>
                                <td>'.$this->html($user->getDateLastLogin('Y-m-d')).'</td>
                                <td>';
            foreach ($user->getRoles() as $role) {
                $string .= '
                        <form action="#" method="post" accept-charset="utf-8">
                        '.$this->html($role->getName()).'
                        <input type="hidden" name="role_id" value="'.$this->html($role->getId()).'"/>
                        <input type="hidden" name="user_id" value="'.$this->html($user->getId()).'"/>
                        <input type="hidden" name="action" value="removeRoleFromUser"/>
                        <input type="submit" value="x"/>
                        </form>';
            }

            $string .= '
    <form action="#" method="post" accept-charset="utf-8">
        <select name="role_id">
            <option selected disabled>Role...</option>';

            /* @var $role Role */
            foreach ($this->model->getRoles() as $role) {
                if (!$user->hasRole($role->getId())) {
                    $string .= '<option value="'.$this->html($role->getId()).'">'.$this->html($role->getName()).'</option>';
                }
            }

            $string .= '</select>
<input type="hidden" name="user_id" value="'.$this->html($user->getId()).'">
<input type="hidden" name="action" value="addRoleToUser">
<input type="submit" value="Add">
</form>';

            $string .= '</td><td>
    <form action="#" method="post" accept-charset="utf-8">
        <input type="hidden" name="user_id" value="'.$this->html($user->getId()).'">
        <input type="hidden" name="action" value="deleteUser">
        <input type="submit" value="Delete" onclick="return confirm(\'Do you really want to delete the user '.$this->html($user->getName()).'?\')">
    </form>
    <form action="#" method="post" accept-charset="utf-8">
        <input type="hidden" name="user_id" value="'.$this->html($user->getId()).'">
        <input type="hidden" name="action" value="sendNewPassword">
        <input type="submit" value="Send new password" onclick="return confirm(\'Do you really want to reset the password of user '.$this->html($user->getName()).'?\\nThe user will receive an email with a new temporary password.\')">
    </form>
</td>
</tr>';
        }

        $string .= '</table>';

        return $string;
    }
}
