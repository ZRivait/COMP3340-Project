<?php
    
    $errors = array();


if(isset($_POST['submit'])){

    include_once 'config.php';

    $email = mysqli_real_escape_string($db,$_POST['email']);
    $pass = mysqli_real_escape_string($db,$_POST['pass']);


     // form validation
    if(empty($email)){array_push($errors,"Email is required.");}
    if(empty($pass)){array_push($errors,"Password is required.");}

    $sql = "select uname from users where uname = '$email'";
    $sqlresult = mysqli_query($db,$sql);
    $salt= '';  
    $up = '';
    $ad = '';
    
    if(mysqli_num_rows($sqlresult)==1){
       // user exists
       
       // salt and hash password
       $sqls = "select admin, salt, upasshashed from users where uname = '$email'";
       
       if ($stmt = mysqli_prepare($db, $sqls)) {

        /* execute statement */
        mysqli_stmt_execute($stmt);
    
        /* bind result variables */
        mysqli_stmt_bind_result($stmt,$a,$s,$p);
    
        /* fetch values */
        while (mysqli_stmt_fetch($stmt)) {
            $salt = $s;
            $up = $p;
            $ad = $a;
        }
    
        /* close statement */
        mysqli_stmt_close($stmt);
    }

    $hashpwd = sha1($pass.$salt);


    if(hash_equals($hashpwd , $up )){
        
        session_start();

        if($ad == "0"){  // start user session
            $_SESSION["user"] = true;
            $_SESSION["email"] = $email;
        }

        if($ad == "1"){ // start admin session 
            $_SESSION["admin"] = true;
            $_SESSION["email"] = $email;
        }
        
        header("Location: index.php");
    }
    else{
        array_push($errors,"Invalid username or password.");
        $autofill = array();
        array_push($autofill,$email);

        $_SESSION["l_errors"] = $errors;
        $_SESSION["l_autofill"] = $autofill;

        header("Location:  login.php?login=failed");
        exit();
    }
    

    }
    else{ // not found
        array_push($errors,"Invalid username or password.");
        $autofill = array();
        array_push($autofill,$email);

        $_SESSION["l_errors"] = $errors;
        $_SESSION["l_autofill"] = $autofill;
        
        header("Location:  login.php?login=failed"); 
        exit();
    }


}
else{
    header("Location: login.php");
    exit();
}

?>