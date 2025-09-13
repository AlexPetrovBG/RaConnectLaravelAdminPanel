param(
  [string]$Message = "chore: auto deploy $(Get-Date -Format 'yyyy-MM-dd HH:mm')"
)

$ErrorActionPreference = "Stop"

# ---- EDIT THESE VALUES ----
$ServerUser = "alex"
$ServerHost = "ubuntu-server"                 # change to your real host
$HealthUrl  = "https://rafactory.raworkshop.bg/health"
# ---------------------------

function Exec($cmd) {
  Write-Host "› $cmd" -ForegroundColor Cyan
  & $env:ComSpec /c $cmd | Out-Host
}

# 1) Ensure we're in a git repo and on 'dev'
Exec "git rev-parse --is-inside-work-tree"
$branch = (git rev-parse --abbrev-ref HEAD).Trim()
if ($branch -ne "dev") {
  Exec "git checkout dev"
}

# 2) Commit if there are changes
$dirty = (git status --porcelain).Trim()
if ($dirty) {
  Exec "git add -A"
  Exec "git commit -m `"$Message`""
} else {
  Write-Host "No local changes to commit." -ForegroundColor Green
}

# 3) Push dev to origin
Exec "git push origin dev"

# 4) SSH to server and run deploy
$sshCmd = "sudo /usr/local/bin/deploy.sh dev"
Write-Host "Deploying on DEV server..." -ForegroundColor Yellow
Exec "ssh $ServerUser@$ServerHost `"$sshCmd`""

# 5) Health check
try {
  $resp = Invoke-WebRequest -Uri $HealthUrl -UseBasicParsing -TimeoutSec 15
  if ($resp.StatusCode -ge 200 -and $resp.StatusCode -lt 300) {
    Write-Host "Health OK: $HealthUrl" -ForegroundColor Green
  } else {
    Write-Host "Health returned $($resp.StatusCode)" -ForegroundColor Yellow
  }
} catch {
  Write-Host "Health check failed: $($_.Exception.Message)" -ForegroundColor Yellow
}

Write-Host "DEV deploy complete." -ForegroundColor Green
