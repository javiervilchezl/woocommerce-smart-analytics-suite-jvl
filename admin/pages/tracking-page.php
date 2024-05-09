<?php
defined( 'ABSPATH' ) or die( '¡Sin acceso directo, por favor!' );
require_once(ABSPATH . 'wp-load.php');

    ?>
    <div class="wrap-wsasj">
    <h1>WooCommerce Smart Analytics Suite JVL</h1>
    <h2>Panel de Seguimiento</h2>
    <p>Esta es la página de seguimiento, donde podrás ver el seguimiento de todo tu Ecommerce.</p>
    
    <?php

    if (isset($_POST["updateChartTracking"])) {

    }else{

        $analytics = new AnalyticsDataManager();
        $start_date = date('Y-m-d', strtotime('-3 months'));  // Fecha de inicio hace 3 meses
        $end_date = date('Y-m-d');  // Fecha de fin hoy

        $revenue_data = $analytics->get_daily_revenue($start_date, $end_date);

        $orders_data = $analytics->get_daily_completed_orders($start_date, $end_date);

        $aov_data = $analytics->get_daily_aov($start_date, $end_date);

        $labels = array_keys($aov_data); 
        $revenue_values = array_values($revenue_data);
        $orders_values = array_values($orders_data);
        $aov_values = array_values($aov_data);

    ?>

        <form id="dateRangeForm" method="POST" action="">
            <label for="startDate">Fecha de inicio:</label>
            <input type="date" id="startDate" name="startDate" value="<?php echo $start_date; ?>">
            <label for="endDate">Fecha de fin:</label>
            <input type="date" id="endDate" name="endDate" value="<?php echo $end_date; ?>">
            <label for="compareYear">Comparar con año:</label>
            <select id="compareYear" name="compareYear">
                <option value="no">No comparar</option>
                <?php
                $current_year = date('Y');
                for ($year = $current_year - 5; $year <= $current_year; $year++) {
                    echo '<option value="' . $year . '">' . $year . '</option>';
                }
                ?>
            </select>
            <button type="submit" name="updateChartTracking" id="updateChartTracking">Actualizar</button>
        </form>
        <div class="date-display" id="date-display">
            <span>Fecha Inicio: <?php echo $start_date; ?></span>
            <span>Fecha Fin: <?php echo $end_date; ?></span>
        </div>
        <hr>
        <canvas id="myChart" width="100%" height="35"></canvas>

        <?php
    }


    if (isset($_POST["updateChartTracking"])) {
        $analytics = new AnalyticsDataManager();
        if (isset($_POST["startDate"]) && isset($_POST["endDate"])) {
            $start_date = date('Y-m-d', strtotime($_POST["startDate"]));
            $end_date = date('Y-m-d', strtotime($_POST["endDate"]));
        } else {
            // Manejar el caso en que las fechas no estén seteadas
            echo "Las fechas no están especificadas.";
        }

        $compareYear = $_POST["compareYear"];

        $revenue_data = $analytics->get_daily_revenue($start_date, $end_date);

        $orders_data = $analytics->get_daily_completed_orders($start_date, $end_date);

        $aov_data = $analytics->get_daily_aov($start_date, $end_date);

        if ($compareYear !== "no") {
            // Convierte las fechas a objetos DateTime
            $start_date_time = new DateTime($start_date);
            $end_date_time = new DateTime($end_date);

            // Extrae los años de las fechas de inicio y fin
            $start_year = (int) $start_date_time->format('Y');
            $end_year = (int) $end_date_time->format('Y');

            // Calcula la diferencia de años
            $years_difference = $end_year - $start_year;

            // Calcula el desplazamiento de años
            $year_diff_start = $compareYear - $start_date_time->format('Y') - $years_difference;
            $year_diff_end = $compareYear - $end_date_time->format('Y');

            
            if($start_year==date("Y")){
                $comparison_start_date = (clone $start_date_time)->modify($year_diff_start . ' year')->modify('-1 day')->format('Y-m-d');
                $comparison_end_date = (clone $end_date_time)->modify($year_diff_end . ' year')->modify('-1 day')->format('Y-m-d');
            }else{
                $comparison_start_date = (clone $start_date_time)->modify($year_diff_start . ' year')->format('Y-m-d');
                $comparison_end_date = (clone $end_date_time)->modify($year_diff_end . ' year')->format('Y-m-d');
            }
            
            $revenue_data_compare = $analytics->get_daily_revenue($comparison_start_date, $comparison_end_date);
            $orders_data_compare = $analytics->get_daily_completed_orders($comparison_start_date, $comparison_end_date);
            $aov_data_compare = $analytics->get_daily_aov($comparison_start_date, $comparison_end_date);

            $revenue_values_compare = array_values($revenue_data_compare);
            $orders_values_compare = array_values($orders_data_compare);
            $aov_values_compare = array_values($aov_data_compare);
        
        }

        $labels = array_keys($aov_data); 
        $revenue_values = array_values($revenue_data);
        $orders_values = array_values($orders_data);
        $aov_values = array_values($aov_data);
        ?>
        

        <form id="dateRangeForm" method="POST" action="">
            <label for="startDate">Fecha de inicio:</label>
            <input type="date" id="startDate" name="startDate" value="<?php echo $start_date; ?>">
            <label for="endDate">Fecha de fin:</label>
            <input type="date" id="endDate" name="endDate" value="<?php echo $end_date; ?>">
            <label for="compareYear">Comparar con año:</label>
            <select id="compareYear" name="compareYear">
                <?php
                    if ($compareYear=="no") {
                        ?>
                        <option value="no" selected>No comparar</option>
                        <?php
                    }else{
                        ?>
                        <option value="no">No comparar</option>
                        <?php
                    }
                ?>
                
                <?php
                $current_year = date('Y');
                for ($year = $current_year - 5; $year <= $current_year; $year++) {
                    if ($compareYear==$year) {
                        echo '<option value="' . $year . '" selected>' . $year . '</option>';
                    }else{
                        echo '<option value="' . $year . '">' . $year . '</option>';
                    }
                }
                ?>
            </select>
            <button type="submit" name="updateChartTracking" id="updateChartTracking">Actualizar</button>
        </form>
        <div class="date-display" id="date-display">
            <span>Fecha Inicio: <?php echo $start_date; ?></span>
            <span>Fecha Fin: <?php echo $end_date; ?></span>
        </div>
        <hr>
        <canvas id="myChart" width="100%" height="35"></canvas>
        
        <?php 
        if ($revenue_values_compare && $compareYear!="no") { 
        ?>
            <script>

                var ctx = document.getElementById('myChart').getContext('2d');
                var displayDateRange = document.getElementById('date-display');
                var dateRangeForm = document.getElementById('dateRangeForm');
                var startDate = document.getElementById('startDate').value;
                var endDate = document.getElementById('endDate').value;
                var selectElement = document.getElementById('compareYear');
                var initialSelectedYear = selectElement.value;
                var compareYearSelect = selectElement.value;

                displayDateRange.innerHTML = '<span>Fecha Inicio: ' + startDate + '</span><span>Fecha Fin: ' + endDate + '</span><span>Comparado con el año: ' + compareYearSelect + '</span>';
    
                var trendData = calculateTrendLine(<?php echo json_encode($revenue_values); ?>);
                var trendDataCompare = calculateTrendLine(<?php echo json_encode($revenue_values_compare); ?>);
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode($labels); ?>,
                        datasets: [{
                            label: 'Ingresos Totales',
                            data: <?php echo json_encode($revenue_values); ?>,
                            borderColor: 'rgba(75, 177, 0, 1)',
                            backgroundColor: 'rgba(75, 177, 0, 1)',
                            borderWidth: 1.5
                        },
                        {
                            label: 'Órdenes Completadas',
                            data: <?php echo json_encode($orders_values); ?>,
                            borderColor: 'rgba(0, 43, 189, 1)',
                            backgroundColor: 'rgba(0, 43, 189, 1)',
                            borderWidth: 1.5
                        },
                        {
                            label: 'Valor Promedio del Pedido',
                            data: <?php echo json_encode($aov_values); ?>,
                            borderColor: 'rgba(199, 71, 0, 1)',
                            backgroundColor: 'rgba(199, 71, 0, 1)',
                            borderWidth: 1.5
                        },
                        {
                            label: 'Línea de Tendencia',
                            data: trendData,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 1))',
                            type: 'line',
                            fill: false,
                            borderWidth: 2
                        },
                        {
                            label: 'Ingresos Totales (comparado en: ' +compareYearSelect+')',
                            data: <?php echo json_encode($revenue_values_compare); ?>,
                            borderColor: 'rgba(138, 181, 106, 1)',
                            backgroundColor: 'rgba(138, 181, 106, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Órdenes Completadas (comparado en: ' +compareYearSelect+')',
                            data: <?php echo json_encode($orders_values_compare); ?>,
                            borderColor: 'rgba(98, 115, 171, 1)',
                            backgroundColor: 'rgba(98, 115, 171, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Valor Promedio del Pedido (comparado en: ' +compareYearSelect+')',
                            data: <?php echo json_encode($aov_values_compare); ?>,
                            borderColor: 'rgba(187, 129, 97, 1)',
                            backgroundColor: 'rgba(187, 129, 97, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Línea de Tendencia (comparado en: ' +compareYearSelect+')',
                            data: trendDataCompare,
                            borderColor: 'rgba(249, 255, 0, 1)',
                            backgroundColor: 'rgba(249, 255, 0, 1))',
                            type: 'line',
                            fill: false,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
                myChart.update();
                </script>
        <?php 
        }else{  
        ?>
            <script>

                var ctx = document.getElementById('myChart').getContext('2d');
                var displayDateRange = document.getElementById('date-display');
                var dateRangeForm = document.getElementById('dateRangeForm');
                var startDate = document.getElementById('startDate').value;
                var endDate = document.getElementById('endDate').value;
                var selectElement = document.getElementById('compareYear');
                var initialSelectedYear = selectElement.value;
                var compareYearSelect = selectElement.value;
                displayDateRange.innerHTML = '<span>Fecha Inicio: ' + startDate + '</span><span>Fecha Fin: ' + endDate + '</span>';
                var trendData = calculateTrendLine(<?php echo json_encode($revenue_values); ?>);
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode($labels); ?>,
                        datasets: [{
                            label: 'Ingresos Totales',
                            data: <?php echo json_encode($revenue_values); ?>,
                            borderColor: 'rgba(75, 177, 0, 1)',
                            backgroundColor: 'rgba(75, 177, 0, 1)',
                            borderWidth: 1.5
                        },
                        {
                            label: 'Órdenes Completadas',
                            data: <?php echo json_encode($orders_values); ?>,
                            borderColor: 'rgba(0, 43, 189, 1)',
                            backgroundColor: 'rgba(0, 43, 189, 1)',
                            borderWidth: 1.5
                        },
                        {
                            label: 'Valor Promedio del Pedido',
                            data: <?php echo json_encode($aov_values); ?>,
                            borderColor: 'rgba(199, 71, 0, 1)',
                            backgroundColor: 'rgba(199, 71, 0, 1)',
                            borderWidth: 1.5
                        },
                        {
                            label: 'Línea de Tendencia',
                            data: trendData,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 1))',
                            type: 'line',
                            fill: false,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
                myChart.update();
            </script>
        <?php 
        } 
    }else{
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var trendData = calculateTrendLine(<?php echo json_encode($revenue_values); ?>);
                var ctx = document.getElementById('myChart').getContext('2d');

                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode($labels); ?>,
                        datasets: [{
                            label: 'Ingresos Totales',
                            data: <?php echo json_encode($revenue_values); ?>,
                            borderColor: 'rgba(75, 177, 0, 1)',
                            backgroundColor: 'rgba(75, 177, 0, 1)',
                            borderWidth: 1.5
                        },
                        {
                            label: 'Órdenes Completadas',
                            data: <?php echo json_encode($orders_values); ?>,
                            borderColor: 'rgba(0, 43, 189, 1)',
                            backgroundColor: 'rgba(0, 43, 189, 1)',
                            borderWidth: 1.5
                        },
                        {
                            label: 'Valor Promedio del Pedido',
                            data: <?php echo json_encode($aov_values); ?>,
                            borderColor: 'rgba(199, 71, 0, 1)',
                            backgroundColor: 'rgba(199, 71, 0, 1)',
                            borderWidth: 1.5
                        },
                        {
                            label: 'Línea de Tendencia',
                            data: trendData,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 1))',
                            type: 'line',
                            fill: false,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        </script>    
        <?php
    }

?>


</div>