<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="index.css" />

    <script
      src="https://kit.fontawesome.com/1d72651eac.js"
      crossorigin="anonymous"
    ></script>
    <title>Mail Server</title>
  </head>
  <body>

<?php

  $servername = "localhost:3307";
  $username = "root";
  $password = "";
  $database = "mailserver";

  //creating connection object
  $conn = mysqli_connect($servername, $username, $password, $database);
  if(!$conn) echo "failed to connect with database";

  if(isset($_POST) && !empty($_POST))
  {
    if(!empty($_FILES['attachment']['name']))
    {
      $file_name = $_FILES['attachment']['name'];  
      $temp_name = $_FILES['attachment']['tmp_name'];  
      $file_type = $_FILES['attachment']['type'];
    
      $base = basename($file_name);
      $extension = substr($base, strlen($base)-4, strlen($base));
    
      //only these file types will be allowed
      $allowed_extensions = array(".doc", "docx", ".pdf", ".zip", ".png", ".jpg");
    
      //check that this file type is allowed
      if(in_array($extension,$allowed_extensions))
      {
          //mail essentials
        $to = $_POST['rec_email'];
        $subject = $_POST['subject'];
        $message = $_POST['user_message'];

        $from = "shubhs.2803@gmail.com";
        // $headers = 'From: ' .$from . "\r\n". 
        // 'Reply-To: ' . $from. "\r\n" .
        // 'X-Mailer: PHP/' . phpversion();

        $file = $temp_name;
        $content = chunk_split(base64_encode(file_get_contents($file)));
        $uid = md5(uniqid(time()));  //unique identifier
  
        //standard mail headers
        $header = "From: ".$from."\r\n";
        //$header .= "Reply-To: ".$replyto. "\r\n";
        $header .= "MIME-Version: 1.0\r\n";
  
  
        //declare multiple kinds of email (plain text + attch)
        $header .="Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n";
        $header .="This is a multi-part message in MIME format.\r\n";
  
        //plain txt part
  
        $header .= "--".$uid."\r\n";
        $header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
        $header .= "Content-Transfer-Encoding: 7bit\r\n";
        $header .= $message. "\r\n";
  
  
        //attch part
        $header .= "--".$uid."\r\n";
        $header .= "Content-Type: ".$file_type."; name=\"".$file_name."\"\r\n";
        $header .= "Content-Transfer-Encoding: base64\r\n";
        $header .= "Content-Disposition: attachment; filename=\"".$file_name."\"\r\n";
        $header .= $content."\r\n";  //chucked up 64 encoded attch
  
  
        //sending the mail - message is not here, but in the header in a multi part
  
        if(mail($to, $subject, $message, $header))
        {
          echo "mail sent";
          $sql = "INSERT INTO `sent_emails` (`sno`, `tomail`, `dated`, `subject`) VALUES (NULL, '$to', current_timestamp(), '$subject');";
          $result = mysqli_query($conn, $sql);
        }
        else
        {
          echo "failed";
        }
    
      }
      else
      {
        echo "file type not allowed";
      }    //echo an html file
    }
    else
    {
      $to = $_POST['rec_email'];
      $subject = $_POST['subject'];
      $message = $_POST['user_message'];
  
      $from = "shubhs.2803@gmail.com";
      $headers = 'From: ' .$from . "\r\n". 
      'Reply-To: ' . $from. "\r\n" .
      'X-Mailer: PHP/' . phpversion();
  
      if(mail($to, $subject, $message, $headers))
      {
        echo "mail sent";
        $sql = "INSERT INTO `sent_emails` (`sno`, `tomail`, `dated`, `subject`) VALUES (NULL, '$to', current_timestamp(), '$subject');";
        $result = mysqli_query($conn, $sql);
      }
      else
      {
        echo "failed";
      }
    }    
  }
?>
    <nav class="container-3" id="navlist">
      <ul>    
        <!-- <img src="img/dove.png" alt=""> -->
        <img class="logo1" src="https://cdn-icons-png.flaticon.com/512/471/471368.png" alt="">
        <!-- <li><img class="logo" src="https://cdn-icons-png.flaticon.com/512/471/471283.png" alt="logo"></li> -->
        <li>
          <a href="index.php" target="_self" class="active" style="background-color: rgb(95, 217, 223);">Home</a>
        </li>
        <li>
          <a href="index.html" target="_self" >About</a>
        </li>
        <li>
          <a target="_self" class="btnt">Sent</a>
        </li>
      </ul>
    </nav>

    <div class="container-4" id="title">
      Welcome to COER mail server    
    </div>

    <div><button class="btn-1" id="button" onclick="openform()">Compose</button></div>

    <div class="container-2" value="add child" id="myform">
      <div class="container-1">
        <form id="myForm" method="post" action="" enctype="multipart/form-data" class="for-example">
          <div class="box">

          <div class="box">
            <label for="rec_email">To</label>
            <input
              type="email"
              id="rec_mail"
              name="rec_email"
              placeholder="Enter receiver's email"
              required
            />
          </div>
          <div class="box-1 box">
            <label for="subject"></label>
            <input
              type="text"
              name="subject"
              id="subject"
              placeholder="Subject"
              required
            />
          </div>
          <div>
          <label> 📎<input type="file" name="attachment" /></label>
          </div>
          <div class="box">
            <textarea
              name="user_message"
              id="msg"
              cols="30"
              rows="10"
            ></textarea>
          </div>
          <div>
            <button class="btn" name="Send">Send</button>
            <button class="btn" onclick="closeform()">Close</button>
          </div>
        </form>
      </div>
    </div>

    <script src="index.js"></script>
  </body>
</html>