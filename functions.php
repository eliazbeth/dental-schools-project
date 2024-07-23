<?php
    function check_login($con)
    {
        if(isset($_SESSION['username']))
        {
            $user = $_SESSION['username'];
            $query = "SELECT * FROM student WHERE username='$user' LIMIT 1";
            $result = $con->query($query);
            if($result && mysqli_num_rows($result)>0)
            {
                $user_data = mysqli_fetch_assoc($result);
                return($user_data);
            }
            // Redirect to login if username not found
            header("Location: login.php");
            die;
        }
    }
    function updateStudentName($con, $name, $user)
    {
        if($name != NULL)
        {
            $query = "UPDATE student SET name='$name' WHERE username='$user'";
            $con->query($query);  
        }
        else
        {   
            $query = "UPDATE student SET name=NULL WHERE username='$user'";
            $con->query($query); 
        }
    }
    function updateStudentState($con, $state, $user)
    {
        if($state != NULL)
        {
            $query = "UPDATE student SET state='$state' WHERE username='$user'";
            $con->query($query);  
        }
        else
        {   
            $query = "UPDATE student SET state=NULL WHERE username='$user'";
            $con->query($query); 
        }
    }
    function updateStudentGPA($con, $gpa, $user)
    {
        if($gpa != NULL)
        {
            $query = "UPDATE student SET gpa='$gpa' WHERE username='$user'";
            $con->query($query);  
        }
        else
        {   
            $query = "UPDATE student SET gpa=NULL WHERE username='$user'";
            $con->query($query); 
        }
    }
    function updateStudentDAT($con, $dat, $user)
    {
        if($dat != NULL)
        {
            $query = "UPDATE student SET dat='$dat' WHERE username='$user'";
            $con->query($query);  
        }
        else
        {   
            $query = "UPDATE student SET dat=NULL WHERE username='$user'";
            $con->query($query); 
        }
    }
    function updateStudentShadow($con, $shadow, $user)
    {
        if($shadow != NULL)
        {
            $query = "UPDATE student SET shadowing_hours='$shadow' WHERE username='$user'";
            $con->query($query);  
        }
        else
        {   
            $query = "UPDATE student SET shadowing_hours=NULL WHERE username='$user'";
            $con->query($query); 
        }
    }
?>