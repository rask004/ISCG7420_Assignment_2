<?php

/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 17/10/2016
 * Time: 19:24 PM
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once('../Includes/Session.php');
include_once('../Includes/Common.php');


if (!(isset($_SESSION[\Common\Security::$SessionAuthenticationKey]) && $_SESSION[\Common\Security::$SessionAuthenticationKey] == 1
	&& isset($_SESSION[\Common\Security::$SessionAdminCheckKey])))
{
    header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/logout.php");
    exit;
}
?>

<?php
	if (isset($_POST["submit"]) && ($_POST["submit"] == "Delete" || $_POST["submit"] == "Upload"))
	{
		// deleting files
		if ($_POST["submit"] == "Delete")
		{
			if (isset($_POST["file_to_delete"]) && !empty($_POST["file_to_delete"]))
			{
				$filepath = "../" . \Common\Constants::$AdminFileuploadFolder ."/" . $_POST["file_to_delete"];
				// only delete files that actually exist.
				if (file_exists($filepath) && unlink( "../" . \Common\Constants::$AdminFileuploadFolder ."/" . $_POST["file_to_delete"]))
				{
					$file_delete_success = 1;
				}
				else 
				{
					$file_delete_error = 1;
				}
					
			}
		}
		else
		{
			// in case try to upload without file.
			$error_msg = "No file specified";
			
			$safe_to_upload = true;
			
			if(isset($_FILES["file_upload"]) && isset($_FILES["file_upload"]["tmp_name"]))
			{					
				// check for file error.			
				if ($_FILES["file_upload"]["error"] != 0)
				{
					$safe_to_upload = false;
					$error_msg = "Error Number: " . $_FILES["file_upload"]["error"];
				}
				elseif($_FILES["file_upload"]["size"] > 124768)
				{
					$safe_to_upload = false;
					$error_msg = "File too big. Must be under 125KB.";
				}
				$file_type_array = explode("/", $_FILES["file_upload"]["type"]);
				
				if ($file_type_array[0] != "image")
				{
					$safe_to_upload = false;
					$error_msg = "File not identified as an image.";
				}
				elseif(!in_array($file_type_array[1], \Common\Constants::$AdminPermittedFileuploadExtensions))
				{
					$safe_to_upload = false;
					$error_msg = "File must be in JPG or PNG format.";
				}
				
			}
			else
			{
				$safe_to_upload = false;
			}
			
			if ($safe_to_upload)
			{
				$file_parts = explode('.',$_FILES['file_upload']['name']);
				$ext = $file_parts[count($file_parts) - 1];
				$file_upload_name = "../" . \Common\Constants::$AdminFileuploadFolder . "/" . sha1_file($_FILES['file_upload']['tmp_name']) . "." . $ext;
				
				if (!move_uploaded_file($_FILES['file_upload']['tmp_name'],
					$file_upload_name
					))
				{
					$file_upload_error = 1;
					$error_msg = "failed to move tmp file to final location.";
				}
				else
				{
					$file_upload_success = 1;
				}
			}
			else
			{
				$file_upload_error = 1;
			}
		}
	}
?>


<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps - Admin Section</title>
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.structure.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.theme.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript">
		function select_filename(name)
		{
			$("#input_delete_filename_hidden").val(name);
			$("#input_submit_delete").prop("disabled", false);
			var path = "../uploaded_pictures/" + name;
			$("#imgDeletePreview").prop("src", path);
		}
		
		function clear_selected_file()
		{
			$("#input_delete_filename_hidden").val("");
			$("#input_submit_delete").prop("disabled", true);
		}
	</script>
</head>

<body>
    <?php include_once("../Includes/navbar.admin.php"); ?>

    <div class="container-fluid PageContainer" style="overflow-y:scroll;">

        <div class="row">
            <div id="divLeftSidebar" class="col-md-1">
            	
            </div>
            <div id="divCentreSpace" class="col-md-10">

                <form method="post" enctype="multipart/form-data">
                    <div class="container-fluid PageSection">
                        <div class="row">
                        	<div class="col-xs-12 col-sm-7 col-md-7">
                                <div class="container-fluid">
                                    <?php
                                        // get list of files in upload folder.
                                        // display as 2 by x table
                                        // of labels, each selectable
                                        
                                        $contents = scandir( "../" . \Common\Constants::$AdminFileuploadFolder);
                                        foreach ($contents as $key => $value)
                                        {
                                            $file_parts = explode(".", $value);
                                            $extension = $file_parts[count($file_parts) - 1 ];
                                            // remove directory idicators, and non-permitted files.
                                            if ($value == "." || $value == ".." 
                                                || !in_array($extension, \Common\Constants::$AdminPermittedFileuploadExtensions))
                                            {
                                                unset($contents[$key]);
                                            }
                                        }
                                        
                                        $i = 0;
                                        
                                        echo '<div class="row">';
                                        
                                        foreach(array_values($contents) as $name)
                                        {
                                            echo '<div class="col-xs-12 col-sm-6 col-md-6">';
											echo '<input style ="width:100%;white-space: normal;" type="button" onclick="';
											echo "select_filename('". $name ."')";
											echo '" value="';
                                            echo $name;
											echo '"/>';
                                            echo '</div>';	
                                            $i++;	
                                            
                                            if ($i >= 2)
                                            {
                                                echo '</div><div class="row">';
                                                $i = 0;
                                            }
                                        }
                                        
                                        echo '</div>';
                                        
                                        
                                    ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-5 col-md-5">
                            	<div class="row">
                                	
									<div class="col-xs-12 col-sm-12 col-md-12">
                                		<label>file selected to delete:</label>
                                    </div>
                                </div>
                                <div class="row">
                                	
									<div class="col-xs-12 col-sm-12 col-md-12">
                                		<input type="text" style="width:100%" readonly value="" id="input_delete_filename_hidden" name="file_to_delete" />
                                    </div>
                                </div>
                                <br/>
                            	<div class="row">
                                	
									<div class="col-xs-12 col-sm-12 col-md-12">
                                		<input type="submit" disabled id="input_submit_delete" value="Delete" name="submit"  />
                                    </div>
                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                		<input type="button" value="Clear" onclick="clear_selected_file();"  />
                                    </div>

                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                		<img style="width:50%;height:50%" id="imgDeletePreview" src="" alt="image selected to delete.">
                                    </div>

                                </div>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <div class="row">
                        	<div class="col-xs-0 col-sm-3 col-md-3">
                            	
                            </div>
                        	<div class="col-xs-12 col-sm-3 col-md-3">
                            	<input type="File" name="file_upload" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-1">
                            </div>
                            <div class="col-xs-12 col-sm-2 col-md-2">
                            	<input type="submit" value="Upload" name="submit"/>
                            </div>
                            <div class="col-xs-0 col-sm-3 col-md-3">
                            	
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <div class="row">
	                        <div class="col-xs-0 col-sm-2 col-md-2">
                            	
                            </div>
                        	<div class="col-xs-12 col-sm-8 col-md-8" style="background-color:#979797; text-align:center">
                            	<b><label style="font-size:2em" id="InfoMsg" >
                                	    <?php
											if(isset($file_delete_success))
											{
												echo "SUCCESS, file deleted: ". $_POST["file_to_delete"];
											}
											elseif(isset($file_upload_success))
											{
												echo "SUCCESS, file uploaded. ";	
											}
											elseif(isset($file_delete_error))
											{
												echo "ERROR, failed to delete file: ". $_POST["file_to_delete"];
											}
											elseif(isset($file_upload_error))
											{
												echo "ERROR, failed to upload file: ". $error_msg;	
											}
											else
											{
												echo "READY";	
											}
										?>
                                </label></b>
                            </div>
                            <div class="col-xs-0 col-sm-2 col-md-2">
                            	
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div id="divRightsidebar" class="col-md-1">
            </div>
        </div>
    </div>
    <?php
		print_r($_FILES);
	?>
    <br/>
    <br/>

    <?php include_once("../Includes/footer.php"); ?>
</body>
</html>