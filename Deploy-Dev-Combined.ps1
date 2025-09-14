# Combined Deploy-Dev Script
# This script combines the desktop shortcut functionality with the deployment logic

Write-Host "Deploy-Dev Script Starting..." -ForegroundColor Cyan
Write-Host "=============================" -ForegroundColor Cyan

# Configuration
$ProjectPath = "C:\Projects\RaConnectLaravelAdminPanel"
$ServerUser = "alex"
$ServerHost = "ubuntu-server"
$HealthUrl = "https://rafactory.raworkshop.bg"

# Function to execute commands with output
function ExecStr([string]$cmd) {
    Write-Host "› $cmd" -ForegroundColor Cyan
    & $env:ComSpec /c $cmd | Out-Host
}

# Check if we're in the correct directory
if (Test-Path (Join-Path $ProjectPath "deploy-dev.ps1")) {
    Write-Host "Found project at: $ProjectPath" -ForegroundColor Green
    Set-Location $ProjectPath
    Write-Host "Current directory: $(Get-Location)" -ForegroundColor Cyan
} else {
    Write-Host "Error: Project not found at $ProjectPath" -ForegroundColor Red
    Write-Host "Please ensure the project is located at: $ProjectPath" -ForegroundColor Yellow
    Read-Host "Press Enter to exit"
    exit 1
}

# Get commit message parameter
param(
    [string]$Message = "chore: auto deploy $(Get-Date -Format 'yyyy-MM-dd HH:mm')"
)

$ErrorActionPreference = "Stop"

Write-Host "`nStarting deployment process..." -ForegroundColor Yellow
Write-Host "Commit message: $Message" -ForegroundColor Cyan

try {
    # 1) Ensure git repo; switch to dev if needed
    Write-Host "`n1. Checking git repository..." -ForegroundColor Yellow
    ExecStr "git rev-parse --is-inside-work-tree"
    $branchOutput = git rev-parse --abbrev-ref HEAD
    if ($branchOutput) {
        $branch = $branchOutput.Trim()
        Write-Host "Current branch: $branch" -ForegroundColor Cyan
        if ($branch -ne "dev") {
            Write-Host "Switching to dev branch..." -ForegroundColor Yellow
            ExecStr "git checkout dev"
        }
    } else {
        Write-Host "Error: Could not determine current git branch" -ForegroundColor Red
        throw "Git branch detection failed"
    }

    # 2) Commit if there are changes
    Write-Host "`n2. Checking for changes to commit..." -ForegroundColor Yellow
    $dirtyOutput = git status --porcelain
    if ($dirtyOutput) {
        $dirty = $dirtyOutput.Trim()
        if ($dirty) {
            Write-Host "Found changes, committing..." -ForegroundColor Yellow
            ExecStr "git add -A"
            & git commit -m $Message
            Write-Host "Changes committed successfully!" -ForegroundColor Green
        } else {
            Write-Host "No local changes to commit." -ForegroundColor Green
        }
    } else {
        Write-Host "No local changes to commit." -ForegroundColor Green
    }

    # 3) Push dev to origin
    Write-Host "`n3. Pushing to remote repository..." -ForegroundColor Yellow
    ExecStr "git push origin dev"
    Write-Host "Push completed successfully!" -ForegroundColor Green

    # 4) SSH to server and run deploy
    Write-Host "`n4. Deploying on DEV server..." -ForegroundColor Yellow
    Write-Host "   Server: $ServerUser@$ServerHost" -ForegroundColor Cyan
    Write-Host "   (no sudo password required)" -ForegroundColor Green
    
    $sshCmd = "cd /home/alex && whoami && ./deploy.sh dev"
    & ssh -t "$ServerUser@$ServerHost" "$sshCmd"
    Write-Host "Deployment completed on server!" -ForegroundColor Green

    # 5) Health check
    Write-Host "`n5. Performing health check..." -ForegroundColor Yellow
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

    Write-Host "`n✅ DEV deploy complete!" -ForegroundColor Green

} catch {
    Write-Host "`n❌ Deployment failed: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Full error details:" -ForegroundColor Red
    Write-Host $_.Exception -ForegroundColor Red
}

# Keep the window open to see results
Write-Host "`nDeployment process completed. Window will stay open for you to copy text." -ForegroundColor Green
Write-Host "Close this window manually when done." -ForegroundColor Cyan
Write-Host "`nPress Ctrl+C to copy selected text, or close the window when finished." -ForegroundColor Yellow

# Keep the window open indefinitely until manually closed
try {
    while ($true) {
        Start-Sleep -Seconds 1
    }
} catch {
    # Window was closed
}
