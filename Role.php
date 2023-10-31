<?php
class Role{
    
    protected $username;
    protected $email;
    protected $role;
    function __construct() {
        session_start();
        if (!isset($_SESSION['user'])) {
            header('Location: Login.php');
        }
        if (!isset($_SESSION['user']->role)) {
            $_SESSION['warning'] = 'You have no permission to access Role';
            header('Location: index.php');
            exit();
        }
        if ((isset($_SESSION['user']->role) && $_SESSION['user']->role != 'Admin')) {
            $_SESSION['warning'] = 'You have no permission to access Role';
            header('Location: index.php');
            exit();
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

    public function roles() {
        $recoveredData = file_get_contents('./data/roles.json', T_CURLY_OPEN);
        $roleList = json_decode($recoveredData);
        return $roleList;
    }
}

$roleObj = new Role();
$roles = $roleObj->roles();

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

    <div class="p-2 bg-primary text-white text-center">
        <h1>Ostad - Module 5 Assignment</h1>
    </div>

    <nav class="navbar navbar-expand-sm bg-dark navbar-dark text-center">
        <div class="container">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="Registration.php">Create New User</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="Role.php"> Role List</a>
                </li>
                <?php 
                    $role = isset($_SESSION['user']->role)? $_SESSION['user']->role: "User";
                    ?>
                <li class="nav-item">
                    <a class="nav-link" href="Logout.php"> Logout | Welcome <?= $_SESSION['user']->name ." (".$role.")"?></a>
                </li>

                
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row ">
            <div class="mask d-flex align-items-center h-100 gradient-custom-3  ">
                <div class="container h-100">
                    <div class="row">
                        <div class="col-12">
                            
                            <h1>Manage Role 
                                <a href="CreateRole.php" class="btn btn-primary float-end">Create New Role</a>
                            </h1>
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
                                    <th>Permissions</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                    <?php 
                                        $i = 0;
                                        if (!empty($roles)) {
                                            foreach ($roles as $key => $role) {
                                                $i++;
                                               ?>
                                                <tr>
                                                    <td><?= $i ?></td>
                                                    <td><?= $role->role ?></td>
                                                    <td><?= implode(", ", $role->permissions);?></td>
                                                    <td>
                                                        <a href="EditRole.php?uid=<?=$role->uid?>" class="btn btn-warning text-white btn-sm ">Edit</a>
                                                        <a href="DeleteRole.php?uid=<?=$role->uid?>" class="btn btn-danger btn-sm text-white">Delete</a>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }else{
                                            ?>
                                                <tr>
                                                    <th colspan="4">No roles are available</th>
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