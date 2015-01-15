<html>
<head>
    <link rel="stylesheet" href="{{ URL::asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet"
          href="{{ URL::asset('bower_components/bootstrap-material-design/dist/css/material.min.css') }}">
    <link rel="stylesheet"
          href="{{ URL::asset('bower_components/bootstrap-material-design/dist/css/ripples.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('bower_components/snackbarjs/dist/snackbar.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('bower_components/pace/themes/blue/pace-theme-flash.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('bower_components/nprogress/nprogress.css') }}">

</head>
<body>


<div class="row">
    <div class="col-md-2">

    </div>

    <div class="col-md-8  well page">


        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="well text-center">
                <h1>Citizen Feedback System</h1>

            </div>
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-material-bluegrey">
                    <div class="panel-heading" role="tab" id="headingOne">
                        <h3 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#uploadFiles" aria-expanded="true"
                               aria-controls="uploadFiles">
                                Upload Files
                            </a>
                        </h3>
                    </div>
                    <div id="uploadFiles" class="panel-collapse collapse in" role="tabpanel"
                         aria-labelledby="headingOne">
                        <div class="panel-body">

                            {{--@plupload()--}}
                            <form action="index.php/form/upload-form" id="fileUploadForm" method="post"
                                  enctype="multipart/form-data">
                                Select file to upload:
                                <!--            <input type="file" name="fileToUpload" id="fileToUpload">-->
                                <input type="file" class="btn" id="chooseFilesButton" multiple="multiple"
                                       name="fileToUpload[]"/>
                                <input type="submit" class="btn btn-raised btn-material-bluegrey" id="uploadButton"
                                       value="Upload File" name="submit">
                            </form>
                        </div>
                    </div>
                </div>


                <div class="panel panel-danger">
                    <div class="panel-heading" role="tab" id="addFilesHeading">
                        <h3 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#addFiles" aria-expanded="true "
                               aria-controls="addFiles">
                                Add Files
                            </a>
                        </h3>
                    </div>
                    <div id="addFiles" class="panel-collapse collapse" role="tabpanel" aria-labelledby="addFilesHeading">
                        <div class="panel-body"  id="addFilesInside">
                            <div id="fileNamesContainer">

                            </div>

                            <form action="index.php/form/add-forms" id="addFilesForm" method="post">

                                <input type="submit" class="btn btn-raised btn-danger" id="addFilesButton"
                                       value="Add Files" name="submit">
                            </form>


                        </div>
                    </div>
                </div>


                <div class="panel panel-material-indigo">
                    <div class="panel-heading" role="tab" id="processFilesHeading">
                        <h3 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#processFiles" aria-expanded="true"
                               aria-controls="processFiles">
                                Process Files
                            </a>
                        </h3>
                    </div>
                    <div id="processFiles" class="panel-collapse collapse out" role="tabpanel"
                         aria-labelledby="processFilesHeading">
                        <div class="panel-body">
                            <form action="index.php/form/process-forms" id="processFilesForm" method="post">

                                <input type="submit" class="btn btn-raised btn-material-indigo" id="processFilesButton"
                                       value="Process Files" name="submit">
                            </form>

                            <a class="btn btn-lg btn-primary" href="reports">Reports</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2"></div>
        </div>


        <div class="col-md-2">

        </div>
    </div>

</div>
<script src="{{ URL::asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ URL::asset('jquery.form.js') }}"></script>
<script src="{{ URL::asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/highcharts/highcharts.js') }}"></script>
<script src="{{ URL::asset('bower_components/bootstrap-material-design/dist/js/material.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/bootstrap-material-design/dist/js/ripples.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/snackbarjs/dist/snackbar.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/pace/pace.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/nprogress/nprogress.js') }}"></script>

<script>
    $(document).ready(function () {
        $.material.init();
    });


    $("#fileUploadForm").ajaxForm({
        beforeSubmit: function()
        {
            NProgress.start();
        },
        url: "index.php/form/upload-form", type: 'post',
        success: function (response) {
            response.body.forEach(function (result) {

                NProgress.done();
                $.snackbar({content: response.header.message});

                $('#uploadFiles').collapse();

                $('#addFiles').collapse({
                    toggle: true
                });

                $("#fileNamesContainer").append("<div class='fileName'>" + result.fileName + "</div>");
                $("#addFilesForm").append("<input type='hidden' class='fileName' name='fileNames[]' value='" + result.fileName + "'/>");
            });

        }
    });




    $("#addFilesForm").ajaxForm({

        beforeSubmit: function()
        {
            NProgress.start();
        },

        url: "index.php/form/add-forms", type: 'post',

        success: function (response) {

            $('#addFilesInside').hide();

            $('#processFiles').collapse({
                toggle: true
            });

            NProgress.done();
            $.snackbar({content: response.header.message});

        }
    });


    $("#processFilesForm").ajaxForm({
        beforeSubmit: function(){
            NProgress.start();
        },
        url: "index.php/form/process-forms", type: 'post',

        success: function (response) {
            NProgress.done();
            $.snackbar({content: response.header.message});

        }
    });

</script>
</body>
</html>