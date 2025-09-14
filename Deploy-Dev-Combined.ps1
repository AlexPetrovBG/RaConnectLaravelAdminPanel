# Combined Deploy-Dev Script
# This script combines the desktop shortcut functionality with the deployment logic

param(
  [string]$Message = "chore: auto deploy $(Get-Date -Format 'yyyy-MM-dd HH:mm')"
)

Write-Host "Deploy-Dev Shortcut Starting..." -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan

# Get the current user's desktop path
$DesktopPath = [Environment]::GetFolderPath("Desktop")
$ProjectPath = "C:\Projects\RaConnectLaravelAdminPanel"
$ScriptPath = Join-Path $ProjectPath "deploy-dev.ps1"

# Check if the script exists
if (Test-Path $ScriptPath) {
    Write-Host "Found script at: $ScriptPath" -ForegroundColor Green
    Write-Host "Changing to project directory: $ProjectPath" -ForegroundColor Cyan
    
    # Change to the project directory and run the script
    Set-Location $ProjectPath
    Write-Host "Current directory: $(Get-Location)" -ForegroundColor Cyan
    
    try {
        Write-Host "Starting deployment script..." -ForegroundColor Yellow
        
        # START OF DEPLOY-DEV.PS1 CONTENT

        $ErrorActionPreference = "Stop"

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
        # END OF DEPLOY-DEV.PS1 CONTENT
        
        Write-Host "Deployment script completed successfully!" -ForegroundColor Green
    } catch {
        Write-Host "Error running deployment script: $($_.Exception.Message)" -ForegroundColor Red
        Write-Host "Full error details:" -ForegroundColor Red
        Write-Host $_.Exception -ForegroundColor Red
    }
} else {
    Write-Host "Error: deploy-dev.ps1 not found at $ScriptPath" -ForegroundColor Red
    Write-Host "Please ensure the project is located at: $ProjectPath" -ForegroundColor Yellow
}

# Keep the window open to see results
Write-Host "`nDeployment completed. Window will stay open for you to copy text." -ForegroundColor Green
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