<?php
/**
 * hyper v1.0.0-beta.2 (https://hyper.com/php)
 * Copyright (c) 2019. J.Charika
 * Licensed under MIT (https://github.com/joecharika/hyper/master/LICENSE)
 */

namespace Hyper\Application;


use DateInterval;
use DateTime;
use Exception;
use Hyper\Database\DatabaseContext;
use Hyper\Functions\Arr;
use Hyper\Functions\Debug;
use Hyper\Models\User;

/**
 * Class Authorization
 * @package hyper\Application
 */
class Authorization
{
    /** @var string $token */
    public $token;

    /** @var User */
    public $user;

    /** @var DatabaseContext */
    private $db;

    /**
     * @var string
     */
    private $cryptoAlgorithm = "whirlpool";

    /**
     * Authorization constructor.
     */
    public function __construct()
    {
        $this->db = new DatabaseContext('user');

        if (session_status() !== 2) session_start();

        $this->restoreSession();
    }

    /**
     *
     */
    private function restoreSession()
    {
        $this->user = $this->getSession()->user;
        $this->token = $this->getSession()->token;
    }

    /**
     * @return object
     */
    public function getSession(): object
    {
        if (session_status() !== 2) return null;

        return (object)[
            'id' => session_id(),
            'token' => $this->token,
            'user' => $this->db->firstById(Arr::key($_SESSION, 'user', 0)),
            'expiryDate' => $this->getExpiryDate(),
        ];
    }

    /**
     * @return DateTime|false|null
     */
    public function getExpiryDate()
    {
        try {
            return date_add(new DateTime(), new DateInterval(session_cache_expire()));
        } catch (Exception $exc) {
            return null;
        }
    }

    /**
     * Logout currently logged in user
     */
    public function logout()
    {
        $this->user = null;
        $this->destroySession();
    }

    /**
     * Destroy user session
     */
    private function destroySession()
    {
        unset($_SESSION['user']);
        if (session_start() === 2) session_destroy();
    }

    /**
     * Register a new user with username and password
     * @param string $username
     * @param string $password
     * @param string $role
     * @return User|string
     */
    public function register(string $username, string $password, $role = 'default')
    {
        $user = new User($username);
        $exists = $this->db->firstWhere('username', '=', $username);


        if (isset($exists))
            return "Username '$username' is already taken";

        if (strlen($password) < 9) return 'Password must be at least 8 characters long';

        $user->id = uniqid();
        $user->salt = uniqid();
        $user->key = $this->encrypt($password, $user->salt);
        $user->name = $username;
        $user->role = $role;

        if ($this->db->insert($user))
            return $this->login($username, $password);

        return 'Registration failed';
    }

    /**
     * Generate a PBKDF2 key derivation of a supplied password
     * @param string $password
     * @param string $salt
     * @return mixed
     */
    public function encrypt(string $password, $salt = null): string
    {
        $salt = isset($salt) ? $salt : uniqid();
        return hash_pbkdf2($this->cryptoAlgorithm, $password, $salt, 7);
    }

    /**
     * Sign in a user with username and password
     * @param string $username
     * @param string $password
     * @return User|string
     */
    public function login(string $username, string $password)
    {
        #Get user from the database
        $this->user = $this->db->first(function ($user) use ($username) {
            return $user->username === $username;
        });

        if (isset($this->user)) {
            if ($this->user->key === $this->encrypt($password, $this->user->salt)) {
                if ($this->createSession($this->user))
                    return HyperApp::$user = $this->user;
            } else return 'Password is incorrect or has not been created yet';
        }

        return 'User is not registered';
    }

    /**
     * Create a new session
     * @param User $user
     * @return bool True if the session update was accepted,
     */
    private function createSession(User $user): bool
    {
        #Update user information
        $user->lastLoginIP = $this->getIPAddress();
        $user->lastLoginDate = date('Y-m-d h:m:s');
        $user->lastLoginBrowser = Arr::key($_SERVER, 'HTTP_USER_AGENT', 'Unknown browser/User agent');

        #Generate new token for user
        $user->lastLogInToken = $this->getNewToken();

        #Check if the update was accepted or not
        if ($update = $this->db->update($user)) {
            #Save user.id to session
            $_SESSION['user'] = $user->id;
        }

        return $update;
    }

    /**
     * Get IP Address
     * @return string
     */
    public function getIPAddress(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    private function getNewToken()
    {
        return strtoupper(uniqid() . uniqid() . uniqid() . uniqid() . uniqid());
    }
}
