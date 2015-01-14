

<html>
<head>
    <link rel="stylesheet" href="{{ URL::asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('bower_components/bootstrap-material-design/dist/css/material.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('bower_components/bootstrap-material-design/dist/css/ripples.min.css') }}">

</head>
<body>

<div class="row">
    <div class="col-md-2"></div>

    <div class="col-md-8">
        <div class="well page">
            <form action="index.php/form/retrieve-reports" id="processFilesForm" method="post">

                <input type="submit" id="retrieveReportsButton" value="Retrieve Reports" name="submit">
            </form>
            <div id="container">

            </div>
        </div>
    </div>

    <div class="col-lg-2">

    </div>
</div>
<script src="{{ URL::asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ URL::asset('jquery.form.js') }}"></script>
<script src="{{ URL::asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/highcharts/highcharts.js') }}"></script>
<script src="{{ URL::asset('bower_components/bootstrap-material-design/dist/js/material.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/bootstrap-material-design/dist/js/ripples.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $.material.init();
    });

    $("#processFilesForm").ajaxForm({url: "index.php/form/retrieve-reports", type: 'post',

        success: function(response)
        {
            $(function () {

                // Radialize the colors
                Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function (color) {
                    return {
                        radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
                        stops: [
                            [0, color],
                            [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
                        ]
                    };
                });

                var totalReport = response.body.total;
                var totalReportGradeCount = totalReport.gradeCount;
                // Build the chart
                $('#container').highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false
                    },
                    title: {
                        text: 'Browser market shares at a specific website, 2014'
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                },
                                connectorColor: 'silver'
                            }
                        }
                    },
                    series: [{
                        type: 'pie',
                        name: 'Feedback',
                        data: [
                            ['Mediocre',   totalReportGradeCount.mediocre],
                            ['Unsatisfactory',  totalReportGradeCount.unsatisfactory],
//                            {
//                                name: 'Chrome',
//                                y: 12.8,
//                                sliced: true,
//                                selected: true
//                            },
                            ['Satisfactory',    totalReportGradeCount.unsatisfactory],
                            ['Good',     totalReportGradeCount.unsatisfactory],
                            ['Excellent',   totalReportGradeCount.unsatisfactory]
                        ]
                    }]
                });
            });

        }
    });

</script>
</body>
</html>