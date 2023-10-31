<?php
class DeleteRole{
    
    public $role;
    public function __construct() {
        session_start();
        
        if (!isset($_SESSION['user'])) {
            header('Location: Login.php');
        }
        if (!isset($_SESSION['user']->role)) {
            $_SESSION['warning'] = 'You have no permission to access Role';
            header('Location: index.php');
            exit;
        }
        if ((isset($_SESSION['user']->role) && $_SESSION['user']->role != 'Admin')) {
            $_SESSION['warning'] = 'You have no permission to access Role';
            header('Location: index.php');
            exit;
        }
        $role = $this->get($_GET['uid']);
        $this->role =  $role;
        $this->delete();
    }

    function get($uid){
        if(isset($uid) && $uid != "") {
            $recoveredData = file_get_contents('./data/roles.json', T_CURLY_OPEN);
            $roleList = json_decode($recoveredData);
            foreach($roleList as $role){
                if($role->uid == $uid){
                    return $role;
                }
            }
            return false;
        }
        
    }

    public function delete() {
        $recoveredData = file_get_contents('./data/roles.json');
        $existingData = json_decode($recoveredData);
        $data= [];
        foreach ($existingData as $key => $role) {
            $exist['id'] = $role->id;
            $exist['uid'] = $role->uid;
            $exist['role'] = $role->role;
            $exist['permissions'] = $role->permissions;
            if ($role->role == $this->role->role) {
                unset($exist);
            }
            !empty($exist)? $data[] = $exist:"";
            
        }
        
        $_SESSION['message'] = "Role successfully deleted";
        file_put_contents('./data/roles.json', json_encode($data));
        header('Location: Role.php');
        exit;
    }
}

$roleObj = new DeleteRole();
if(empty($roleObj->role)){
    $_SESSION['warning'] = "You UID is invalid";
    header('Location: Role.php');
    exit;
}

?>