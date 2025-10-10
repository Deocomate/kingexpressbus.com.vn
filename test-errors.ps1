#!/usr/bin/env pwsh
# Test Error Pages Script
# Usage: .\test-errors.ps1

Write-Host "🧪 King Express Bus - Error Pages Testing" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

$baseUrl = "http://localhost:8000"

Write-Host "📌 Available test URLs:" -ForegroundColor Yellow
Write-Host ""

Write-Host "1. Test 404 (Not Found):" -ForegroundColor Green
Write-Host "   $baseUrl/page-not-exists" -ForegroundColor White
Write-Host ""

Write-Host "2. Test 403 (Forbidden):" -ForegroundColor Green
Write-Host "   $baseUrl/test-errors/403" -ForegroundColor White
Write-Host ""

Write-Host "3. Test 401 (Unauthorized):" -ForegroundColor Green
Write-Host "   $baseUrl/test-errors/401" -ForegroundColor White
Write-Host ""

Write-Host "4. Test 419 (CSRF Token Mismatch):" -ForegroundColor Green
Write-Host "   Submit a form after keeping page open for >2 hours" -ForegroundColor White
Write-Host ""

Write-Host "5. Test 429 (Too Many Requests):" -ForegroundColor Green
Write-Host "   $baseUrl/test-errors/429" -ForegroundColor White
Write-Host ""

Write-Host "6. Test 500 (Internal Server Error):" -ForegroundColor Green
Write-Host "   $baseUrl/test-error-500" -ForegroundColor White
Write-Host ""

Write-Host "7. Test 503 (Service Unavailable):" -ForegroundColor Green
Write-Host "   Run: php artisan down" -ForegroundColor White
Write-Host "   Then visit any page" -ForegroundColor White
Write-Host "   Run: php artisan up (to restore)" -ForegroundColor White
Write-Host ""

Write-Host "8. Test 405 (Method Not Allowed):" -ForegroundColor Green
Write-Host "   $baseUrl/test-errors/405" -ForegroundColor White
Write-Host ""

Write-Host "⚠️  Note: Test routes only work when APP_DEBUG=true" -ForegroundColor Yellow
Write-Host ""

Write-Host "🚀 Quick Actions:" -ForegroundColor Cyan
Write-Host ""

$action = Read-Host "What would you like to do? (1-8, 'all' to see all, 's' to start server, 'q' to quit)"

switch ($action) {
    "s" {
        Write-Host "`n🚀 Starting development server..." -ForegroundColor Green
        composer run dev
    }
    "1" {
        Write-Host "`n🌐 Opening 404 page..." -ForegroundColor Green
        Start-Process "$baseUrl/page-not-exists"
    }
    "2" {
        Write-Host "`n🌐 Opening 403 page..." -ForegroundColor Green
        Start-Process "$baseUrl/test-errors/403"
    }
    "3" {
        Write-Host "`n🌐 Opening 401 page..." -ForegroundColor Green
        Start-Process "$baseUrl/test-errors/401"
    }
    "4" {
        Write-Host "`n📝 CSRF test requires manual action:" -ForegroundColor Yellow
        Write-Host "   1. Open a page with a form" -ForegroundColor White
        Write-Host "   2. Wait >2 hours (or clear session)" -ForegroundColor White
        Write-Host "   3. Submit the form" -ForegroundColor White
    }
    "5" {
        Write-Host "`n🌐 Opening 429 page..." -ForegroundColor Green
        Start-Process "$baseUrl/test-errors/429"
    }
    "6" {
        Write-Host "`n🌐 Opening 500 page..." -ForegroundColor Green
        Start-Process "$baseUrl/test-error-500"
    }
    "7" {
        Write-Host "`n🔧 Running maintenance mode..." -ForegroundColor Yellow
        php artisan down
        Write-Host "✅ Maintenance mode enabled" -ForegroundColor Green
        Write-Host "Visit $baseUrl to see maintenance page" -ForegroundColor White
        Write-Host ""
        $restore = Read-Host "Press Enter to restore service..."
        php artisan up
        Write-Host "✅ Service restored" -ForegroundColor Green
    }
    "8" {
        Write-Host "`n🌐 Opening 405 page..." -ForegroundColor Green
        Start-Process "$baseUrl/test-errors/405"
    }
    "all" {
        Write-Host "`n🌐 Opening all test pages..." -ForegroundColor Green
        Start-Sleep -Seconds 1
        Start-Process "$baseUrl/page-not-exists"
        Start-Sleep -Seconds 1
        Start-Process "$baseUrl/test-errors/403"
        Start-Sleep -Seconds 1
        Start-Process "$baseUrl/test-errors/401"
        Start-Sleep -Seconds 1
        Start-Process "$baseUrl/test-errors/429"
        Start-Sleep -Seconds 1
        Start-Process "$baseUrl/test-errors/405"
        Start-Sleep -Seconds 1
        Start-Process "$baseUrl/test-error-500"
        Write-Host "✅ All test pages opened in browser" -ForegroundColor Green
    }
    "q" {
        Write-Host "`n👋 Goodbye!" -ForegroundColor Cyan
        exit
    }
    default {
        Write-Host "`n⚠️  Invalid option. Please run the script again." -ForegroundColor Red
    }
}

Write-Host "`n✅ Done!" -ForegroundColor Green
