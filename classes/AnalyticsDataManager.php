<?php
defined( 'ABSPATH' ) or die( '¡Sin acceso directo, por favor!' );

class AnalyticsDataManager {
    public function __construct() {
        
    }

    /**
     * Obtiene los ingresos diarios entre dos fechas utilizando WC_Order_Query.
     */
    public function get_daily_revenue($start_date, $end_date) {
        $data = [];
        $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), new DateTime($end_date));

        foreach ($period as $date) {
            $date_formatted = $date->format('Y-m-d');

            $args = [
                'limit'        => -1,
                'return'       => 'ids',
                'status'       => 'completed',
                'date_created' => $date_formatted,
            ];
            $orders = wc_get_orders($args);
            $total_revenue = 0;

            foreach ($orders as $order_id) {
                $order = wc_get_order($order_id);
                $total_revenue += $order->get_total();
            }

            $data[$date_formatted] = $total_revenue;
        }
        return $data;
    }

    /**
     * Obtiene el número de órdenes completadas entre dos fechas.
     */
    public function get_daily_completed_orders($start_date, $end_date) {
        $data = [];
        $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), new DateTime($end_date));

        foreach ($period as $date) {
            $date_formatted = $date->format('Y-m-d');

            $args = [
                'limit'        => -1,
                'return'       => 'ids',
                'status'       => 'completed',
                'date_created' => $date_formatted,
            ];
            $orders_count = count(wc_get_orders($args));

            $data[$date_formatted] = $orders_count;
        }
        return $data;
    }

    /**
     * Calcula el valor promedio del pedido entre dos fechas.
     */
    public function get_daily_aov($start_date, $end_date) {
        $revenues = $this->get_daily_revenue($start_date, $end_date);
        $orders = $this->get_daily_completed_orders($start_date, $end_date);
        $aov = [];

        foreach ($revenues as $date => $total_revenue) {
            $order_count = $orders[$date] ?? 0;
            $aov[$date] = $order_count > 0 ? round($total_revenue / $order_count, 2) : 0;
        }

        return $aov;
    }
}

?>
