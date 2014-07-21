<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Shiqianmiao's Home Page!</title>
    <meta name="keywords" content="个人网站,miaoshiqian, shiqianmiao, 缪石乾" />
    <meta name="description" content="缪石乾的个人网站" />
    <?php echo $this->staHtml;?>

    <style type="text/css">
        /*#content_wrapper table {
            border:solid 3px white;
        }*/
        #content_wrapper table tr td {
            color: black;
            text-align: center;
            background-color:#C5CDCB;
        }
        #content_wrapper table tr td.td_value {
            color:yellow;
            background-color: transparent;
        }
    </style>
</head>


<body>

<div id="header">
    <?php $this->load('widget/header.php');?>
</div>

<div id="templatemo_wrapper"> 
    
    <div id="content_wrapper"> 
        <div id="content">

        <h1>weblog 建设中......</h1>

      </div>
        
        <div class="cleaner"></div>
    
    </div>
    
    <?php echo $this->load('widget/footer.php');?>
     
</div> <!-- end of template wrapper -->

<?php echo $this->load('widget/footer.php');?>

</body>
</html>
