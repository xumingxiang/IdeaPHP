<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>demo</title>
<script src="/PHPMVC3/Scripts/jquery-1.4.4.min.js" type="text/javascript"></script>
</head>

<body>
<h1><?php echo $viewData['title'];?></h1>
<?php 
foreach ($viewData['list'] as $item)
{
echo $item.'<br>';
}
?>
</body>
</html>