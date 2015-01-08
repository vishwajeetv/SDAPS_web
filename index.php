<?php
?>

<html>
    <head>

    </head>
    <body>





        <form action="uploadforms.php" method="post" enctype="multipart/form-data">
            Select file to upload:
<!--            <input type="file" name="fileToUpload" id="fileToUpload">-->
            <input type="file" multiple="multiple" name="fileToUpload[]" />
            <input type="submit" value="Upload File" name="submit">
        </form>

        <form action="processforms.php" method="POST">
            <input type="submit" value="Process Forms">
        </form>
        <form action="generateoutput.php" method="POST">
            <input type="submit" value="Generate Output">
        </form>

    </body>
</html>


