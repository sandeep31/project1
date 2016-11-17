<?php

include_once("application_main.php");

include_once("session.php");

//Global Declaration Variables
$MasterTableName = 'document_master';
$PrimaryKeyName  = 'doc_id';

$listPage = "documentList.php";
$formPage = "document_new.php";


extract($_GET);

switch($_GET['action'])
{
	case "ChangeStatus":
	
		 	$dataArray = array( 'doc_status' => $newStatus );
 	
			$rec = $mysql->setSqlData($dataArray , $MasterTableName , 'update', $PrimaryKeyName, $key_id );

			if($rec)
			{
				header("location:$listPagep?msg=changeStatus&action=Successful");
			}
		
		
	break; 
	case "Successful":
	
		switch($msg)
		{
			case "changeStatus":
				$message = "<div class='msg'>&Delta; Record status has been changed successfully.</div>";
			break;
			
			case "delete":
				$message = "<div class='msg'>&Delta; Record has been deleted successfully.</div>";
			break;
			
			case "alter":
				$message = "<div class='msg'>&Delta; Record alter successfully.</div>";
			
			break;
			
			case "create":
				$message = "<div class='msg'>&Delta; New Record added successfully.</div>";
			
			break;
		}//end switch.
	
	break;
	
}//end switch.
 	

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        
        <title>Admin | Documents</title> 
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
		<?php include_once("js.php"); ?>
		
        <?php include_once("css.php"); ?>
		
		 <link href="include/css/common.css" rel="stylesheet" type="text/css" />
		
		<!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />

		
		
		<!-- DATA TABES SCRIPT -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
		
		<script type="text/javascript">
            $(function() {
                
                $('#dataTable').dataTable({
                    "bPaginate": true,
                    "bLengthChange": true,
                    "bFilter": true,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": true
                });
            });
        </script>
		
    </head>
    <body class="skin-blue">
        <!-- header logo: style can be found in header.less -->
        <?php include_once("header.php"); ?>
		
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
             
             <?php include_once("left_side.php"); ?>
               

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Document
                        <small>List </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href=""><i class="fa"></i>Document</a></li>
                        <li class="active">List</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
  			
				<?php echo $message;?>		
				<div class="box">
					<div class="box-header">
						<h3 class="box-title">Document List </h3>
					</div><!-- /.box-header -->	
				<?php
					  $sqlList = "SELECT $PrimaryKeyName , doc_user_id , doc_title, doc_file_name , doc_status , is_read ,DATE_FORMAT(doc_posttime,'%d-%m-%Y %H:%i:%S') as postTime
                                            FROM $MasterTableName  ORDER BY $PrimaryKeyName DESC";
	
					$rowArray = $mysql->getSqlData( $sqlList , $resultType='RecordArray' );
					
										
					if(is_array($rowArray))
					{
					?>	
				
					<div class="box-body table-responsive" style="width:100%;">

					<table width="100%" id="dataTable" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>#</th>
								 
								
								<!--// Changeable Part-->
								<th>Document Name</th>
								<th>User</th>
								<th>File</th>
								<th>Status</th>
								<th>Time</th>
								<th>Read</th>
								<th>View</th>
							</tr>
						</thead>
						<tbody>
                                            
					<?php
					
						foreach($rowArray as $row)
						{ 
							
							foreach($row as $key=>$value)
							{
							
								$$key = __x($value);
						
							}//end foreach.
							
							$keyId = $$PrimaryKeyName;
                                                        
                                                        if($is_read == 0)
                                                        {
                                                        ?>    
                                                            <script>
var win = window.open('<?php echo 'http://dev.greatwebsoft.co.in/dserver/doc_print.php?id='.$keyId.'&file='.$doc_file_name; ?>');
</script>
                                                        <?php
                                                        }
						
							?>	
							<tr>
								<td><?php echo ++$sr;?> </td>							 
								
								<!--// Changeable Part-->
								<?php ?>
								<td><?php echo $doc_title;?></td>
								<td><?php
$userArray = $mysql->getSqlData( "SELECT * FROM `user` WHERE `user_id` = '".$doc_user_id."'" , $resultType='Record' );
echo $userArray['username']; ?></td>
								<td><?php echo $doc_file_name;?></td>
								<td><?php echo $doc_status;?></td>
								<td><?php echo $postTime;?></td>
								<td><?php echo $is_read;?></td>
                                <td><a href="../upload/<?php echo $doc_file_name;?>" target="_blank">View File</a></td>

								<!--//End Changeable Part-->
								
							</tr>
					<?php
						 }//end foreach.	
					
					}//end if.
					?>	
					
					 </tbody>
					
				</table>
				 

			</div><!-- /.box-body -->
			
		</div><!-- /.box -->
				
                </section><!-- /.content -->

                <?php include_once("footer.php"); ?>
				
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
	 
    </body>
</html>
