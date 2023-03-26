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

            <p> We have received a password reset request for the Excelsportdata account associated with <?php echo $user_email;?>. No changes have been made yet.</p>
            <p>You can reset your password by clicking on the link below :</p>
                        
            <p> <a style="color: #e6e6e6; background-color: #009245;  font-size: 14pt;  font-family: var(--first_font_family);  padding: 5px 20px;" href="<?php echo $reset_link;?>">Create New Password</a> </p>

            <p>If you have not requested a new password, please let us know immediately by simply replying to this email.</p>
            
            <p>--- The Excel Sport Data team</p>

    </div>

</body>
</html>