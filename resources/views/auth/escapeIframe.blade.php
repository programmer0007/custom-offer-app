<!DOCTYPE html>
<html>
    <head>
    </head>
    <body>
        <div style="display:none" id="installUrl" data-url="<?php echo $installUrl;?>"></div>
    
        <script type="text/javascript">
            var installUrl = document.getElementById("installUrl").getAttribute("data-url");
            window.top.location.href = installUrl;
            // console.log(installUrl);
        </script>
    </body>
</html>