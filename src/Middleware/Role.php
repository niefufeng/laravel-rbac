<?php

namespace NieFufeng\LaravelRbac;


use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class Role
{
    /**
     * @var Guard
     */
    private $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param Request $request
     * @param \Closure $next
     * @param $roles
     * @return mixed
     */
    public function handle($request, \Closure $next, $roles)
    {
        if ($this->auth->guest() || !$request->user()->hasRoles($roles)) {
            abort(403);
        }

        return $next($request);
    }
}