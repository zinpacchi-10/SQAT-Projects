<?php
require_once APP_ROOT . '/app/core/Controller.php';

class DeliveryController extends Controller
{
    private DeliveryAgentModel $agentModel;
    private DeliveryZoneModel $zoneModel;
    private DeliveryAssignmentModel $assignmentModel;
    private OrderModel $orderModel;
    private NotificationModel $notificationModel;
    private UserModel $userModel;

    public function __construct()
    {
        $this->agentModel = new DeliveryAgentModel();
        $this->zoneModel = new DeliveryZoneModel();
        $this->assignmentModel = new DeliveryAssignmentModel();
        $this->orderModel = new OrderModel();
        $this->notificationModel = new NotificationModel();
        $this->userModel = new UserModel();
        $this->requireRole('delivery_manager');
    }

    public function dashboard(): void
    {
        $pendingDispatch = count($this->orderModel->readyForDispatch());
        $activeDeliveries = count($this->assignmentModel->active());
        $deliveredToday = $this->assignmentModel->deliveredToday();
        $recentActive = array_slice($this->assignmentModel->active(), 0, 6);
        $this->view('delivery/dashboard', compact('pendingDispatch', 'activeDeliveries', 'deliveredToday', 'recentActive'));
    }

    // ---------------- Agents ----------------
    public function agents(): void
    {
        $items = $this->agentModel->all();
        $this->view('delivery/agents', compact('items'));
    }

    public function addAgent(): void
    {
        $name = $this->input('name');
        $vehicle = $this->input('vehicle_type');
        $phone = $this->input('phone');

        $v = new Validator();
        $v->required($name, 'Name')->required($vehicle, 'Vehicle type')->required($phone, 'Phone');

        if ($v->fails()) {
            $this->setFlash('error', implode(' ', $v->errors()));
        } else {
            $this->agentModel->create($name, $vehicle, $phone);
            $this->setFlash('success', 'Delivery agent added.');
        }
        $this->redirect('delivery/agents');
    }

    public function editAgent(int $id): void
    {
        if ($this->isPost()) {
            $this->agentModel->update($id, $this->input('name'), $this->input('vehicle_type'), $this->input('phone'));
            $this->setFlash('success', 'Agent details updated.');
        }
        $this->redirect('delivery/agents');
    }

    public function toggleAgent(int $id): void
    {
        $this->agentModel->toggleActive($id);
        $this->setFlash('success', 'Agent status updated.');
        $this->redirect('delivery/agents');
    }

    // ---------------- Zones ----------------
    public function zones(): void
    {
        $items = $this->zoneModel->all();
        $this->view('delivery/zones', compact('items'));
    }

    public function addZone(): void
    {
        $name = $this->input('zone_name');
        $fee = $this->input('delivery_fee');
        $days = $this->input('estimated_days');

        $v = new Validator();
        $v->required($name, 'Zone name')->required($fee, 'Delivery fee')->numeric($fee, 'Delivery fee')->min($fee, 0, 'Delivery fee')
          ->required($days, 'Estimated days')->numeric($days, 'Estimated days')->min($days, 1, 'Estimated days');

        if ($v->fails()) {
            $this->setFlash('error', implode(' ', $v->errors()));
        } else {
            $this->zoneModel->create($name, (float)$fee, (int)$days);
            $this->setFlash('success', 'Delivery zone added.');
        }
        $this->redirect('delivery/zones');
    }

    public function editZone(int $id): void
    {
        if ($this->isPost()) {
            $this->zoneModel->update($id, $this->input('zone_name'), (float)$this->input('delivery_fee'), (int)$this->input('estimated_days'));
            $this->setFlash('success', 'Zone updated.');
        }
        $this->redirect('delivery/zones');
    }

    public function deleteZone(int $id): void
    {
        $ok = $this->zoneModel->delete($id);
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Zone deleted.' : 'Cannot delete: zone has existing orders.');
        $this->redirect('delivery/zones');
    }

    // ---------------- Dispatch ----------------
    public function dispatch(): void
    {
        $zoneFilter = $this->input('zone_id', '');
        $orders = $this->orderModel->readyForDispatch($zoneFilter);
        $zones = $this->zoneModel->all();
        $agents = $this->agentModel->activeAgents();
        $this->view('delivery/dispatch', compact('orders', 'zones', 'agents', 'zoneFilter'));
    }

    /** AJAX: assign an available agent to an order */
    public function assignAgent(): void
    {
        $orderId = (int)$this->input('order_id');
        $agentId = (int)$this->input('agent_id');
        $agent = $this->agentModel->find($agentId);
        $order = $this->orderModel->find($orderId);

        if (!$agent || !$order) {
            $this->json(['success' => false, 'message' => 'Order or agent not found.'], 404);
        }
        if (!$agent['is_active']) {
            $this->json(['success' => false, 'message' => 'Cannot assign an inactive delivery agent.'], 400);
        }
        $this->assignmentModel->create($orderId, $agentId, $order['zone_name']);
        $this->orderModel->updateStatus($orderId, 'shipped');
        $this->notificationModel->create($order['customer_id'], 'Your order #' . $orderId . ' has been dispatched with agent ' . $agent['name'] . '.');
        $this->json(['success' => true, 'message' => 'Agent ' . $agent['name'] . ' assigned to order #' . $orderId . '.']);
    }

    // ---------------- Active deliveries ----------------
    public function active(): void
    {
        $items = $this->assignmentModel->active();
        $this->view('delivery/active', compact('items'));
    }

    public function updateDeliveryStatus(): void
    {
        $id = (int)$this->input('assignment_id');
        $status = $this->input('status');
        $allowed = ['picked_up', 'in_transit', 'delivered', 'failed'];
        $assignment = $this->assignmentModel->find($id);

        if (!$assignment || !in_array($status, $allowed, true)) {
            $this->json(['success' => false, 'message' => 'Invalid request.'], 400);
        }

        $reason = $status === 'failed' ? $this->input('reason', 'Not specified') : null;
        $this->assignmentModel->updateStatus($id, $status, $reason);

        if ($status === 'delivered') {
            $this->orderModel->updateStatus($assignment['order_id'], 'delivered');
            $this->orderModel->markAllItemsStatus($assignment['order_id'], 'delivered');
            $this->notificationModel->create($assignment['customer_id'], 'Your order #' . $assignment['order_id'] . ' has been delivered!');
        } elseif ($status === 'failed') {
            $this->notificationModel->create($assignment['customer_id'], 'Delivery attempt failed for order #' . $assignment['order_id'] . '. Reason: ' . $reason);
        }

        $this->json(['success' => true, 'message' => 'Delivery status updated to ' . str_replace('_', ' ', $status) . '.']);
    }

    public function reassign(): void
    {
        $assignmentId = (int)$this->input('assignment_id');
        $newAgentId = (int)$this->input('agent_id');
        $old = $this->assignmentModel->find($assignmentId);
        $agent = $this->agentModel->find($newAgentId);

        if (!$old || !$agent || !$agent['is_active']) {
            $this->setFlash('error', 'Unable to reassign: invalid or inactive agent.');
            $this->redirect('delivery/history');
            return;
        }
        $this->assignmentModel->create($old['order_id'], $newAgentId, $old['delivery_zone']);
        $this->orderModel->updateStatus($old['order_id'], 'shipped');
        $this->notificationModel->create($old['customer_id'], 'Your order #' . $old['order_id'] . ' has been reassigned to a new delivery agent.');
        $this->setFlash('success', 'Delivery reassigned to ' . $agent['name'] . '.');
        $this->redirect('delivery/active');
    }

    // ---------------- History & reports ----------------
    public function history(): void
    {
        $items = $this->assignmentModel->history();
        $agents = $this->agentModel->activeAgents();
        $this->view('delivery/history', compact('items', 'agents'));
    }

    public function reports(): void
    {
        $agentPerformance = $this->agentModel->performanceReport();
        $zonePerformance = $this->assignmentModel->zonePerformance();
        $daily = $this->assignmentModel->summary('day');
        $weekly = $this->assignmentModel->summary('week');
        $this->view('delivery/reports', compact('agentPerformance', 'zonePerformance', 'daily', 'weekly'));
    }

    public function profile(): void
    {
        $user = $this->userModel->findById($this->currentUser()['id']);
        $this->view('delivery/profile', compact('user'));
    }

    public function updateProfile(): void
    {
        $uid = $this->currentUser()['id'];
        $name = $this->input('name');
        $phone = $this->input('phone');
        if (trim($name) !== '') {
            $this->userModel->updateProfile($uid, $name, $phone);
            $_SESSION['user']['name'] = $name;
            $this->setFlash('success', 'Profile updated.');
        }
        $this->redirect('delivery/profile');
    }
}
