<?php

interface AuthInterface
{
    public function removeLoggedInUser();
    public function start($user);
    public function check();
    public function end();
}
