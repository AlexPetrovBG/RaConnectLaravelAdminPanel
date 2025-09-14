param(
  [string]$Message = "chore: auto deploy $(Get-Date -Format 'yyyy-MM-dd HH:mm')"
)

$ErrorActionPreference = "Stop"

# ---- EDIT THESE ----
$ServerUser = "alex"
$ServerHost = "ubuntu-server"   # hostname or IP of your server
$HealthUrl  = "https://rafactory.raworkshop.bg/health"
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

# 4) SSH to server and run deploy as alex user from workspace
$sshCmd = "cd /home/alex/admin-dev && whoami && ./deploy.sh dev"
Write-Host "Deploying on DEV server as alex user from workspace" -ForegroundColor Yellow
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
