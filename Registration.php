<?php
class Registration
{
    function __construct()
    {
        session_start();
    }

    function register($formData)
    {
        $userData = [
            "id"=> 1,
            'uid' => uniqid(),
            'name' => $formData['name'],
            'username' => $formData['username'],
            'email' => $formData['email'],
            'role' => isset($formData['role'])? $formData['role']: "User",
            'password' => sha1($formData['password'])
        ];
        
        $validation = $this->validation($formData);

        if ($validation === true) {
            $recoveredData = file_get_contents('./data/userList.json');
            $existingData = json_decode($recoveredData);

            if (!empty($existingData)) {
                $userData['id'] = (count($existingData)+1);
                $existingData = (array) $existingData;
                array_push($existingData, $userData);
                $serializedData = json_encode($existingData);
            } else {
                $userDataFirst[] = $userData;
                $serializedData = json_encode($userDataFirst);
            }
            file_put_contents('./data/userList.json', $serializedData);
            $_SESSION['message'] = "A New User successfully created";
            header('Location: Login.php');
            exit;
        } else {
            return $validation;
        }
        
    }

    function validation(array $data)
    {
        $name = $data["name"];
        $username = $data["username"];
        $email = $data["email"];
        $password = $data["password"];
        $confirmPassword = $data["confirmPassword"];

        if (trim($name) == "") {
            return "Name Filed is required";
        }elseif (trim($username) == "") {
            return "Username Filed is required";
        }elseif (trim($email) == "") {
            return "Email Filed is required";
        }elseif (trim($password) == "" || trim($confirmPassword) == "") {
            return "Password and confirm password field is required";
        }
        elseif (trim($password) != trim($confirmPassword)) {  
            return "Your password and confirm password dose not match";
        }else{
            $recoveredData = file_get_contents('./data/userList.json', T_CURLY_OPEN);
            $userList = json_decode($recoveredData);
            if (!empty($userList)) {
                foreach ($userList as $key => $user) {
                    if ($user->email == $email) {
                        return "Register email already exist";
                    } elseif ($user->username == $username) {
                        return "Register username already exist";
                    }
                }
            }
            return true;
        }

        
    }
}
$regObj = new Registration();
$formData = [];
if (isset($_POST['register'])) {
    $formData['name'] = $_POST['name'];
    $formData['username'] = $_POST["username"];
    $formData['email'] = $_POST["email"];
    $formData['role'] = isset($_POST["role"])? $_POST["role"]: NULL;
    $formData['password'] = $_POST["password"];
    $formData['confirmPassword'] = $_POST["confirm"];
    $response = $regObj->register( $formData );
    if ($response) {
        $_SESSION["message"] = $response;
    } else {
        $_SESSION["message"] = "an error has been occurred";
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

    <div class="p-2 bg-primary text-white text-center">
        <h1>Ostad - Module 5 Assignment</h1>
    </div>
    <?php 
        if (isset($_SESSION['user'])) {
    ?>
    <nav class="navbar navbar-expand-sm bg-dark navbar-dark text-center">
        <div class="container">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="Registration.php"><?= isset($_SESSION['user'])? "Create New User": "Register" ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="Role.php"> Role List</a>
                </li>
                <?php 
                if(isset($_SESSION['user'])){
                    ?>
                <li class="nav-item">
                    <a class="nav-link" href="Logout.php"> Logout | Welcome <?= $_SESSION['user']->name ?></a>
                </li>
                    <?php
                }
                ?>

                
            </ul>
        </div>
    </nav>
    <?php 
        }
    ?>

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
                                        <div class="alert alert-warning" role="alert">
                                        <?= $_SESSION['message']; ?>

                                        </div>
                                        <?php
                                        unset( $_SESSION['message'] );
                                    }
                                    ?>
                                    <h3><?= isset($_SESSION['user'])? "Create New User": "Registration" ?></h3>
                                    <hr>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" name="name" value="<?= isset($_POST['name'])? trim($_POST['name']):"" ?> " class="form-control" id="name" placeholder="Your Name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" name="username" value="<?= isset($_POST['username'])? trim($_POST['username']):"" ?> " class="form-control" id="username" placeholder="Username">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="text" name="email" value="<?= isset($_POST['email'])? trim($_POST['email']):"" ?> " class="form-control" id="email" placeholder="address@domain.com">
                                    </div>
                                    <?php 
                                    if (isset($_SESSION["user"])){
                                    ?>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Select Role</label>
                                            <select name="role" class="form-control" value="" id="role">
                                                <option value="">Select</option>
                                                <option value="Admin">Admin</option>
                                                <option value="User">User</option>
                                            </select>
                                    </div>
                                    <?php 
                                    }
                                    ?>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm" class="form-label">Confirm Password</label>
                                        <input type="password" name="confirm" class="form-control" id="confirm" placeholder="Re-Type Password">
                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" name="register" class="btn btn-primary"><?= isset($_SESSION['user'])? "Save": "Register" ?></button>
                                    </div>
                                    
                                    <div class="mb-3">
                                    <?php 
                                        if (!isset($_SESSION["user"])){
                                        ?>
                                        Have already an account?
                                        <a href="Login.php" class="btn btn-secondary" class="btn btn-secondary" >Login</a>
                                        <?php 
                                        }
                                        ?>
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