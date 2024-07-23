<?php
session_start();
    include("connection.php");
    include("functions.php");
    $user_data = check_login($con);

    if($_SERVER['REQUEST_METHOD']=="POST")
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        if(!empty($username) && !empty($password))
        {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $query = "SELECT * FROM student WHERE username='$username' LIMIT 1";
            $result = $con->query($query);
            if($result && mysqli_num_rows($result)>0)
            {
                $user_data = mysqli_fetch_assoc($result);
                if(password_verify($password, $user_data['password']))
                {
                    $_SESSION['username'] = $user_data['username'];
                    header("Location: index.php");
                    die;
                }
            }
            echo "Wrong username or password.";
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
        <title>Login</title>
    </head>
    <body>
        <div>
            <form method="post">
                <div>Log In</div><br>
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
                <input type="submit" name="login"><br><br>
                <a href="signup.php">Sign Up</a><br><br>
            </form>
        </div>
    </body>
    </html>
