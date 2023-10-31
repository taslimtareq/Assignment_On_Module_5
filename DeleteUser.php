<?php
class DeleteUser{
    
    public $user;
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
            $_SESSION['warning'] = 'You have no permission to delete user';
            header('Location: index.php');
            exit;
        }
        $user = $this->get($_GET['uid']);
        $this->user =  $user;
        $this->delete();
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
            return false;
        }
        
    }

    public function delete() {
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
                unset($exist);
            }
            !empty($exist)? $data[] = $exist:"";
            
        }
        
        $_SESSION['message'] = "User successfully deleted";
        file_put_contents('./data/userList.json', json_encode($data));
        header('Location: index.php');
        exit;
    }
}

$userObj = new DeleteUser();
if(empty($userObj->user)){
    $_SESSION['warning'] = "You UID is invalid";
    header('Location: index.php');
    exit;
}

?>