
<?php
include_once('application_main.php');
$id = $_GET['id'];
$file = $_GET['file'];

$dataArray = array( 'is_read' => 1 );
				 
$rec = $mysql->setSqlData($dataArray , 'document_master' , 'update', $id, 'doc_id' );

 $sql = "UPDATE document_master SET is_read = '1' WHERE  doc_id = '$id' ";
 
 $rec = $objDB->query($sql);


$sql = "SELECT *
            FROM `document_master`
            WHERE `doc_id` = '$id'";
    
    $rowArray = $mysql->getSqlData( $sql , $resultType='Record' );

$userArray = $mysql->getSqlData( "SELECT * FROM `user` WHERE `user_id` = '".$rowArray['doc_user_id']."'" , $resultType='Record' );

 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8"> 
<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
  </head>
  <body>
      <h3 style="margin:0; padding:0;"><?php echo $rowArray['doc_title']; ?> by <?php echo $userArray['username']; ?></h3>
      <img  src="http://dev.greatwebsoft.co.in/dserver/upload/<?php echo $file;?>" alt='image' width="80%" />
      <script>
print();    
window.onafterprint = function(){
    var newWindow = window.open('', '_self', ''); //open the current window
    
    newWindow.close();

}
jQuery( document ).ready(function(){
//    alert(this.window);
    var newWindow = window.open('', '_self', ''); //open the current window
    
    //newWindow.close();
    //document.window.close();
})
//alert(document.window.close());
</script>
  </body>
  
</html>
 