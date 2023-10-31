<?php
class Login {
    private $db;

    function __construct() {
        session_start();
        if (isset($_SESSION['user'])) {
            header('Location: index.php');
            exit();
        }
        // Initialize your database connection here.
        $this->db = new PDO("mysql:host=your_host;dbname=your_db", "your_username", "your_password");
    }

    function login($username, $password) {
        $validation = $this->validation(['username' => $username, 'password' => $password]);
        if ($validation === true) {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                unset($user['password']);
                $this->setSession($user);
                return true; // Return true for a successful login
            } else {
                $_SESSION['warning'] = 'Your username or password is invalid';
                return false; // Return false for an unsuccessful login
            }
        } else {
            $_SESSION['warning'] = $validation;
            return false; // Return false for an unsuccessful login
        }
    }

    function validation($data) {
        if (trim($data['username']) == '') {
            return "The username field is required";
        } elseif (trim($data['password']) == "") {
            return "The password field is required";
        }
        return true;
    }

    function setSession($user) {
        session_start();
        $_SESSION['user'] = $user;
        header('Location: index.php');
        exit();
    }

    function logOut() {
        session_destroy();
        header('Location: Login.php');
        exit();
    }
}

$logObj = new Login();

if (isset($_POST['login'])) {
    $response = $logObj->login($_POST["username"], $_POST["password"]);
    if ($response === true) {
        header('Location: index.php');
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .fakeimg {
            height: 200px;
            background: #aaa;
        }
    </style>
</head>

<body class="bg-secondary">

    <div class="container mt-5">
        <div class="row ">
            <div class="mask d-flex align-items-center h-100 gradient-custom-3  ">
                <div class="container h-100">
                    <div class="row d-flex justify-content-center align-items-center h-100">
                        <div class="col-12 col-md-9 col-lg-7 col-xl-6">
                            <div class="card" style="border-radius: 15px;">
                                <div class="card-body p-5">

                                <form method="post" action="">
                                    <?php 
                                    if (isset($_SESSION['message'])){
                                        ?>
                                        <div class="alert alert-success" role="alert">
                                        <?= $_SESSION['message']; ?>

                                        </div>
                                        <?php
                                        unset($_SESSION['message']);
                                    }

                                    if (isset($_SESSION['warning'])){
                                        ?>
                                        <div class="alert alert-warning" role="alert">
                                        <?= $_SESSION['warning']; ?>

                                        </div>
                                        <?php
                                        unset($_SESSION['warning']);
                                    }

                                    ?>
                                    <h3>Login</h3>
                                    <hr>
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" name="username" value="<?= isset($_POST['username'])? trim($_POST['username']):"" ?> " class="form-control" id="username" placeholder="Username">
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" name="login" class="btn btn-primary">Login</button>
                                    </div>
                                    <div class="mb-3">
                                        Create a new account
                                        <a href="Registration.php" class="btn btn-secondary" >Registration</a>
                                        
                                    </div>
                                </form>

                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
        </div>
    </div>

    <div class="mt-5 p-4 bg-dark text-white text-center">
        <p>Footer</p>
    </div>

</body>

</html>