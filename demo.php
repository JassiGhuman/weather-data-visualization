<!DOCTYPE HTML> 
<html>
<head>
<meta charset="utf-8">
<title>Weather Wizard</title>
<style>
.error {color: #FF0000;}
</style>
</head>
<body> 

<?php
// Define the variable and set to null by default
$nameErr = $emailErr = $genderErr = $websiteErr = "";
$name = $email = $gender = $comment = $website = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if (empty($_POST["name"])) {
      $nameErr = "Name is required";
      } else {
         $name = test_input($_POST["name"]);
         // Checks if the name contains only letters and Spaces
         if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
         $nameErr = "only letters and Spaces are allowed!"; 
         }
     }
   
   if (empty($_POST["email"])) {
      $emailErr = "Email is required";
   } else {
      $email = test_input($_POST["email"]);
      // check email
      if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)) {
         $emailErr = "illegal email address"; 
      }
   }
     
   if (empty($_POST["website"])) {
      $website = "";
   } else {
      $website = test_input($_POST["website"]);
      // check URL address
     if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$website)) {
         $websiteErr = "illegal URL address"; 
      }
   }

   if (empty($_POST["comment"])) {
      $comment = "";
   } else {
      $comment = test_input($_POST["comment"]);
   }

   if (empty($_POST["gender"])) {
      $genderErr = "gender is required";
   } else {
      $gender = test_input($_POST["gender"]);
   }
}

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
?>

<h2>User SignUp</h2>
<p><span class="error">* required</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
   Name: <input type="text" name="name">
   <span class="error">* <?php echo $nameErr;?></span>
   <br><br>
   E-mail: <input type="text" name="email">
   <span class="error">* <?php echo $emailErr;?></span>
   <br><br>
   URL: <input type="text" name="website">
   <span class="error"><?php echo $websiteErr;?></span>
   <br><br>
   Note: <textarea name="comment" rows="5" cols="40"></textarea>
   <br><br>
   gender:
   <input type="radio" name="gender" value="female">F
   <input type="radio" name="gender" value="male">M
   <span class="error">* <?php echo $genderErr;?></span>
   <br><br>
   <input type="submit" name="submit" value="Submit"> 
</form>

<?php
echo "<h2>Your input content:</h2>";
echo $name;
echo "<br>";
echo $email;
echo "<br>";
echo $website;
echo "<br>";
echo $comment;
echo "<br>";
echo $gender;
?>

</body>
</html>