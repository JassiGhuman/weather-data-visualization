<!DOCTYPE HTML> 
<html>
<head>
<meta charset="utf-8">
<title>Weather Wizard</title>
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
</head>
<body> 
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
<?php
// Define the variable and set to null by default
$city_name = $category_name =  "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {   
    $city_name = get_input($_POST["city_name"]);
    $category_name = get_input($_POST["category_name"]);
    //$c1 = get_input($_POST["c1"]);
}

function get_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

$cities=array("Vancouver", "Portland", 'San Francisco', "Seattle", "Los Angeles", "San Diego", "Las Vegas", "Phoenix", "Albuquerque", "Denver", "San Antonio"
, "Dallas", "Houston", "Kansas City", "Minneapolis", "Saint Louis", "Chicago", "Nashville", "Indianapolis", "Atlanta", "Detroit", "Jacksonville", "Charlotte"
, "Miami", "Pittsburgh", "Toronto", "Philadelphia", "New York", "Montreal", "Boston", "Beersheba", "Tel Aviv District", "Eilat", "Haifa", "Nahariyya");

$categories = array ('humidity_per_day' => 'Humidity', 'pressure_per_day' => 'Pressure', 'temperature_per_day' => 'Temperature', 'wind_speed_per_day' => 'Wind Speed');
?>


<div class="container">
<div class="topnav" id="myTopnav">
	<a href=".\index.html" class="active">Home</a>
	<a href=".\e_linechart.php">Visualizations</a>
	<a href="#contact">Contact</a>
	<a href="#about">About</a>
	<a href=".\login.html" class="search-container"><i class="fa fa-fw fa-user"></i> Login</a>
	<a href="javascript:void(0);" class="icon" onclick="myFunction()">
	  <i class="fa fa-bars"></i>
	</a>
</div>
</div>


<!-- form to get user input -->
<h2 style="color:black;"> Weather Wizard Vizualization</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
   City Name: <!--<input type="text" name="c1"> -->
   <select name="city_name" required>
   <option value="">Select City</option>
        <?php
        foreach ($cities as $value) {
        echo "<option value='".$value."'>$value</option>";
        }?>
    </select>

   <br><br>
   Category Name: <!-- <input type="text" name="category_name"> -->
   <select name="category_name" required>
   <option value="" >Select Category</option>
        <?php
        foreach ($categories as $key => $value) {
        echo "<option value=$key>$value</option>";
        }?>
    </select>


   <br><br>
   <input type="submit" name="submit" value="Submit"> 
</form>

<?php
if ($city_name!=null && $category_name!=null){
    echo "<br><b>Selected City: </b>".$city_name."<br>";
    echo "<b>Selected Category: </b>".$category_name."<br><br><br>";
    //echo $c1;
    //echo "<br>";

    //variable to connect database
    $server_name = "localhost";
    $user_name = "root";
    $user_password = "";
    $database_name = "weather data";

    // create connection 
    $con =mysqli_connect($server_name, $user_name, $user_password, $database_name); 
    // test  connection
    if (mysqli_connect_errno()) 
    { 
    echo "Failed to connect to MySQL: " . mysqli_connect_error(); 
    } 
    
    class line_data_element{
        public $name = "";
        public $type = "line";
        public $data = null;
    }

    class full_data{
    public $xAxis = null;
    public $sereis = null;
    }
    
    //$sql = "SELECT date_time,{$city_name} FROM {$category_name}";
    $sql = "SELECT date_time,`".$city_name."` FROM ".$category_name;
    //echo $sql;
    $result = mysqli_query($con,$sql); 

    //x axis
    $date_arr = array();
    //collect city names
    $category_arr = array();
    //
    $data_array = array();
    array_push($category_arr,$city_name);

    while($row = mysqli_fetch_array($result)) { 
    #echo "#### ".$row[$city_name];
    $date_arr[] = $row['date_time'] ;
    $data_array[] = $row[$city_name];
    }

    $line_el = new line_data_element();
    $line_el->data = $data_array;
    $line_el->name = $city_name;

    $pass_data = new full_data();
    $pass_data->xAxis = $date_arr;
    $pass_data->sereis = $line_el;

    // echo $pass_data; 
    $pass_data_json = json_encode($pass_data);

    // //write json file
    file_put_contents('full_data.json', $pass_data_json);

    // end connection
    mysqli_close($con); 
}
?>

<script src="js/echarts.min.js"></script>
<script src="js/jquery-1.11.0.min.js"></script>
<!-- dom for visualization -->
<div id="main" style="width: 80%;height:500px;"></div>

<script type="text/javascript">
        
        var myChart = echarts.init(document.getElementById('main'));

        var chartDom = document.getElementById('main');
        var myChart = echarts.init(chartDom);

        //get data from json file
        $.get('full_data.json').done(function (data) {
                    myChart.setOption({
                        title: {
                            text: "<?php echo $category_name.' Data ' ;?>"
                        },
                        tooltip: {
                            trigger: 'axis'
                        },
                        legend: {
                            data: []
                        },
                        xAxis: {
                            type: 'category',
                            data: data.xAxis
                        },
                        yAxis: {
                            type: 'value'
                        },
                    
                        series: data.sereis,

                        grid: {
                            left: '3%',
                            right: '4%',
                            bottom: '3%',
                            containLabel: true
                        },
                        toolbox: {
                            feature: {
                                saveAsImage: {}
                            }
                        },
                    });
             });
    
             
    </script>

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

</body>
</html>
