<?php

namespace App\controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{

    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showAuthLogin()
    {
        $this->view("/auth/login", []);
    }

    public function showAuthRegister()
    {
        $this->view("/auth/register", []);
    }

    public function register()
    {
        $message = "";

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            if (empty($name) || empty($phone) || empty($username) || empty($password)) {
                $message = "Semua field harus diisi.";
            } elseif (strlen($username) < 4) {
                $message = "Username minimal 4 karakter.";
            } elseif (strlen($password) < 6) {
                $message = "Password minimal 6 karakter.";
            }

            if (empty($message)) {
                if ($this->userModel->register($name, $email, $password)) {
                    header('Location: /auth/login');
                    exit;
                } else {
                    $message = "Registrasi gagal!";
                }
            }

            $result = [
                'message' => $message
            ];

            $this->view('/auth/register', $result);
        }
    }


    public function login()
    {
        $message = "";

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            if (empty($email) || empty($password)) {
                $message = "Semua field harus diisi.";
            } else {
                $isLoggedInUser = $this->userModel->login($email, $password);

                if ($isLoggedInUser) {
                    $_SESSION['user_id'] = $isLoggedInUser['id'];
                    $_SESSION['email'] = $isLoggedInUser['email'];
                    $_SESSION['name'] = $isLoggedInUser['name'];
                    $_SESSION['role'] = $isLoggedInUser['role'];


                    if ($isLoggedInUser['role'] == "admin") {
                        header('Location: /dashboard');
                    } else {
                        header('Location: /');
                    }
                    exit;
                } else {
                    $message = "Invalid credentials.";
                }
            }
        }

        $result = [
            "message" => $message
        ];

        $this->view('/auth/login', $result);
    }

    public function logout()
    {
        // Start the session if it's not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Unset all of the session variables
        $_SESSION = array();

        // Destroy the session
        session_destroy();

        // Redirect to the login page or homepage
        header('Location: /auth/login');
        exit;
    }
}
