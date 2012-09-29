<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<style type="text/css" title="currentStyle">
        @import "js/dataTables/css/demo_page.css";
        @import "js/dataTables/css/demo_table.css";
</style>
<script type="text/javascript" src="/js/dataTables/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    		
    var chartUsers,chartCalendarUsers;
    $(document).ready(function() {
        
        $('#userActivitiesTBL').dataTable();
        
        chartUsers = new Highcharts.Chart({
            chart: {
                renderTo: 'usersPie',
                backgroundColor:'transparent'
            },
            title: {
                text: null,
                style:{
                    textDecoration: 'underline'
                },
                enabled: false
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    size: "75%",
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        distance: 10,
                        connectorColor: '#000000',
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
                        }
                    }
                }
            },
            series: [{
                    type: 'pie',
                    name: 'User Activities',
                    data: [<? echo $statistics->TotalActivitiesPerUser(); ?>]
                }],
            credits:{
                enabled:false
            },
            exporting:{
                buttons:{
                    printButton:{
                        enabled:false
                    }
                }
            }
        });
        chartCalendarUsers = new Highcharts.Chart({
            chart: {
                    renderTo: 'usersCalendar',
                    defaultSeriesType: 'line',
                    backgroundColor: 'transparent',
                    //marginRight: 130,
                    marginTop: 40
            },
            title: {
                    text: null,
                    x: -20, //center
                    
                    margin: 30
            },
            xAxis: {
                    categories: [<? echo $statistics->lastDaysString(15); ?>],
                    labels:{
                        rotation: -45,
                        style: {
                            align: 'right',
                            color: '#333333',
                            fontWeight: 'normal'
                        },
                        y: 20
                    }
                    
            },
            yAxis: {
                    title: {
                            text: 'Activities'
                    },
                    plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                    }]
            },
            tooltip: {
                    formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                                    this.x +': '+ this.y +' Activities';
                    }
            },
            plotOptions: {
                    line: {
                        dataLabels: {enabled: true},
                        enableMouseTracking: true
                    }
            },
            legend: {
                    layout: 'horizontal',
                    //align: 'left',
                    verticalAlign: 'top',
                    x: 0,
                    y: 0,
                    borderWidth: 1,
                    enabled: true
            },
            series: [<? $statistics->lastDaysTotalActivitiesPerUser(15); ?>],
            credits:{
                enabled:false
            },
            exporting:{
                buttons:{
                    printButton:{
                        enabled:false
                    }
                }
            }
        });
    });			
</script>