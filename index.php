<?php
class Home{
    
    protected $username;
    protected $email;
    protected $role;
    function __construct() {
        session_start();
        if (!isset($_SESSION['user'])) {
            header('Location: Login.php');
        }
        $this->username = $_SESSION['user']->username;
        $this->email = $_SESSION['user']->email;
        $this->role =  isset($_SESSION['user']->role)? $_SESSION['user']->role: "User";
    }

    function index() {
        echo "Successfully Login";
    }

    function profile() {
        return $this->username;
    }

    public function users() {
        $recoveredData = file_get_contents('./data/userList.json', T_CURLY_OPEN);
        $userList = json_decode($recoveredData);
        return $userList;
    }
}

$homeObj = new Home();
$users = $homeObj->users();

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

<body class="">


    <nav class="navbar navbar-expand-sm bg-dark navbar-dark text-center">
        <div class="container">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="Registration.php">Create New User</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="Role.php"> Role List</a>
                </li>
                <?php 
                if(isset($_SESSION['user'])){
                    $role = isset($_SESSION['user']->role)? $_SESSION['user']->role: "User";
                    ?>
                <li class="nav-item">
                    <a class="nav-link" href="Logout.php"> Logout | Welcome <?= $_SESSION['user']->name ." (".$role.")"?></a>
                </li>
                <?php
                }
                ?>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row ">
            <div class="mask d-flex align-items-center h-100 gradient-custom-3  ">
                <div class="container h-100">
                    <div class="row">
                        <div class="col-12">
                            <h1>Manage Users</h1>
                            <hr>
                            <?php 
                                if(isset($_SESSION["warning"])){     
                            ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>Warning!</strong> <?= $_SESSION["warning"] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php 
                                unset($_SESSION['warning']);
                                }
                            ?>
                            <?php 
                                if(isset($_SESSION["message"])){     
                            ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success!</strong> <?= $_SESSION["message"] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php 
                                unset($_SESSION['message']);
                                }
                            ?>
                            <table class="table table-hover">
                                <thead>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                    <?php 
                                        $i = 0;
                                        if (!empty($users)) {
                                        foreach ($users as $key => $user) {
                                            $i++;
                                           ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td><?= $user->name ?></td>
                                                <td><?= $user->username ?></td>
                                                <td><?= $user->email ?></td>
                                                <td><?= isset($user->role)? $user->role:"User" ?></td>
                                                <td>
                                                    <a href="EditUser.php?uid=<?= $user->uid ?>" class="btn btn-warning text-white btn-sm ">Edit</a>
                                                    <a href="DeleteUser.php?uid=<?= $user->uid ?>" class="btn btn-danger btn-sm text-white">Delete</a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }else{
                                    ?>
                                        <tr>
                                            <th colspan="6">No user available</th>
                                        </tr>
                                    <?php 
                                    }
                                        ?>
                                    
                                </tbody>
                            </table>
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