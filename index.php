<?php
    session_start();
    include("connection.php");
    include("functions.php");
    $user_data = check_login($con);

    // Adding courses student has taken based on checkbox entry
    if (!empty($_POST['courseForm']))
    {
        $array = $_POST['courseForm'];
        for($i=0; $i<count($array); $i++)
        {
            $query = "INSERT INTO has_taken VALUES('$user_data[id]', '$array[$i]')";
            $con->query($query); 
        }
    }
    // Delete student courses
    if(isset($_POST['courseToRemove']))
    {
        $course = $_POST['courseToRemove'];
        $query = "DELETE FROM has_taken WHERE (course='$course' AND student_id='$user_data[id]')";
        $con->query($query); 
    }

    // Updating student information based on text entries
    if(isset($_POST['name']) || isset($_POST['state'])
    || isset($_POST['gpa']) || isset($_POST['dat'])
    || isset($_POST['shadowing_hours']))
    {
        $name = $_POST['name'];
        $state = $_POST['state'];
        $gpa = $_POST['gpa'];
        $dat = $_POST['dat'];
        $shadowing_hours = $_POST['shadowing_hours'];

        $user = $user_data['username'];
        updateStudentName($con, $name, $user);
        updateStudentState($con, $state, $user);
        updateStudentGPA($con, $gpa, $user);
        updateStudentDAT($con, $dat, $user);
        updateStudentShadow($con, $shadowing_hours, $user);
        header("Location: index.php");
    }
    
    // Remove schools from student's list
    if(isset($_POST['nameToRemove']) && isset($_POST['stateToRemove']))
    {
        $name = $_POST['nameToRemove'];
        $state = $_POST['stateToRemove'];
        $query = "DELETE FROM might_apply_to WHERE (school_state='$state' AND school_name='$name' AND student_id='$user_data[id]')";
        $con->query($query); 
    }
    // Add schools to student's list
    if(isset($_POST['nameToAdd']) && isset($_POST['stateToAdd']))
    {
        $name = $_POST['nameToAdd'];
        $state = $_POST['stateToAdd'];
        $query = "INSERT INTO might_apply_to(student_id, school_name, school_state) VALUES('$user_data[id]','$name','$state')";
        $con->query($query); 
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Index Page</title>
        <style>
            #two-columns {display: flex; flex-shrink:0; justify-content:space-around;}
            #column-2 {padding:10px;}
            #header {display: flex; justify-content: space-between;}
            #course-list {display:flex; gap:10px;}
            #filters {text-align:center;}
            
            table{margin-left: auto; margin-right:auto; text-align:center;}
            td{width: 70px;}
            li{margin-bottom:10px;}
            span{text-align:center;}

            .long-data{width: 200px;}
            .search-form {text-align: center}
            .course-reqs {list-style-type: none; padding:0; column-count: 2;}

        </style>
    </head>
    <div id=header>
        <h4>Logged in as <?php echo $user_data['username'] ?></h4>
        <h1>Dental School Application Helper</h1>
        <h4><a href="logout.php">Log Out</a></h4>
    </div>
    <div id=two-columns>
        <div id=column-1 >
            <!--//DISPLAY STUDENT INFO-->
            <p>
                <h2>Hello, <?php echo $user_data['name'] ?></h2>
                You are a <?php if($user_data['state']==NULL) echo "_____"; else echo $user_data['state']; ?> resident
                with <?php if($user_data['shadowing_hours']==NULL) echo "_____"; else echo $user_data['shadowing_hours']; ?> shadowing hours,
                a GPA of <?php if($user_data['gpa']==NULL) echo "_____"; else echo number_format($user_data['gpa'],2);
                if($user_data['dat']!=NULL) echo ", and a DAT score of ".$user_data['dat'];?><br>
                You have taken the following courses: <br> <?php $query = "SELECT * FROM has_taken WHERE student_id = $user_data[id]"; $result=$con->query($query);?>
            </p>
            <!---//DISPLAY STUDENT COURSES-->
            <ul>
                <?php while($row = mysqli_fetch_assoc($result))
                {?> 
                    <li><div id="course-list">
                        <?php echo $row['course'];?>
                        <form id="delete-course" action="index.php" method="post">
                            <input type="submit" name="remove" value="Remove">
                            <input type="hidden" name="courseToRemove" value="<?php echo $row['course']?>">
                        </form>
                    </div></li>
               <?php } ?>
            </ul>
        </div><!--//END column-1 div-->
        <div id=column-2>
        <?php//UPDATE STUDENT INFORMATION FORM?>
            <form action = "index.php" method = "post"> <pre>
                    Update your info here:<br>
            Name        <input type="text" name="name" value="<?php echo $user_data['name'] ?>">
            State       <input type="text" name="state" value="<?php echo $user_data['state'] ?>">
            GPA         <input type="text" name="gpa" value="<?php echo $user_data['gpa'] ?>">
            DAT         <input type="text" name="dat" value="<?php echo $user_data['dat'] ?>">
            Shadowing   <input type="text" name="shadowing_hours" value="<?php echo $user_data['shadowing_hours'] ?>"><br>
                                <input type="submit" name="UPDATE RECORD" value="Update">
            </pre> </form>
            <?php//STUDENT ADD COURSES TAKEN FORM?>
            <?php $query = "SELECT * FROM course"; $result=$con->query($query);?>
            <form action="index.php" method="post">
            <?php   $counter = 0; while($row = mysqli_fetch_assoc($result))
                    {?>
                        <?php $counter++; if($counter % 4 == 0){ echo nl2br("\n");}?>
                        <input type="checkbox" value="<?php echo $row['name']?>" name="courseForm[]">
                        <label for="<?php echo $row['name']?>"> <?php echo $row['name']?> </label>
            <?php    }?>
                <input type="submit" name="ADD COURSES" value="Add">
            </form> <br>
        </div> <?php//END column-2 div?>
    </div><!--//END two-columns div-->
            <!--//STUDENT SAVED-SCHOOLS TABLE-->
            <table border = "3">
                <caption>Your Schools</caption>
                <tr>
                    <th>State</th>
                    <th>Name</th>
                    <th> </th>
                    <th>Required Courses</th>
                    <th>Recommended Courses</th>
                    <th>Required Courses Left to Take</th>
                    <th>Recommended Courses Left to Take</th>
                    <th>Tuition</th>
                </tr>
                <?php
                    $query = "SELECT * FROM might_apply_to WHERE student_id='$user_data[id]' ORDER BY school_state ASC";
                    $result = $con->query($query);
                    while($row = mysqli_fetch_assoc($result))
                    { ?>
                        <tr>
                            <td> <?php echo htmlspecialchars($row['school_state']) ?></td> 
                            <td class="long-data">  <?php echo htmlspecialchars($row['school_name']) ?> </td>
                            <td> <!--Remove school from list-->
                                <form id="delete-school" action="index.php" method="post">
                                    <input type="submit" name="remove" value="Remove">
                                    <input type="hidden" name="nameToRemove" value="<?php echo $row['school_name']?>">
                                    <input type="hidden" name="stateToRemove" value="<?php echo $row['school_state']?>">
                                </form>
                            </td>
                            <td> <!--Display required courses-->
                                <ul class="course-reqs">
                                    <?php $query2 = "SELECT course FROM website_mentions WHERE school_name='$row[school_name]' AND state='$row[school_state]' AND
                                    required_or_recommended='required'";
                                    $result2 = $con->query($query2);
                                    while($row2 = mysqli_fetch_assoc($result2))
                                    { ?>
                                        <li class="course"><?php echo $row2['course']?></li>
                                    <?php } ?>
                                </ul>
                            </td>
                            <td> <!--Display recommended courses -->
                                <ul class="course-reqs">
                                    <?php $query2 = "SELECT course FROM website_mentions WHERE school_name='$row[school_name]' AND state='$row[school_state]' AND
                                    required_or_recommended='recommended'";
                                    $result2 = $con->query($query2);
                                    while($row2 = mysqli_fetch_assoc($result2))
                                    { ?>
                                        <li><?php echo $row2['course']?></li>
                                    <?php } ?>
                                </ul>
                            </td>
                            <td>
                                <ul> <!--Display required courses student hasn't taken-->
                                    <?php $query2 = "SELECT course FROM website_mentions WHERE school_name='$row[school_name]' AND state='$row[school_state]' AND
                                    required_or_recommended='required' AND course NOT IN (SELECT course FROM has_taken WHERE student_id='$user_data[id]');";
                                    $result2 = $con->query($query2);
                                    while($row2 = mysqli_fetch_assoc($result2))
                                    { ?>
                                        <li><?php echo $row2['course']?></li>
                                    <?php } ?>
                                </ul>         
                            </td>
                            <td>
                                <ul> <!--Display recommended courses student hasn't taken-->
                                    <?php $query2 = "SELECT course FROM website_mentions WHERE school_name='$row[school_name]' AND state='$row[school_state]' AND
                                    required_or_recommended='recommended' AND course NOT IN (SELECT course FROM has_taken WHERE student_id='$user_data[id]');";
                                    $result2 = $con->query($query2);
                                    while($row2 = mysqli_fetch_assoc($result2))
                                    { ?>
                                        <li><?php echo $row2['course']?></li>
                                    <?php } ?>
                                </ul>         
                            </td>
                            <td>  <!--Display tuition based on student's residency-->
                                <?php  
                                $resident = $row['school_state']==$user_data['state'];
                                $query2="SELECT tuition,tuition_out_of_state,tuition_and_fees,tuition_and_fees_out_of_state FROM dental_school WHERE name='$row[school_name]' AND state='$row[school_state]'";
                                $result2 = $con->query($query2);
                                while($row2 = mysqli_fetch_assoc($result2))
                                { 
                                    if($resident==true)
                                    {
                                        echo "(Resident)\n";
                                        if($row2['tuition'] != NULL)
                                        {
                                            echo "Tuition: $".$row2['tuition'];
                                        }
                                        else{echo "Tuition & Fees: $".$row2['tuition_and_fees']; }
                                    }
                                    else //not a resident
                                    {
                                        echo "(Non-resident)\n";
                                        if($row2['tuition_out_of_state'] != NULL)
                                        {
                                            echo "Tuition: $".$row2['tuition_out_of_state'];
                                        }
                                        else if($row2['tuition_and_fees_out_of_state'] != NULL)
                                            echo "Tuition & Fees: $".$row2['tuition_and_fees_out_of_state'];
                                        else if($row2['tuition_and_fees'] != NULL)
                                            echo "Tuition & Fees: $".$row2['tuition_and_fees'];
                                        else {echo "Tuition: $".$row2['tuition']; }
                                    }
                                    
                                } ?>
                            </td>
                        </tr>
            <?php   } ?>
            </table><br><br>
    <!---SCHOOL SEARCH FORM--->
    <form id=search-form class=search-form action=index.php method=post>
        <input type="submit" name="clear" value="Clear Filters"><br><br>
        <label for="searchQuery">Search:</label>
        <input type="text" name="searchQuery">
        <input type="submit" name="search" value="Go"><br>
    </form>
    <!---FILTERING OPTIONS-->
    <form id="filters" action=index.php method=post>
        <label for="filters">Filter by:</label>
        <input type="submit" name="filterByQualifying" value="Schools you qualify for">
        <input type="submit" name="filterByList" value="Schools in your list"><br>
    </form>
    <!---SORTING-->
    <form id="sorting" action=index.php method=post style="text-align:center">
        <label for="filters">Sort by:</label>
        <input type="submit" name="sortBySimilarity" value="Most similar class stats">
    </form>

    <!---ALL ENROLLMENT STATS JOIN DENTAL SCHOOL INFO TABLE-->
    <table border=3>
        <tr>
            <th>State</th>
            <th>Name</th>
            <th></th>
            <th>Minimum GPA</th>
            <th>Minimum DAT</th>
            <th>Minimum Shadowing Hours</th>
            <th>Tuition</th>
            <th>Tuition (non-residents)</th>
            <th>Year</th>
            <th>Average GPA</th>
            <th>Average DAT</th>
            <th>Applications</th>
            <th>Class Size</th>
            <th>In State Enrollments</th>
            <th>Out of State Enrollments</th>
            <th>Acceptance Rate</th>
            <?php if(isset($_POST['sortBySimilarity'])){?> <th>Difference Score</th> <?php } ?>
        </tr>
        <?php
            if(isset($_POST['clear']))
                $_POST['searchQuery'] = NULL;
            if(isset($_POST['searchQuery'])) // Apply search query to table join
            {
                $query = 
                "SELECT ds.name,ds.state,min_gpa,min_dat,min_shadowing_hours,tuition,tuition_out_of_state,tuition_and_fees,tuition_and_fees_out_of_state,
                        year,class_size,average_gpa,average_gpa_science,average_dat,applications,in_state_enrollments,out_state_enrollments 
                FROM dental_school AS ds LEFT JOIN enrollment_Statistics AS es ON ds.name=es.name AND ds.state=es.state
                WHERE (ds.name LIKE '%$_POST[searchQuery]%') OR (ds.state LIKE '%$_POST[searchQuery]%') ORDER BY ds.state";
                ?><span><?phpecho "Searching for '".$_POST['searchQuery']."'...";?></span><?php
            }
            else if(isset($_POST['filterByList'])) // Filter by student's saved schools list
            {
                $query = 
                "SELECT ds.name,ds.state,min_gpa,min_dat,min_shadowing_hours,tuition,tuition_out_of_state,tuition_and_fees,tuition_and_fees_out_of_state,
                        year,class_size,average_gpa,average_gpa_science,average_dat,applications,in_state_enrollments,out_state_enrollments 
                FROM dental_school AS ds LEFT JOIN enrollment_Statistics AS es ON ds.name=es.name AND ds.state=es.state 
                WHERE (ds.name,ds.state) IN
                    (SELECT school_name,school_state 
                    FROM might_apply_to
                    WHERE student_id='$user_data[id]') ORDER BY ds.state";
            }
            else if(isset($_POST['filterByQualifying'])) // Filter by minimum gpa, dat, and shadowing hours being met by student
            {
                $query = 
                "SELECT ds.name,ds.state,min_gpa,min_dat,min_shadowing_hours,tuition,tuition_out_of_state,tuition_and_fees,tuition_and_fees_out_of_state,
                        year,class_size,average_gpa,average_gpa_science,average_dat,applications,in_state_enrollments,out_state_enrollments 
                FROM dental_school AS ds LEFT JOIN enrollment_Statistics AS es ON ds.name=es.name AND ds.state=es.state 
                WHERE (min_gpa IS NULL or min_gpa <= $user_data[gpa]) 
                    AND (min_dat IS NULL OR min_Dat<=$user_data[dat]) 
                    AND (min_shadowing_hours IS NULL OR min_shadowing_hours<=$user_data[shadowing_hours]) ORDER BY ds.state";
            }
            else if(isset($_POST['sortBySimilarity'])) // Sort list by most similar class stats (average GPA and DAT) to student
            {
                $query = 
                "SELECT ds.name,ds.state,min_gpa,min_dat,min_shadowing_hours,tuition,tuition_out_of_state,tuition_and_fees,tuition_and_fees_out_of_state,
                    year,class_size,average_gpa,average_gpa_science,average_dat,applications,in_state_enrollments,out_state_enrollments, 
                    (ABS($user_data[gpa]*7.5-average_gpa*7.5) + ABS($user_data[dat] - average_dat)) AS totalDiff 
                FROM dental_school AS ds LEFT JOIN enrollment_Statistics AS es ON ds.name=es.name AND ds.state=es.state 
                    ORDER BY 
                        CASE 
                            WHEN totalDiff IS NULL 
                            THEN 1 
                            ELSE 2 
                        END DESC, totalDiff";
            }
            else // No filters applied
                $query = 
                "SELECT ds.name,ds.state,min_gpa,min_dat,min_shadowing_hours,tuition,tuition_out_of_state,tuition_and_fees,tuition_and_fees_out_of_state,
                    year,class_size,average_gpa,average_gpa_science,average_dat,applications,in_state_enrollments,out_state_enrollments 
                FROM dental_school AS ds LEFT JOIN enrollment_Statistics AS es ON ds.name=es.name AND ds.state=es.state 
                    ORDER BY ds.state";
            $result = $con->query($query); 
            while($row = mysqli_fetch_assoc($result))
            { ?>    <!--If sorting by similarity, grey out schools that can't be scored (NULL GPA or DAT) -->
                <tr <?php if(isset($_POST['sortBySimilarity']) && $row['totalDiff']==NULL) { ?> bgcolor="grey" <?php } ?> >
                    <td>  <?php echo htmlspecialchars($row['state']) ?></td> 
                    <td class="long-data">  <?php echo htmlspecialchars($row['name']) ?> </td>
                    <td> <!-- Add school to student's list -->
                        <form id="add-school" action="index.php" method="post">
                            <input type="submit" name="add" value="Add">
                            <input type="hidden" name="nameToAdd" value="<?php echo $row['name']?>">
                            <input type="hidden" name="stateToAdd" value="<?php echo $row['state']?>">
                        </form>
                    </td>
                    <td>  <?php if($row['min_gpa']!=NULL) echo number_format($row['min_gpa'],2) ?> </td>
                    <td>  <?php echo $row['min_dat'] ?> </td>
                    <td>  <?php echo $row['min_shadowing_hours'] ?> </td>
                    <td>  <?php if($row['tuition_and_fees']!=NULL){echo $row['tuition_and_fees']."*";}
                                else{echo $row['tuition'];} ?> </td>
                    <td>  <?php if($row['tuition_and_fees_out_of_state']!=NULL){echo $row['tuition_and_fees_out_of_state']."*";}
                                else{echo $row['tuition_out_of_state'];}  ?></td>
                    <td>  <?php echo $row['year'] ?> </td>
                    <td>  <?php echo $row['average_gpa'] ?> </td>
                    <td>  <?php echo $row['average_dat'] ?> </td>
                    <td>  <?php echo $row['applications'] ?></td>
                    <td>  <?php echo $row['class_size'] ?> </td>
                    <td>  <?php echo $row['in_state_enrollments'] ?> </td>
                    <td>  <?php echo $row['out_state_enrollments'] ?></td>
                    <td>  <?php if($row['applications'] && $row['class_size']) echo number_format($row['class_size']/$row['applications']*100, 0)."%"; ?></td>
                    <?php   if(isset($_POST['sortBySimilarity'])) // S how similarity score only if filter is selected
                            {?> 
                                <td>
                                    <?php if($row['totalDiff']==NULL) echo "Statistics Not Reported!";
                                    else echo number_format($row['totalDiff'], 2);?> 
                                </td> 
                    <?php   } ?>
                </tr>
        <?php } ?>  
    </table>
    <h4 style="text-align:center">* School website listed Tuition AND Fees together</h4>
    <!--Display average class average for GPA and DAT for all schools-->
    <?php $query = "SELECT AVG(average_gpa),AVG(average_dat) FROM enrollment_statistics";
            $result = $con->query($query);
            $row = mysqli_fetch_assoc($result); ?>
    <table border=1>
        <caption>Averages for all schools</caption>
        <tr>
            <th>Average class GPA</th>
            <th>Average class DAT score</th>
        <tr>
        <tr>
            <td><?php echo number_format($row['AVG(average_gpa)'], 2) ?></td>
            <td><?php echo number_format($row['AVG(average_dat)'], 2) ?></td>
        </tr>
    </table>
</html>

