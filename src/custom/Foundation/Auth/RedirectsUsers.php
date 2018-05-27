<?php

namespace Custom\Foundation\Auth;

trait RedirectsUsers
{
    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

//        $redirectUrl = property_exists($this, 'redirectTo') ? $this->redirectTo : '/';
//
//        return $redirectUrl;

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/';
    }
}
