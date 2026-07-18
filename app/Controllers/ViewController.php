<?php
namespace App\Controllers;

use App\Core\Controller;

/**
 * Fix&Go — ViewController
 *
 * Serves all user dashboard views.
 * Checks authentication + role before rendering.
 * Views live in app/Views/user/{role}/{page}.php
 */
class ViewController extends Controller
{
    /**
     * Serve a view for a given role.
     * Redirects to /login.html if not authenticated.
     * Returns 403 if role doesn't match.
     */
    public function serve(string $role, string $page): void
    {
        // Auth check
        if (empty($_SESSION['user_id'])) {
            header('Location: /login.html');
            exit;
        }

        $userRole = $_SESSION['user_role'] ?? '';

        // Role check — admins can see everything
        if ($userRole !== $role && $userRole !== 'admin') {
            http_response_code(403);
            echo '<html><body style="font-family:sans-serif;padding:2rem;">
                    <h2>Access Denied</h2>
                    <p>You do not have permission to view this page.</p>
                    <a href="/dashboard.php">← Back to Dashboard</a>
                  </body></html>';
            exit;
        }

        $viewFile = APP_DIR . '/Views/user/' . $role . '/' . $page . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(404);
            echo '<html><body style="font-family:sans-serif;padding:2rem;">
                    <h2>404 — Page Not Found</h2>
                    <a href="/dashboard.php">← Back to Dashboard</a>
                  </body></html>';
            exit;
        }

        require $viewFile;
    }

    // ── Customer views ────────────────────────────────────────────────
    public function customerDashboard(): void        { $this->serve('customer', 'dashboard'); }
    public function customerOrders(): void           { $this->serve('customer', 'orders'); }
    public function customerRepairs(): void          { $this->serve('customer', 'repairs'); }
    public function customerMessages(): void         { $this->serve('customer', 'messages'); }
    public function customerNotifications(): void    { $this->serve('customer', 'notifications'); }
    public function customerProfile(): void          { $this->serve('customer', 'profile'); }
    public function customerSettings(): void         { $this->serve('customer', 'settings'); }
    public function customerWishlist(): void         { $this->serve('customer', 'wishlist'); }
    public function customerVouchers(): void         { $this->serve('customer', 'vouchers'); }
    public function customerCheckout(): void         { $this->serve('customer', 'checkout'); }
    public function customerSellerCentre(): void     { $this->serve('customer', 'seller-centre'); }
    public function customerBecomeTechnician(): void { $this->serve('customer', 'become-technician'); }

    // ── Owner views ───────────────────────────────────────────────────
    public function ownerDashboard(): void           { $this->serve('owner', 'dashboard'); }
    public function ownerProducts(): void            { $this->serve('owner', 'products'); }
    public function ownerInventory(): void           { $this->serve('owner', 'inventory'); }
    public function ownerOrders(): void              { $this->serve('owner', 'orders'); }
    public function ownerStaff(): void               { $this->serve('owner', 'staff'); }
    public function ownerMessages(): void            { $this->serve('owner', 'messages'); }
    public function ownerProfile(): void             { $this->serve('owner', 'profile'); }
    public function ownerSettings(): void            { $this->serve('owner', 'settings'); }
    public function ownerSalesReport(): void         { $this->serve('owner', 'sales-report'); }
    public function ownerSupervisorReports(): void   { $this->serve('owner', 'supervisor-reports'); }
    public function ownerCart(): void                { $this->serve('owner', 'cart'); }
    public function ownerCheckout(): void            { $this->serve('owner', 'checkout'); }
    public function ownerDeliveries(): void          { $this->serve('owner', 'deliveries'); }
    public function ownerTechOrders(): void          { $this->serve('owner', 'tech-orders'); }

    // ── Supplier views ────────────────────────────────────────────────
    public function supplierDashboard(): void        { $this->serve('supplier', 'dashboard'); }
    public function supplierProducts(): void         { $this->serve('supplier', 'products'); }
    public function supplierOrders(): void           { $this->serve('supplier', 'orders'); }
    public function supplierMessages(): void         { $this->serve('supplier', 'messages'); }
    public function supplierProfile(): void          { $this->serve('supplier', 'profile'); }
    public function supplierDeliveries(): void       { $this->serve('supplier', 'deliveries'); }
    public function supplierSalesReport(): void      { $this->serve('supplier', 'sales-report'); }
    public function supplierOwnerPurchases(): void   { $this->serve('supplier', 'owner-purchases'); }
    public function supplierTechOrders(): void       { $this->serve('supplier', 'tech-orders'); }
    public function supplierTechRequests(): void     { $this->serve('supplier', 'tech-requests'); }

    // ── Sales Person views ────────────────────────────────────────────
    public function salesDashboard(): void           { $this->serve('sales_person', 'dashboard'); }
    public function salesProducts(): void            { $this->serve('sales_person', 'products'); }
    public function salesOrders(): void              { $this->serve('sales_person', 'orders'); }
    public function salesInventory(): void           { $this->serve('sales_person', 'inventory'); }
    public function salesMessages(): void            { $this->serve('sales_person', 'messages'); }
    public function salesProfile(): void             { $this->serve('sales_person', 'profile'); }
    public function salesSettings(): void            { $this->serve('sales_person', 'settings'); }
    public function salesSupplyRequests(): void      { $this->serve('sales_person', 'supply-requests'); }

    // ── Supervisor views ──────────────────────────────────────────────
    public function supervisorDashboard(): void      { $this->serve('supervisor', 'dashboard'); }
    public function supervisorInventory(): void      { $this->serve('supervisor', 'inventory'); }
    public function supervisorReports(): void        { $this->serve('supervisor', 'reports'); }
    public function supervisorMessages(): void       { $this->serve('supervisor', 'messages'); }
    public function supervisorProfile(): void        { $this->serve('supervisor', 'profile'); }

    // ── Phone Technician views ────────────────────────────────────────
    public function technicianDashboard(): void      { $this->serve('phone_technician', 'dashboard'); }
    public function technicianInventory(): void      { $this->serve('phone_technician', 'inventory'); }
    public function technicianMarketplace(): void    { $this->serve('phone_technician', 'marketplace'); }
    public function technicianMessages(): void       { $this->serve('phone_technician', 'messages'); }
    public function technicianProducts(): void       { $this->serve('phone_technician', 'products'); }
    public function technicianProfile(): void        { $this->serve('phone_technician', 'profile'); }
    public function technicianRepairs(): void        { $this->serve('phone_technician', 'repairs'); }
    public function technicianSupplyRequests(): void { $this->serve('phone_technician', 'supply-requests'); }
}
