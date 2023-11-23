<?php
include 'connect.php';
session_start();

if (isset($_POST['updateid'])) {
    $id = $_POST['updateid']; // You need to retrieve the 'id' from the POST data.
    $title = mysqli_real_escape_string($con, $_POST['Title']);
    $date = mysqli_real_escape_string($con, $_POST['Date']);
    $content = mysqli_real_escape_string($con, $_POST['Content']);
    $publish = isset($_POST['diary_Publish']) ? 1 : 0; // Use 1 for Yes, 0 for No (assuming it's a boolean field).

    $sql = "UPDATE `newdiary` SET Title='$title', Date='$date', Content='$content', Publish='$publish' WHERE id='$id' AND UserID='" . $_SESSION['UserID'] . "'";
    $result = mysqli_query($con, $sql);
    
    if ($result) {
        header("Location: landingpage.php");
        exit; // Add an exit to stop execution after the redirect.
    } else {
        die(mysqli_error($con));
    }
}
?>

<html lang="en">
<head>
<title>Landing Page</title>
<link rel="stylesheet" href="style1.css">
<link href='https://fonts.googleapis.com/css?family=DM Sans'>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<body>
<section>
  <header class="header">
    <li><div class="headertext">My Diary Online</div>
    <li><div class= "logoutButton"><form method="post" action="login.php">
			       <button name="submit" class= "logout" style="text-decoration: none; color: black" value="logout">Logout
			         <img src="logout.svg" style="vertical-align: middle; padding-left: 5px; height: 50px;">
			       </button>
			    </form>
  </header>
  <nav>
    <ul>
    <div class="navbarcontainer">
    <div style="background-size: cover; height:480px; padding-top:-5px; text-align: center;">
    <img src="male-avatar.jpg" style="height:150px; border-radius: 50%; border: 10px solid #FEDE00;">
    <li><div class= "navbarUsername"><?php  echo $_SESSION['Username']; ?></a></div></li>
      <li><div class= "navbarbutton"><a href="landingpage.php" style="text-decoration: none; color: white;">My Diary</a></div></li>
      <li><div class= "navbarbutton"><a href="public.php" style="text-decoration: none; color: white;">Public Diary</a></div></li>
      <li><div class= "button buttonRound"><a href="AddNew.php" style="text-decoration: none; color: white;">+ Add New</a></div></li>
    </div>
    </ul>
  </div>
  </nav>
  </nav>
  <article>
    <div class="articleContainer">
        <div class ="diary_New">
     
          <div class="diary_Container2 diary_Round">
		      <h2><center> <b> Edit Diary <b> </center></h2> <br>
			   <p> Please fill this form! </p> </br>
		   <?php 
				if (isset($_GET['ID'])) {
				$id = $_GET['ID']; // Corrected to use $_GET
				$sql = "SELECT `Title`, `Date`, `Content`, `Publish` FROM `newdiary` WHERE id = $id AND `UserID` = '" . $_SESSION['UserID'] . "'";
				$result = mysqli_query($con, $sql);
				if ($result->num_rows) {
					$row = mysqli_fetch_assoc($result);
					$title = $row['Title'];
					$date = $row['Date'];
					$content = $row['Content'];
					$diaryPublish = $row['Publish'];

                echo '<form method="post" action="edit.php">
                            <label for="title"><b>Title:</b></label><br>
                            <input type="text" id="title" class="date" name="Title" value='.$title.'><br>
                            <label for="date">Date:</label><br>
                            <input type="date" id="date" class= "date" name="Date" value=' . $date . '><br>
                            <label for="diary_Content">Contents:</label><br>
                            <textarea id="diary_Content" class="content" name="Content">' . $content . '></textarea><br><br>
                            <p>Public the diary?</p>
                            <input type="radio" id="diary-publish-yes" name="diary_Publish" value="1" ' . ($diaryPublish == 1 ? 'checked' : '') . '>
                            <label for="diary-publish-yes">Yes</label>
                            <input type="radio" id="diary-publish-no" name="diary_Publish" value="0" ' . ($diaryPublish == 0 ? 'checked' : '') . '>
                            <label for="diary-publish-no">No</label>
                            <br>
                            <input type="hidden" name="updateid" value="' . $id . '">
                            <button type="submit" class="btn-submit"> Submit</button>
                            <button class="btn-back"><a href="landingpage.php" class="text-light">Back</a></button><br>
                        </form>';
              }
            }
         ?>
      </div>
    </div>
  </div>
         
      </article>
      </section>
  </body>
</html>

<!-- Private Diary Page -->
<h2>My Diary</h2>
<ul>
  <li>
    <span>Day and Date</span>
    <span>"Cuplikan Diary / Paragraph Awal"</span>
    <button class="edit-button" onclick="editDiaryEntry()"><a href=update.html> </a>Edit</button>
    <button class="remove-button" onclick="showRemoveConfirmation()">Remove</button>
  </li>
  <!-- Repeat for each diary entry -->
</ul>

<script>
function editDiaryEntry() {
  // Redirect to the edit page
  <a href="update.html">Update your diary</a>
}

function saveDiaryEntry() {
  window.location.href = "/private-diary";
}
</script>