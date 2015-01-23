<html>
<head>
    <link rel="stylesheet" href="{{ URL::asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('bower_components/bootstrap-material-design/dist/css/material.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('bower_components/bootstrap-material-design/dist/css/ripples.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('bower_components/pace/themes/blue/pace-theme-flash.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('bower_components/nprogress/nprogress.css') }}">

</head>
<body>

<div class="row">
    <div class="col-md-1"></div>

    <div class="col-md-10">
        <div class="well page">


            <div>
                <form action="index.php/form/retrieve-reports" id="processFilesForm" class="pull-left" method="post">

                    <input type="submit" id="retrieveReportsButton" class="btn btn-raised btn-lg btn-primary" value="Retrieve Reports" name="submit">
                </form>

                <div class="pull-right">
                    <a class="btn btn-danger btn-lg" href="http://192.168.2.94/pmccs_aundh/data_1.csv">Export to Excel Sheet</a>
                </div>
            </div>
            <div id="container">

            </div>

            <div id="frequencyGraphContainer">
            </div>

            <div class="row">
                @for ($i = 1; $i < 13; $i++)
                    <div class="col-md-3" id="container{{$i}}">

                    </div>
                @endfor
            </div>
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
<script src="{{ URL::asset('bower_components/pace/pace.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/nprogress/nprogress.js') }}"></script>
<script>
    $(document).ready(function() {
        $.material.init();
    });


    $("#processFilesForm").ajaxForm({url: "index.php/form/retrieve-reports", type: 'post',
        beforeSubmit: function()
        {
            NProgress.start();
        },
        success: function(response)
        {
            console.log(response);

            NProgress.done();

            var report = response.body;
            var totalReportGradeCount = report.total.gradeCount;
            var totalCount = report.total.count;

            function generateBarChart(totalReportGradeCount, totalCount,selector, title) {
                $(selector).highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false
                    },
                    title: {
                        text: title
                    },
                    xAxis: {
                        categories: ['1-3 times', '3-6 times', 'more than 6 times']
                    },
                    yAxis: {
                        labels: {
                            formatter:function() {
                                var pcnt = (this.value / totalCount) * 100;
                                return Highcharts.numberFormat(pcnt,0,',') + '%';
                            }
                        }
                    },
                    tooltip: {

//                          pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                        formatter: function () {
                            var pcnt = (this.y / totalCount) * 100;
                            return Highcharts.numberFormat(pcnt) + '%';
                        }
                    },
                    plotOptions: {
                        series: {
                            shadow: false,
                            borderWidth: 0,
                            dataLabels: {
                                enabled: true,
                                formatter: function () {
                                    var pcnt = (this.y / totalCount) * 100;
                                    return Highcharts.numberFormat(pcnt) + '%';
                                }
                            }
                        }
                    },
                    series: [{
                        type: 'bar',
                        colorByPoint: true,
                        data: [

                            ['times_1_3', totalReportGradeCount.times_1_3],
                            ['3-6_times', totalReportGradeCount.times_3_6],
                            ['6_more_times', totalReportGradeCount.times_6_more]
                        ]
                    }]
                });
            };


//            var report = response.body;
//            var totalReportGradeCount = report.total.gradeCount;
//            var totalCount = report.total.count;


            generatePieChart(totalReportGradeCount, totalCount, "#container", 'Overall Report');

            function generatePieChart(totalReportGradeCount, totalCount, selector, title) {
                $(selector).highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false
                    },
                    title: {
                        text: title
                    },
                    xAxis: {
                        categories: ['Mediocre', 'Unsatisfactory', 'Satisfactory', 'Good', 'Excellent']
                    },
                    yAxis: {
                        labels: {
                            formatter:function() {
                                var pcnt = (this.value / totalCount) * 100;
                                return Highcharts.numberFormat(pcnt,0,',') + '%';
                            }
                        }
                    },

                    tooltip: {
//                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                        formatter: function () {
                            var pcnt = (this.y / totalCount) * 100;
                            return Highcharts.numberFormat(pcnt) + '%';
                        }
                    },
                    plotOptions: {
                        series: {
                            shadow: false,
                            borderWidth: 0,
                            dataLabels: {
                                enabled: true,
                                formatter: function () {
                                    var pcnt = (this.y / totalCount) * 100;
                                    return Highcharts.numberFormat(pcnt) + '%';
                                }
                            }
                        }
                    },
                    series: [{
                        type: 'bar',
                        colorByPoint: true,
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




//                Build the chart

                generatePieChart(totalReportGradeCount, totalCount, "#container", 'Overall Report');
                generateBarChart(report.number_of_appearances.gradeCount, report.number_of_appearances.count, "#frequencyGraphContainer", "Frequency Report");
                var i = 1;
                $.each(report, function( criteria, count ) {
                    if(i==12)
                    {
                        return;
                    }
                    generatePieChart(count.gradeCount, count.count, "#container"+i, criteria);
                    i++;
                });

            });

        }
    });

</script>
</body>
</html>