<?php
function validate($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function db_connect(){
    $servername = "localhost";
    $db_username = "id21560512_root";
    $password = "Mydiaryonline2.";
    $database = "id21560512_mydiary";
    
    $conn = new mysqli($servername, $db_username, $password, $database);

    // Check connection
    if ($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

function login(){
    if (isset($_POST['Username']) && isset($_POST['Password'])) {
        $Username = validate($_POST['Username']);
        $Pass = validate($_POST['Password']);

        if (empty($Username) || empty($Pass)){
            header("Location: index.php?error=Username and Password are required");
            exit();
        } else {
            $conn = db_connect();

            $stmt = $conn->prepare("SELECT `ID`, `Username`, `Password` FROM `user` WHERE `Username` = ?");
            $stmt->bind_param("s", $Username);
            $stmt->execute();
             $stmt->bind_result($UserID, $FetchedUsername, $HashedPassword);
            $stmt->fetch();
            $stmt->close();

            $conn->close();

            if ($FetchedUsername === $Username && password_verify($Pass, $HashedPassword)){
                session_start();
                $_SESSION['UserID'] = $UserID;
                $_SESSION['Username'] = $Username;
                header("Location: landingpage.php");
                exit();
            } else {
                header("Location: index.php?error=Username or Password is wrong");
                exit();
            }
        }
    } else {
        header("Location: index.php");
        exit();
    }
}


function signup(){
    $Username = validate($_POST["Username"]);
    $Pass = password_hash(validate($_POST["Password"]), PASSWORD_BCRYPT);
    $Question = validate($_POST["Question"]);
    $Answer = validate($_POST["Answer"]);

    $conn = db_connect();

    $stmt = $conn->prepare("SELECT `Username` FROM `user` WHERE `Username` = ?");
    $stmt->bind_param("s", $Username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        $conn->close();
        header("Location: register.php?error=Username already taken");
        exit();
    }

    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO `user` (`Username`, `Password`, `Secretquestion`, `Answer`) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $Username, $Pass, $Question, $Answer);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: index.php?success=New User Successfully Added");
    exit();
}

function CheckUserExist(){
    $Username = validate($_POST["Username"]);
    $Pass = validate($_POST["Password"]);
    $Confirm = validate($_POST["Confirm"]);
    $conn = db_connect();
    
    $query = "SELECT 'Username' FROM `user` WHERE `Username`='".$Username."'";
    if($conn->query($query)->num_rows){
        // User exists
        $conn->close();
        header("Location: register.php?error=Username already taken");
        exit();
    } else {
        $conn->close();
        
        // Username does not exist
        echo '<form action="secretq.php" method="post">';
        echo '<input type="hidden" name="Username" value='.$Username.'>';
        echo '<input type="hidden" name="Password" value='.$Pass.'>';
        echo '<input type="hidden" name="Confirm" value='.$Confirm.'>';
        echo '</form>';
        echo '<script>document.forms[0].submit();</script>';
        exit();
    }
}

function CheckUsername() {
    if (isset($_POST['Username'])) {
        $Username = validate($_POST['Username']);

        $servername = "localhost";
        $db_username = "id21560512_root";
        $password = "Mydiaryonline2.";
        $database = "id21560512_mydiary";

        $conn = new mysqli($servername, $db_username, $password, $database);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $query = "SELECT `Username` FROM `user` WHERE `Username` = '$Username'";
        $result = $conn->query($query);

        if ($result->num_rows) {
            // Username exists, redirect to the reset password page
            echo '<form action="resetpass2.php" method="post">';
            echo '<input type="hidden" name="Username" value="' . $Username . '">';
            echo '</form>';
            echo '<script>document.forms[0].submit();</script>';
            exit();
        } else {
            // Username does not exist, redirect to a page with an error message
            echo '<form action="resetpass1.php" method="post">';
            echo '<input type="hidden" name="error" value="Username not found">';
            echo '</form>';
            echo '<script>document.forms[0].submit();</script>';
            exit();
        }
    }
}

function CheckSecretquestion(){
	 if (isset($_POST['Username']) && isset($_POST['Answer']) ) {
        $Username = validate($_POST['Username']);
		$Answer = validate($_POST['Answer']);
		

        $conn = db_connect();

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $query = "SELECT `Username` FROM `user` WHERE `Username` = '$Username' AND `Answer`= '$Answer'";
        $result = $conn->query($query);
		$conn->close();
		
		if ($result->num_rows) {
            // Answer correct
            echo '<form action="resetpass3.php" method="post">';
            echo '<input type="hidden" name="Username" value="' . $Username . '">';
            echo '</form>';
            echo '<script>document.forms[0].submit();</script>';
            exit();
        } else {
            //  
			echo '<form action="resetpass2.php" method="post">';
            echo '<input type="hidden" name="Username" value="' . $Username . '">';
            echo '<input type="hidden" name="error" value="Wrong Answer">';
            echo '</form>';
            echo '<script>document.forms[0].submit();</script>';
            exit();
        }
	 }
}



function UpdatePassword(){
    if (isset($_POST['Username']) && isset($_POST['NewPassword']) && isset($_POST['ConfirmPassword'])) {
        $Username = validate($_POST['Username']);
        $NewPassword = validate($_POST['NewPassword']);
        $HashedPassword = password_hash($NewPassword, PASSWORD_BCRYPT);

        $conn = db_connect();

        if ($conn->connect_error){
            die("Connection failed: " . $conn->connect_error);
        }

        $query = "UPDATE `user` SET `Password` = '$HashedPassword' WHERE `Username`= '".$Username."'";
        $result = $conn->query($query);

        $conn->close();

        if ($result){
            header("Location: index.php?success=Password updated successfully");
            exit();
        } else {
            header("Location: resetpass3.php?error=Connection Error");
            exit();
        }
    }
}



function LogOut(){
	$_SESSION =array();
	header("Location: index.php");
    exit();
}

if ($_POST['submit'] == "Login"){
	login();
}
else if ($_POST['submit'] == "Sign Up"){
	signup();
}
else if ($_POST['submit'] == "resetpass1"){
	CheckUsername();
}else if ($_POST['submit'] == "resetpass2"){
	CheckSecretquestion();
}else if ($_POST['submit'] == "Update"){
	UpdatePassword();
}else if ($_POST['submit'] == "logout"){
	LogOut();
}else if ($_POST['submit'] == "register"){
	CheckUserExist();
}
?>