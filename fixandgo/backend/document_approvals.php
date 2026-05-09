<?php
/**
 * Fix&Go — Document Approval API
 * Individual document approval/rejection for seller applications
 *
 * GET  ?action=get_documents&application_id=X  → Get all documents with approval status
 * POST action=approve_document                  → Approve a single document
 * POST action=reject_document                   → Reject a single document with reason
 */

require_once __DIR__ . '/helpers.php';
startSecureSession();
header('Content-Type: application/json');
header('Cache-Control: no-store');

$pdo = require __DIR__ . '/db.php';
$method = $_SERVER['REQUEST_METHOD'];

// ============================================================
// GET - Fetch documents with approval status
// ============================================================
if ($method === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'get_documents') {
        // Admin only for this action
        if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Admin access required.']);
            exit;
        }
        $appId = (int)($_GET['application_id'] ?? 0);
        if (!$appId) {
            echo json_encode(['success' => false, 'message' => 'Application ID required']);
            exit;
        }
        
        // Get application details
        $stmt = $pdo->prepare("
            SELECT sa.*, u.id as user_id
            FROM seller_applications sa
            LEFT JOIN users u ON u.email = sa.email AND u.role = sa.role
            WHERE sa.id = ?
        ");
        $stmt->execute([$appId]);
        $app = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$app) {
            echo json_encode(['success' => false, 'message' => 'Application not found']);
            exit;
        }
        
        // Get document approval statuses
        $stmt = $pdo->prepare("
            SELECT document_type, status, rejection_reason, reviewed_at
            FROM document_approvals
            WHERE application_id = ?
        ");
        $stmt->execute([$appId]);
        $approvals = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $approvals[$row['document_type']] = $row;
        }
        
        // Build document list with statuses
        $documents = [];
        $docMap = [
            'gov_id' => ['label' => 'Government-Issued ID', 'path' => $app['doc_gov_id'], 'required' => true],
            'bir'    => ['label' => 'BIR Certificate', 'path' => $app['doc_bir'], 'required' => false],
            'dti'    => ['label' => 'DTI / SEC Registration', 'path' => $app['doc_dti'], 'required' => $app['role'] === 'owner'],
            'bank'   => ['label' => 'Bank Account Proof', 'path' => $app['doc_bank'], 'required' => true],
        ];
        
        foreach ($docMap as $type => $info) {
            $documents[] = [
                'type' => $type,
                'label' => $info['label'],
                'path' => $info['path'],
                'required' => $info['required'],
                'status' => $approvals[$type]['status'] ?? 'pending',
                'rejection_reason' => $approvals[$type]['rejection_reason'] ?? null,
                'reviewed_at' => $approvals[$type]['reviewed_at'] ?? null,
            ];
        }
        
        echo json_encode([
            'success' => true,
            'application' => $app,
            'documents' => $documents
        ]);
        exit;
    }
    
    // ── Get applicant's own document status (for applicant view) ──
    if ($action === 'my_documents') {
        // Allow both admin and the applicant themselves
        if (empty($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Authentication required.']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'] ?? '';
        
        // Get the user's email to find their application
        $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }
        
        // Find application by user_id (customer who applied)
        $customerId = (int)($_GET['customer_id'] ?? 0);
        if ($customerId) {
            // Query by user_id directly
            $stmt = $pdo->prepare("SELECT * FROM seller_applications WHERE user_id = ? ORDER BY id DESC LIMIT 1");
            $stmt->execute([$customerId]);
        } else {
            // For logged-in seller checking their own status
            $stmt = $pdo->prepare("SELECT * FROM seller_applications WHERE email = ? ORDER BY id DESC LIMIT 1");
            $stmt->execute([$user['email']]);
        }
        
        $app = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$app) {
            echo json_encode(['success' => false, 'message' => 'No application found']);
            exit;
        }
        
        // Get document approval statuses
        $stmt = $pdo->prepare("
            SELECT document_type, status, rejection_reason, reviewed_at
            FROM document_approvals
            WHERE application_id = ?
        ");
        $stmt->execute([$app['id']]);
        $approvals = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $approvals[$row['document_type']] = $row;
        }
        
        // Build document list with statuses
        $documents = [];
        $docMap = [
            'gov_id' => ['label' => 'Government-Issued ID', 'path' => $app['doc_gov_id'], 'required' => true],
            'bir'    => ['label' => 'BIR Certificate', 'path' => $app['doc_bir'], 'required' => false],
            'dti'    => ['label' => 'DTI / SEC Registration', 'path' => $app['doc_dti'], 'required' => $app['role'] === 'owner'],
            'bank'   => ['label' => 'Bank Account Proof', 'path' => $app['doc_bank'], 'required' => true],
        ];
        
        foreach ($docMap as $type => $info) {
            $documents[] = [
                'type' => $type,
                'label' => $info['label'],
                'path' => $info['path'],
                'required' => $info['required'],
                'status' => $approvals[$type]['status'] ?? 'pending',
                'rejection_reason' => $approvals[$type]['rejection_reason'] ?? null,
                'reviewed_at' => $approvals[$type]['reviewed_at'] ?? null,
            ];
        }
        
        echo json_encode([
            'success' => true,
            'application' => $app,
            'documents' => $documents
        ]);
        exit;
    }
}

// ============================================================
// POST - Approve or reject document
// ============================================================
if ($method === 'POST') {
    // Check if this is a file upload (resubmission)
    $isFileUpload = !empty($_FILES);
    
    if ($isFileUpload) {
        // Handle document resubmission
        $action = $_POST['action'] ?? '';
        
        if ($action === 'resubmit_document') {
            // Allow applicants to resubmit their own documents
            if (empty($_SESSION['user_id'])) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Authentication required.']);
                exit;
            }
            
            $appId = (int)($_POST['application_id'] ?? 0);
            $docType = $_POST['document_type'] ?? '';
            
            if (!$appId || !$docType) {
                echo json_encode(['success' => false, 'message' => 'Application ID and document type required']);
                exit;
            }
            
            // Verify the document was previously rejected
            $stmt = $pdo->prepare("SELECT status FROM document_approvals WHERE application_id = ? AND document_type = ?");
            $stmt->execute([$appId, $docType]);
            $approval = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$approval || $approval['status'] !== 'rejected') {
                echo json_encode(['success' => false, 'message' => 'Document was not rejected or does not exist']);
                exit;
            }
            
            // Handle file upload
            $fileKey = $docType . 'File';
            if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
                exit;
            }
            
            $file = $_FILES[$fileKey];
            $allowedExts = ['jpg', 'jpeg', 'png', 'pdf'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowedExts)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, PDF allowed.']);
                exit;
            }
            
            if ($file['size'] > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit.']);
                exit;
            }
            
            // Generate unique filename
            $uploadDir = __DIR__ . '/../uploads/applications/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $uniqueName = $fileKey . '_' . uniqid() . '.' . $ext;
            $uploadPath = $uploadDir . $uniqueName;
            
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file.']);
                exit;
            }
            
            // Update the application document path
            $docColumn = 'doc_' . $docType;
            $stmt = $pdo->prepare("UPDATE seller_applications SET {$docColumn} = ? WHERE id = ?");
            $stmt->execute(['uploads/applications/' . $uniqueName, $appId]);
            
            // Reset the document approval status to pending
            $stmt = $pdo->prepare("
                UPDATE document_approvals 
                SET status = 'pending', 
                    rejection_reason = NULL, 
                    reviewed_by = NULL, 
                    reviewed_at = NULL
                WHERE application_id = ? AND document_type = ?
            ");
            $stmt->execute([$appId, $docType]);
            
            // Update overall status back to pending
            $pdo->prepare("UPDATE seller_applications SET overall_status = 'pending' WHERE id = ?")
                ->execute([$appId]);
            
            // Notify admin about resubmission
            require_once __DIR__ . '/notification_helper.php';
            $docLabels = [
                'gov_id' => 'Government-Issued ID',
                'bir' => 'BIR Certificate',
                'dti' => 'DTI / SEC Registration',
                'bank' => 'Bank Account Proof',
            ];
            $docLabel = $docLabels[$docType] ?? $docType;
            
            // Get admin users
            $stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'admin'");
            $stmt->execute();
            while ($admin = $stmt->fetch(PDO::FETCH_ASSOC)) {
                sendNotification(
                    $admin['id'],
                    'system',
                    'Document Resubmitted',
                    "An applicant has resubmitted their {$docLabel}. Please review the updated document."
                );
            }
            
            echo json_encode(['success' => true, 'message' => 'Document resubmitted successfully. Admin will review it shortly.']);
            exit;
        }
    }
    
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';
    
    // ── Approve document ──────────────────────────────────────
    if ($action === 'approve_document') {
        // Admin only for this action
        if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Admin access required.']);
            exit;
        }
        
        $appId = (int)($body['application_id'] ?? 0);
        $docType = $body['document_type'] ?? '';
        
        if (!$appId || !$docType) {
            echo json_encode(['success' => false, 'message' => 'Application ID and document type required']);
            exit;
        }
        
        // Insert or update approval record
        $stmt = $pdo->prepare("
            INSERT INTO document_approvals (application_id, document_type, status, reviewed_by, reviewed_at)
            VALUES (?, ?, 'approved', ?, NOW())
            ON DUPLICATE KEY UPDATE 
                status = 'approved',
                rejection_reason = NULL,
                reviewed_by = ?,
                reviewed_at = NOW()
        ");
        $stmt->execute([$appId, $docType, $_SESSION['user_id'], $_SESSION['user_id']]);
        
        // Check if all required documents are approved
        $stmt = $pdo->prepare("
            SELECT sa.role, sa.doc_gov_id, sa.doc_bir, sa.doc_dti, sa.doc_bank,
                   COUNT(CASE WHEN da.status = 'approved' THEN 1 END) as approved_count,
                   COUNT(CASE WHEN da.status = 'rejected' THEN 1 END) as rejected_count
            FROM seller_applications sa
            LEFT JOIN document_approvals da ON da.application_id = sa.id
            WHERE sa.id = ?
            GROUP BY sa.id
        ");
        $stmt->execute([$appId]);
        $check = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Determine required document count
        $requiredDocs = 2; // gov_id + bank (always required)
        if ($check['role'] === 'owner' && $check['doc_dti']) {
            $requiredDocs++; // DTI required for owners
        }
        if ($check['doc_bir']) {
            $requiredDocs++; // BIR if uploaded
        }
        
        // Update overall status if all required docs are approved
        if ($check['approved_count'] >= $requiredDocs && $check['rejected_count'] == 0) {
            $pdo->prepare("UPDATE seller_applications SET overall_status = 'docs_approved' WHERE id = ?")
                ->execute([$appId]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Document approved']);
        exit;
    }
    
    // ── Reject document ───────────────────────────────────────
    if ($action === 'reject_document') {
        // Admin only for this action
        if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Admin access required.']);
            exit;
        }
        
        $appId = (int)($body['application_id'] ?? 0);
        $docType = $body['document_type'] ?? '';
        $reason = trim($body['reason'] ?? '');
        
        if (!$appId || !$docType) {
            echo json_encode(['success' => false, 'message' => 'Application ID and document type required']);
            exit;
        }
        
        if (!$reason) {
            echo json_encode(['success' => false, 'message' => 'Rejection reason is required']);
            exit;
        }
        
        // Insert or update rejection record
        $stmt = $pdo->prepare("
            INSERT INTO document_approvals (application_id, document_type, status, rejection_reason, reviewed_by, reviewed_at)
            VALUES (?, ?, 'rejected', ?, ?, NOW())
            ON DUPLICATE KEY UPDATE 
                status = 'rejected',
                rejection_reason = ?,
                reviewed_by = ?,
                reviewed_at = NOW()
        ");
        $stmt->execute([$appId, $docType, $reason, $_SESSION['user_id'], $reason, $_SESSION['user_id']]);
        
        // Update overall status to pending (needs resubmission)
        $pdo->prepare("UPDATE seller_applications SET overall_status = 'pending' WHERE id = ?")
            ->execute([$appId]);
        
        // NOTE: Notification is NOT sent here automatically
        // Admin will use the "Send Notification" button to notify applicant
        
        echo json_encode(['success' => true, 'message' => 'Document rejected. Use "Send Notification" button to notify applicant.']);
        exit;
    }
    
    // ── Notify applicant about rejected documents ─────────────
    if ($action === 'notify_rejections') {
        // Admin only for this action
        if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Admin access required.']);
            exit;
        }
        
        $appId = (int)($body['application_id'] ?? 0);
        
        if (!$appId) {
            echo json_encode(['success' => false, 'message' => 'Application ID required']);
            exit;
        }
        
        // Get application details
        $stmt = $pdo->prepare("SELECT * FROM seller_applications WHERE id = ?");
        $stmt->execute([$appId]);
        $app = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$app) {
            echo json_encode(['success' => false, 'message' => 'Application not found']);
            exit;
        }
        
        // Get rejected documents
        $stmt = $pdo->prepare("
            SELECT document_type, rejection_reason 
            FROM document_approvals 
            WHERE application_id = ? AND status = 'rejected'
        ");
        $stmt->execute([$appId]);
        $rejectedDocs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($rejectedDocs)) {
            echo json_encode(['success' => false, 'message' => 'No rejected documents found']);
            exit;
        }
        
        // Build document list for notification
        $docLabels = [
            'gov_id' => 'Government-Issued ID',
            'bir' => 'BIR Certificate',
            'dti' => 'DTI / SEC Registration',
            'bank' => 'Bank Account Proof',
        ];
        
        $docList = [];
        $docListHTML = [];
        foreach ($rejectedDocs as $doc) {
            $label = $docLabels[$doc['document_type']] ?? $doc['document_type'];
            $reason = $doc['rejection_reason'] ?? 'No reason provided';
            $docList[] = "• {$label}: {$reason}";
            $docListHTML[] = "<li><strong>{$label}:</strong> {$reason}</li>";
        }
        $docListText = implode("\n", $docList);
        $docListHTMLText = implode("\n", $docListHTML);
        
        $roleLabel = $app['role'] === 'owner' ? 'Shop Owner' : 'Supplier';
        
        // Send in-app notification to CUSTOMER account (user_id in seller_applications)
        if ($app['user_id']) {
            require_once __DIR__ . '/notification_helper.php';
            $notifTitle = 'Documents Require Resubmission';
            $notifBody = "Your {$roleLabel} application has " . count($rejectedDocs) . " rejected document(s). Please review and resubmit them in the Seller Centre.";
            sendNotification($app['user_id'], 'system', $notifTitle, $notifBody);
        }
        
        // Get customer email for CC
        $customerEmail = null;
        if ($app['user_id']) {
            $stmt = $pdo->prepare("SELECT email, first_name, last_name FROM users WHERE id = ?");
            $stmt->execute([$app['user_id']]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($customer) {
                $customerEmail = $customer['email'];
            }
        }
        
        // Send detailed email to BOTH customer email AND seller application email
        require_once __DIR__ . '/mailer.php';
        $subject = "Document Resubmission Required — Fix&Go {$roleLabel} Application";
        $emailBody = "
<h2>Document Resubmission Required</h2>
<p>Dear {$app['first_name']} {$app['last_name']},</p>
<p>Thank you for your interest in becoming a <strong>{$roleLabel}</strong> on Fix&Go.</p>
<p>After reviewing your application, we found that <strong>" . count($rejectedDocs) . " document(s)</strong> need to be resubmitted:</p>
<div style='background:#fff3cd;border-left:4px solid #ffc107;padding:15px;margin:20px 0;'>
<ul style='margin:0;padding-left:20px;'>
{$docListHTMLText}
</ul>
</div>
<p><strong>What to do next:</strong></p>
<ol>
  <li>Log in to your Fix&Go <strong>customer account</strong>" . ($customerEmail && $customerEmail !== $app['email'] ? " ({$customerEmail})" : "") . "</li>
  <li>Go to the <strong>Seller Centre</strong> page</li>
  <li>Review the rejection reasons for each document</li>
  <li>Click <strong>\"Resubmit Document\"</strong> to upload corrected files</li>
</ol>
<p>Once you resubmit the documents, our team will review them within 1-2 business days.</p>
<p>If you have any questions, please contact our support team.</p>
<p>Best regards,<br>Fix&Go Team</p>
";
        
        // Send to seller application email
        sendEmail($app['email'], $app['first_name'] . ' ' . $app['last_name'], $subject, $emailBody);
        
        // Also send to customer email if different
        if ($customerEmail && $customerEmail !== $app['email']) {
            $customerSubject = "Action Required: {$roleLabel} Application Documents";
            $customerBody = "
<h2>Document Resubmission Required</h2>
<p>Dear {$app['first_name']} {$app['last_name']},</p>
<p>Your <strong>{$roleLabel}</strong> application (submitted with email: <strong>{$app['email']}</strong>) requires document resubmission.</p>
<p><strong>" . count($rejectedDocs) . " document(s)</strong> were rejected:</p>
<div style='background:#fff3cd;border-left:4px solid #ffc107;padding:15px;margin:20px 0;'>
<ul style='margin:0;padding-left:20px;'>
{$docListHTMLText}
</ul>
</div>
<p><strong>To resubmit documents:</strong></p>
<ol>
  <li>Log in to your Fix&Go account with <strong>this email ({$customerEmail})</strong></li>
  <li>Go to <strong>Seller Centre</strong></li>
  <li>Click <strong>\"Resubmit Document\"</strong> for each rejected file</li>
</ol>
<p>Best regards,<br>Fix&Go Team</p>
";
            sendEmail($customerEmail, $app['first_name'] . ' ' . $app['last_name'], $customerSubject, $customerBody);
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Notification sent successfully',
            'rejected_count' => count($rejectedDocs),
            'emails_sent' => $customerEmail && $customerEmail !== $app['email'] ? 2 : 1
        ]);
        exit;
    }
    
    
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
