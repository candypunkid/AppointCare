$ws = New-Object Microsoft.PowerShell.Commands.WebRequestSession

# Step 1: GET /login to extract CSRF token
$loginPage = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/login' -WebSession $ws -UseBasicParsing
$html = $loginPage.Content
if ($html -match 'name="_token" value="([^"]+)"') {
    $token = $matches[1]
    Write-Output "CSRF Token extracted: $token"
} else {
    Write-Output "ERROR: Could not extract CSRF token"
    exit 1
}

# Step 2: POST /login with credentials
$body = @{
    _token = $token
    email = 'testadmin@test.local'
    password = 'password123'
}
$postResponse = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/login' -Method POST -Body $body -WebSession $ws -UseBasicParsing -MaximumRedirection 0 -ErrorAction SilentlyContinue
$postStatus = $postResponse.StatusCode
Write-Output "POST /login status: $postStatus"
if ($postResponse.Headers['Location']) {
    Write-Output "POST /login redirect location: $($postResponse.Headers['Location'])"
}

# Step 3: GET /login again - should redirect to dashboard
$getResponse = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/login' -WebSession $ws -UseBasicParsing -MaximumRedirection 0 -ErrorAction SilentlyContinue
$getStatus = $getResponse.StatusCode
Write-Output "GET /login status (after login): $getStatus"
if ($getResponse.Headers['Location']) {
    Write-Output "GET /login redirect location: $($getResponse.Headers['Location'])"
    if ($getResponse.Headers['Location'] -like '*admin*' -or $getResponse.Headers['Location'] -like '*dashboard*') {
        Write-Output "SUCCESS: Redirected to dashboard!"
    } else {
        Write-Output "WARNING: Unexpected redirect location"
    }
} else {
    Write-Output "ERROR: No redirect from GET /login after authentication"
}

# Step 4: Check logs
Write-Output ""
Write-Output "=== Latest logs ==="
Get-Content storage/logs/laravel.log -Tail 15
