param([string]$Message = "chore: promote dev->main & prod deploy $(Get-Date -Format 'yyyy-MM-dd HH:mm')")

$ErrorActionPreference = "Stop"
$ProjectPath = "C:\Projects\RaConnectLaravelAdminPanel"

# --- server ---
$ServerUser = "alex"
$ServerHost = "ubuntu-server"
$HealthUrl  = "https://admin.raworkshop.bg/health"

# Resolve git.exe explicitly to avoid recursion (PowerShell function names are case-insensitive)
$gitExe = (Get-Command git.exe -ErrorAction Stop).Source
function Git {
  # Disable auto background maintenance/gc to avoid interactive prompts and filesystem deletes
  & $gitExe -c gc.auto=0 -c maintenance.auto=false -c gc.autoDetach=false @args
}
Write-Host "Starting deployment script..." -ForegroundColor Yellow

try {
  Set-Location $ProjectPath

  # Ensure repo
  Git rev-parse --is-inside-work-tree | Out-Null

  # Update local refs
  Git fetch origin

  # Make sure dev is pushed
  Git checkout dev
  $dirtyDev = ((Git status --porcelain) -join "`n").Trim()
  if ($dirtyDev) { Git add -A; Git commit -m "$Message" }
  Git push origin dev

  # Fast-forward remote main from dev without switching working tree (avoid Windows deletion prompts)
  $shouldDeploy = $true
  Git fetch origin
  Git merge-base --is-ancestor origin/main origin/dev
  if ($LASTEXITCODE -eq 0) {
    Git push origin origin/dev:refs/heads/main
  } else {
    Write-Host "Skip: origin/main is ahead or diverged; not fast-forwarding." -ForegroundColor Yellow
    $shouldDeploy = $false
  }

  # Deploy prod
  if ($shouldDeploy) {
    & ssh -t "$ServerUser@$ServerHost" "cd /home/alex && ./deploy.sh prod"
  } else {
    Write-Host "Deployment skipped due to non-FF remote main." -ForegroundColor Yellow
  }

  # Health
  if ($shouldDeploy) {
    try {
      $resp = Invoke-WebRequest -Uri $HealthUrl -UseBasicParsing -TimeoutSec 20
      if ($resp.StatusCode -ge 200 -and $resp.StatusCode -lt 300) {
        Write-Host "PROD health OK" -ForegroundColor Green
      } else {
        Write-Host "PROD health returned $($resp.StatusCode)" -ForegroundColor Yellow
      }
    } catch { Write-Host "PROD health failed: $($_.Exception.Message)" -ForegroundColor Yellow }

    Write-Host "PROD deploy complete." -ForegroundColor Green
  }
} catch {
  Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`nPress Ctrl+C to copy selected text, or close the window when finished." -ForegroundColor Yellow

try {
    while ($true) {
        Start-Sleep -Seconds 1
    }
} catch {
    # Window was closed
}