<?php
header('Content-type : text/html; Charset=UTF-8');
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "db";
$conn = new mysqli($servername, $username, $password,$dbname);
if(!$conn){
        echo "???";
}
$conn->query("SET NAMES 'utf8'");
$page = isset($_GET['page'])?$_GET['page']:1;
$limit = 10;
$search = isset($_GET['name'])?$_GET['name']: '';
$size = isset($_GET['size'])?$_GET['size']: 524288000;
$sql = "SELECT * FROM info WHERE name like '%".  $search ."%' AND length > ".$size." ORDER BY freq DESC  LIMIT ".$limit." OFFSET ".$limit*($page -1);
$result = $conn->query($sql);
//echo $sql;
$sql2 ="select count(id) as count  from info where id > 0";
$count = $conn->query($sql2);

function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 

    // Uncomment one of the following alternatives
    // $bytes /= pow(1024, $pow);
     $bytes /= (1 << (10 * $pow)); 

    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>WOW</title>
<!-- 此文件为了显示Demo样式，项目中不需要引入 -->


  <link href="http://g.alicdn.com/bui/bui/1.1.21/css/bs3/dpl.css" rel="stylesheet">
  <link href="http://g.alicdn.com/bui/bui/1.1.21/css/bs3/bui.css" rel="stylesheet">
<style>
        .demo-content{
                margin: 1em auto;
		width: calc(100% - 100px);
    		margin: 1em 0;
	    	height: 30px;
	    	line-height: 30px;
        }
        .search-form .name{
		width: 100%;
		margin: 1em 0;
		background-color: white;
		background-image: url('/img/logo.jpg');
		width: 40px;
		height: 40px;
		background-size: contain;
		background-repeat: no-repeat;
		background-position: center;
        }
        body{
                font-size: 1.2em;
                line-height: 1.4;
        }
        .suggest{
			margin-right: 1em;
			margin-bottom:1em;
			display:inline-block;
        }
		.submit{
			padding: 0 20px;
			line-height: 2;
			margin: 1em 0;
		}
		.radio-box label{
    cursor: pointer;
		}	
.radio {
  margin: 0.5rem;
}
.radio input[type="radio"] {
  position: absolute;
  opacity: 0;
}
.radio input[type="radio"] + .radio-label:before {
  content: '';
  background: #f4f4f4;
  border-radius: 100%;
  border: 1px solid #b4b4b4;
  display: inline-block;
  width: 1.4em;
  height: 1.4em;
  position: relative;
  top: -0.2em;
  margin-right: 1em;
  vertical-align: top;
  cursor: pointer;
  text-align: center;
  -webkit-transition: all 250ms ease;
  transition: all 250ms ease;
}
.radio input[type="radio"]:checked + .radio-label:before {
  background-color: #48C9B0;
  box-shadow: inset 0 0 0 4px #f4f4f4;
}
.radio input[type="radio"]:focus + .radio-label:before {
  outline: none;
  border-color: #48C9B0;
}
.radio input[type="radio"]:disabled + .radio-label:before {
  box-shadow: inset 0 0 0 4px #f4f4f4;
  border-color: #b4b4b4;
  background: #b4b4b4;
}
.radio input[type="radio"] + .radio-label:empty:before {
  margin-right: 0;
}
.logo-box .logo{
	margin: 1em auto;
	display: block;
	width: 100px;
}
</style>
</head>
<body>
  <div class="demo-content">

<!-- 简单搜索页 ================================================== -->
    <div class="row">
      <div>
	<div class="logo-box">
		<img src="/img/logo.jpg" class="logo" />
	 </div>
        <form class="search-form" action="index.php" method="get">
			<div>
			<input class="name" type="text" name="name" placeholder="<?php echo $search; ?>" value="<?php echo $search; ?>">
			</div>
			<div class="radio-box">
                        <div class="radio">  
			<input id="size0" type="radio" name="size" value="0" />
			<label for="size0" class="radio-label">0</label>
			</div>
			<div class="radio">	
			<input id="size1" type="radio" name="size" value="314572800" />
			<label for="size1" class="radio-label">300M</label>
			 </div>
                        <div class="radio">
			<input id="size2" type="radio" checked="checked" name="size" value="524288000" />
			<label for="size2" class="radio-label">500M</label>
			</div>
                        <div class="radio">
			<input id="size3" type="radio" name="size" value="1073741824" />
			<label for="size3" class="radio-label">1G</label>
			</div>
			</div>
			<div>
				<input class="submit" type="submit" value="搜索">
			</div>
        </form>
        <p style="margin: 1em 0;">现在已经有<?php if($count->num_rows> 0){$total = $count->fetch_row();echo $total[0];}else{echo 0;} ?>个热乎乎的种子</p>
        <a class="suggest" href="index.php?name=<?php echo date('Y年m月合集'); ?>"><?php echo date('Y年m月合集'); ?></a>
        <br>
        <table cellspacing="0" class="table table-bordered">
          <thead>
            <tr>
              <th>名字</th>
              <th>文件数量</th>
              <th>大小</th>
              <th>时间</th>
              <th>freq</th>
            </tr>
          </thead>
          <tbody>
                  <?php
                  if ($result->num_rows > 0) {
                        // 输出数据
                        while($row = $result->fetch_assoc()) {
                                echo
                                "<tr>  <td> <a href='magnet:?xt=urn:btih:".$row['infohash']."'>" . $row["name"]. "</a></td>
                                <td>".$row['filenum']."</td>".
                                "<td>".formatBytes($row['length'])."</td>".
                                "<td>".$row['updatetime']."</td>".
                                "<td>".$row['freq']."</td>"
                                ;
                        }
                        } else {
                                echo "<td colspan='5'>0 结果, 还需要爬一爬</td>";
                }
                $conn->close();
                  ?>

          </tbody>
        </table>
        <div>
<div class="pagination pull-right">
            <ul>
			  <?php 
			  if($page >1){
				  echo "
				  <li><a href='index.php?page=1&name=".$search."&size=".$size."'>首页</a></li>
				  ";
			  }
			  ?>
              <li><a href="index.php?page=<?php echo $page<1?1:$page-1;?>&name=<?php echo $search; ?>&size=<?php echo $size; ?>">« 上一页</a></li>
              <li><a href="#"><?php echo $page?></a></li>
              <li><a href="index.php?page=<?php echo $page+1;?>&name=<?php echo $search; ?>&size=<?php echo $size; ?>">下一页 »</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
<!-- script end -->
  </div>
</body>
</html>

