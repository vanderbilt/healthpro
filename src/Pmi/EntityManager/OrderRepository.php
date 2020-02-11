<?php

namespace Pmi\EntityManager;

use Pmi\Order\Order;

class OrderRepository extends DoctrineRepository
{
    public function getParticipantOrdersWithHistory($participantId)
    {
        $ordersQuery = "
            SELECT o.*,
                   oh.order_id AS oh_order_id,
                   oh.user_id AS oh_user_id,
                   oh.site AS oh_site,
                   oh.type AS oh_type,
                   oh.created_ts AS oh_created_ts
            FROM orders o
            LEFT JOIN orders_history oh ON o.history_id = oh.id
            WHERE o.participant_id = :participantId
            ORDER BY o.id DESC
        ";
        return $this->dbal->fetchAll($ordersQuery, [
            'participantId' => $participantId
        ]);
    }

    public function getUnfinalizedOrders()
    {
        $ordersQuery = "
            SELECT o.*,
                   oh.order_id AS oh_order_id,
                   oh.user_id AS oh_user_id,
                   oh.site AS oh_site,
                   oh.type AS oh_type,
                   oh.created_ts AS oh_created_ts,
                   s.name as created_site_name,
                   sc.name as collected_site_name,
                   sp.name as processed_site_name,
                   sf.name as finalized_site_name
            FROM orders o
            LEFT JOIN orders_history oh ON o.history_id = oh.id
            LEFT JOIN sites s ON s.site_id = o.site
            LEFT JOIN sites sc ON sc.site_id = o.collected_site
            LEFT JOIN sites sp ON sp.site_id = o.processed_site
            LEFT JOIN sites sf ON sf.site_id = o.finalized_site
            WHERE o.finalized_ts IS NULL
              AND (oh.type != :type
              OR oh.type IS NULL)
            ORDER BY o.created_ts DESC
        ";
        return $this->dbal->fetchAll($ordersQuery, [
            'type' => Order::ORDER_CANCEL
        ]);
    }

    public function getSiteUnfinalizedOrders($siteId)
    {
        $ordersQuery = "
            SELECT o.*,
                   oh.order_id AS oh_order_id,
                   oh.user_id AS oh_user_id,
                   oh.site AS oh_site,
                   oh.type AS oh_type,
                   oh.created_ts AS oh_created_ts
            FROM orders o
            LEFT JOIN orders_history oh ON o.history_id = oh.id
            WHERE o.site = :site
              AND o.finalized_ts IS NULL
              AND (oh.type != :type
              OR oh.type IS NULL)
            ORDER BY o.created_ts DESC
        ";
        return $this->dbal->fetchAll($ordersQuery, [
            'site' => $siteId,
            'type' => Order::ORDER_CANCEL
        ]);
    }

    public function getUnlockedOrders()
    {
        $ordersQuery = "
            SELECT o.*,
                   oh.order_id AS oh_order_id,
                   oh.user_id AS oh_user_id,
                   oh.site AS oh_site,
                   oh.type AS oh_type,
                   oh.created_ts AS oh_created_ts,
                   s.name as created_site_name,
                   sc.name as collected_site_name,
                   sp.name as processed_site_name,
                   sf.name as finalized_site_name
            FROM orders o
            INNER JOIN orders_history oh ON o.history_id = oh.id
            LEFT JOIN sites s ON s.site_id = o.site
            LEFT JOIN sites sc ON sc.site_id = o.collected_site
            LEFT JOIN sites sp ON sp.site_id = o.processed_site
            LEFT JOIN sites sf ON sf.site_id = o.finalized_site
            WHERE oh.type = :type
            ORDER BY o.created_ts DESC
        ";
        return $this->dbal->fetchAll($ordersQuery, [
            'type' => Order::ORDER_UNLOCK
        ]);
    }


    public function getSiteUnlockedOrders($siteId)
    {
        $ordersQuery = "
            SELECT o.*,
                   oh.order_id AS oh_order_id,
                   oh.user_id AS oh_user_id,
                   oh.site AS oh_site,
                   oh.type AS oh_type,
                   oh.created_ts AS oh_created_ts
            FROM orders o
            INNER JOIN orders_history oh ON o.history_id = oh.id
            WHERE o.site = :site
              AND oh.type = :type
            ORDER BY o.created_ts DESC
        ";
        return $this->dbal->fetchAll($ordersQuery, [
            'site' => $siteId,
            'type' => Order::ORDER_UNLOCK
        ]);
    }

    public function getRecentModifiedOrders()
    {
        $ordersQuery = "
            SELECT o.*,
                   oh.order_id AS oh_order_id,
                   oh.user_id AS oh_user_id,
                   oh.site AS oh_site,
                   oh.type AS oh_type,
                   oh.created_ts AS oh_created_ts,
                   s.name as created_site_name,
                   sc.name as collected_site_name,
                   sp.name as processed_site_name,
                   sf.name as finalized_site_name
            FROM orders o
            INNER JOIN orders_history oh ON o.history_id = oh.id
            LEFT JOIN sites s ON s.site_id = o.site
            LEFT JOIN sites sc ON sc.site_id = o.collected_site
            LEFT JOIN sites sp ON sp.site_id = o.processed_site
            LEFT JOIN sites sf ON sf.site_id = o.finalized_site
            WHERE oh.type != :type1
              AND oh.type != :type2
              AND oh.created_ts >= UTC_TIMESTAMP() - INTERVAL 7 DAY
            ORDER BY oh.created_ts DESC
        ";
        return $this->dbal->fetchAll($ordersQuery, [
            'type1' => Order::ORDER_ACTIVE,
            'type2' => Order::ORDER_RESTORE
        ]);
    }

    public function getSiteRecentModifiedOrders($siteId)
    {
        $ordersQuery = "
            SELECT o.*,
                   oh.order_id AS oh_order_id,
                   oh.user_id AS oh_user_id,
                   oh.site AS oh_site,
                   oh.type AS oh_type,
                   oh.created_ts AS oh_created_ts
            FROM orders o
            INNER JOIN orders_history oh ON o.history_id = oh.id
            WHERE o.site = :site
              AND oh.type != :type1
              AND oh.type != :type2
              AND oh.created_ts >= UTC_TIMESTAMP() - INTERVAL 7 DAY
            ORDER BY oh.created_ts DESC
        ";
        return $this->dbal->fetchAll($ordersQuery, [
            'site' => $siteId,
            'type1' => Order::ORDER_ACTIVE,
            'type2' => Order::ORDER_RESTORE
        ]);
    }
}
