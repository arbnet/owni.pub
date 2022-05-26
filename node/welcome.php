<?php $_SESSION['ldt']=time();?>
<!doctype html>
<html>
 <head>
  <title>Welcome</title>
  <meta name="yandex-verification" content="bf5dcbeecdff7c63"/>
  <meta http-equiv=refresh content="1;URL=?SID=<?=session_id();?>">
 </head>
<body>
<script>
    document.cookie='ldt=<?=$_SESSION['ldt'];?>';
</script>
</body>
</html>