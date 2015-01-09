<html>
<head>
    <link rel="stylesheet" href="{{ URL::asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('bower_components/bootstrap-material-design/dist/css/material.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('bower_components/bootstrap-material-design/dist/css/ripples.min.css') }}">

</head>
<body>


<div class="row">
    <div class="col-md-3">

    </div>

    <div class="col-md-6  well page">
        <div class="col-md-2"></div>
        <div class="col-md-8">
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-material-cyan">
                <div class="panel-heading" role="tab" id="headingOne">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#uploadFiles" aria-expanded="true" aria-controls="uploadFiles">
                            Upload Files
                        </a>
                    </h4>
                </div>
                <div id="uploadFiles" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                    <div class="panel-body">

                        <form action="index.php/form/upload-form" method="post" enctype="multipart/form-data">
                            Select file to upload:
                            <!--            <input type="file" name="fileToUpload" id="fileToUpload">-->
                            <input type="file" id="chooseFilesButton" multiple="multiple" name="fileToUpload[]" />
                            <input type="submit" id="uploadButton" value="Upload File" name="submit">
                        </form>
                    </div>
                </div>
            </div>





            <div class="panel panel-material-cyan">
                <div class="panel-heading" role="tab" id="addFilesHeading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#addFiles" aria-expanded="true " aria-controls="addFiles">
                           Add Files
                        </a>
                    </h4>
                </div>
                <div id="addFiles" class="panel-collapse collapse" role="tabpanel" aria-labelledby="addFilesHeading">
                    <div class="panel-body">
                        adkajkda

                    </div>
                </div>
            </div>


        <div class="panel panel-material-cyan">
            <div class="panel-heading" role="tab" id="processFilesHeading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#processFiles" aria-expanded="true" aria-controls="processFiles">
                        Process Files
                    </a>
                </h4>
            </div>
            <div id="processFiles" class="panel-collapse collapse out" role="tabpanel" aria-labelledby="processFilesHeading">
                <div class="panel-body">

                    asdd
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-2"></div>
    </div>


    <div class="col-md-3">

    </div>
</div>


<script src="{{ URL::asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/bootstrap-material-design/dist/js/material.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/bootstrap-material-design/dist/js/ripples.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $.material.init();
    });


    $("#uploadButton").click(



            function(e) {

                e.preventDefault();

                var filesToUpload = new Array();

                for (index = 0; index < $('#chooseFilesButton').prop('files').length; ++index) {
                    filesToUpload.push(
                                $('#chooseFilesButton').prop('files')[index]
                    )
                }

                console.log(filesToUpload);

                $.ajax({

                    type: "POST",
                    url: "{{ URL::to('index.php/form/upload-form'); }}",
                    data:  filesToUpload,

                    contentType: "multipart/form-data"

                })
                .done(function (response) {
                            console.log(response);
                    if (response.header.status == 'success') {

                    }
                    else {
                        $("#errorMessage").show(400);
                    }
                }).
                error(function (response) {
                });

    });


</script>
</body>
</html>