<?php
class EditUser
{
    public $user;
    public $roles;
    function __construct()
    {
        session_start();
        if (!isset($_SESSION['user'])) {
            header('Location: Login.php');
        }
        $this->user = $this->get($_GET['uid']);
        $this->roles = $this->getRoles();

    }

    function get($uid){
        if(isset($uid) && $uid != "") {
            $recoveredData = file_get_contents('./data/userList.json', T_CURLY_OPEN);
            $userList = json_decode($recoveredData);
            foreach($userList as $user){
                if($user->uid == $uid){
                    return $user;
                }
            }
        }
        
    }

    function getRoles(){
        $recoveredData = file_get_contents('./data/roles.json');
        $existingData = json_decode($recoveredData);
        return $existingData;
    }

    function update($formData)
    {
        $userData = [
            "id"=> 1,
            'uid' => uniqid(),
            'name' => $formData['name'],
            'username' => $formData['username'],
            'email' => $formData['email'],
            'role' => isset($formData['role'])? $formData['role']: "User",
        ];
        
        $validation = $this->validation($formData);

        if ($validation === true) {
            $recoveredData = file_get_contents('./data/userList.json');
            $existingData = json_decode($recoveredData);

            $data= [];
            foreach ($existingData as $key => $user) {
                $exist['id'] = $user->id;
                $exist['uid'] = $user->uid;
                $exist['name'] = $user->name;
                $exist['username'] = $user->username;
                $exist['email'] = $user->email;
                $exist['role'] = $user->role;
                $exist['password'] = $user->password;
                if ($user->uid == $this->user->uid) {
                    $exist['uid'] = $user->uid;
                    $exist['name'] = trim($formData['name']);
                    $exist['username'] = trim($formData['username']);
                    $exist['email'] = trim($formData['email']);
                    $exist['role'] = trim($formData['role']);
                }
                $data[] = $exist;
            }
            
            $_SESSION['message'] = "User successfully updated";
            file_put_contents('./data/userList.json', json_encode($data));
            header('Location: index.php');
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

        if (trim($name) == "") {
            return "Name Filed is required";
        }elseif (trim($username) == "") {
            return "Username Filed is required";
        }elseif (trim($email) == "") {
            return "Email Filed is required";
        }else{
            $recoveredData = file_get_contents('./data/userList.json', T_CURLY_OPEN);
            $userList = json_decode($recoveredData);
            if (!empty($userList)) {
                foreach ($userList as $key => $user) {
                    if ($user->email == $email && $user->uid != $this->user->uid) {
                        return "Register email already exist";
                    } elseif ($user->username == $username && $user->uid != $this->user->uid) {
                        return "Register username already exist";
                    }
                }
            }
            return true;
        }

        
    }
}
$userObj = new EditUser();
if (isset($_POST['register'])) {
    $formData['name'] = $_POST['name'];
    $formData['username'] = $_POST["username"];
    $formData['email'] = $_POST["email"];
    $formData['role'] = $_POST["role"];
    $response = $userObj->update( $formData );
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
                                    <h3>User Update</h3>
                                    <hr>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" name="name" value="<?= isset($userObj->user)? $userObj->user->name:"" ?> " class="form-control" id="name" placeholder="Your Name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" name="username" value="<?= isset($userObj->user)? $userObj->user->username:"" ?>" class="form-control" id="username" placeholder="Username">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="text" name="email" value="<?= isset($userObj->user)? $userObj->user->email:"" ?>" class="form-control" id="email" placeholder="address@domain.com">
                                    </div>
                                    <?php 
                                    if (isset($_SESSION["user"])){
                                    ?>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Select Role</label>
                                            <select name="role" class="form-control" value="" id="role">
                                                <option value="">Select</option>
                                                <?php 
                                                    foreach ($userObj->roles as $key => $role) {
                                                ?>
                                                <option <?= isset($userObj->user) && $userObj->user->role == $role->role? "selected":"" ?> value="<?= $role->role ?>"><?= $role->role ?></option>

                                                <?php 
                                                    }
                                                ?>
                                            </select>
                                    </div>
                                    <?php 
                                    }
                                    ?>
                                    <div class="mb-3">
                                        <button type="submit" name="register" class="btn btn-primary">Update</button>
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