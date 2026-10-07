# Automated Deploy Script for Flora Dental Clinic (PowerShell)
Write-Host "=================================================" -ForegroundColor Cyan
Write-Host "   FLORA DENTAL CLINIC - DEPLOY TO PRODUCTION    " -ForegroundColor Yellow
Write-Host "=================================================" -ForegroundColor Cyan

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
python "$scriptDir\deploy.py"
