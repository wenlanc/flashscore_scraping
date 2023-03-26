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

            <p> Thank you for your purchase, your order has been processed and you can now download your data sets from your user account under "My downloads" or by simply clicking on the link below:</p>
                                    
            <p> <a style="color: #e6e6e6; background-color: #009245;  font-size: 14pt;  font-family: var(--first_font_family);  padding: 5px 20px;" href="<?php echo $download_link;?>">Download My Files</a> </p>

            <p>You can also find a receipts for your purchase  <a href="<?php echo $invoice_url;?>">&lt;Here&gt;</a>.</p>
            
            <p>--- The Excel Sport Data team</p>

    </div>

</body>
</html>