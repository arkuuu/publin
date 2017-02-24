<?php

namespace arkuuu\Publin;

/**
 * Class UserView
 *
 * @package arkuuu\Publin
 */
class UserView extends View
{

    /**
     * @var    User
     */
    private $user;


    /**
     * Constructs the author view.
     *
     * @param User  $user
     * @param array $errors
     */
    public function __construct(User $user, array $errors)
    {
        parent::__construct('user', $errors);
        $this->user = $user;
    }


    /**
     * @return string
     */
    public function showRoles()
    {
        $string = '';

        foreach ($this->user->getRoles() as $role) {
            $string .= '<li>'.$this->html($role->getName()).'</li>';
        }

        if (empty($string)) {
            $string = '<li>None</li>';
        }

        return $string;
    }


    /**
     * @return string
     */
    public function showName()
    {
        return $this->html($this->user->getName());
    }


    /**
     * @return string
     */
    public function showMail()
    {
        return $this->html($this->user->getMail());
    }
}
