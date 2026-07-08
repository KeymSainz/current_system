$file = "c:\xampp\htdocs\current_system\fixandgo\views\user\customer\messages.html"
$c = [System.IO.File]::ReadAllText($file, [System.Text.Encoding]::UTF8)

$agreementModal = @'

  <!-- Agreement / Disclaimer Modal — shown BEFORE booking form -->
  <div id="agreementModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.75);backdrop-filter:blur(6px);z-index:10000;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--fg-card-bg);border:1px solid var(--fg-border);border-radius:20px;width:100%;max-width:560px;max-height:94vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 32px 80px rgba(0,0,0,0.6);">
      <!-- Header -->
      <div style="background:linear-gradient(135deg,#dc3545,#b02a37);padding:1.1rem 1.35rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <div>
          <div style="color:#fff;font-weight:800;font-size:1rem;" id="agreementModalTitle">Repair Service Agreement</div>
          <div style="color:rgba(255,255,255,0.75);font-size:0.73rem;margin-top:0.1rem;">Read the full agreement before proceeding</div>
        </div>
        <button onclick="closeAgreementModal()"
          style="background:rgba(255,255,255,0.18);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:1rem;cursor:pointer;"
          onmouseenter="this.style.background='rgba(255,255,255,0.32)'" onmouseleave="this.style.background='rgba(255,255,255,0.18)'">&#x2715;</button>
      </div>

      <!-- Scroll hint -->
      <div id="agreementScrollHint" style="display:flex;align-items:center;justify-content:center;gap:0.5rem;background:rgba(230,168,0,0.1);border-bottom:1px solid rgba(230,168,0,0.25);padding:0.5rem 1rem;font-size:0.78rem;color:var(--fg-primary);font-weight:700;flex-shrink:0;">
        <i class="bi bi-arrow-down-circle-fill"></i> Please scroll to the bottom to enable the agreement button
      </div>

      <!-- Scrollable body -->
      <div id="agreementScrollArea" style="overflow-y:auto;flex:1;padding:1.35rem;">
        <div id="agreementBody"></div>
      </div>

      <!-- Footer -->
      <div style="padding:1rem 1.35rem;border-top:1px solid var(--fg-border);background:var(--fg-bg);flex-shrink:0;">
        <button id="agreementAgreeBtn" onclick="agreeAndProceed()" disabled
          style="width:100%;padding:0.85rem;border-radius:12px;background:var(--fg-primary);color:#000;border:none;font-weight:800;font-size:0.95rem;cursor:not-allowed;display:flex;align-items:center;justify-content:center;gap:0.5rem;opacity:0.5;transition:opacity 0.2s;"
          onmouseenter="if(!this.disabled)this.style.opacity='0.88'" onmouseleave="if(!this.disabled)this.style.opacity='1'">
          <i class="bi bi-check-circle-fill"></i> I Agree &amp; Proceed to Booking
        </button>
        <p style="text-align:center;font-size:0.72rem;color:var(--fg-muted);margin:0.5rem 0 0;">
          By clicking "I Agree", you confirm that you have read and understood all terms.
        </p>
      </div>
    </div>
  </div>

'@

# Insert before </body>
$marker = "</body>"
$lastIdx = $c.LastIndexOf($marker)
if ($lastIdx -lt 0) { Write-Host "ERROR: </body> not found"; exit 1 }

$newContent = $c.Substring(0, $lastIdx) + $agreementModal + $c.Substring($lastIdx)
[System.IO.File]::WriteAllText($file, $newContent, [System.Text.Encoding]::UTF8)

# Verify
$v = [System.IO.File]::ReadAllText($file, [System.Text.Encoding]::UTF8)
Write-Host ("agreementModal: "    + $v.Contains("agreementModal"))
Write-Host ("agreementBody: "     + $v.Contains("agreementBody"))
Write-Host ("agreeAndProceed: "   + $v.Contains("agreeAndProceed"))
Write-Host ("agreementScrollArea: " + $v.Contains("agreementScrollArea"))
Write-Host ("closeAgreementModal: " + $v.Contains("closeAgreementModal"))
Write-Host "SUCCESS"
