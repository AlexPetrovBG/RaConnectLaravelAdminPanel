# Desktop shortcut for deploy-dev.ps1
# This script will run the deploy-dev.ps1 script from the project directory

# Get the current user's desktop path
$DesktopPath = [Environment]::GetFolderPath("Desktop")
$ProjectPath = "C:\Projects\RaConnectLaravelAdminPanel"
$ScriptPath = Join-Path $ProjectPath "deploy-dev.ps1"

# Check if the script exists
if (Test-Path $ScriptPath) {
    # Change to the project directory and run the script
    Set-Location $ProjectPath
    & $ScriptPath
} else {
    Write-Host "Error: deploy-dev.ps1 not found at $ScriptPath" -ForegroundColor Red
    Write-Host "Please ensure the project is located at: $ProjectPath" -ForegroundColor Yellow
}

# Keep the window open to see results
Write-Host "`nPress any key to close this window..." -ForegroundColor Cyan
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
