<?php
namespace App\Controllers\Api;

use App\Core\Controller;

/**
 * LegacyController — bridges MVC routing to existing backend PHP files.
 *
 * During the MVC transition, this controller delegates requests for
 * endpoints not yet refactored into proper MVC controllers to the
 * original backend files. This keeps the system fully functional
 * while the migration progresses incrementally.
 */
class LegacyController extends Controller
{
    /**
     * Delegate to a legacy backend file.
     * The file runs in its own scope so it can use $pdo, require helpers, etc.
     */
    public function delegate(string $file): void
    {
        // In deploy: backend/ is at htdocs root (APP_ROOT/backend/)
        // In source: fixandgo/backend/ — try both locations
        $path = APP_ROOT . '/backend/' . $file . '.php';
        if (!file_exists($path)) {
            $path = APP_ROOT . '/fixandgo/backend/' . $file . '.php';
        }
        if (!file_exists($path)) {
            $this->json(false, 'Endpoint not found: ' . $file, [], 404);
        }
        include $path;
    }

    // Convenience methods for each legacy endpoint
    public function shopProducts(): void         { $this->delegate('shop_products'); }
    public function marketplaceProducts(): void  { $this->delegate('marketplace_products'); }
    public function marketplaceTechnicians(): void { $this->delegate('marketplace_technicians'); }
    public function technicians(): void          { $this->delegate('technicians'); }
    public function repairBookings(): void       { $this->delegate('repair_bookings'); }
    public function repairPayment(): void        { $this->delegate('repair_payment'); }
    public function repairPaymentReturn(): void  { $this->delegate('repair_payment_return'); }
    public function customerOrders(): void       { $this->delegate('customer_orders'); }
    public function customerPaymongo(): void     { $this->delegate('customer_paymongo'); }
    public function customerPaymentReturn(): void { $this->delegate('customer_payment_return'); }
    public function customerProfile(): void      { $this->delegate('customer_profile'); }
    public function ownerProducts(): void        { $this->delegate('owner_products'); }
    public function ownerInventory(): void       { $this->delegate('owner_inventory'); }
    public function ownerShopProducts(): void    { $this->delegate('owner_shop_products'); }
    public function ownerStaff(): void           { $this->delegate('owner_staff'); }
    public function ownerSupervisorReports(): void { $this->delegate('owner_supervisor_reports'); }
    public function supplierProducts(): void     { $this->delegate('supplier_products'); }
    public function supplierOrders(): void       { $this->delegate('supplier_orders'); }
    public function supplierSales(): void        { $this->delegate('supplier_sales'); }
    public function supplierShopView(): void     { $this->delegate('supplier_shop_view'); }
    public function supplierTechRequests(): void { $this->delegate('supplier_tech_requests'); }
    public function salesProducts(): void        { $this->delegate('sales_products'); }
    public function salesOrders(): void          { $this->delegate('sales_orders'); }
    public function salesSupplyRequests(): void  { $this->delegate('sales_supply_requests'); }
    public function salesInventory(): void       { $this->delegate('sales_inventory'); }
    public function supervisorReports(): void    { $this->delegate('supervisor_reports'); }
    public function supervisorInventory(): void  { $this->delegate('supervisor_inventory'); }
    public function technicianDashboard(): void  { $this->delegate('technician_dashboard'); }
    public function technicianOrders(): void     { $this->delegate('technician_orders'); }
    public function technicianMarketplace(): void { $this->delegate('technician_marketplace'); }
    public function technicianCredentials(): void { $this->delegate('technician_credentials'); }
    public function technicianApply(): void      { $this->delegate('technician_apply'); }
    public function technicianPaymentReturn(): void { $this->delegate('technician_payment_return'); }
    public function sellerApply(): void          { $this->delegate('seller_apply'); }
    public function sellerTechOrders(): void     { $this->delegate('seller_tech_orders'); }
    public function switchToSeller(): void       { $this->delegate('switch_to_seller'); }
    public function productTransfers(): void     { $this->delegate('product_transfers'); }
    public function documentApprovals(): void    { $this->delegate('document_approvals'); }
    public function messages(): void             { $this->delegate('messages'); }
    public function reviews(): void              { $this->delegate('reviews'); }
    public function profile(): void              { $this->delegate('profile'); }
    public function paymongo(): void             { $this->delegate('paymongo'); }
    public function paymongoReturn(): void       { $this->delegate('paymongo_return'); }
    public function paymongoWebhook(): void      { $this->delegate('paymongo_webhook'); }
    public function mapsConfig(): void           { $this->delegate('maps-config'); }
    public function admin(): void                { $this->delegate('admin'); }
    public function unlockAccount(): void        { $this->delegate('unlock_account'); }
    public function googleAuthInit(): void       { $this->delegate('google-auth-init'); }
    public function googleCallback(): void       { $this->delegate('google-callback'); }
}
