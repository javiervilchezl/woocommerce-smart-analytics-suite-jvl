<?php
defined( 'ABSPATH' ) or die( '¡Sin acceso directo, por favor!' );

$analytics = new WooCommerceAnalytics();

$start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : date('Y-01-01'); // default a primer día del año
$end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : date('Y-m-d'); // default a hoy

$start_date = date('Y-m-d', strtotime($start_date));
$end_date = date('Y-m-d', strtotime($end_date));

// Restar un año a la fecha de inicio
$start_date_compare_year = date('Y-m-d', strtotime($start_date . ' -1 year'));
// Restar un año a la fecha de fin
$end_date_compare_year = date('Y-m-d', strtotime($end_date . ' -1 year'));


$sales_data = $analytics->get_sales_data($start_date, $end_date);
$sales_data_compare = $analytics->get_sales_data($start_date_compare_year, $end_date_compare_year);
if ($sales_data_compare !=0) {
    $sales_data_compare = ($sales_data - $sales_data_compare) / $sales_data_compare * 100;
    $sales_data_compare = number_format($sales_data_compare, 2, ',', '.');
}else{
    $sales_data_compare = 100;
}
$sales_data = number_format($sales_data, 2, ',', '.');

$aov_data = $analytics->get_aov_data($start_date, $end_date);
$aov_data_compare = $analytics->get_aov_data($start_date_compare_year, $end_date_compare_year);
if ($aov_data_compare !=0){
    $aov_data_compare = ($aov_data - $aov_data_compare) / $aov_data_compare * 100;
    $aov_data_compare = number_format($aov_data_compare, 2, ',', '.');
}else{
    $aov_data_compare = 100;
}
$aov_data = number_format($aov_data, 2, ',', '.');

$product_sales_data = $analytics->get_product_sales_data($start_date, $end_date);
$product_names = json_encode(array_column($product_sales_data, 'name'));
$product_quantities = json_encode(array_column($product_sales_data, 'quantity'));
$product_total_sales = json_encode(array_column($product_sales_data, 'total_sales'));

$total_customers = $analytics->get_total_customers($start_date, $end_date);
$total_customers_compare = $analytics->get_total_customers($start_date_compare_year, $end_date_compare_year);
if ($total_customers_compare !=0){
    $total_customers_compare = ($total_customers - $total_customers_compare) / $total_customers_compare * 100;
}else{
    $total_customers_compare = 100;
}


$customer_retention_rate = $analytics->get_customer_retention_rate($start_date, $end_date);
$customer_retention_rate_compare = $analytics->get_customer_retention_rate($start_date_compare_year, $end_date_compare_year);
if ($customer_retention_rate !=0){
    $customer_retention_rate_compare = ($customer_retention_rate - $customer_retention_rate_compare) / $customer_retention_rate_compare * 100;
    if (floor($customer_retention_rate_compare) == $customer_retention_rate_compare) {
        $customer_retention_rate_compare = number_format($customer_retention_rate_compare, 0);
    } else {
        $customer_retention_rate_compare = number_format($customer_retention_rate_compare, 2); 
    }
}else{
    $customer_retention_rate_compare = 100;
}
if (floor($customer_retention_rate) == $customer_retention_rate) {
    $customer_retention_rate = number_format($customer_retention_rate, 0);
} else {
    $customer_retention_rate = number_format($customer_retention_rate, 2); 
}

$Customer_lifetime_value = $analytics->get_customer_lifetime_value($start_date, $end_date);
$Customer_lifetime_value_compare = $analytics->get_customer_lifetime_value($start_date_compare_year, $end_date_compare_year);
if ($Customer_lifetime_value_compare !=0){
    $Customer_lifetime_value_compare = ($Customer_lifetime_value - $Customer_lifetime_value_compare) / $Customer_lifetime_value_compare * 100;
    $Customer_lifetime_value_compare = number_format($Customer_lifetime_value_compare, 2, ',', '.');
}else{
    $Customer_lifetime_value_compare = 100;
}
$Customer_lifetime_value = number_format($Customer_lifetime_value, 2, ',', '.');

$cart_abandonment_rate = $analytics->get_cart_abandonment_rate($start_date, $end_date);
$cart_abandonment_rate_compare = $analytics->get_cart_abandonment_rate($start_date_compare_year, $end_date_compare_year);
if ($cart_abandonment_rate_compare !=0){
    $cart_abandonment_rate_compare = ($cart_abandonment_rate - $cart_abandonment_rate_compare) / $cart_abandonment_rate_compare * 100;
    if (floor($cart_abandonment_rate_compare) == $cart_abandonment_rate_compare) {
        $cart_abandonment_rate_compare = number_format($cart_abandonment_rate_compare, 0);
    } else {
        $cart_abandonment_rate_compare = number_format($cart_abandonment_rate_compare, 2);
    }
}else{
    $cart_abandonment_rate_compare = 100;
}
if (floor($cart_abandonment_rate) == $cart_abandonment_rate) {
    $cart_abandonment_rate = number_format($cart_abandonment_rate, 0);
} else {
    $cart_abandonment_rate = number_format($cart_abandonment_rate, 2);
}

$order_cancellation_rate = $analytics->get_order_cancellation_rate($start_date, $end_date);
$order_cancellation_rate_compare = $analytics->get_order_cancellation_rate($start_date_compare_year, $end_date_compare_year);
if ($order_cancellation_rate_compare !=0){
    $order_cancellation_rate_compare = ($order_cancellation_rate - $order_cancellation_rate_compare) / $order_cancellation_rate_compare * 100;
    if (floor($order_cancellation_rate_compare) == $order_cancellation_rate_compare) {
        $order_cancellation_rate_compare = number_format($order_cancellation_rate_compare, 0);
    } else {
        $order_cancellation_rate_compare = number_format($order_cancellation_rate_compare, 2); 
    }
}else{
    $order_cancellation_rate_compare = 100;
}
if (floor($order_cancellation_rate) == $order_cancellation_rate) {
    $order_cancellation_rate = number_format($order_cancellation_rate, 0);
} else {
    $order_cancellation_rate = number_format($order_cancellation_rate, 2); 
}

?>
<div class="wrap-wsasj">
    <h1>WooCommerce Smart Analytics Suite JVL</h1>
    <h2>Cuadro de mando [Personalizado con fechas]</h2>
    <p>Esta es la página principal donde puedes ver los datos del intervalo de fechas que desees.</p>
    <form method="post" id="kpisForm" name="kpisForm">
        <label for="start_date">Fecha Inicio:</label>
        <input type="date" id="start_date" name="start_date" value="<?php echo esc_attr($start_date); ?>">
        <label for="end_date">Fecha Fin:</label>
        <input type="date" id="end_date" name="end_date" value="<?php echo esc_attr($end_date); ?>">
        <button type="submit" name="updateChartKpis" id="updateChartKpis">Actualizar Gráfico</button>
    </form>
    <div class="date-display">
        <span>Fecha Inicio: <?php echo htmlspecialchars(date('d-m-Y', strtotime($start_date))); ?></span>
        <span>Fecha Fin: <?php echo htmlspecialchars(date('d-m-Y', strtotime($end_date))); ?></span>
        <span>Frente al año: <?php echo htmlspecialchars(date('Y', strtotime($start_date . ' -1 year'))); ?></span>
    </div>
    <hr class="separator-header">

<!-- Divs para los gráficos -->
<div class="graficos">
    <div class="plantapiso" >
        <div class="piso" >
            <?php
                if ($customer_retention_rate_compare != 0) {
                    if($customer_retention_rate_compare < 0){
                        ?>
                            <h3>Tasa de retención de clientes <span style="color: red;">&#x25BC; <?php echo $customer_retention_rate_compare . '%'; ?></span></h3>
                        <?php
                    }else{
                        ?>
                            <h3>Tasa de retención de clientes <span style="color: green;">&#x25B2; <?php echo $customer_retention_rate_compare . '%'; ?></span></h3>
                        <?php
                    }
                }else{
                    ?>
                        <h3>Tasa de retención de clientes <span style="color: green;">&#x25B2; 100%</span></h3>
                    <?php
                }    
            ?>
            <canvas id="retentionRateChart"></canvas>
        </div>
        <div class="piso pisocentro" >
            <?php
                if ($cart_abandonment_rate_compare != 0) {
                    if($cart_abandonment_rate_compare < 0){
                        ?>
                            <h3>Tasa de Abandono de Carrito <span style="color: red;">&#x25B2; <?php echo $cart_abandonment_rate_compare . '%'; ?></span></h3>
                        <?php
                    }else{
                        ?>
                            <h3>Tasa de Abandono de Carrito <span style="color: green;">&#x25BC; <?php echo $cart_abandonment_rate_compare . '%'; ?></span></h3>
                        <?php
                    }
                }else{
                    ?>
                        <h3>Tasa de Abandono de Carrito <span style="color: green;">&#x25BC; 100%</span></h3>
                    <?php
                }    
            ?>
            <canvas id="cartAbandonmentRateChart"></canvas>
        </div>
        <div class="piso">
            <?php
                if ($order_cancellation_rate_compare != 0) {
                    if($order_cancellation_rate_compare < 0){
                        ?>
                            <h3>Tasa de Cancelación de Pedidos <span style="color: red;">&#x25B2; <?php echo $order_cancellation_rate_compare . '%'; ?></span></h3>
                        <?php
                    }else{
                        ?>
                            <h3>Tasa de Cancelación de Pedidos <span style="color: green;">&#x25BC; <?php echo $order_cancellation_rate_compare . '%'; ?></span></h3>
                        <?php
                    }
                }else{
                    ?>
                        <h3>Tasa de Cancelación de Pedidos <span style="color: green;">&#x25BC; 100%</span></h3>
                    <?php
                }    
            ?>
            <canvas id="orderCancellationRateChart"></canvas>
        </div>
    </div>
    <div class="plantabarra">
        <div class="piso pisobarra">
            <?php
                if ($sales_data_compare != 0) {
                    if($sales_data_compare < 0){
                        ?>
                            <h3>Total de ventas <span style="color: red;">&#x25BC; <?php echo $sales_data_compare . '%'; ?></span></h3>
                        <?php
                    }else{
                        ?>
                            <h3>Total de ventas <span style="color: green;">&#x25B2; <?php echo $sales_data_compare . '%'; ?></span></h3>
                        <?php
                    }
                }else{
                    ?>
                        <h3>Total de ventas <span style="color: green;">&#x25B2; 100%</span></h3>
                    <?php
                }    
            ?>
            <canvas id="salesChart"></canvas>
        </div>
        <div class="piso pisobarra pisocentro">
            <?php
                if ($aov_data_compare != 0) {
                    if($aov_data_compare < 0){
                        ?>
                            <h3>Valor promedio del pedido <span style="color: red;">&#x25BC; <?php echo $aov_data_compare . '%'; ?></span></h3>
                        <?php
                    }else{
                        ?>
                            <h3>Valor promedio de pedido <span style="color: green;">&#x25B2; <?php echo $aov_data_compare . '%'; ?></span></h3>
                        <?php
                    }
                }else{
                    ?>
                        <h3>Valor promedio del pedido <span style="color: green;">&#x25B2; 100%</span></h3>
                    <?php
                }    
            ?>
            <canvas id="aovChart"></canvas>
        </div>
        <div class="piso pisobarra pisocentro">
            <?php
                if ($Customer_lifetime_value_compare != 0) {
                    if($Customer_lifetime_value_compare < 0){
                        ?>
                            <h3>CLV - Valor de Vida del Cliente <span style="color: red;">&#x25BC; <?php echo $Customer_lifetime_value_compare . '%'; ?></span></h3>
                        <?php
                    }else{
                        ?>
                            <h3>CLV - Valor de Vida del Cliente <span style="color: green;">&#x25B2; <?php echo $Customer_lifetime_value_compare . '%'; ?></span></h3>
                        <?php
                    }
                }else{
                    ?>
                        <h3>CLV - Valor de Vida del Cliente <span style="color: green;">&#x25B2; 100%</span></h3>
                    <?php
                }    
            ?>
            <canvas id="clvChart"></canvas>
        </div>
        <div class="piso pisobarra">
            <?php
                if ($total_customers_compare != 0) {
                    if($total_customers_compare < 0){
                        ?>
                            <h3>Total de clientes <span style="color: red;">&#x25BC; <?php echo $total_customers_compare . '%'; ?></span></h3>
                        <?php
                    }else{
                        ?>
                            <h3>Total de clientes <span style="color: green;">&#x25B2; <?php echo $total_customers_compare . '%'; ?></span></h3>
                        <?php
                    }
                }else{
                    ?>
                        <h3>Total de clientes <span style="color: green;">&#x25B2; 100%</span></h3>
                    <?php
                }    
            ?>
            <canvas id="customersChart"></canvas>
        </div>
    </div>
</div>
<div class="tablaproductosmasvendidos">
    <h3 class="titulotabla">Productos más vendidos</h3>
    <table class="productsTable">
        <thead>
            <tr>
                <th>Nombre del Producto</th>
                <th>Cantidad Vendida</th>
                <th>Total de Ventas (€)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($product_sales_data as $product) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($product['total_sales']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function(){
        //Gráfico de Ventas Totales
        var ctxSales = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(ctxSales, {
            type: 'bar',
            data: {
                labels: ['Ventas Totales'],
                datasets: [{
                    label: 'Total de Ventas (€)',
                    data: [<?php echo $sales_data; ?>],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
        //Gráfico de AOV (Valor Promedio del Pedido)
        var ctxAov = document.getElementById('aovChart').getContext('2d');
        var aovChart = new Chart(ctxAov, {
            type: 'bar',
            data: {
                labels: ['AOV'],
                datasets: [{
                    label: 'AOV (€)',
                    data: [<?php echo $aov_data; ?>],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
        
        //Gráfico del Total de Clientes
        var ctxCustomers = document.getElementById('customersChart').getContext('2d');
        var customersChart = new Chart(ctxCustomers, {
            type: 'bar',
            data: {
                labels: ['Clientes'],
                datasets: [{
                    label: 'Total de Clientes',
                    data: [<?php echo $total_customers; ?>],
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
        //Gráfico de Tasa de Retención de Clientes
        var ctxRetention = document.getElementById('retentionRateChart').getContext('2d');
        var retentionRateChart = new Chart(ctxRetention, {
            type: 'pie',
            data: {
                labels: ['Retenidos', 'No Retenidos'],
                datasets: [{
                    label: 'Tasa de Retención (%)',
                    data: [<?php echo $customer_retention_rate; ?>, 100 - <?php echo $customer_retention_rate; ?>],
                    backgroundColor: ['rgba(54, 162, 235, 0.6)', 'rgba(255, 99, 132, 0.6)'],
                    borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 99, 132, 1)'],
                    borderWidth: 1
                }]
            }
        });
        //Gráfico de Valor de Vida del Cliente (CLV)
        var ctxClv = document.getElementById('clvChart').getContext('2d');
        var clvChart = new Chart(ctxClv, {
            type: 'bar',
            data: {
                labels: ['CLV'],
                datasets: [{
                    label: 'Valor de Vida del Cliente (€)',
                    data: [<?php echo $Customer_lifetime_value; ?>],
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
        //Gráfico de Tasa de Abandono de Carrito
        var ctxCartAbandonment = document.getElementById('cartAbandonmentRateChart').getContext('2d');
        var cartAbandonmentRateChart = new Chart(ctxCartAbandonment, {
            type: 'doughnut',
            data: {
                labels: ['Completados', 'Abandonados'],
                datasets: [{
                    label: 'Tasa de Abandono de Carrito (%)',
                    data: [100 - <?php echo $cart_abandonment_rate; ?>, <?php echo $cart_abandonment_rate; ?>],
                    backgroundColor: ['rgba(75, 192, 192, 0.5)', 'rgba(255, 99, 132, 0.5)'],
                    borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                    borderWidth: 1
                }]
            }
        });
        //Gráfico de Tasa de Cancelación de Órdenes
        var ctxOrderCancellation = document.getElementById('orderCancellationRateChart').getContext('2d');
        var orderCancellationRateChart = new Chart(ctxOrderCancellation, {
            type: 'pie',
            data: {
                labels: ['Canceladas', 'Completadas'],
                datasets: [{
                    label: 'Tasa de Cancelación (%)',
                    data: [<?php echo $order_cancellation_rate; ?>, 100 - <?php echo $order_cancellation_rate; ?>],
                    backgroundColor: ['rgba(255, 159, 64, 0.6)', 'rgba(153, 102, 255, 0.6)'],
                    borderColor: ['rgba(255, 159, 64, 1)', 'rgba(153, 102, 255, 1)'],
                    borderWidth: 1
                }]
            }
        });
    });
</script>
</div>