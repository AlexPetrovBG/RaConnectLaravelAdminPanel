param([string]$Message = "chore: promote dev→main & prod deploy $(Get-Date -Format 'yyyy-MM-dd HH:mm')")

$ErrorActionPreference = "Stop"

# Prevent instant exit on terminating errors so we can display the error and keep the window open
trap {
  Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
  continue
}
$ProjectPath = "C:\Projects\RaConnectLaravelAdminPanel"

# --- server ---
$ServerUser = "alex"
$ServerHost = "ubuntu-server"
$HealthUrl  = "https://admin.raworkshop.bg/health"

function Git { param([Parameter(ValueFromRemainingArguments=$true)]$Args) & git @Args }

Set-Location $ProjectPath

# Ensure repo
Git rev-parse --is-inside-work-tree | Out-Null

# Update local refs
Git fetch origin

# Make sure dev is pushed
Git checkout dev
$dirtyDev = (Git status --porcelain).Trim()
if ($dirtyDev) { Git add -A; Git commit -m "$Message" }
Git push origin dev

# Fast-forward main from dev
Git checkout main
Git pull --ff-only origin main
Git merge --ff-only origin/dev
Git push origin main

# Deploy prod
& ssh -t "$ServerUser@$ServerHost" "cd /home/alex && ./deploy.sh prod"

# Health
try {
  $resp = Invoke-WebRequest -Uri $HealthUrl -UseBasicParsing -TimeoutSec 20
  if ($resp.StatusCode -ge 200 -and $resp.StatusCode -lt 300) {
    Write-Host "✓ PROD health OK" -ForegroundColor Green
  } else {
    Write-Host "⚠ PROD health returned $($resp.StatusCode)" -ForegroundColor Yellow
  }
} catch { Write-Host "⚠ PROD health failed: $($_.Exception.Message)" -ForegroundColor Yellow }

Write-Host "PROD deploy complete." -ForegroundColor Green

Write-Host "`nPress Ctrl+C to copy selected text, or close the window when finished." -ForegroundColor Yellow

try {
    while ($true) {
        Start-Sleep -Seconds 1
    }
} catch {
    # Window was closed
}