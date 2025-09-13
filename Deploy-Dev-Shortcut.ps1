# Desktop shortcut for deploy-dev.ps1
# This script will run the deploy-dev.ps1 script from the project directory

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
        & $ScriptPath
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
