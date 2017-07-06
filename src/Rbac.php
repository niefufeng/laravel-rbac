<?php

namespace NieFufeng\LaravelRbac;


use NieFufeng\LaravelRbac\Contracts\UserInterface;

class Rbac
{
    protected $user;

    /**
     * Rbac constructor.
     */
    public function __construct()
    {
        $this->user = auth()->user();
    }

    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    public function user()
    {
        return $this->user;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->user, $name], $arguments);
    }
}