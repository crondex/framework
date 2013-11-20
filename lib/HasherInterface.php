<?php

interface HasherInterface
{
    public function PasswordHash($iteration_count_log2, $portable_hashes);
    public function get_random_bytes($count);
    public function encode64($input, $count);
    public function gensalt_private($input);
    public function crypt_private($password, $setting);
    public function gensalt_extended($input);
    public function gensalt_blowfish($input);
    public function HashPassword($password);
    public function CheckPassword($password, $stored_hash);
}
