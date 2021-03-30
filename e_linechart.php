<!DOCTYPE HTML> 
<html>
<head>
<meta charset="utf-8">
<title>Weather Wizard</title>
</head>
<body> 

<?php
// Define the variable and set to null by default
$city_name = $category_name =  "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {   
    $city_name = get_input($_POST["city_name"]);
    $category_name = get_input($_POST["category_name"]);
    $c1 = get_input($_POST["c1"]);
}

function get_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
?>




<?php

function dynamic_select($the_array, $element_name, $label = '', $init_value = '') {
    $menu = '';
    if ($label != '') $menu .= '
    	<label for="'.$element_name.'">'.$label.'</label>';
    $menu .= '
    	<select name="'.$element_name.'" id="'.$element_name.'">';
    if (empty($_REQUEST[$element_name])) {
        $curr_val = $init_value;
    } else {
        $curr_val = $_REQUEST[$element_name];
    }
    foreach ($the_array as $key => $value) {
        $menu .= '
			<option value="'.$key.'"';
        if ($key == $curr_val) $menu .= ' selected="selected"';
        $menu .= '>'.$value.'</option>';
    }
    $menu .= '
    	</select>';
    return $menu;
}




$cities=array("Vancouver", "Portland", 'San Francisco', "Seattle", "Los Angeles", "San Diego", "Las Vegas", "Phoenix", "Albuquerque", "Denver", "San Antonio"
, "Dallas", "Houston", "Kansas City", "Minneapolis", "Saint Louis", "Chicago", "Nashville", "Indianapolis", "Atlanta", "Detroit", "Jacksonville", "Charlotte"
, "Miami", "Pittsburgh", "Toronto", "Philadelphia", "New York", "Montreal", "Boston", "Beersheba", "Tel Aviv District", "Eilat", "Haifa", "Nahariyya");

$categories = array ('humidity_per_day' => 'Humidity', 'pressure_per_day' => 'Pressure', 'temperature_per_day' => 'Temperature', 'wind_speed_per_day' => 'Wind Speed');

$month = array (1=>"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");  
$colors = array("red", "green", "blue", "yellow");
$curr_month = date("m"); 
//echo dynamic_select($month, 'month', 'Select a month', $curr_month);
//echo dynamic_select($categories, '$category1', 'Choose Category', 'pressure_per_day');
?>





<!-- form to get user input -->
<h2>Weather Wizard Vizualization</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
   City Name: <input type="text" name="city_name">
   <select name="c1" required>
   <option value="SanDiego">San Diego</option>
        <?php
        foreach ($cities as $value) {
        echo "<option value={$value}>$value</option>";
        }?>
    </select>

   <br><br>
   Category Name: <!-- <input type="text" name="category_name"> -->
   <select name="category_name" required>
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
    echo "<h2>Selected City: ";
    echo $city_name;
    echo " & Category: ";
    echo $category_name;
    echo "</h2>";
    echo $c1;
    //$("#err").text("Login Failed")
    echo "<br>";

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
    $sql = "SELECT date_time,{$city_name} FROM {$category_name}"; 
    $result = mysqli_query($con,$sql); 

    //x axis
    $date_arr = array();
    //collect city names
    $category_arr = array();
    //
    $data_array = array();
    array_push($category_arr,$city_name);

    while($row = mysqli_fetch_array($result)) { 
    
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
<div id="main" style="width: 1200px;height:800px;"></div>

<script type="text/javascript">
        
        var myChart = echarts.init(document.getElementById('main'));

        var chartDom = document.getElementById('main');
        var myChart = echarts.init(chartDom);

        //get data from json file
        $.get('full_data.json').done(function (data) {
                    myChart.setOption({
                        title: {
                            text: 'weather data'
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

</body>
</html>



