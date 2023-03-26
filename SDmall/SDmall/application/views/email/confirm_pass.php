<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
</head>
<body>

    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation" style="padding: 8px;">

        <span style="font-size: 20px;font-weight: 600;">--excelsportdata.com--</span>

    </nav>

    <div class="container" style="margin-left: 5%; margin-top: 5%;">
        <p style="font-weight: 600; font-size: 18px;">Hi, <?php echo $username;?>.</p>

            <p> At your request, your Excelsportdata password for the  <?php echo $user_email;?> account has been successfully changed.</p>
            <p>New Password: <?php echo $new_password;?></p>
            <p>You can log in with your username and new password by clicking on the link below:</p>
                        
            <p> <a style="color: #e6e6e6; background-color: #009245;  font-size: 14pt;  font-family: var(--first_font_family);  padding: 5px 20px;" href="<?php echo $login_link;?>">Login To My Account</a> </p>

            <p>If you have not requested a new password, please let us know immediately by simply replying to this email.</p>
            
            <p>--- The Excel Sport Data team</p>

    </div>

</body>
</html>