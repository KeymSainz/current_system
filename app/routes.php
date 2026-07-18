<?php
/**
 * Fix&Go — Route Definitions
 * All application routes are registered here.
 *
 * Pattern:
 *   $router->get('/path',  'Namespace\ClassName@method');
 *   $router->post('/path', 'Namespace\ClassName@method');
 *   $router->any('/path',  'Namespace\ClassName@method');
 *
 * Controller namespace root: App\Controllers\
 */

use App\Core\Router;

$router = new Router();

// ── Auth ──────────────────────────────────────────────────────────────────
$router->post('/api/login',              'Auth\LoginController@login');
$router->post('/api/logout',             'Auth\LoginController@logout');
$router->post('/api/register',           'Auth\RegisterController@register');
$router->post('/api/password/reset',     'Auth\PasswordController@reset');

// ── Session ───────────────────────────────────────────────────────────────
$router->get('/api/session/user',        'Api\SessionController@user');
$router->any('/api/session/ping',        'Api\SessionController@ping');
$router->get('/api/session/csrf',        'Api\SessionController@csrf');

// ── Notifications ─────────────────────────────────────────────────────────
$router->any('/api/notifications',       'Api\NotificationController@handle');

// ── OAuth ─────────────────────────────────────────────────────────────────
$router->get('/api/auth/google',         'Api\LegacyController@googleAuthInit');
$router->get('/api/auth/google/callback','Api\LegacyController@googleCallback');

// ── Shop / Marketplace (public) ───────────────────────────────────────────
$router->any('/api/shop/products',       'Api\LegacyController@shopProducts');
$router->any('/api/marketplace/products','Api\LegacyController@marketplaceProducts');
$router->any('/api/marketplace/technicians','Api\LegacyController@marketplaceTechnicians');
$router->any('/api/technicians',         'Api\LegacyController@technicians');

// ── Repair bookings ───────────────────────────────────────────────────────
$router->any('/api/repair/bookings',     'Api\LegacyController@repairBookings');
$router->any('/api/repair/payment',      'Api\LegacyController@repairPayment');
$router->get('/api/repair/payment/return','Api\LegacyController@repairPaymentReturn');

// ── Customer ─────────────────────────────────────────────────────────────
$router->any('/api/customer/orders',     'Api\LegacyController@customerOrders');
$router->post('/api/customer/paymongo',  'Api\LegacyController@customerPaymongo');
$router->get('/api/customer/payment/return','Api\LegacyController@customerPaymentReturn');
$router->any('/api/customer/profile',    'Api\LegacyController@customerProfile');

// ── Owner ─────────────────────────────────────────────────────────────────
$router->any('/api/owner/products',      'Api\LegacyController@ownerProducts');
$router->any('/api/owner/inventory',     'Api\LegacyController@ownerInventory');
$router->any('/api/owner/shop-products', 'Api\LegacyController@ownerShopProducts');
$router->any('/api/owner/staff',         'Api\LegacyController@ownerStaff');
$router->any('/api/owner/supervisor-reports','Api\LegacyController@ownerSupervisorReports');

// ── Supplier ─────────────────────────────────────────────────────────────
$router->any('/api/supplier/products',   'Api\LegacyController@supplierProducts');
$router->any('/api/supplier/orders',     'Api\LegacyController@supplierOrders');
$router->any('/api/supplier/sales',      'Api\LegacyController@supplierSales');
$router->any('/api/supplier/shop-view',  'Api\LegacyController@supplierShopView');
$router->any('/api/supplier/tech-requests','Api\LegacyController@supplierTechRequests');

// ── Sales Person ──────────────────────────────────────────────────────────
$router->any('/api/sales/products',      'Api\LegacyController@salesProducts');
$router->any('/api/sales/orders',        'Api\LegacyController@salesOrders');
$router->any('/api/sales/supply-requests','Api\LegacyController@salesSupplyRequests');
$router->any('/api/sales/inventory',     'Api\LegacyController@salesInventory');

// ── Supervisor ────────────────────────────────────────────────────────────
$router->any('/api/supervisor/reports',  'Api\LegacyController@supervisorReports');
$router->any('/api/supervisor/inventory','Api\LegacyController@supervisorInventory');

// ── Technician ────────────────────────────────────────────────────────────
$router->any('/api/technician/dashboard','Api\LegacyController@technicianDashboard');
$router->any('/api/technician/orders',   'Api\LegacyController@technicianOrders');
$router->any('/api/technician/marketplace','Api\LegacyController@technicianMarketplace');
$router->any('/api/technician/credentials','Api\LegacyController@technicianCredentials');
$router->any('/api/technician/apply',    'Api\LegacyController@technicianApply');
$router->get('/api/technician/payment/return','Api\LegacyController@technicianPaymentReturn');

// ── Shared / Misc ─────────────────────────────────────────────────────────
$router->any('/api/messages',            'Api\LegacyController@messages');
$router->any('/api/reviews',             'Api\LegacyController@reviews');
$router->any('/api/profile',             'Api\LegacyController@profile');
$router->any('/api/product-transfers',   'Api\LegacyController@productTransfers');
$router->any('/api/document-approvals',  'Api\LegacyController@documentApprovals');
$router->any('/api/seller/apply',        'Api\LegacyController@sellerApply');
$router->any('/api/seller/tech-orders',  'Api\LegacyController@sellerTechOrders');
$router->any('/api/seller/switch',       'Api\LegacyController@switchToSeller');
$router->any('/api/paymongo',            'Api\LegacyController@paymongo');
$router->get('/api/paymongo/return',     'Api\LegacyController@paymongoReturn');
$router->post('/api/paymongo/webhook',   'Api\LegacyController@paymongoWebhook');
$router->get('/api/maps/config',         'Api\LegacyController@mapsConfig');
$router->any('/api/admin',               'Api\LegacyController@admin');
$router->any('/api/unlock-account',      'Api\LegacyController@unlockAccount');

return $router;

// ══════════════════════════════════════════════════════════════
//  VIEW ROUTES — served by ViewController (auth + role gated)
// ══════════════════════════════════════════════════════════════

// ── Customer ─────────────────────────────────────────────────
$router->get('/views/user/customer/dashboard',           'ViewController@customerDashboard');
$router->get('/views/user/customer/orders',              'ViewController@customerOrders');
$router->get('/views/user/customer/repairs',             'ViewController@customerRepairs');
$router->get('/views/user/customer/messages',            'ViewController@customerMessages');
$router->get('/views/user/customer/notifications',       'ViewController@customerNotifications');
$router->get('/views/user/customer/profile',             'ViewController@customerProfile');
$router->get('/views/user/customer/settings',            'ViewController@customerSettings');
$router->get('/views/user/customer/wishlist',            'ViewController@customerWishlist');
$router->get('/views/user/customer/vouchers',            'ViewController@customerVouchers');
$router->get('/views/user/customer/checkout',            'ViewController@customerCheckout');
$router->get('/views/user/customer/seller-centre',       'ViewController@customerSellerCentre');
$router->get('/views/user/customer/become-technician',   'ViewController@customerBecomeTechnician');

// ── Owner ────────────────────────────────────────────────────
$router->get('/views/user/owner/dashboard',              'ViewController@ownerDashboard');
$router->get('/views/user/owner/products',               'ViewController@ownerProducts');
$router->get('/views/user/owner/inventory',              'ViewController@ownerInventory');
$router->get('/views/user/owner/orders',                 'ViewController@ownerOrders');
$router->get('/views/user/owner/staff',                  'ViewController@ownerStaff');
$router->get('/views/user/owner/messages',               'ViewController@ownerMessages');
$router->get('/views/user/owner/profile',                'ViewController@ownerProfile');
$router->get('/views/user/owner/settings',               'ViewController@ownerSettings');
$router->get('/views/user/owner/sales-report',           'ViewController@ownerSalesReport');
$router->get('/views/user/owner/supervisor-reports',     'ViewController@ownerSupervisorReports');
$router->get('/views/user/owner/cart',                   'ViewController@ownerCart');
$router->get('/views/user/owner/checkout',               'ViewController@ownerCheckout');
$router->get('/views/user/owner/deliveries',             'ViewController@ownerDeliveries');
$router->get('/views/user/owner/tech-orders',            'ViewController@ownerTechOrders');

// ── Supplier ─────────────────────────────────────────────────
$router->get('/views/user/supplier/dashboard',           'ViewController@supplierDashboard');
$router->get('/views/user/supplier/products',            'ViewController@supplierProducts');
$router->get('/views/user/supplier/orders',              'ViewController@supplierOrders');
$router->get('/views/user/supplier/messages',            'ViewController@supplierMessages');
$router->get('/views/user/supplier/profile',             'ViewController@supplierProfile');
$router->get('/views/user/supplier/deliveries',          'ViewController@supplierDeliveries');
$router->get('/views/user/supplier/sales-report',        'ViewController@supplierSalesReport');
$router->get('/views/user/supplier/owner-purchases',     'ViewController@supplierOwnerPurchases');
$router->get('/views/user/supplier/tech-orders',         'ViewController@supplierTechOrders');
$router->get('/views/user/supplier/tech-requests',       'ViewController@supplierTechRequests');

// ── Sales Person ─────────────────────────────────────────────
$router->get('/views/user/sales_person/dashboard',       'ViewController@salesDashboard');
$router->get('/views/user/sales_person/products',        'ViewController@salesProducts');
$router->get('/views/user/sales_person/orders',          'ViewController@salesOrders');
$router->get('/views/user/sales_person/inventory',       'ViewController@salesInventory');
$router->get('/views/user/sales_person/messages',        'ViewController@salesMessages');
$router->get('/views/user/sales_person/profile',         'ViewController@salesProfile');
$router->get('/views/user/sales_person/settings',        'ViewController@salesSettings');
$router->get('/views/user/sales_person/supply-requests', 'ViewController@salesSupplyRequests');

// ── Supervisor ───────────────────────────────────────────────
$router->get('/views/user/supervisor/dashboard',         'ViewController@supervisorDashboard');
$router->get('/views/user/supervisor/inventory',         'ViewController@supervisorInventory');
$router->get('/views/user/supervisor/reports',           'ViewController@supervisorReports');
$router->get('/views/user/supervisor/messages',          'ViewController@supervisorMessages');
$router->get('/views/user/supervisor/profile',           'ViewController@supervisorProfile');

// ── Phone Technician ─────────────────────────────────────────
$router->get('/views/user/phone_technician/dashboard',       'ViewController@technicianDashboard');
$router->get('/views/user/phone_technician/inventory',       'ViewController@technicianInventory');
$router->get('/views/user/phone_technician/marketplace',     'ViewController@technicianMarketplace');
$router->get('/views/user/phone_technician/messages',        'ViewController@technicianMessages');
$router->get('/views/user/phone_technician/products',        'ViewController@technicianProducts');
$router->get('/views/user/phone_technician/profile',         'ViewController@technicianProfile');
$router->get('/views/user/phone_technician/repairs',         'ViewController@technicianRepairs');
$router->get('/views/user/phone_technician/supply-requests', 'ViewController@technicianSupplyRequests');

// Also support .php extension in URLs (backward compat)
$router->get('/views/user/customer/dashboard.php',           'ViewController@customerDashboard');
$router->get('/views/user/owner/dashboard.php',              'ViewController@ownerDashboard');
$router->get('/views/user/supplier/dashboard.php',           'ViewController@supplierDashboard');
$router->get('/views/user/sales_person/dashboard.php',       'ViewController@salesDashboard');
$router->get('/views/user/supervisor/dashboard.php',         'ViewController@supervisorDashboard');
$router->get('/views/user/phone_technician/dashboard.php',   'ViewController@technicianDashboard');
