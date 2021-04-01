<html>
    <head>    
        <title>Weather Wizard</title>
        <meta charset="utf-8">
        
        <!-- Bootstrap and Font Awesome css-->
        <link rel="stylesheet" href="css/font-awesome.css">
        <!-- <link rel="stylesheet" href="css/bootstrap.min.css"> -->
        <!-- Google fonts-->
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Pacifico">
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700">
        <!-- Theme stylesheet-->
        <link rel="stylesheet" href="css/style.default.css" id="theme-stylesheet">
        <!-- Style sheet for Navbar-->
        <link rel="stylesheet" href="css/navbar.css">

        <!-- FusionCharts Library -->
        <script type="text/javascript" src="//cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
        <script type="text/javascript" src="//cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
        <!--
            <script type="text/javascript" src="//cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.gammel.js"></script>
            <script type="text/javascript" src="//cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.zune.js"></script>
            <script type="text/javascript" src="//cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.carbon.js"></script>
            <script type="text/javascript" src="//cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.ocean.js"></script>
        -->
    </head>

    <body>

<?php

    // Define the variable and set to null by default
    $selectedDate = $cat_name =  "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {   
        $selectedDate = get_input($_POST["selectedDate"]);
        $cat_name = get_input($_POST["cat_name"]);
    }

    function get_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
    }
    $categories = array ('humidity_per_day' => 'Humidity', 'pressure_per_day' => 'Pressure', 'temperature_per_day' => 'Temperature', 'wind_speed_per_day' => 'Wind Speed');
?>


<div class="container">
    <div class="topnav" id="myTopnav">
        <a href=".\dashboard.html" class="active">Home</a>
        <a href=".\e_linechart.php">Line Chart</a>
        <a href=".\barchart.php">Bar Chart</a>
        <a href="#">Contact</a>
        <a href="#">About</a>
        <a href=".\index.html" class="search-container"><i class="fa fa-fw fa-user"></i> Login</a>
        <a href="javascript:void(0);" class="icon" onclick="myFunction()">
            <i class="fa fa-bars"></i>
        </a>
    </div>
</div>

<h2 style="color:black;"> Weather Wizard Bar Chart Vizualization for Specific Date</h2>


<!-- form to get user input -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
   
    <label for="selectedDate">Date:</label>
    <input type="date" id="selectedDate" name="selectedDate">

    <br>
    <div class="danger">
        <p><strong>Note!</strong> Please enter date between 1-OCT-2012  to  28-OCT-2017 only  </p>
    </div>
    <br>

    <label for="cat_name">Category Name:</label>
    <select name="cat_name" required>
        <option value="" >Select Category</option>
        <?php
            foreach ($categories as $key => $value) {
            echo "<option value=$key>$value</option>";
            }
        ?>
    </select>
   <br><br>

   <input type="submit" name="submit" value="Submit"> 
</form>


<?php
    /* Include the `../src/fusioncharts.php` file that contains functions to embed the charts.*/
    include("fusioncharts.php");
    header('Content-type: text/html');

    //Variable to connect to database
    $server_name = "localhost";
    $user_name = "root";
    $user_password = "";
    $database_name = "weather data";


    //create connection 
    $con =mysqli_connect($server_name, $user_name, $user_password, $database_name); 
    
    //test  connection
    if (mysqli_connect_errno()){
    echo "Failed to connect to MySQL: " . mysqli_connect_error(); 
    } 
    
    //SQL Query
    //$sql = "SELECT * FROM users where username = '". $_GET["user"] ."'  && password = '".$_GET["password"]."'";
    //$sql = "SELECT * FROM temperature_per_day where date_time = '2012-10-03'";
    if($selectedDate==null){
        $sql = "SELECT * FROM temperature_per_day where date_time = '2012-10-03'";
        $selectedDate = '2012-10-03';
    }
    else
        $sql = "SELECT * FROM `".$cat_name."` where date_time = '".$selectedDate."'";

    //echo $sql."<br>"; 
    /*
    // Query to get columns from table
    $query = $con->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'weather data' AND TABLE_NAME = 'temperature_per_day'");
    while($row1 = $query->fetch_assoc()){
        $result1[] = $row1;
        //echo $row1['COLUMN_NAME'];
    }
    // Array of all column names
    $columnArr = array_column($result1, 'COLUMN_NAME');
    */

    $result = mysqli_query($con,$sql); 

    $data_array = array();
    $col_value = array();
    
    if($result -> num_rows > 0) {

        while($row = $result->fetch_assoc()){
            $data_array = $row;
        }
    
        array_shift($data_array);  // Remove 'date_time' column from array
    
        $finalData = array();
        foreach ($data_array as $key => $value) {
           //echo "@@".$key."---------".$value."<br>";
           array_push($finalData,array($key , $value));
        }
    }else{
        echo "Data Not Found";
    }

    //end connection
    mysqli_close($con); 
?>

<?php
$category = ucwords(substr($cat_name, 0, strpos($cat_name, "_")));
if($category === "Temperature")
    {$unit = "Kelvin";}   
elseif($category === "Pressure")
    {$unit = "Pascal";}
elseif($category === "Wind"){
    $unit = "mph";
    $category = "Wind Speed";
}
else{
    $unit = "Percentage";
}
        $arrChartConfig = array(
          "chart" => array(
            "caption" => $category." Vs Cities Plot",
            "subCaption" => "Date:".$selectedDate, 
            "xAxisName" => "Cities",
            "yAxisName" => $category." (in ".$unit.")", 
            "numberSuffix" => "", 
            "theme" => "fusion"
            )
        );
/*
      // An array of hash objects which stores data
      $arrChartData = array(
        ["Venezuela", "290"],
        ["Saudi", "260"],
        ["Canada", "180"],
        ["Iran", "140"],
        ["Russia", "115"],
        ["UAE", "100"],
        ["US", "30"],
        ["China", "30"]
      );
*/
      $arrLabelValueData = array();
/*
    // Pushing labels and values
    for($i = 0; $i < count($arrChartData); $i++) {
        //echo $arrChartData[$i][0];
        array_push($arrLabelValueData, array(
            "label" => $arrChartData[$i][0], "value" => $arrChartData[$i][1]
        ));
    }
*/

// Pushing labels and values
for($i = 0; $i < count($finalData); $i++) {
    //echo $finalData[$i];
    array_push($arrLabelValueData, array("label" => $finalData[$i][0], "value" => $finalData[$i][1]));
}

$arrChartConfig["data"] = $arrLabelValueData;

// JSON Encode the data to retrieve the string containing the JSON representation of the data in the array.
$jsonEncodedData = json_encode($arrChartConfig);

// chart object
$Chart = new FusionCharts("column2d", "MyFirstChart" , "800", "500", "chart-container", "json", $jsonEncodedData);

// Render the chart
$Chart->render();

?>



<?php
//if ($selectedDate!=null && $cat_name!=null){
    echo "<br><b>Selected Date: </b>".$selectedDate."<br>";
    echo "<b>Selected Category: </b>".$category."<br><br><br>";
//}
?>


<div id="chart-container">Chart will render here!</div>
<br/>
<br/>

<div class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p>Copyright &copy; 2021. All rights reserved. &copy;<a target="_blank" href="#">Yilin Wang , Jaskirat Singh;</a></p>
            </div>
            <div class="col-md-6">
                <p class="credit"></p>
            </div>
        </div>
    </div>
</div>
<script>
        function myFunction() {
            var x = document.getElementById("myTopnav");
            if (x.className === "topnav") {
                x.className += " responsive";
            } else {
                x.className = "topnav";
            }
        }
    </script>
</body>

</html>