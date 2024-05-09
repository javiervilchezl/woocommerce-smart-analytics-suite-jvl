<?php

defined('ABSPATH') or die('¡Sin acceso directo, por favor!');

class WooCommerceAnalytics {

    public function __construct() {
        add_action('init', array($this, 'initialize'));
    }

    public function initialize() {
        
    }

    //Función para obtener datos de ventas totales
    public function get_sales_data($start_date, $end_date) {
    
        $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), new DateTime($end_date));
        $total_revenue = 0;
        foreach ($period as $date) {
            $date_formatted = $date->format('Y-m-d');

            $args = [
                'limit'        => -1,
                'return'       => 'ids',
                'status'       => 'completed',
                'date_created' => $date_formatted,
            ];
            $orders = wc_get_orders($args);
            

            foreach ($orders as $order_id) {
                $order = wc_get_order($order_id);
                $total_revenue += $order->get_total();
            }
        }
        return $total_revenue;
    }
    
    //Función para obtener el AOV (Average Order Value)
    function get_aov_data($start_date, $end_date) {
        $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), new DateTime($end_date));
        $total_sales = 0;
        $count_orders = 0;
        foreach ($period as $date) {
            $date_formatted = $date->format('Y-m-d');
            $params = [
                'limit'        => -1,
                'return'       => 'ids',
                'status'       => 'completed',
                'date_created' => $date_formatted,
            ];
            $orders = wc_get_orders($params);
            $count_orders += count($orders);
            foreach ($orders as $order_id) {
                $order = wc_get_order($order_id);
                $total_sales += $order->get_total();
            }
        }
        return $count_orders ? $total_sales / $count_orders : 0; // Calcula el Average Order Value
    }

    //Función para obtener datos de los productos más vendidos
    function get_product_sales_data($start_date, $end_date) {
        $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), new DateTime($end_date));
        $product_sales = [];
        foreach ($period as $date) {
            $date_formatted = $date->format('Y-m-d');
            $params = [
                'limit'        => -1,
                'return'       => 'ids',
                'status'       => 'completed',
                'date_created' => $date_formatted,
            ];
            $orders = wc_get_orders($params);
            foreach ($orders as $order_id) {
                $order = wc_get_order($order_id);
                foreach ($order->get_items() as $item_id => $item) {
                    $product_id = $item->get_product_id();
                    $product_name = $item->get_name();
                    $quantity = $item->get_quantity();
                    $total_sales = $item->get_total();
                    if ($product_name!="" && $quantity > 0 && $total_sales > 0) {
                        if (!isset($product_sales[$product_id])) {
                            $product_sales[$product_id] = [
                                'name' => $product_name,
                                'quantity' => $quantity,
                                'total_sales' => $total_sales
                            ];
                        } else {
                            $product_sales[$product_id]['quantity'] += $quantity;
                            $product_sales[$product_id]['total_sales'] += $total_sales;
                        }
                    }
                }
            }
        }
        usort($product_sales, function($a, $b) {
            return $b['quantity'] - $a['quantity'];
        });
        // Limitar el resultado a los 50 productos más vendidos
        $top_ten_products = array_slice($product_sales, 0, 50);
    
        return $top_ten_products;
    }
    
    //Función para obtener el número total de clientes
    function get_total_customers($start_date, $end_date) {
        // Convertir las fechas a formato de tiempo
        $start_timestamp = strtotime($start_date . ' 00:00:00');
        $end_timestamp = strtotime($end_date . ' 23:59:59');
    
        // Obtener todos los usuarios con el rol de 'customer'
        $args = array(
            'role'    => 'customer',
            'fields'  => 'ID', 
            'date_query' => array(
                array(
                    'after'     => array(
                        'year'  => date('Y', $start_timestamp),
                        'month' => date('n', $start_timestamp),
                        'day'   => date('j', $start_timestamp),
                    ),
                    'before'    => array(
                        'year'  => date('Y', $end_timestamp),
                        'month' => date('n', $end_timestamp),
                        'day'   => date('j', $end_timestamp),
                    ),
                    'inclusive' => true
                )
            ),
        );
    
        $user_query = new WP_User_Query($args);
        $customers = $user_query->get_results();
    
        return count($customers);
    }
    
    //Función para obtener la tasa de retención de clientes
    function get_customer_retention_rate($start_date, $end_date) {
        // Definir los períodos anterior y actual usando DatePeriod
        $previous_period = new DatePeriod(
            new DateTime("$start_date -1 year"),
            new DateInterval('P1D'),
            new DateTime("$end_date -1 year")
        );
    
        $current_period = new DatePeriod(
            new DateTime($start_date),
            new DateInterval('P1D'),
            new DateTime($end_date)
        );
    
        $last_year_customers = [];
        $this_year_customers = [];
    
        // Recolectar IDs de clientes del año pasado
        foreach ($previous_period as $date) {
            $date_formatted = $date->format('Y-m-d');
            $params = [
                'limit'        => -1,
                'return'       => 'ids',
                'status'       => 'completed',
                'date_created' => $date_formatted,
            ];
            $orders = wc_get_orders($params);
            foreach ($orders as $order_id) {
                $order = wc_get_order($order_id);
                $last_year_customers[] = $order->get_customer_id();
            }
        }
        $last_year_customers = array_unique($last_year_customers);
    
        // Recolectar IDs de clientes de este año
        foreach ($current_period as $date) {
            $date_formatted = $date->format('Y-m-d');
            $params = [
                'limit'        => -1,
                'return'       => 'ids',
                'status'       => 'completed',
                'date_created' => $date_formatted,
            ];
            $orders = wc_get_orders($params);
            foreach ($orders as $order_id) {
                $order = wc_get_order($order_id);
                $this_year_customers[] = $order->get_customer_id();
            }
        }
        $this_year_customers = array_unique($this_year_customers);
    
        // Calcular los clientes retenidos
        $retained_customers = array_intersect($last_year_customers, $this_year_customers);
        $retention_rate = count($last_year_customers) > 0 ? count($retained_customers) / count($last_year_customers) * 100 : 0;
    
        return $retention_rate;
    }
    
    function get_customer_lifetime_value($start_date, $end_date) {
        // Obtener el AOV
        $aov = $this->get_aov_data($start_date, $end_date);
    
        // Configurar el periodo de fechas
        $period = new DatePeriod(
            new DateTime($start_date),
            new DateInterval('P1D'),
            (new DateTime($end_date))->modify('+1 day')
        );
    
        // Preparar datos para calcular la frecuencia y la duración del cliente
        $customer_orders = [];
    
        foreach ($period as $date) {
            $date_formatted = $date->format('Y-m-d');
            $params = [
                'limit'        => -1,
                'return'       => 'ids',
                'status'       => 'completed',
                'date_created' => $date_formatted,
            ];
            $order_ids = wc_get_orders($params);
    
            foreach ($order_ids as $order_id) {
                $order = wc_get_order($order_id);
                $customer_id = $order->get_customer_id();
                if (!isset($customer_orders[$customer_id])) {
                    $customer_orders[$customer_id] = [];
                }
                $customer_orders[$customer_id][] = $order->get_date_created()->date('Y-m-d H:i:s');
            }
        }
    
        // Calcular la frecuencia media de compra y la duración del cliente
        $total_purchase_frequencies = 0;
        $customer_lifetimes = 0;
        $num_customers = count($customer_orders);
    
        foreach ($customer_orders as $customer_id => $dates) {
            if (count($dates) > 1) {
                usort($dates, function($a, $b) {
                    return strtotime($a) - strtotime($b);
                });
                $first_purchase = strtotime($dates[0]);
                $last_purchase = strtotime($dates[count($dates) - 1]);
                $customer_lifetime = ($last_purchase - $first_purchase) / (365 * 24 * 60 * 60); // años
                $purchase_frequency = count($dates) / $customer_lifetime; // frecuencia de compra por año
                $total_purchase_frequencies += $purchase_frequency;
                $customer_lifetimes += $customer_lifetime;
            }
        }
    
        // Calcular la frecuencia y la duración promedio
        $average_purchase_frequency = $num_customers > 0 ? $total_purchase_frequencies / $num_customers : 0;
        $average_customer_lifetime = $num_customers > 0 ? $customer_lifetimes / $num_customers : 0;
    
        // Calculamos el CLV
        $clv = $aov * $average_purchase_frequency * $average_customer_lifetime;
        return $clv;
    }
    
    //Función para obtener la tasa de abandono de carrito
    function get_cart_abandonment_rate($start_date, $end_date) {
        $completed_orders = 0;
    
        $period = new DatePeriod(
            new DateTime($start_date),
            new DateInterval('P1D'),
            (new DateTime($end_date))->modify('+1 day')
        );
    
        foreach ($period as $date) {
            $date_formatted = $date->format('Y-m-d');
            
            $order_params = [
                'limit'  => -1,
                'return' => 'ids',
                'date_created' => $date_formatted,
            ];
            $daily_orders = wc_get_orders($order_params);
            $completed_orders += count($daily_orders);
        }
    
        $carts_created = $this->count_all_created_carts($start_date, $end_date);
        
        $abandonment_rate = 0;
        if ($carts_created > 0) {
            $abandonment_rate = (($carts_created - $completed_orders) / $carts_created) * 100;
        }
    
        return $abandonment_rate;
    }
    
    //Función para obtener todos los carritos de compra
    function count_all_created_carts($start_date, $end_date) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wcasjvl_cart_starts';
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE start_time BETWEEN %s AND %s",
            $start_date . ' 00:00:00',
            $end_date . ' 23:59:59'
        );
        return $wpdb->get_var($query);
    }
    

    //Función para obtener la tasa de cancelación de órdenes
    function get_order_cancellation_rate($start_date, $end_date) {
        // Definir el periodo sobre el cual se calcularán las estadísticas
        $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), new DateTime($end_date));
    
        // Inicializar contadores
        $total_orders = 0;
        $cancelled_orders = 0;
    
        // Iterar sobre cada día en el periodo
        foreach ($period as $date) {
            $date_formatted = $date->format('Y-m-d');
    
            // Parámetros para pedidos completados
            $params_completed = [
                'limit'        => -1,
                'return'       => 'ids',
                'status'       => 'completed',
                'date_created' => $date_formatted,
            ];
            // Recuperar pedidos completados
            $completed_orders = wc_get_orders($params_completed);
            $total_orders += count($completed_orders);
    
            // Parámetros para pedidos cancelados
            $params_cancelled = [
                'limit'        => -1,
                'return'       => 'ids',
                'status'       => 'cancelled',
                'date_created' => $date_formatted,
            ];
            // Recuperar pedidos cancelados
            $cancelled_orders_list = wc_get_orders($params_cancelled);
            $cancelled_orders += count($cancelled_orders_list);
        }
    
        // Calcular la tasa de cancelación
        if ($total_orders > 0) {
            $cancellation_rate = ($cancelled_orders / $total_orders) * 100;
        } else {
            $cancellation_rate = 0; // Evitar división por cero si no hay pedidos
        }
    
        return $cancellation_rate;
    }  
    
    function wc_recom_analyze_data($metrics) {
        $recommendations = [];
    
        $this->analyze_cart_abandonment_rate($metrics['cart_abandonment_rate'], $recommendations);
        $this->analyze_retention_rate($metrics['retention_rate'], $recommendations);
        $this->analyze_order_cancellation_rate($metrics['order_cancellation_rate'], $recommendations);
        $this->analyze_total_sales($metrics['total_sales'], $recommendations);
        $this->analyze_average_order_value($metrics['average_order_value'], $recommendations);
        $this->analyze_customer_lifetime_value($metrics['customer_lifetime_value'], $recommendations);
        $this->analyze_total_customers($metrics['total_customers'], $recommendations);
    
        return $recommendations;
    }
    
    function analyze_cart_abandonment_rate($rate, &$recommendations) {
        if ($rate > 75) {
            $recommendations[] = ["Revisa urgentemente el proceso de checkout; considera implementar un sistema de pago más rápido y sin fricciones.","bad"];
        } elseif ($rate > 50) {
            $recommendations[] = ["Optimiza la página de checkout y considera ofrecer incentivos como descuentos o envío gratis al primer signo de abandono.","warning"];
        } elseif ($rate > 30) {
            $recommendations[] = ["Implementa recordatorios por correo electrónico para recuperar carritos abandonados.","medium"];
        } else {
            $recommendations[] = ["Tu tasa de abandono de carritos es excepcionalmente baja, ¡mantén las buenas prácticas!","good"];
        }
    }
    
    function analyze_retention_rate($rate, &$recommendations) {
        if ($rate < 10) {
            $recommendations[] = ["Urgente mejorar la retención: considera programas intensivos de lealtad y revisa la calidad de servicio al cliente.","bad"];
        } elseif ($rate < 30) {
            $recommendations[] = ["Implementa un programa de puntos o beneficios para aumentar la retención.","warning"];
        } elseif ($rate < 50) {
            $recommendations[] = ["Mejora el engagement a través de contenido personalizado y ofertas basadas en el comportamiento de compra previo.","medium"];
        } else {
            $recommendations[] = ["Excelente tasa de retención de clientes, sigue optimizando y personalizando la experiencia del usuario.","good"];
        }
    }
    
    function analyze_order_cancellation_rate($rate, &$recommendations) {
        if ($rate > 10) {
            $recommendations[] = ["Revisa las políticas de cancelación y la claridad en la información de los productos para reducir las cancelaciones.","bad"];
        } elseif ($rate > 5) {
            $recommendations[] = ["Mejora los procesos logísticos para asegurar tiempos de entrega rápidos y precisos, reduciendo así las cancelaciones.","warning"];
        } else {
            $recommendations[] = ["Tu tasa de cancelación de órdenes es baja, lo cual es ideal. Asegúrate de mantener altos estándares en la calidad del servicio.","good"];
        }
    }
    
    function analyze_total_sales($sales, &$recommendations) {
        if ($sales < 5000) {
            $recommendations[] = ["Estrategias agresivas de marketing digital son necesarias para aumentar las ventas.","bad"];
        } elseif ($sales < 20000) {
            $recommendations[] = ["Explora nuevas verticales de mercado y amplía el catálogo de productos para mejorar las ventas.","warning"];
        } elseif ($sales < 50000) {
            $recommendations[] = ["Optimiza las campañas de marketing existentes y mejora la conversión de la tienda en línea.","medium"];
        } else {
            $recommendations[] = ["Estás logrando excelentes ventas totales, considera expandir aún más o diversificar la oferta de productos.","good"];
        }
    }
    
    function analyze_average_order_value($aov, &$recommendations) {
        if ($aov < 50) {
            $recommendations[] = ["Implementa estrategias de upselling y cross-selling para aumentar el AOV.","bad"];
        } elseif ($aov < 100) {
            $recommendations[] = ["Crea paquetes de productos para mejorar el valor del pedido promedio.","warning"];
        } else {
            $recommendations[] = ["Tu AOV es impresionante. Considera mantener tus estrategias actuales y explorar nuevas oportunidades para productos premium.","good"];
        }
    }
    
    function analyze_customer_lifetime_value($clv, &$recommendations) {
        if ($clv < 100) {
            $recommendations[] = ["Estrategias dirigidas a mejorar la retención pueden incrementar el CLV significativamente.","bad"];
        } elseif ($clv < 300) {
            $recommendations[] = ["Considera la personalización y la mejora de la experiencia de usuario para aumentar el CLV.","warning"];
        } else {
            $recommendations[] = ["Tu CLV es excelente. Continúa fomentando la lealtad del cliente y considera programas exclusivos para clientes de alto valor.","good"];
        }
    }
    
    function analyze_total_customers($total, &$recommendations) {
        if ($total < 100) {
            $recommendations[] = ["Es crítico incrementar la base de clientes: considera campañas de marketing online y offline.","bad"];
        } elseif ($total < 500) {
            $recommendations[] = ["Implementa estrategias para convertir visitantes ocasionales en compradores recurrentes.","warning"];
        } else {
            $recommendations[] = ["Tienes una sólida base de clientes. Considera estrategias para aumentar aún más la lealtad y el valor de cada cliente.","good"];
        }
    }
}
?>
