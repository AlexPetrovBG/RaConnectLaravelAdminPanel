param(
  [string]$Message = "chore: auto deploy $(Get-Date -Format 'yyyy-MM-dd HH:mm')"
)

$ErrorActionPreference = "Stop"

# Ensure running from project directory (merged behavior from Deploy-Dev-Shortcut.ps1)
$ProjectPath = "C:\Projects\RaConnectLaravelAdminPanel"
if (Test-Path $ProjectPath) {
  Write-Host "Changing to project directory: $ProjectPath" -ForegroundColor Cyan
  Set-Location $ProjectPath
  Write-Host "Current directory: $(Get-Location)" -ForegroundColor Cyan
} else {
  Write-Host "Error: Project not found at $ProjectPath" -ForegroundColor Red
  Write-Host "Please ensure the project is located at: $ProjectPath" -ForegroundColor Yellow
  exit 1
}
Write-Host "Starting deployment script..." -ForegroundColor Yellow

# ---- EDIT THESE ----
$ServerUser = "alex"
$ServerHost = "ubuntu-server"   # hostname or IP of your server
$HealthUrl  = "https://rafactory.raworkshop.bg"
# --------------------

function ExecStr([string]$cmd) {
  Write-Host "› $cmd" -ForegroundColor Cyan
  & $env:ComSpec /c $cmd | Out-Host
}

# 1) Ensure git repo; switch to dev if needed
ExecStr "git rev-parse --is-inside-work-tree"
$branchOutput = git rev-parse --abbrev-ref HEAD
if ($branchOutput) {
  $branch = $branchOutput.Trim()
  if ($branch -ne "dev") {
    ExecStr "git checkout dev"
  }
} else {
  Write-Host "Error: Could not determine current git branch" -ForegroundColor Red
  exit 1
}

# 2) Commit if there are changes
$dirtyOutput = git status --porcelain
if ($dirtyOutput) {
  $dirty = $dirtyOutput.Trim()
  if ($dirty) {
    ExecStr "git add -A"
    & git commit -m $Message
  } else {
    Write-Host "No local changes to commit." -ForegroundColor Green
  }
} else {
  Write-Host "No local changes to commit." -ForegroundColor Green
}

# 3) Push dev to origin
ExecStr "git push origin dev"

# 4) SSH to server and run deploy as alex user from root directory
$sshCmd = "cd /home/alex && whoami && ./deploy.sh dev"
Write-Host "Deploying on DEV server as alex user from root directory" -ForegroundColor Yellow
Write-Host "   (no sudo password required)" -ForegroundColor Green
& ssh -t "$ServerUser@$ServerHost" "$sshCmd"

# 5) Health check
try {
  $resp = Invoke-WebRequest -Uri $HealthUrl -UseBasicParsing -TimeoutSec 20
  if ($resp.StatusCode -ge 200 -and $resp.StatusCode -lt 300) {
    Write-Host "Health OK: $HealthUrl" -ForegroundColor Green
  } else {
    Write-Host "Health returned $($resp.StatusCode)" -ForegroundColor Yellow
  }
} catch {
  Write-Host "Health check failed: $($_.Exception.Message)" -ForegroundColor Yellow
}

Write-Host "DEV deploy complete." -ForegroundColor Green
