

<html>
<head>
    <link rel="stylesheet" href="{{ URL::asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('bower_components/bootstrap-material-design/dist/css/material.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('bower_components/bootstrap-material-design/dist/css/ripples.min.css') }}">

</head>
<body>

<div class="row">
    <div class="col-md-1"></div>

    <div class="col-md-10">
        <div class="well page">
            <form action="index.php/form/retrieve-reports" id="processFilesForm" method="post">

                <input type="submit" id="retrieveReportsButton" class="btn btn-raised btn-lg btn-primary" value="Retrieve Reports" name="submit">
            </form>

            <a class="btn btn-danger btn-lg" href="http://192.168.2.94/pmccs_aundh/data_1.csv">Export to Excel Sheet</a>
            <div id="container">

            </div>
        </div>
    </div>

    <div class="col-md-1">

    </div>
</div>

<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <div class="row well page">

            @for ($i = 1; $i < 13; $i++)
                <div class="col-md-3" id="container{{$i}}">

                </div>
            @endfor


        </div>

    </div>
    <div class="col-md-1">

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
            function generatePieChart(totalReportGradeCount, selector, title) {
                $(selector).highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false
                    },
                    title: {
                        text: title
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
                            ['Mediocre', totalReportGradeCount.mediocre],
                            ['Unsatisfactory', totalReportGradeCount.unsatisfactory],
                            ['Satisfactory', totalReportGradeCount.satisfactory],
                            ['Good', totalReportGradeCount.good],
                            ['Excellent', totalReportGradeCount.excellent]
                        ]
                    }]
                });
            }

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

                var report = response.body;
                var totalReportGradeCount = report.total.gradeCount;

                // Build the chart
                generatePieChart(totalReportGradeCount, "#container", 'Overall Report');


                var i = 1;
                $.each(report, function( criteria, count ) {

                    generatePieChart(count.gradeCount, "#container"+i, criteria);
                    i++;
                });

            });

        }
    });

</script>
</body>
</html>