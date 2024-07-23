<?php
session_start();
    include("connection.php");
    include("functions.php");
    $user_data = check_login($con);
    
    if($_SERVER['REQUEST_METHOD']=="POST")
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $name = $_POST['name'];
        if(!empty($username) && !empty($password))
        {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO student(username, password, name) values ('$username','$hash','$name')";
            $con->query($query);

            header("Location: login.php");
            die;
        }
        else
            echo "Enter valid information";
    }
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Signup</title>
    </head>
    <body>
        <div>
            <form method="post">
                <div>Sign Up</div><br>
                <div display=flex>
                    <label>Username </label>
                    <input type="text" name="username">
                    <br><br>
                </div>
                <div display=flex>
                    <label>Password </label>
                    <input type="password" name="password">
                    <br><br>
                </div>
                <div display=flex>
                    <label>Name </label>
                    <input type="text" name="name">
                    <br><br>
                </div>
                <input type="submit" name="signup"><br><br>
                <a href="login.php">Log In</a><br><br>
            </form>
        </div>
    </body>
    </html>