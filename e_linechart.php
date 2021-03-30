<!DOCTYPE HTML> 
<html>
<head>
<meta charset="utf-8">
<title>(runoob.com)</title>
</head>
<body> 

<?php
// Define the variable and set to null by default
$city_name = $category_name =  "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $city_name = get_input($_POST["city_name"]);
    $category_name = get_input($_POST["category_name"]);

}

function get_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
?>

<!-- form to get user input -->
<h2>PHP formtest</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
   City Name: <input type="text" name="city_name">
   <br><br>
   Category Name: <input type="text" name="category_name">
   <br><br>
   <input type="submit" name="submit" value="Submit"> 
</form>

<?php
if ($city_name!=null && $category_name!=null){
    echo "<h2>The input content is:</h2>";
    echo $city_name;
    echo $category_name;
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



